<?php

class User {
    
    public $lastError;
    public $result;
    private $DB;
    
    public function __construct()
    {
        $this->DB = Database::getInstance();
    }
    
    public function add() 
    {
        if(!$this->validateInput(array('login', 'password'), $GLOBALS['_POST'])){
            return FALSE;
        }
        
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $name = $_POST['name'] ?? '0';
        $surname = $_POST['surname'] ?? '0';
        $email = $_POST['email'] ?? '0';
        $permission = $_POST['permission'] ?? 0;
			
        $SQL = "SELECT * FROM users WHERE login=?";
        $request = $this->DB->loadData($SQL, array($login));

    	if($request['count'] != 0){
            $this->lastError = array('description' => "User already exist");
            return FALSE;
        }
		
        $SQL = "INSERT INTO users (`login`, `password`, `permission`, `email`, `name`, `surname`) VALUES (?, ?, ?, ?, ?, ?)";
        $request = $this->DB->loadData($SQL, array($login, $password, $permission, $email, $name, $surname));
						
        $this->result = array('description' => "User added successfully", 'user_id' => $request['insert_id']);
        return TRUE;					
        
    } 
    
    public function check()
    {
        if(!$this->validateInput(array('login', 'password'), $GLOBALS['_GET'])){
            return FALSE;
        }
        
        $login = $_GET['login'];
        $password = $_GET['password'];
		
        $SQL = "SELECT `id`, `password` FROM users WHERE login=?";
        $request = $this->DB->loadData($SQL, array($login));

        if($request['count'] == 0){
            $this->lastError = array('description' => "user not found");
            return FALSE;
        }
		
        if (password_verify($password, $request['password'])){
            $this->result = array('user_id' => $request['id']);
            return TRUE;
        } else {
            $this->lastError = array('description' => "Password incorrect");
            return FALSE;
        }
    }
    
    public function info()
    {
        if(!$this->validateInput(array('id'), $GLOBALS['_GET'])){
            return FALSE;
        }
        
        $id = $_GET['id'];
        
        $SQL = "SELECT `id` as `user_id`, `login`, `permission`, `email`, `name`, `surname` FROM users WHERE id = ?";
        $request = $this->DB->loadData($SQL, array($id));

        if($request['count'] == 0){
            $this->lastError = array('description' => "id not found");
            return FALSE;
        }
        unset($request['count']);
        unset($request['insert_id']);
		    
        $this->result = $request;
        return TRUE;
    }
    
    public function update()
    {
        if(!$this->validateInput(array('id'), $GLOBALS['_POST'])){
            return FALSE;
        }
        
        $id = $_POST['id'];
		
        $SQL = "SELECT * FROM users WHERE id = ?";
        $request = $this->DB->loadData($SQL, array($id));

        if($request['count'] == 0){
            $this->lastError = array('description' => "id not found");
            return FALSE;
        }
        
        if(isset($_POST['password'])){
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            $password = $request['password'];
        }
        
        if(isset($_POST['login'])){
    		
            $SQL = "SELECT * FROM users WHERE login=?";
            $checkLogin = $this->DB->loadData($SQL, array($_POST['login']));
    
            if($checkLogin['count'] != 0){
                $this->lastError = array('description' => "Login already exist");
                return FALSE;
            }
    		
            $login = $_POST['login'];
    		
        } else {
            $login = $request['login'];
        }
        
        $userID = $request['id'];
        $name = $_POST['name'] ?? $request['name'];
        $surname = $_POST['surname'] ?? $request['surname'];
        $email = $_POST['email'] ?? $request['email'];
        $permission = $_POST['permission'] ?? $request['permission'];
		
        $SQL = "UPDATE users SET `login`=?,`password`=?,`permission`=?,`email`=?,`name`=?,`surname`=? WHERE id = ?";
        $request = $this->DB->loadData($SQL, array($login, $password, $permission, $email, $name, $surname, $userID));
						
        $this->result = array('description' => "User updated successfully");
        return TRUE;	
    }
    
    private function validateInput($requiredParams, $inputParams)
    {
        if(count($inputParams) == 0){
            $this->lastError = array('description' => "Missing input"); 
            return FALSE;    
        }
        
        foreach($requiredParams as $param){
            
            if(!array_key_exists($param, $inputParams)){
                $this->lastError = array('description' => "parameter $param required"); 
                return FALSE;
            }
            
        }  
 
        return TRUE;
    }
    
    public function __call($method, $parameters) 
    {
        $this->lastError = array('description' => 'function not exist');  
        return FALSE;
    }
    
}