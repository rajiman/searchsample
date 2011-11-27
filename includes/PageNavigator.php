<?php
////////////////////////////////////////////////////////////////////
/**
Class for navigating over multiple pages
*/
class PageNavigator{
  //data members
  private $pagename;
  private $totalpages;
  private $recordsperpage;
  private $maxpagesshown;
  private $currentstartpage;
  private $currentendpage;
  private $currentpage;
  //next and previous inactive
  private $spannextinactive;
  private $spanpreviousinactive;
  //first and last inactive
  private $firstinactivespan;
  private $lastinactivespan;  
  //must match $_GET['offset'] in calling page
  private $firstparamname = "offset";
  //use as "&name=value" pair for getting
  private $params;
  //css class names
  private $inactivespanname = "inactive";
  private $pagedisplaydivname = "totalpagesdisplay";
  private $divwrapperclass = "navigator";
  private $divwrapperid = "navigator";
  //text for navigation
/*
  private $strfirst = '<img src="/RKMWork/YHI/YHI07/images/allprev.gif" alt="first" />';
  private $strnext = '<img src="/RKMWork/YHI/YHI07/images/next.gif" alt="next" />';
  private $strprevious = '<img src="/RKMWork/YHI/YHI07/images/prev.gif" alt="prev" />';
  private $strlast = '<img src="/RKMWork/YHI/YHI07/images/allnext.gif" alt="last" />';
 */
  private $strfirst = "|&lt;";
  private $strnext = "Next";
  private $strprevious = "Prev";
  private $strlast = "&gt;|";
  /*
  private $strfirst = "&nbsp;";
  private $strnext = "&nbsp;";
  private $strprevious = "&nbsp;";
  private $strlast = "&nbsp;";
   */
  
