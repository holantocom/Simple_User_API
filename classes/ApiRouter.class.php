<?php

class ApiRouter {
    
    public $params = [];
    
    public function __construct() 
    {
        $this->validateParams();
    }
    
    private function validateParams() 
    {
        
        $route_url = rtrim($_GET['q'], '/');
        $this->params = explode('/', $route_url);
        $this->params[0] = ucfirst($this->params[0]); 
        
        if( !file_exists('classes/' . $this->params[0] . '.class.php') ){
            $this->buildResponse(TRUE, array('description' => 'Method not exist') );
        } else {
            $this->processingRequest();
        }
        
    }
    
    private function processingRequest()
    {
        
        $request = new $this->params[0];
        $function = $this->params[1];
        
        if($request->$function()){
            $this->buildResponse(FALSE, $request->result );
        } else {
            $this->buildResponse(TRUE, $request->lastError);
        }

    }
    
    private function buildResponse($errors, $data)
    {
        
        $answer = array();
        $answer['errors'] = $errors;
        $answer = array_merge($answer, $data);
        
        echo json_encode($answer);
        
    }
    
}