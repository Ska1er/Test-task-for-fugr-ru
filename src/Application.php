<?php
namespace App;

use App\Controllers\NotebookController;
use App\Traits\Response;

class Application {
    private array $controllers;

    use Response;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    public function start()
    {
        $this->setHeaders();

        $method = $_SERVER['REQUEST_METHOD'];
        $params = $this->getParams();

        switch($method){
            case 'GET':{
                $this->getHandler($params);
                break;
            }
            case "POST":{
                $this->postHandler($params);
                break;
            }
            case "DELETE":{
                $this->deleteHandler($params);
                break;
            }
            
            default:{
                $response = $this->simpleResponse(501, 'Send method error', false);
                $this->sendResponse($response);
                break;
            }
        }
    }

    /**
     * Getting url after domain
     * 
     * @return array $params
     */
    private function getParams()
    {
        if(isset($_GET['page'])){

            $params = explode("/", $_GET['page']);

            if(count($params) > 0 && array_shift($params) == "api" && array_shift($params) == "v1"){
                return $params;
            }
        }

        $response = $this->simpleResponse(400, 'Bad url', false);
        $this->sendResponse($response);
    }

    private function postHandler($params)
    {
        $response = "";
        switch($params[0])
        {
            case "notebook": {
                $notebookController = $this->controllers[NotebookController::class];

                if(!isset($params[1])){
                    $response = $notebookController->add($this->getPostValues(), $this->getFile('image'));
                    break;
                }
                
                if(filter_var($params[1], FILTER_VALIDATE_INT)){
                    $response = $notebookController->update(intval($params[1]), $this->getPostValues(), $this->getFile('image'));
                    break;
                }

                $response = $this->simpleResponse(404, 'Bad url', false);
                break;
            }
            default: {
                $response = $this->simpleResponse(400, 'Bad url', false);
                
                break;
            }
        }

        $this->sendResponse($response);
    }

    private function getHandler($params)
    {
        $response = "";
        switch($params[0])
        {
            case "notebook": {
                $notebookController = $this->controllers[NotebookController::class];
                
                if(!isset($params[1])){
                    $response = $notebookController->getAll();
                    break;
                }

                if(filter_var($params[1], FILTER_VALIDATE_INT)){
                    $response = $notebookController->getOne(intval($params[1]));
                    break;
                }
                
                if($params[1] == "page" && isset($params[2]) && filter_var($params[2], FILTER_VALIDATE_INT)){
                    $response = $notebookController->getPage($params[2]);
                    break;
                }

                if($params[1] == "image" && isset($_GET['image'])){
                    $response = $notebookController->getImage($_GET['image']);
                    break;
                }
                
                $response = $this->simpleResponse(400, 'Bad request', false);
                break;
            }
            default: {
                $response = $this->simpleResponse(400, 'Bad request', false);
                break;
            }
        }
        $this->sendResponse($response);
    }
        

    private function deleteHandler($params)
    {
        $response = "";
        switch($params[0])
        {
            case "notebook": {
                if(isset($params[1]) && filter_var($params[1], FILTER_VALIDATE_INT)){
                    $notebookController = $this->controllers[NotebookController::class];
                    $response = $notebookController->remove(intval($params[1]));
                    break;
                }

                $response = $response = $this->simpleResponse(400, 'Bad request', false);
                break;
            }
            default: {
                $response = $this->simpleResponse(400, 'Bad request', false);
                break;
            }
        }

        $this->sendResponse($response);
    }

    private function setHeaders()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Credentials: true');
        header('Content-type: json/application');  
    }

    private function sendResponse($response)
    {
        echo $response;
        die();
    }

    private function getPostValues(){
        $params = [];
        foreach($_POST as $key => $value)
            $params[$key] = strip_tags($_POST[$key]);
        return $params;
    }

    private function getFile(string $name){
        if(isset($_FILES[$name]))
            return $_FILES[$name];
        else null;
    }
}

