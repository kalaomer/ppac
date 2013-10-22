<?php

/*
 * v0.0.1
 *
 * PPAC configration file.
 * All configrations is in this file.
 */

$config["php_modules"] = array(
	/*
	 * Public folder settings.
	 */
	"public" => array(
		/*
		 * When searching module, add module pool first for seaching.
		 */
		"add_first" => false,
		/*
		 *	Public php module folder path.
		 */
		"path" => PPAC_ROOT . "php_modules" . DS
	),

	/*
	 * Private folder settings.
	 */
	"private" => array(
		/*
		 *	Folder name which folder that ise module folder is in there.
		 */
		"folder_name" => "php_modules",

		/*
		 * No settings for Module now :(
		 */
		"module" => array(
			
		)
	),

	"common" => array(
		"module" => array(
			/*
			 * Module settings file name.
			 * Don't Change It! :)
			 */
			"settings_file_name" => "package.json",
			/**
			 * If module(or module file) added and request is want to add again...
			 * This is very sensitive opition! So be careful!
			 */
			"reloadable" => true,
			/*
			 * When require some file, add() function return require's return value.
			 * This is very sensitive opition! So be careful!
			 */
			"return_require" => true,
			/*
			 * When Class isn't added and need it, Add Class automaticly..
			 * This is very sensitive opition! So be careful!
			 */
			"auto_load_class" => true
		)
	)
);

return $config;