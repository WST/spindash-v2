<?php

namespace SpinDash\Exceptions;

class IOException extends CoreException
{
	public function __construct($message, $file) {
		parent::__construct("IOException: $message\nFile: $file");
	}
}
