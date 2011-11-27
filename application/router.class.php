<?php

class router {
 /*
 * @the registry
 */
 private $registry;

 /*
 * @the controller path
 */
 private $path;

 private $args = array();

 public $file;

 public $controller;

 public $action; 

 function __construct($registry) {
        $this->registry = $registry;
 }

 /**
 *
 * @set controller directory path
 *
 * @param string $path
 *
 * @return void
 *
 */
 function setPath($path) {

	/*** check if path i sa directory ***/
	if (is_dir($path) == false)
	{
	     throw new Exception ('Invalid controller path: `' . $path . '`');
	}
	/*** set the path ***/
 	$this->path = $path;
}


 /**
 *
 * @load the controller
 *
 * @access public
 *
 * @return void
 *
 */
 public function loader() {
	 
    /*** check the route ***/
    $this->getController();

    /*** if the file is not there diaf ***/
    if (is_readable($this->file) == false) {
	$this->file = $this->path.'/error404.php';
        $this->controller = 'error404';
    }

    /*** include the controller ***/
    include $this->file;

    /*** a new controller class instance ***/
    $class = $this->controller . 'Controller';
    $controller = new $class($this->registry);

    /*** check if the action is callable ***/
    if (is_callable(array($controller, $this->action)) == false) {
	$action = 'index';
    } else {
	$action = $this->action;
    }

    /*** run the action ***/
    $controller->$action();

 }


 /**
 *
 * @get the controller
 *
 * @access private
 *
 * @return void
 *
 */
 private function getController() {

    $this->registry->ajax  = 'false'; 	//init var
    $this->registry->ajaxaction  = 'false'; 	//init var

    /*** get the route from the url ***/
    $route = (empty($_GET['rt'])) ? '' : $_GET['rt'];

    if (empty($route)) {
	$route = 'index';
    } else {
	/*** get the parts(controller/action) of the route ***/
	$parts = explode('/', $route);

	$this->controller = $parts[0]; //implied isset

	if(isset($parts[1])) {
	    $this->action = $parts[1];
	}

	if(isset($parts[2])) {
	    $this->param[] = $parts[2];
	}
	if(isset($parts[3])) {
	    $this->param[] = $parts[3];
	}
	if(isset($parts[4])) {
	    $this->param[] = $parts[4];
	}
    }


    /*** Set default controller ***/
    if (!isset($this->controller)) {
	$this->controller = 'index';
    }

    /*** Set default action ***/
    if (!isset($this->action)) {
	$this->action = 'index';
    }

    /*** Set default param ***/
    if (!isset($this->param[0])) {
	$this->param[0] = 'false';
    } 


    /*** Send param to registry for use in controller/view ***/
    $this->registry->param = $this->param;
    $this->registry->action = $this->action;
    $this->registry->controller = $this->controller;


    /*** Check for ajax request ***/
    $subaction = explode('=', $this->action);
    if ($subaction[0] == 'ajax') {
	$this->action = 'index';
	$this->registry->ajax = 'true';
	$this->registry->ajaxaction = $subaction[1];
    }

    /*** Set the php file path.name ***/
    $this->file = $this->path .'/'. $this->controller . 'Controller.php';

    /*** Check for file.  Set default in case needed. ***/
    if (is_readable($this->file) == false) {
	$this->file = $this->path .'/indexController.php';
	$this->controller = 'index';
    }

 } // end function


} // end class

?>
