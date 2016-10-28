<?php

use PDO as PDO;

class Db extends PDO {
	private static $instance = NULL;

	public static function getInstance() {

    	if (!isset(self::$instance)) {
	    	try {
	    		$config = parse_ini_file('config.ini'); 
		        $type = $config['dbtype'];
		        $host = $config['dbhost'];
		        $name = $config['dbname'];
		        $user = $config['dbuser'];
		        $pass = $config['dbpass'];	    	
		        	
	        	$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	        	self::$instance = new Db("$type:host=$host;dbname=$name;charset=utf8", 
	        				$user, $pass, $options);
	        } catch(\Exception $e) {
	        	exit($e->getMessage());
	        }
      	}

		return self::$instance;
	}
}
