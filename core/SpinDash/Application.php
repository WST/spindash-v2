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

abstract class Application extends Router implements Interfaces\IApplication
{
	public function __construct($configuration_file) {
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
}
