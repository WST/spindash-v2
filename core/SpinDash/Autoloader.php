<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

class SpinDash_Autoloader
{
	public static function register($prepend = false) {
		if (PHP_VERSION_ID < 50300) {
			spl_autoload_register(array(__CLASS__, 'autoload'));
		} else {
			spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
		}
	}

	public static function autoload($class) {
		if (0 !== strpos($class, 'SpinDash')) {
			return;
		}

		$file = __DIR__ . '/' . strtolower($class) . '.php';
		if(is_file($file)) require $file;
	}
}
