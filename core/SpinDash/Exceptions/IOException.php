<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash\Exceptions;

class IOException extends CoreException
{
	public function __construct($message, $file) {
		parent::__construct("IOException: $message\nFile: $file");
	}
}
