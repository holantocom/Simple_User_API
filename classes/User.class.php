<?php

class User {
    
    public $lastError;
    public $result;
    private $DB;
    
    public function __construct()
    {
        $this->DB = Database::getInstance();
    }
    
    public function add($method = '_POST') 
    {
        $inputParams = $GLOBALS[$method];
        
        if(!$this->validateInput(array('login', 'password'), $inputParams) || !$this->validatePhone($inputParams['login']) ){
            return FALSE;
        }
        
        $login = $inputParams['login'];
        $password = password_hash($inputParams['password'], PASSWORD_DEFAULT);
        
        $name = $inputParams['name'] ?? '';
        $surname = $inputParams['surname'] ?? '';
        $email = $inputParams['email'] ?? '';
        $permission = $inputParams['permission'] ?? 0;
			
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
    
    public function check($method = '_GET')
    {
        $inputParams = $GLOBALS[$method];
        
        if(!$this->validateInput(array('login', 'password'), $inputParams) || !$this->validatePhone($inputParams['login']) ){
            return FALSE;
        }
        
        $login = $inputParams['login'];
        $password = $inputParams['password'];
		
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
    
    public function info($method = '_GET')
    {
        $inputParams = $GLOBALS[$method];
        
        if(!$this->validateInput(array('id'), $inputParams)){
            return FALSE;
        }
        
        $id = $inputParams['id'];
        
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
    
    public function update($method = '_POST')
    {
        $inputParams = $GLOBALS[$method];
        
        if(!$this->validateInput(array('id'), $inputParams)){
            return FALSE;
        }
        
        $id = $inputParams['id'];
		
        $SQL = "SELECT * FROM users WHERE id = ?";
        $request = $this->DB->loadData($SQL, array($id));

        if($request['count'] == 0){
            $this->lastError = array('description' => "id not found");
            return FALSE;
        }
        
        if(isset($inputParams['password'])){
            $password = password_hash($inputParams['password'], PASSWORD_DEFAULT);
        } else {
            $password = $request['password'];
        }
        
        if(isset($inputParams['login'])){
            
            if(!$this->validatePhone($inputParams['login']) ){
                return FALSE;
            }
    		
            $SQL = "SELECT * FROM users WHERE login=?";
            $checkLogin = $this->DB->loadData($SQL, array($inputParams['login']));
    
            if($checkLogin['count'] != 0){
                $this->lastError = array('description' => "Login already exist");
                return FALSE;
            }
    		
            $login = $inputParams['login'];
    		
        } else {
            $login = $request['login'];
        }
        
        $userID = $request['id'];
        $name = $inputParams['name'] ?? $request['name'];
        $surname = $inputParams['surname'] ?? $request['surname'];
        $email = $inputParams['email'] ?? $request['email'];
        $permission = $inputParams['permission'] ?? $request['permission'];
		
        $SQL = "UPDATE users SET `login`=?,`password`=?,`permission`=?,`email`=?,`name`=?,`surname`=? WHERE id = ?";
        $request = $this->DB->loadData($SQL, array($login, $password, $permission, $email, $name, $surname, $userID));
						
        $this->result = array('description' => "User updated successfully");
        return TRUE;	
    }
    
    private function validatePhone($phone)
    {
        if(preg_match('/^\+?[0-9]+$/', $phone)){
            return TRUE;
        } else {
            $this->lastError = array('description' => "Invalid phone number"); 
            return FALSE;
        }
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