  //for error reporting
  private $errorstring;  
////////////////////////////////////////////////////////////////////
//constructor
////////////////////////////////////////////////////////////////////
  public function __construct($pagename, $totalrecords, $recordsperpage, $recordoffset, $maxpagesshown = 3, $params = ""){
    $this->pagename = $pagename;
    $this->recordsperpage = $recordsperpage;  
    $this->maxpagesshown = $maxpagesshown;
    //already urlencoded
    $this->params = $params;
    //check recordoffset a multiple of recordsperpage
    $this->checkRecordOffset($recordoffset, $recordsperpage) or
      die($this->errorstring);
    $this->setTotalPages($totalrecords, $recordsperpage);
    $this->calculateCurrentPage($recordoffset, $recordsperpage);
    $this->createInactiveSpans();
    $this->calculateCurrentStartPage();
    $this->calculateCurrentEndPage();
  }
////////////////////////////////////////////////////////////////////
//public methods
////////////////////////////////////////////////////////////////////
//give css class name to inactive span
////////////////////////////////////////////////////////////////////
  public function setInactiveSpanName($name){
    $this->inactivespanname = $name;
    //call function to rename span
    $this->createInactiveSpans();  
  }
////////////////////////////////////////////////////////////////////
  public function getInactiveSpanName(){
    return $this->inactivespanname;
  }
////////////////////////////////////////////////////////////////////
  public function setPageDisplayDivName($name){
    $this->pagedisplaydivname = $name;    
  }
////////////////////////////////////////////////////////////////////
  public function getPageDisplayDivName(){
    return $this->pagedisplaydivname;
  }
////////////////////////////////////////////////////////////////////
  public function setDivWrapperClass($name){
    $this->divwrapperclass = $name;    
  }
////////////////////////////////////////////////////////////////////
  public function getDivWrapperClass(){
    return $this->divwrapperclass;
  }
////////////////////////////////////////////////////////////////////
  public function setDivWrapperID($name){
    $this->divwrapperid = $name;    
  }
////////////////////////////////////////////////////////////////////
  public function getDivWrapperID(){
    return $this->divwrapperid;
  }
////////////////////////////////////////////////////////////////////
  public function setStrFirst($str){
    $this->strfirst = $str;    
    $this->createInactiveSpans();  
  }
////////////////////////////////////////////////////////////////////
  public function setStrNext($str){
    $this->strnext = $str;    
    $this->createInactiveSpans();  
  }
////////////////////////////////////////////////////////////////////
  public function setStrPrevious($str){
    $this->strprevious = $str;    
    $this->createInactiveSpans();  
  }
////////////////////////////////////////////////////////////////////
  public function setStrLast($str){
    $this->strlast = $str;    
    $this->createInactiveSpans();  
  }
////////////////////////////////////////////////////////////////////
  public function setFirstParamName($name){
    $this->firstparamname = $name;    
  }
////////////////////////////////////////////////////////////////////
  public function getFirstParamName(){
    return $this->firstparamname;
  }
////////////////////////////////////////////////////////////////////
/**
Returns HTML code for the navigator
*/
  public function getNavigator(){
    //wrap in div tag
    $strnavigator = "<div id=\"$this->divwrapperid\" class=\"$this->divwrapperclass\">\n";
    //output movefirst button    
    if($this->currentpage == 0){
      $strnavigator .= $this->firstinactivespan;
    }else{
      $strnavigator .= $this->createLink(0, $this->strfirst, 'afirst');
    }
    //output moveprevious button
    if($this->currentpage == 0){
      $strnavigator .= $this->spanpreviousinactive;
    }else{
      $strnavigator.= $this->createLink($this->currentpage-1, $this->strprevious, 'aprev');
    }
    //loop through displayed pages from $currentstart
    for($x = $this->currentstartpage; $x < $this->currentendpage; $x++){
      //make current page inactive
      if($x == $this->currentpage){
        $strnavigator .= "<span class=\"$this->inactivespanname\">";
        $strnavigator .= $x+1;
        $strnavigator .= "</span>\n";
      }else{
        $strnavigator .= $this->createLink($x, $x+1);
      }
    }
    //???rkm$strnavigator .= $this->getPageNumberDisplay(); //rkm moved
    //next button    
    if($this->currentpage == $this->totalpages-1){
      $strnavigator .= $this->spannextinactive;      
    }else{
      $strnavigator .= $this->createLink($this->currentpage + 1, $this->strnext, 'anext');
    }
    //move last button
    if($this->currentpage == $this->totalpages-1){
      $strnavigator .= $this->lastinactivespan;
    }else{
      $strnavigator .= $this->createLink($this->totalpages -1, $this->strlast, 'alast');
    }
    $strnavigator .=  "</div>\n";
    $strnavigator .= $this->getPageNumberDisplay(); //rkm moved
    return $strnavigator;
  }
////////////////////////////////////////////////////////////////////
//private methods
////////////////////////////////////////////////////////////////////
  private function createLink($offset, $strdisplay, $id='' ){
    //$strtemp = "<span class=\"active\"><a class=\"rounded2 \" id=\"$id\" href=\"$this->pagename/$offset\">";
    $strtemp = "<span class=\"active\"><a  id=\"$id\" href=\"$this->pagename/$offset/?";
    //$strtemp .= $offset;
    $strtemp .= "$this->params\">$strdisplay</a></span>\n";
    //$strtemp .= "$strdisplay</a></span>\n";
    return $strtemp;
  }
////////////////////////////////////////////////////////////////////  
  private function getPageNumberDisplay(){
    $str = "<div class=\"$this->pagedisplaydivname\">\nPage ";
    $str .= $this->currentpage+1;
    $str .= " of $this->totalpages";
    $str .= "</div>\n";
    return $str;
  }
////////////////////////////////////////////////////////////////////
  private function setTotalPages($totalrecords, $recordsperpage){
    $this->totalpages = ceil($totalrecords/$recordsperpage);
  }
////////////////////////////////////////////////////////////////////
  private function checkRecordOffset($recordoffset, $recordsperpage){
    $bln = true;
    if($recordoffset%$recordsperpage != 0){
      $this->errorstring = "Error - not a multiple of records per page.";
      $bln = false;  
    }
    return $bln;
  }
////////////////////////////////////////////////////////////////////  
  private function calculateCurrentPage($recordoffset, $recordsperpage){
    $this->currentpage = $recordoffset/$recordsperpage;
  }
////////////////////////////////////////////////////////////////////
// not always needed but create anyway
////////////////////////////////////////////////////////////////////
  private function createInactiveSpans(){
    $this->spannextinactive = "<span id=\"spannext\" class=\"rounded2 ".
      "$this->inactivespanname\">$this->strnext</span>\n";
    $this->lastinactivespan = "<span id=\"spanlast\" class=\"rounded2 ".
      "$this->inactivespanname\">$this->strlast</span>\n";
    $this->spanpreviousinactive = "<span id=\"spanprev\" class=\"rounded2 ".
      "$this->inactivespanname\">$this->strprevious</span>\n";
    $this->firstinactivespan = "<span id=\"spanfirst\" class=\"rounded2 ".
      "$this->inactivespanname\">$this->strfirst</span>\n";
  }
////////////////////////////////////////////////////////////////////
// find start page based on current page
////////////////////////////////////////////////////////////////////
  private function calculateCurrentStartPage(){
    $temp = ($this->maxpagesshown == 0) ? 0 : floor($this->currentpage/$this->maxpagesshown);
    $this->currentstartpage = $temp * $this->maxpagesshown;
  }
////////////////////////////////////////////////////////////////////
  private function calculateCurrentEndPage(){
    $this->currentendpage = $this->currentstartpage+$this->maxpagesshown;
    if($this->currentendpage > $this->totalpages)
    {
      $this->currentendpage = $this->totalpages;
    }
  }
}//end class
////////////////////////////////////////////////////////////////////
?>
