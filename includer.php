<?php

/*
 * v0.0.1
 * Writen by Ömer KALA ( kalaomer@hotmail.com )
 * 2013
 *
 * Load PPAC Includer mode!
 * This mode is design for include modules and we try to write very light.
 *
 */

/*
 * If PPAC didn't load, LOAD IT!
 */
if ( !class_exists( "PPAC" ) ) {

	if ( !defined("DS") )
		define( "DS", DIRECTORY_SEPARATOR );

	//	PPAC ROOT path.
	if ( !defined( "PPAC_ROOT" ) )
		define( "PPAC_ROOT", __DIR__ . DS);

	//	PPAC ROOT path.
	if ( !defined( "PPAC_CORE" ) )
		define( "PPAC_CORE", PPAC_ROOT . "core" . DS);

	//	PPAC Pack Includer is loading.
	require_once PPAC_CORE . "ppac_pack_includer.php";

	// Create PPAC Class that is child of PPAC_PACK_INCLUDER for easy access.
	class PPAC extends PPAC_PACK_INCLUDER {};

	PPAC::setup();
}