$(document).ready(function(){


    callPreps();
    prepControls();
 

    $('div#pgcontent a.directlink').live('click', function(event){
	//event.preventDefault(); //use return false to pay nice w/ie
	window.open(this.href,this.href.substring(7, this.href.length-1));
	return false;
    });

});




 function callPreps() {
    $("#searchtext").focus();
 }


 function prepControls() {


    /* attach a submit handler to the form */
    $("#searchform").submit(function(event) {

    event.preventDefault(); 
        
    /* check for valid search text. */
    if(!$.trim($('#searchform').find('input[name="searchtext"]').val()).length) {
	alert('Please enter search text.');
	callPreps();
    } else { //post form
    	var $form = $( this ),
            term  = $form.find( 'input[name="searchtext"]' ).val(),
            url   = $form.attr( 'action' );

    	$.post( url, { searchtext: term },
	     function(data) {
		var content = $( data ).find( '#searchContent' );
		jQuery('#searchContent').html(content);
		prepControls();
	
	     }
    	);
    }
  });
	 

    /* attach a click handler to page navigation */
    $('.navigator a').click(
	function(event) {
	    event.preventDefault();
    	    var $link = $( this ),
            	url   = $link.attr( 'href' );

    	    $.get( url,
	     	function(data) {
		    var content = $( data ).find( '#searchContent' );
		    $('#searchContent').html(content);
		    prepControls();
	
	     	}
    	    );

	}
    );



	 
 }


 function setCookie( name, value, expires, path, domain, secure )  {

/* set time. default=msecs */
  var today = new Date();
  today.setTime(today.getTime());

  if (expires) {
    expires = expires * 1000 * 60 * 60 * 24;
  }

  var expires_date = new Date(today.getTime() + (expires));

  document.cookie = name + "=" +escape(value) +
  ((expires) ? ";expires=" + expires_date.toGMTString() : "" ) + 
  ((path)    ? ";path=" + path     : "" ) + 
  ((domain)  ? ";domain=" + domain : "" ) +
  ((secure)  ? ";secure"           : "" );
 }

 function getCookie(c_name) {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++) {
  	x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  	y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  	x=x.replace(/^\s+|\s+$/g,"");

  	if (x==c_name) {
    	    return unescape(y);
    	}
    }
 }
 
