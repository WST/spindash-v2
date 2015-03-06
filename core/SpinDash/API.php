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

abstract class API
{
	const FRONTEND_BASIC = 0;
	const FRONTEND_FASTCGI = 1;
	const FRONTEND_CLISERVER = 2;
	const FRONTEND_PHPSGI = 3;

	private $frontend;

	public function __construct($frontend = self::FRONTEND_BASIC) {
		$this->frontend = $frontend;
	}

	public function run() {
		return function($request) {};
	}
}
