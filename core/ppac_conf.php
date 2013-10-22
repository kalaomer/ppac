<?php

/*
 * Simple Configration Setting Class.
 */

class PPAC_CONF {

	public static $ver = "0.0.1";

	/**
	 * Configration File.
	 */
	public static $data;

	/**
	 * Load Configration From file.
	 */
	public static function setup() {
		self::$data = require PPAC_ROOT . "config.php";
	}

	public static function get( $path ) {

		//	Yol Parçalanıyor.
		$path = explode(".", $path);

		//	Hedef olarak ile data'yı seç.
		$target_data = &self::$data;

		//	Yolları tek tek git.
		foreach ($path as $key => $way) {
			
			//	Eğer gidilecek yol yok ise..
			if (!isset($target_data[$way])) {
				return trigger_error("Way is wrong! Way: " . implode(".", $path));
			}
			
			//	Yolu aşama aşama git ve hedefi daralt.
			$target_data = &$target_data[$way];
		}

		return $target_data;
	}

	public static function set( $path, $val ) {
		
		//	Yol Parçalanıyor.
		$path = explode(".", $path);
		
		//	En son değiştirilecek olan yerin adresi.
		$target = array_pop($path);
		
		$target_data = &self::$data;

		foreach ($path as $way) {
			if (!isset($target_data[$way]))
				$target_data[ $way ] = [];
			$target_data = &$target_data[$way];
		}

		return $target_data[ $target ] = $val;
	}

}