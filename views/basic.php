<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Alexa Sample Code Search will return results based on sample XML file." />
<meta name="keywords" content="Search Engine" />
<link rel="shortcut icon" type="image/ico" href="http://alexa.com/favicon.ico" />
<title><?php echo $pgtitle; ?></title>
<link href="<?php echo __SITE_ROOT; ?>css/style1.css"  rel="stylesheet" type="text/css" /> 
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<?php flush(); ?>
<body>
<div class="clearfloats" id="pgcontent"><!-- Content -->
<?php include __SITE_PATH."/views/".$content.".php"; ?>
</div> <!--pgcontent -->
<div id="footer">
<p>Alexa Code Sample Search</p>
</div> <!-- footer -->
<!-- JavaScripts -->
<script type="text/javascript"
      src="http://code.jquery.com/jquery-1.7.min.js"></script>
<script 
src="<?php echo __SITE_ROOT;?>js/history.js/scripts/bundled/html4+html5/jquery.history.js"></script>
<script type="text/javascript">
    function loadScript(url, callback){

        var script = document.createElement("script")
        script.type = "text/javascript";

        if (script.readyState){  //IE
            script.onreadystatechange = function(){
                if (script.readyState == "loaded" ||
                        script.readyState == "complete"){
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {  //Others
            script.onload = function(){
                callback();
            };
        }

        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    loadScript("<?php echo __SITE_ROOT; ?>js/ajax.js", function(){
        App.history(window, undefined);
        App.init();
        App.events();
	App.preload("<?php echo __SITE_ROOT; ?>images/link-icon.png");
    });
</script>
<!-- END Javascripts -->
</body>
</html>
