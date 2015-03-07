<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash;

abstract class Router
{
	const FRONTEND_BASIC = 0;
	const FRONTEND_PHPSGI = 1;
	const FRONTEND_CGI = 2;
	const FRONTEND_FASTCGI = 3;

	// Frontend used
	private $frontend;

	// Request callbacks
	private $routes = [];
	private $middleware = [];

	// Cache engine
	private $cache = NULL;

	public function __construct($frontend) {
		$this->frontend = $frontend;
		$this->routes['get'] = [];
		$this->routes['post'] = [];
	}

	public function frontend() {
		return $this->frontend;
	}

	public function get($route, $callable) {
		$this->routes['get'][$route] = $callable;
	}
	
	public function post($route, $callable) {
		$this->routes['post'][$route] = $callable;
	}
	
	public function gp($route, $callable) {
		$this->routes['get'][$route] = $callable;
		$this->routes['post'][$route] = $callable;
	}

	private function selectHandler(Http\Request $request) {
		if(!array_key_exists($type = $request->method(), $this->routes)) {
			throw new Exceptions\CoreException("Unsupported request method {$type}");
		}
		
		$request_path = $request->requestUri();
		if(($pos = strpos($request_path, '?')) !== false) $request_path = substr($request_path, 0, $pos);
		foreach($this->routes[$type] as $k => $v) {
			$pattern = preg_replace(['#(:[a-z_]+?)#iU', '#(%[a-z_]+?)#iU'], ['([^/]+)', '([\\d]+)'], $k);
			$matches = [];
			if(preg_match("#^$pattern$#", $request_path, $matches)) {
				return count($matches) > 1 ? [$v, $matches] : [$v];
			}
		}
		
		throw new Exceptions\CoreException("no route matching {$request_path}");
	}

	private function handleRequest(Http\Request $request) {
		$response = new Http\Response($this);
		
		// Trying to fetch cached response
		if(!is_null($this->cache) && $this->cache->handleRequest($request, $response)) {
			return $response;
		}
		
		foreach($this->middleware as $callback) {
			if(!is_callable($callback)) continue;
			
			ob_start();
			call_user_func($callback, $request, $response);
			$error = ob_get_contents();
			ob_clean();
			
			if($error != '') {
				throw new Exceptions\CoreException($error);
			}
			
			if($response->ready()) {
				return $response;
			}
		}
		
		try {
			$handler = $this->selectHandler($request);
		} catch(Exceptions\CoreException $e) {
			$this->documentNotFound($request, $response);
			return $response;
		}
		
		if(count($handler[0]) == 3) {
			$handler[0] = [new $handler[0][0]($handler[0][2]), $handler[0][1]];
		}
		
		if(!is_callable($handler[0])) {
			$this->documentNotFound($request, $response);
			return $response;
		}
		
		isset($handler[1]) ? call_user_func($handler[0], $request, $response, $handler[1]) : call_user_func($handler[0], $request, $response);
		
		if(!is_null($this->cache)) {
			$this->cache->handleResponse($request, $response);
		}
		
		return $response;
	}

	private function handleBasicRequest() {
		$request = new Http\Request($this);
		$response = $this->handleRequest($request);

		if(!is_object($response) || !($response instanceof Http\Response)) {
			throw new Exceptions\CoreException("request handler has corrupted it’s Response instance");
		}

		return $response->sendBasic();
	}

	private function handleFastCGIClient($client) {
		socket_set_nonblock($client);
		
		$raw_request = '';
		while(true) {
			$recv = socket_read($client, 1024, PHP_BINARY_READ);
			if($recv == '') break;
			$raw_request .= $recv;
		}
		
		try {
			// Creating Request instance
			$request = new Http\Request($this);
			$request->parseFastCGIRequest($raw_request);
			if(is_object($response = $this->handleRequest($request))) {
				$response->sendFastCGI($client);
			} else {
				// Log an error
			}
		} catch(Exceptions\CoreException $e) {
			// Log an error
		}
	}
	
	private function serveFastCGI() {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($socket, '127.0.0.1', 8000);
		socket_listen($socket);
		
		while(($client = socket_accept($socket)) !== false) {
			$this->handleFastCGIClient($client);
			socket_close($client);
		}
	}

	public function handlePHPSGIRequest($env) {
		$request = new Http\Request($this, $env);
		$response = $this->handleRequest($request);

		if(!is_object($response) || !($response instanceof Http\Response)) {
			throw new Exceptions\CoreException("request handler has corrupted it’s Response instance");
		}

		return $response->sendPHPSGI();
	}

	public function simplePage($title, $body, $description = '') {
		$page = new IO\TextFile(SPINDASH_ROOT . 'misc' . DIRECTORY_SEPARATOR . 'simple-page.htt');
		$page->replace(['{TITLE}', '{BODY}', '{DESCRIPTION}', '{VERSION}'], [ucfirst($title), ucfirst($body), ucfirst($description), SPINDASH_VERSION]);
		return (string) $page;
	}

	public function documentNotFound(Http\Request $request, Http\Response $response) {
		if(count($this->routes['get'] == 0)) {
			$message = 'You don’t have any routes defined. You have to define the root route in your Site::routeMap';
		} else {
			$message = 'This means that you’ve requested something that does not exist within this site. If you beleive this should not happen, contact the website owner.';
		}

		$error_page = $this->simplePage('404 Not Found', 'Requested page was not found', $message);
		$response->setStatusCode(404);
		$response->setBody($error_page);
	}

	public function run() {
		switch($this->frontend) {
			case self::FRONTEND_BASIC:
				return $this->handleBasicRequest();
			break;
			case self::FRONTEND_FASTCGI:
				return $this->serveFastCGI();
			break;
			case self::FRONTEND_PHPSGI:
				return function($env) { $this->handlePHPSGIRequest($env); };
			break;
		}

		throw new Exceptions\CoreException('unknown frontend selected');
	}
}
