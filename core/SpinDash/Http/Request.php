<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash\Http;

final class Request
{
	private $method;
	private $data = [];
	private $files = [];
	private $request_uri = '';

	public function __construct(\Spindash\Router $router, $env = NULL) {
		switch($router->frontend()) {
			case \Spindash\Router::FRONTEND_BASIC:
				$this->data['get'] = & $_GET;
				$this->data['post'] = & $_POST;
				$this->data['cookie'] = & $_COOKIE;
				$this->files = & $_FILES;

				$this->method = $_SERVER['REQUEST_METHOD'];
				$this->request_uri = $_SERVER['REQUEST_URI'];
			break;

			case \Spindash\Router::FRONTEND_PHPSGI:
				$query_string = $env['QUERY_STRING'];
				$this->method = $env['REQUEST_METHOD'];
				$this->request_uri = $env['REQUEST_URI'];

				$parts = [];
				parse_str($query_string, $parts);
			break;

			default:
				throw new \Spindash\Exceptions\CoreException('Not implemented');
			break;
		}
	}

	public function requestUri() {
		return $this->request_uri;
	}

	public function method() {
		return strtolower($this->method);
	}
}
