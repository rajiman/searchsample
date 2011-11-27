<?php
 session_start();
 ob_start();

 /*** error reporting on ***/
 error_reporting(E_ALL);

 /*** define the site root for css and img src tags relative to www ***/
 $work_dir    = (basename(dirname(dirname(dirname(__FILE__)))));
 $project_dir = (basename((dirname(dirname(__FILE__)))));
 $site_dir    = (basename(((dirname(__FILE__)))));
 $site_root   = '/'.$work_dir.'/'.$project_dir.'/'.$site_dir.'/';
 //$site_root   = '/'.$project_dir.'/'.$site_dir.'/';
 //$site_root   = '/'.$site_dir.'/';
 //$site_root   = '/';
 define ('__SITE_ROOT', $site_root);

 /*** define the site path ***/
 $site_path = realpath(dirname(__FILE__));
 define ('__SITE_PATH', $site_path);


 /*** define PageNavigator constants.		***/
 define("PAGELINKS", 3);
 define("PERPAGE", 4);
 define("OFFSET", "offset");
 
  /*** include the init.php file ***/
 include 'includes/init.php';

 /*** load the router ***/
 $registry->router = new router($registry);

 /*** load up the template ***/
 $registry->template = new template($registry);

 /*** set the controller path ***/
 $registry->router->setPath (__SITE_PATH . '/controller');

 /*** load the controller ***/
 $registry->router->loader();

 ob_end_flush();
?>
