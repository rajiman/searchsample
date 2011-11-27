<div id="searchText">
<form name="searchform" id="searchform" action="<?php echo __SITE_ROOT.'results' ?>" method="post">
<fieldset>
<input type="text"  id="searchtext" name="searchtext" size="40" value="<?php echo $srchtext; ?>" />
<!--img src="http://alexa.com/favicon.ico"  id="icon" /-->
<input type="submit"  value="Search" />
</fieldset>
</form>
</div><!-- saerchText -->
<div id="searchContent">
<?php if ($withresults) { ?>
<div id="searchResults">
<?php foreach( $results as $entry) {
	$alexalnk  = '<a class="alexalink" href="http://www.alexa.com/siteinfo/';
	$alexalnk .= $entry['url'];
	$alexalnk .= '"><img id="linkicon" src="'.__SITE_ROOT.'images/link-icon.png" ';
	$alexalnk .= 'alt="link icon"/></a>';

	echo '<div class="resultitem"><p class="resultlink"><a class="directlink" href="http://'.$entry['url'].'">'.$entry['url'].'</a>&nbsp;'.$alexalnk.'</p>';
	echo '<p>Rank:&nbsp;'.$entry['rank1m'];
	echo '&nbsp;&nbsp;Retail:&nbsp;'.$entry['retail'];
	echo '&nbsp;&nbsp;Adult:&nbsp;'.$entry['adult'].'</p>';
	echo '<p>Category:&nbsp;'.$entry['category'].'</p></div>';
}?>
<div id="pagenav">
<?php echo $pagenav; ?>
</div>
</div><!-- saerchResults -->
<?php } ?>
</div><!-- searchContent -->
