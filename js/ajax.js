 var App = {}

/////////////////////////////////////////////////////////////////////////
//		Plugin Modules
//
//  1)Pub Sub - Decouple Application logic from ajax calls
//  2)History.js - jQuery Back Button Support
//
//


/**********************************************************************/
/* 1.
/* Library Agnostic Pubsub - v1.0
/* Copyright 2010
/* Darcy Clarke http://darcyclarke.me
/*
/**********************************************************************/
 
 App.cache = {};

 App.publish = function(topic, args){

    App.cache[topic] && $.each(App.cache[topic], function(){
	this.apply($, args || []);
    });
 };

 App.subscribe = function(topic, callback){
    if(!App.cache[topic]){
	App.cache[topic] = [];
    }
    App.cache[topic].push(callback);
    return [topic, callback];
 };

 App.unsubscribe = function(handle){
    var t = handle[0];
    App.cache[t] && $.each(App.cache[t], function(idx){
	if(this == handle[1]){
	    App.cache[t].splice(idx, 1);
	}
    });
 };

/**********************************************************************/
/* 2.	github(dot)com/balupton/History.js/
/* 
/* Copyright (c) 2011, Benjamin Arthur Lupton
/* All rights reserved.
/* 
/* Redistribution and use in source and binary forms, with or without 
/* modification, are permitted provided that the following conditions 
/* are met:
/* 
/* Redistributions of source code must retain the above copyright notice, 
/* this list of conditions and the following disclaimer.
/* Redistributions in binary form must reproduce the above copyright notice,
/* this list of conditions and the following disclaimer in the documentation
/* and/or other materials provided with the distribution.
/* Neither the name of Benjamin Arthur Lupton nor the names of its 
/* contributors may be used to endorse or promote products derived from 
/* this software without specific prior written permission.
/* 
/* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
/* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
/* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
/* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
/* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
/* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
/* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
/* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
/* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
/* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
/* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
/* POSSIBILITY OF SUCH DAMAGE.
/*  
/**********************************************************************/

 App.history = function(window,undefined){

    // Prepare
    var History = window.History; // Note: We are using a capital H instead of a lower h
    if ( !History.enabled ) {
         // History.js is disabled for this browser.
         // This is because we can optionally choose to support HTML4 browsers or not.
        return false;
    }

    // Bind to StateChange Event
    History.Adapter.bind(window,'statechange',function(){ //  using statechange instead of popstate
        var State = History.getState(); //  using History.getState() instead of event.state
        History.log(State.data, State.title, State.url);
    });

 };

/////////////////////////////////////////////////////////////////////////
//		Application Specific Logic
//
//
  
 App.init = function () {

    /* place cursor in text search */
    $("#searchtext").focus();

 }


 App.events = function () {


    var $form   = $('#searchform'),
        srchTxt = $form.find( 'input[name="searchtext"]' ),
	title   = document.title || null;

    //Subscribe(bind event to callback)
    App.subscribe("statechange", function(data, type) {
	if (type == 'none') {
	    $('#searchContent').html('');
    	    srchTxt.val('')
	    App.init();
	} else {
	    var content = $(data).find('#searchResults'); 
	    var termCln = $(data).find('input[name="searchtext"]').val(); //server sanitize
	    $('#searchContent').html(content);
    	    srchTxt.val(termCln)
	    App.init();
	}
    });

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
    
	    //encodeURIComponent does not work with History.js which decodes 
    	    baseUrl = stUrl.split('results')[0]+'results?q='+encodeURIComponent(term)+'&p='+page;

    	    $.post( baseUrl, { searchtext: term },
	     	function(data) {
		    //Publish(trigger event)
		    App.publish("statechange", [data, 'post']); //array required
	    });

    	} else {
	    App.publish("statechange", ['', 'none']);
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
    
/////////////////////////////////////////////////////////////////////////
//		Helper Functions
//
  
// stackoverflow(dot)com/questions/901115/get-query-string-values-in-javascript
  
 App.urlParse = function (queryString) {

    var urlParams = {},
        e,
        a = /\+/g,  // Regex for replacing addition symbol with a space
        r = /([^&=]+)=?([^&]*)/g,
        d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
        //q = window.location.search.substring(1);
        q = queryString.replace('%', '%25');

    while (e = r.exec(q))
       urlParams[d(e[1])] = d(e[2]);

    return urlParams;
 }

