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

	public function __construct(\Spindash\Application $application) {
		switch($application->frontend()) {
			case \Spindash\Router::FRONTEND_BASIC:
				$this->data['get'] = & $_GET;
				$this->data['post'] = & $_POST;
				$this->data['cookie'] = & $_COOKIE;
				$this->files = & $_FILES;

				$this->method = $_SERVER['REQUEST_METHOD'];
			break;
			default:
				throw new \Spindash\Exceptions\CoreException('Not implemented');
			break;
		}
	}

	public function method() {
		return strtolower($this->method);
	}
}
