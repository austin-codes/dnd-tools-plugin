<?php

/**
 * @TODO Take this existing class and alter it to work for ReferPro
 *
 * Make this an abstract DB Table handler that can be used for the API
 */

/**
* Class used to build and manipulate
* the AO Calendar event table
* @author Austin Adamson
*
* @since 1.0.0
*/
class FCDBTableInteraction extends FCDBTableCreation {

    protected $select = 'SELECT *';
    protected $from = 'FROM wp_posts';
    protected $where = '';
    protected $limit = '';
    protected $group_by = '';
    protected $order_by = '';

    protected $statement = NULL;

    public function __construct() {
        $r = parent::__construct();
        $this->from = "FROM " . $this->sqltable;
        return $r;
    }

    /**
     * CRUD methods for the database table
     */

    /**
     * @param [type] $data [description]
     */
    public function add($data) {
        global $wpdb;

        $row = array();
        foreach ( $this->defaults as $key => $val ) {
            if ( isset($data[$key]) ) {
                $row[$key] = $data[$key];
            }
            else {
                $row[$key] = $this->defaults[$key];
            }

        }

        foreach ( $row as $key=>$val ) {
            $row[$key] = $this->filter_input( $val );
        }
        //dump($row);
        $wpdb->show_errors();
        $val = $wpdb->insert( $this->sqltable, $row );
        //dump($val);
        if ($val == 1) {
            $val = array( 'ID' => $wpdb->insert_id);
        }
        return $val;
    }

    /**
     * Retrieves desired information from the database table.
     * @param  [type] $id   ID of specific desired row
     * @param  BOOL $indi   Whether or not you want a single response
     * @param  [type] $str  [description]
     * @param  BOOL     $reset  Whether or not the query variables need to be reset
     * @return [type]       [description]
     */
    public function get($id = NULL, $indi = FALSE, $str = NULL, $reset = TRUE) {
        global $wpdb;

        // If an ID is set then we know what row we want,
        // so we set the ID to be specifically that row.
        if (isset($id)) {
            $this->where('ID', $id);
        }

        // Build the statement.
        if (is_null($this->statement)) {
            $this->statement();
        }


        if ($indi) {
            $response = $wpdb->get_row($this->statement);
            if (is_null($response)) {return '';}
            if (count(get_object_vars($response)) == 1) {
                foreach ($response as $r) {
                    $result = $r;
                }
            }
            else {
                $result = $response;
            }
        }


        else {
            $result = $wpdb->get_results($this->statement);
        }

        // rp_console_log($this->statement);
        // rp_console_log($result, 'RESULTS');
        // echo $this->statement;
        // echo '<br><br>';
        if ($reset) {
            $this->reset();
        }
        //$this->statement();

        return $result;
    }

    /**
     * Update row.
     * @param  INT/STRING $id   ID of row to be changed
     * @param  ARRAY $data The information to be updated
     * @return
     */
    public function update($id, $data) {
        global $wpdb;

        if (!is_array($data)) {
            return;
        }
        return $wpdb->update($this->sqltable, $data, array('ID' => $id));
    }

    /**
     * Delete an entry at the database table
     * @param  INT/STRING $id ID of the row to be deleted
     * @return
     */
    public function delete($id) {
        global $wpdb;
        $wpdb->delete($this->sqltable, array('id' => $id));
    }

    /**
     * Statement Creation
     */

    /**
     * Format the select portion of the SQL statement.
     * @param  STRING/ARRAY $select String with values or array of values to select
     * @return
     */
    public function select($select) {

        $this->select = 'SELECT ';

        // Check to see if param is an array
        if (is_array($select)) {

            // Filter through elements of the array and appending to
            // select var
            foreach ($select as $item) {

                // If the count or the array - 1 is equal to the index of this element
                // then this element is the last and therefore doesn't require a
                // trailing comma.
                $last = (count($select) - 1 == array_search($item, $select));

                if ($last) {
                    $this->select .= $item;
                }
                else {
                    $this->select .= $item . ', ';
                }

            }
        }
        // Param wasn't an array, simply set the select var.
        else {
            $this->select .= $select;
        }
    }

