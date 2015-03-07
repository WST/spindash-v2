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

define('SPINDASH_ROOT', __DIR__ . '/');
define('SPINDASH_VERSION', '2.0.0-git');

abstract class Application extends Router implements Interfaces\IApplication
{
	public function __construct($configuration_file) {
		@ set_exception_handler([$this, 'handleException']);

		if(!file_exists($configuration_file)) {
			throw new Exceptions\IOException('File does not exist', $configuration_file);
		}

		if(!file_exists($configuration_file) || !is_readable($configuration_file)) {
			throw new Exceptions\IOException('Access denied', $configuration_file);
		}

		require $configuration_file;

		if(!isset($config)) {
			throw new Exceptions\CoreException('The given configuration file does not define $config array');
		}
	}

	public function handleException(\Exception $e) {
		die($this->simplePage('General error', $e->getMessage(), 'This could happen because of an error in the web application’s code, settings or database. If you are the owner of this website, contact your web programming staff.'));
	}
}
