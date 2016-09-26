<?php


class FCDBTableCreation {

    protected $sqltable;

    protected $name;
    protected $cols;
    protected $defaults;

    public function __construct() {
        global $wpdb;
        $this->sqltable = $wpdb->prefix . $this->name;
        return $this->create_table();
    }

    public function sqltable() {
        return $this->sqltable;
    }

    /**
     * create_table()
     *
     * Take the class variable cols and render a mysql table
     * with respect to those settings, if the desired table doesn't
     * already exists.
     *
     * @return VOID
     */
    public function create_table() {
        global $wpdb;
        $table_check = $wpdb->get_var("SHOW TABLES LIKE '$this->sqltable'");
        if ($table_check != $this->sqltable) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $sql = "CREATE TABLE $this->sqltable (
    ID INT(10) NOT NULL AUTO_INCREMENT,";

            foreach ($this->cols as $col) {
                $sql .= '
    ' .$col[0] . ' ' . $col[1];
                if (isset($col[2])) {
                    $sql .= ' ' . $col[2];
                }

                $sql .= ',';

            }
            $sql .= '
    PRIMARY KEY (ID)';
            $sql .= "
)
CHARACTER SET utf8
COLLATE utf8_general_ci;";
            dbDelta($sql);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Add a column to the db table
     * @param STRING $column   The name of the column to be added
     * @param STRING $datatype The mysql datatype that is used in this column
     */
    public function add_column($column, $datatype, $descript = NULL) {
        global $wpdb;
        $sql = "ALTER TABLE $this->sqltable ADD $column $datatype";
        if (! is_null($descript) ) {
            $sql .= " $descript";
        }
        $wpdb->query($sql);
    }


    /**
     * Drop a column from the db table
     * @param STRING $column   The name of the column to be dropped
     * @param STRING $datatype The mysql datatype that is used in this column
     */
    public function drop_column($column) {
        global $wpdb;
        $wpdb->query("ALTER TABLE $this->sqltable DROP $column");
    }

    /**
     * Return array of defualt values
     * @return ARRAY The default values for the table
     */
    public function defaults() {
        return $this->defaults;
    }
}
