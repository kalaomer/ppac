<?php

class PrivateClass {
	function __construct() {
		echo __CLASS__ . " is loaded.";
		$fooClass = new FooClass();
	}
}