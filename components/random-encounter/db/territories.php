<?php
/**
 * Monster Table
 * @extends FCDBTableCreation
 * @extends FCDBTableInteraction
 *
 * Table Name: monsters
 * Table Schema:
 *
 * 		COLUMN                 Data Type               Defualt
 * 		---------------        ---------------         ---------------
 * 		ID                     INT(6)                  N/A
 * 		name                   VARCHAR(255)            NULL (NOT NULL)
 * 		size                   VARCHAR(255)            NULL (NOT NULL)
 * 		type                   VARCHAR(255)            NULL (NOT NULL)
 * 		tags                   VARCHAR(255)            NULL (NOT NULL)
 * 		alignment              VARCHAR(255)            NULL (NOT NULL)
 * 		challenge              DOUBLE                  NULL (NOT NULL)
 * 		xp                     INT(6)                  NULL (NOT NULL)
 * 		source                 VARCHAR(255)            NULL (NOT NULL)
 *
 *
 */
class FCTerritoriesTable extends FCDBTableInteraction {

    public function __construct() {
        $name = 'territories';
        $columns = array(
            array( 'mon_id',              'INT(6)',         'NOT NULL' ),
            array( 'arcrew',              'BOOL',           'NOT NULL' ),
            array( 'uthgar',              'BOOL',           'NOT NULL' ),
            array( 'hells_sands',         'BOOL',           'NOT NULL' ),
            array( 'northern_pass',       'BOOL',           'NOT NULL' ),
            array( 'void',                'BOOL',           'NOT NULL' ),
            array( 'salencia',            'BOOL',           'NOT NULL' ),
            array( 'swamps',              'BOOL',           'NOT NULL' ),
            array( 'none',                'BOOL',           'NOT NULL' ),
        );

        $this->defaults = array(
            'mon_id' => NULL,
            'arcrew' => FALSE,
            'uthgar' => FALSE,
            'hells_sands' => FALSE,
            'northern_pass' => FALSE,
            'void' => FALSE,
            'salencia' => FALSE,
            'swamps' => FALSE,
            'none' => FALSE,
        );

        $this->name = $name;
        $this->cols = $columns;

        parent::__construct();
        $this->reset();
    }

    public function populate() {}
}
