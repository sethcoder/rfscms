<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Loading directly an RSS feed and displaying it</title></head>

	
<body bgcolor="#FFFFFF">
<h1>RSS 2.0 Demo Direct with Site Link</h1>
<hr>
<div id="zone" > Loads directly an RSS file and displays the list of recent articles. 
</div>

<br>
<p>
<?php
	require_once("rsslib.php");
	$url = "http://www.scriptol.com/rss.xml";
	echo RSS_Display($url, 15, true);
?>

</p>
</body>
</html>
