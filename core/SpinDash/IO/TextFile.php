<?php

/**
* SpinDash — A web development framework
* © 2007–2015 Ilya I. Averkov
*
* Contributors:
* Irfan Mahfudz Guntur <ayes@bsmsite.com>
* Evgeny Bulgakov <evgeny@webline-masters.ru>
*/

namespace SpinDash\IO;

class TextFile
{
	private $data;
	private $filename;
	
	public function __construct($filename) {
		// TODO: ability not to store entire file in the memory
		if(! @ file_exists($filename)) {
			throw new \SpinDash\Exceptions\IOException('not found', $filename);
		}
		if(! @ is_readable($filename)) {
			throw new \SpinDash\Exceptions\IOException('access denied', $filename);
		}
		$this->data = file_get_contents($filename);
		$this->filename = $filename;
	}
	
	public function __destruct() {
		
	}
	
	public function save() {
		file_put_contents($this->filename, $this->data);
	}
	
	public function setData($text) {
		$this->data = $text;
	}
	
	public function replace($from, $to) {
		$this->data = str_replace($from, $to, $this->data);
	}
	
	public function __toString() {
		return $this->data;
	}
}