    /**
     * Format the from join portions of the MySQL query
     *
     * TODO: Not thoroughly tested.
     *
     * @param  [type] $left_table  [description]
     * @param  [type] $right_table [description]
     * @param  string $join        [description]
     * @return [type]              [description]
     */
     public function from($left_table, $right_table = NULL, $on = NULL, $join = 'LEFT JOIN' ) {
        $f = 'FROM ';

        if ( !is_null($right_table) ) {
            $f .= $left_table . ' ' . $join . ' ' . $right_table . ' ON ' . $on;
        }
        else {
            $f .= $left_table . ' ';
        }

        $this->from = $f;
    }

    /**
     * Build the where portion of the MySQL query
     * @param  STRING/ARRAY $source String: Column. Array: View schema below.
     * @param  [type] $target [description]
     * @param  string $comp   [description]
     * @return [type]         [description]
     */
    public function where($source, $target = NULL, $comp = '=') {
        if (is_array($source)) {
            /**
             * $source array schema:
             * array(
             * 		array($source, $target, $comp, $follow),
             * 		array($source, $target, $comp, $follow),
             * 		array($source, $target, $comp, $follow)
             * );
             *
             *
             */

            $sql = 'WHERE ';

            if (!is_array($source[0]) ) {
                $source = array($source);
            }
            foreach ($source as $key=>$s) {

                // Make sure each element is an array
                if (is_array($s)) {
                    // Set the source of the sub array
                    $sql .= $s[0] . ' ';

                    // Is the comp val set in the sub array
                    if (isset($s[2])) {
                        $sql .= $s[2] . ' ';
                    }
                    else {
                        $sql .= '= ';
                    }

                    // set the target of the sub array
                    if (is_string($s[1])) {
                        //dump(is_int(strpos( $s[1], $this->sqltable)), 'Pos');
                        if (is_int(strpos( $s[1], $this->sqltable))) {
                            $s[1] = $s[1];
                        }
                        else {
                            $s[1] = '"' . $s[1] . '"';
                        }
                    }
                    $sql .= $s[1] . ' ';


                    if (count($source) - 1 == $key) {
                        $sql .= '';
                    }
                    else {
                        if ($target) {
                            $sql .= $target . ' ';
                        }
                        else {
                            $sql .= 'AND ';
                        }

                    }
                }
            }
        }

        else {

            $sql = 'WHERE ' . $source . ' ' . $comp . ' ' . $target . ' ';

        }

        $this->where = $sql;

    }

    /**
     *
     */
    public function limit($limit, $offset = 0) {

        if ( !is_int($offset) ) {
            $offset = intval($offset);
        }

        if ( !is_int($limit) ) {
            $limit = intval($limit);
        }

        if ($offset !== 0) {
            $this->limit = 'LIMIT ' . $offset . ',' . $limit;
        }
        else {
            $this->limit = 'LIMIT ' . $limit;
        }

    }

    public function group_by($gb) {
        if (is_string($gb)) {
            $this->group_by = ' GROUP BY ' . $gb;
        }
    }

    public function order_by($ob) {
        if (is_string($ob)) {
            $this->order_by = ' ORDER BY ' . $ob;
        }
    }


    /**
     * Format the statement
     * @param  [type] $stmt Optional statement to use.
     * @return [type]       [description]
     */
    public function statement($stmt = NULL, $return_current = NULL) {
        if (!is_null($return_current)) {
            if ( is_null($this->statement) ) {
                return $this->select . ' ' . $this->from . ' ' . $this->where . ' ' . $this->group_by . $this->order_by . $this->limit;
            }
            else {
                return $this->statement;
            }
        }
        if (is_null($stmt)) {
            $this->statement = $this->select . ' ' . $this->from . ' ' . $this->where . ' ' . $this->group_by . $this->order_by . $this->limit;
        }
        else {
            $this->statement = $stmt;
        }
        //return $this->statement;
    }


    /**
     * Other Methods
     */

    /**
     * Cleanse input to prevent harmful additions to DB
     * @param  STRING $val Data to be put into the DB table
     * @since 1.0.0
     * @return STRING      Data cleansed to be added to DB table
     */
    public function filter_input($val) {
        $input = esc_html($val);
        return $input;
    }

    /**
     * Reset class variables to defualts
     */
    public function reset() {
        global $wpdb;
        $this->sqltable = $wpdb->prefix .  $this->name;
        $this->select = 'SELECT *';
        $this->from = 'FROM ' . $this->sqltable;
        $this->where = '';
        $this->group_by = '';
        $this->order_by = '';
        $this->statement = NULL;
    }



}
