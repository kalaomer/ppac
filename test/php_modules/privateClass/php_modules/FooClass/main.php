<?php

var_dump( "EasyArray is added. " . __FILE__ . " adding's return value: " . PPAC::add( "EasyArray", array("return_require" => true) ) );

class FooClass {

	function __construct() {
		echo __CLASS__ . " is loaded.";
	}

}