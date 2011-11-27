<?php

Class indexController Extends baseController {

public function index() {


/*//////////////////////////////////////////////////////////////////////////
//   	Aquire Search Results (read local XML file).
*/

    if(isset($_POST['searchtext'])) {
	$srchtext = filter_var($_POST['searchtext'], FILTER_SANITIZE_STRING);
    } elseif(isset($_GET['q'])) {
	$srchtext =  urldecode($_GET['q']);
    } else {
	$srchtext =  '';
    }

    $result_xml = __SITE_PATH."/sample_search_result.xml"; 

    if (file_exists($result_xml)) {
    	$xml = simplexml_load_file($result_xml);
    } else {
    	exit('Failed to load '.$result_xml);
    }
    


/*//////////////////////////////////////////////////////////////////////////
//   	Format Search Results (based on sample image).
*/

    $results = array();
    
    foreach($xml->SearchResult as $result) {

	$url    = (string)$result->Url;
	$rank1m = (int)$result->Rank1M;
	$retail = ((string)$result->Retailness == 'true') ? 'Yes' : 'No';
	$adult  = ucfirst((string)$result->Adult);

	$category = (string)$result->Category;
	($category == '-') ? $category = 'Unspecified' : $category; 
	$category = str_replace(' ', ': ', $category);
	$category = str_replace('-', ' > ', $category);
	$category = str_replace('_', ' ', $category);

	$domain = substr($url, 0, strpos($url, '.')); //parse for domain(no extensions)  

	$results[] =  array(	'url'      => $url, 
				'rank1m'   => $rank1m,
				'retail'   => $retail,
				'adult'    => $adult,
				'domain'   => $domain,
				'category' => $category);

    }

/*//////////////////////////////////////////////////////////////////////////
//   	Sort Search Results
*/

    /* Default sort by Rank1M. */
    $results = $this->orderBy($results, 'rank1m');

    /* Check for Url match and move to top of displayed result. */
    $srchtext_arr = explode(" ", $srchtext);
    $key = null;
    $dontmatch = array ('Yes', 'No');
    foreach($srchtext_arr as $text) {
	if($text && !in_array($text, $dontmatch)) { //exclude empty string && $dontmatch


	    /* also check for domain w/o extensions */
	    if( !is_int($key) && is_int(strpos($text, '.')) ){
    	    	$key = $this->recursiveArraySearch($results, substr($text, 0, strpos($text, '.')));
	    } else  {
    	    $key = $this->recursiveArraySearch($results, $text);
	    }

    	    if(is_int($key)){ //move to 1st position
    	    	$value = $results[$key];
    	    	unset($results[$key]);
    	    	$tail = array_splice( $results, 0 );
    	    	array_push( $results, $value );
    	    	$results = array_merge( $results, $tail );
	    	$key = null;
    	    } 
	}
    }
    

/*//////////////////////////////////////////////////////////////////////////
//   	Create PageNavigotor 
*/


    /* $this->registry->action is 2nd URL parameter and will default to 'index if not specified.*/
    $offset      = $this->registry->action == 'index' ? 0 : $this->registry->action;
    $pagename    = __SITE_ROOT.'results';
    $totaloffset = $offset * PERPAGE;
    $totalcount  = 10; // As provided by Alexa
    $numpages    = ceil($totalcount/PERPAGE);
    $maxpagesshown = PAGELINKS;
    $params      = 'q='.urlencode($srchtext);
    $pagenav     = '';

    if($numpages > 1) {
    	$nav = new PageNavigator($pagename, $totalcount, PERPAGE, 
	    					$totaloffset, $maxpagesshown, $params);
  	$pagenav = $nav->getNavigator();
    } else {
  	$pagenav = 'Page 1 of 1';
    }

/*//////////////////////////////////////////////////////////////////////////
//   	Presentation Logic.
*/

    /***   Alias view templates.	*/
    $views = $this->registry->template;

    /* debug */
    $views->srchtext_arr = $srchtext_arr; //debug
    $views->srchtext = $srchtext; //debug
    $views->offset = $offset; //debug
    $views->msg = $this->registry->controller.':'.$this->registry->action; //debug
    $views->key = $key; //debug

    $views->results   = array_slice($results, PERPAGE*$offset, PERPAGE);
    $views->pagenav   = $pagenav;
    $views->layout    = 'basic';
    $views->pgtitle   = 'Alexa Code Sample';
    $views->withresults =  $this->registry->controller == 'results';
    $views->show('home');

}


/*//////////////////////////////////////////////////////////////////////////
//   	Helper Functions.
*/

 /* Taken from www(dot)the-art-of-web.com(slash)php(slash)sortarray */
 function orderBy($data, $field) { 
	$code = "return strnatcmp(\$a['$field'], \$b['$field']);"; 
	usort($data, create_function('$a,$b', $code)); 
	return $data; 
 } 

 /* Taken form php(dot)net */
 function recursiveArraySearch($haystack, $needle, $index = null) {

    $aIt = new RecursiveArrayIterator($haystack);
    $it  = new RecursiveIteratorIterator($aIt);
   
    while($it->valid())
    {       
        if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
            return $aIt->key();
        }
       
        $it->next();
    }
   
    return false;
 } 


}
?>
