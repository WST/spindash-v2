<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

final class SpinDash_WebApp
{
	public function __construct($configuration_file) {

	}

	/**
	* returns PHPSGI callable when using PHPSGI frontend
	*/
	public function run() {
		return function($request) {};
	}
}
