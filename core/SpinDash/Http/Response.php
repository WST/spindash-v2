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
	private $status_code = 200;
	private $body = '';

	public function setBody($data) {
		$this->body = $data;
	}

	public function sendBasic() {
		echo $this->body;
	}

	public function setStatusCode($code) {
		$this->code = $code;
	}
}
