<?php

Class Template {

/*
 * @the registry
 * @access private
 */
private $registry;

/*
 * @Variables array
 * @access private
 */
private $vars = array();

/**
 *
 * @constructor
 *
 * @access public
 *
 * @return void
 *
 */
 function __construct($registry) {

    $this->registry = $registry;
 }


 /**
 *
 * @set undefined vars
 *
 * @param string $index
 *
 * @param mixed $value
 *
 * @return void
 *
 */
 public function __set($index, $value) {

    $this->vars[$index] = $value;
 }


 /**
 * 
 *  
 * 
 * 
 * 
 * 
 */
 public function show($content) {
    /***  Load $content for inclusion in layout  */
    $this->vars['content'] = $content;
    
    /*** Get Layout (set in Controller)		*/
    $layout = $this->vars['layout'];
    
    $lfile  = __SITE_PATH . '/views' . '/' . $layout . '.php';

    /***  content file name	*/
    $cfile  = __SITE_PATH . '/views' . '/' . $content . '.php';

    /*** Make sure layout and content files exist.	*/
    if (file_exists($lfile) == false) {

	throw new Exception('Layout Template not found in '. $lfile);
	return false;
    }

    if (file_exists($cfile) == false) {

	throw new Exception('Content Template not found in '. $cfile);
	return false;
    }

    /*** Load variables		*/
    foreach ($this->vars as $key => $value) {

	$$key = $value;
    }

    if(($this->registry->ajax == 'true')) {
        /*** Get content only		*/
        include ($cfile);
    } else {
        /*** Get layout(with content included)		*/
        include ($lfile);               
    }
 }

 /*
 public function ajax($content) {
    $cfile  = __SITE_PATH . '/views' . '/' . $content . '.php';

    if (file_exists($cfile) == false) {

	throw new Exception('Template not found in '. $cfile);
	return false;
    }

    foreach ($this->vars as $key => $value) {

	$$key = $value;
    }

    var_dump($this->registry->ajax);
    include ($cfile);               
 }
  */

}

?>
