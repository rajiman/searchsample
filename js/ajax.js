

var App = {}



 App.init = function () {
	 //alert('App.init');

    /* place cursor in text search */
    $("#searchtext").focus();

 }


 App.events = function () {

	// alert('App.events');

    /* attach a submit handler to the form */
    $("#searchform").submit(function(event) {

    event.preventDefault(); 
        
    /* check for valid search text. */
    if(!$.trim($('#searchform').find('input[name="searchtext"]').val()).length) {
	alert('Please enter search text.');
	//callPreps();
	App.init();
    } else { //post form
    	var $form = $( this ),
            term  = $form.find( 'input[name="searchtext"]' ).val(),
            url   = $form.attr( 'action' );

    	$.post( url, { searchtext: term },
	     function(data) {
		var content = $( data ).find( '#searchResults' ); //bugfix  was searchContent
		jQuery('#searchContent').html(content);
		//prepControls();
	
	     }
    	);
    }
  });
	 

 }

