<?php

class Database {
    
    private $_connection;
    private static $_instance;
	
    private $_host = "host";
    private $_username = "username";
    private $_password = "password";
    private $_database = "database";

    private function __construct() 
    {
        
        $this->_connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
        $this->_connection->query("SET NAMES 'utf8'"); 
        $this->_connection->query("SET CHARACTER SET 'utf8'");
        $this->_connection->query("SET SESSION collation_connection = 'utf8_general_ci'");
	
        if(mysqli_connect_error()) {
            trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(), E_USER_ERROR);
        }
		
    }
	
    public static function getInstance() 
    {
        if(!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __clone() { }
	
    public function getConnection() 
    {
        return $this->_connection;
    }
	
    public function loadData($SQL, $params) 
    {
        $types = $this->prepareParams($params);
	    
        $query = $this->_connection->prepare($SQL);
        $query->bind_param($types, ...$params);
        $query->execute();

        $result = $query->get_result();
        $data['insert_id'] = $query->insert_id;
        $count = ($result !== FALSE) ? $result->num_rows : 0;
        $data['count'] = $count;
		
        if($count > 0){
            $data = array_merge($data, $result->fetch_array(MYSQLI_ASSOC));
        }
        
        $query->close();
        
        return $data;
    } 
	
    private function prepareParams($params)
    {
        $typesString = '';
        foreach ($params as $value) {
            if (is_int($value) ) {
                $typesString.='i';
            }
            if (is_string($value)) {
                $typesString.='s';
            }
        }
        return $typesString;
    }
	
}