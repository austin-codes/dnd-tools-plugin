<?php
/**
 * Plugin Name: Fallen Crown
 * Plugin URI:
 * Description: D&D 5E Helpers
 * Author: Austin Adamson
 * Version: 0.0.1
 * Author URI: nerd.heavenfallsstud.io
 */


define( "FCROOT", trailingslashit(dirname(__FILE__)) );

define( "FCLIB", FCROOT . 'lib/');
define( "FCCOMP", FCROOT . 'components/');


require_once FCROOT . "helper-functions.php";
require_once FCLIB . 'core.php';
