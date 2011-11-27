$(document).ready(function(){


    callPreps();
    prepControls();
 

});




 function callPreps() {

    /* place cursor in text search */
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
		//prepControls();
	
	     }
    	);
    }
  });
	 

 }

