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

final class Response
{
	private $code = 200;
	private $body = '';

	public function setBody($data) {
		$this->body = $data;
	}

	public function statusCodeDescription() {
		switch($this->code) {
			case 404: return 'Not Found'; break;
			default: return 'OK'; break;
		}
	}

	public function sendBasic() {
		if($this->code !== 200) {
			header("HTTP/1.1  {$this->code} " . $this->statusCodeDescription());
		}
		echo $this->body;
	}

	public function sendPHPSGI() {
		return ["{$this->code} " . $this->statusCodeDescription(), ['Content-Type' => 'text/html'], $this->body];
	}

	public function setStatusCode($code) {
		$this->code = $code;
	}
}
