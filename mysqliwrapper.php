<?php

/*
	phpMysqliWrapper by vjeux, August 2009
	<vjeuxx@gmail.com> http://blog.vjeux.com/2009/php/mysqli-wrapper-short-and-secure-queries.html

	Write short and secure queries with mysqli
*/

/*
	// Example
	
	// Connect to the database:
	// The last parameter is the debug mode.
	
	$db = new dbWrapper('host', 'user', 'pass', 'database', true);

	// Do the query:
	// It fits into a single line now
	// mysqli replaces '?' safely with your parameters, sql injections are no longer possible

	$result = $db->q(
		"SELECT name, country_code FROM city WHERE zip = ? AND population > ?",
		'si',        // Types of the parameters: s for string, i for integer
		$zip, $pop); // Parameters

	// Use the results:
	// The results are returned in an array! This makes it a lot easier to process

	foreach ($result as $key => $city) {
	  // $city['Name'], $city['CountryCode']
	}

	// Additional notes:
	// It returns false when an error happens
	// It returns the number of affected rows for UPDATE and INSERT queries
	// Use $mysqli = $db->handle() to get the mysqli object
*/

Class mysqliWrapper {
    protected $_mysqli;
    protected $_debug;
 
    public function __construct($host, $username, $password, $database, $debug) {
        $this->_mysqli = new mysqli($host, $username, $password, $database);
        $this->_debug = (bool) $debug;
        if (mysqli_connect_errno()) {
            if ($this->_debug) {
                echo mysqli_connect_error();
                debug_print_backtrace();
            }
            return false;
        }
        return true;
    }
 
    public function q($query) {
        if ($query = $this->_mysqli->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_merge(array(func_get_arg(1)),
                    array_slice($x, 2));
                $args_ref = array();
                foreach($args as $k => &$arg) {
                    $args_ref[$k] = &$arg; 
                }
                call_user_func_array(array($query, 'bind_param'), $args_ref);
            }
            $query->execute();
 
            if ($query->errno) {
              if ($this->_debug) {
                echo mysqli_error($this->_mysqli);
                debug_print_backtrace();
              }
              return false;
            }
 
            if ($query->affected_rows > -1) {
                return $query->affected_rows;
            }

            $params = array();
            $meta = $query->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($query, 'bind_result'), $params);
 
            $result = array();
            while ($query->fetch()) {
                $r = array();
                foreach ($row as $key => $val) {
                    $r[$key] = $val;
                }
                $result[] = $r;
            }
            $query->close(); 
            return $result;
        } else {
            if ($this->_debug) {
                echo $this->_mysqli->error;
                debug_print_backtrace();
            }
            return false;
        }
    }
 
    public function handle() {
        return $this->_mysqli;
    }
}


?>