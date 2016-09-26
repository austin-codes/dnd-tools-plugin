<?php

define( "FC_RANDOM_ENCOUNTER", trailingslashit(dirname(__FILE__)) );

define( "FC_RAND_ENC_URL", trailingslashit( plugin_dir_url( __FILE__ ) ) );


require_once( FC_RANDOM_ENCOUNTER . 'shortcode.php' );
require_once( FC_RANDOM_ENCOUNTER . 'db/monsters.php' );
require_once( FC_RANDOM_ENCOUNTER . 'db/territories.php' );

require_once( FC_RANDOM_ENCOUNTER . 'admin/monster-sort.php' );


global $monsters;
$monsters = new FCMonsterTable();


global $territories;
$territories = new FCTerritoriesTable();


function fc_encounter_admin_scripts() {
        wp_register_style( 'fc_encounter_admin_scripts', FC_RAND_ENC_URL . 'admin/css/styles.css', false, '1.0.0' );
        wp_enqueue_style( 'fc_encounter_admin_scripts' );
}
add_action( 'admin_enqueue_scripts', 'fc_encounter_admin_scripts' );


function fc_encounter_scripts() {
        wp_register_style( 'fc_encounter_scripts', FC_RAND_ENC_URL . 'css/styles.css', false, '1.0.0' );
        wp_enqueue_style( 'fc_encounter_scripts' );
}
add_action( 'wp_enqueue_scripts', 'fc_encounter_scripts' );
