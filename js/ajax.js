

var App = {}


 App.init = function () {

    /* place cursor in text search */
    $("#searchtext").focus();

 }


 App.events = function () {


    var $form   = $('#searchform'),
        srchTxt = $form.find( 'input[name="searchtext"]' ),
	title   = document.title;

    /* initialize History stack */
    History.pushState({}, title, "");

    /* setup History actions */
    $(window).bind('statechange',function(){

	var State = History.getState(),
	    stUrl = State.url;


    	if(stUrl.indexOf('results') != -1) {

    	    var	term =  '',
    	    	page =  0;

	    if(stUrl.indexOf('?q=') != -1) {
    	    	//term = stUrl.split('?')[1].split('=')[1].split('&')[0];
    	    	//page = stUrl.split('?')[1].split('&')[1].split('=')[1];
		params = App.urlParse(stUrl.split('?')[1]);
    	    	term   = params['q'];
    	    	page   = params['p'];
	    }
    
    	    baseUrl = stUrl.split('results')[0]+'results?q='+encodeURIComponent(term)+'&p='+page;

    	    $.post( baseUrl, { searchtext: term },
	     	function(data) {
		    var content = $(data).find('#searchResults'); 
		    var termCln = $(data).find('input[name="searchtext"]').val(); //server sanitize
		    $('#searchContent').html(content);
    		    srchTxt.val(termCln)
		    App.init();
	
	    });
    	} else {
	    $('#searchContent').html('');
    	    srchTxt.val('')
	    App.init();
    	}
    });

    /* attach a submit handler to the form */
    $form.submit(function(event) {

        var term = srchTxt.val();

    	event.preventDefault(); 
        
    	if(!$.trim(term).length) { // check for search text

	    alert('Please enter search text.');
	    App.init();

    	} else { //push state change

	    History.pushState({}, title, "results?q="+encodeURIComponent(term.replace(' ', '+'))+'&p=0');
    	}
    });

    $('#pgcontent').on("click", "span.active a", function(event){ 

    	event.preventDefault(); 

    	var  term =  '',
    	     page =  0;

	if(this.href.indexOf('?q=') != -1) {
    	    //term = this.href.split('?')[1].split('=')[1].split('&')[0];
    	    //page = this.href.split('?')[1].split('&')[1].split('=')[1];
	    params = App.urlParse(this.href.split('?')[1]);
    	    term   = params['q'];
    	    page   = params['p'];
	}

	History.pushState({}, title, "results?q="+encodeURIComponent(term)+'&p='+page);
    });
 }
	 
 App.preload = function (images) {
    if (document.images) {
        var i = 0;
        var imageArray = new Array();
        imageArray = images.split(',');
        var imageObj = new Image();
	var arrlen = imageArray.length;
        for(i=0; i<=arrlen-1; i++) {
	    // Write to page (uncomment following line to check images)
            //document.write('<img src="' + imageArray[i] + '" />');
            imageObj.src=imageArray[i];
        }
    }
 }
    
 /* taken from stackoverflow(dot)com/questions/901115/get-query-string-values-in-javascript */
 App.urlParse = function (queryString) {

    var urlParams = {},
        e,
        a = /\+/g,  // Regex for replacing addition symbol with a space
        r = /([^&=]+)=?([^&]*)/g,
        d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
        //q = window.location.search.substring(1);
        q = queryString;

    while (e = r.exec(q))
       urlParams[d(e[1])] = d(e[2]);

    return urlParams;
 }
