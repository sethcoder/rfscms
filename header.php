<?php
/////////////////////////////////////////////////////////////////////////////////////////
// RFSCMS http://rfscms.org/
/////////////////////////////////////////////////////////////////////////////////////////
// if(isset($RFS_LITTLE_HEADER)) { if($RFS_LITTLE_HEADER==true) { include("lilheader.php"); exit(); } }
if(!file_exists("config/config.php")) {
    $RFS_SITE_URL  = "http://".$_SERVER['SERVER_NAME'];
    echo "<html><body style='background-color:#000; color:#0f0;'>
	NO CONFIG FILE FOUND<img src=images/icons/spinner.gif><META HTTP-EQUIV=\"refresh\" content=\"2;URL=$RFS_SITE_URL/install/install.php\">";
    exit();
}
include_once("include/lib.all.php");
if(empty($RFS_SITE_NAME)) {
    $RFS_SITE_URL  = "http://".$_SERVER['SERVER_NAME'];
    echo "NO RFS_SITE_URL FOUND
	<body style='background-color:#000; color:#0f0;'><img src=images/icons/spinner.gif><META HTTP-EQUIV=\"refresh\" content=\"2;URL=$RFS_SITE_URL/install/install.php\">";
    exit();
}
lib_rfs_maintenance();
lib_debug_header(0);
// Divert ajax requests
$action="";
if(!empty($_REQUEST['action'])) $action = $_REQUEST['action'];

if(stristr($action,"ajax")) {
	include("include/lib.all.php");
	eval("$action();");
	exit();
}
lib_log_count($data->name);
$addon_folder=lib_modules_get_base_url("");
// include theme definition file (if it exists)
if( file_exists("$RFS_SITE_PATH/themes/$theme/t.php")) include("$RFS_SITE_PATH/themes/$theme/t.php");
// include theme header file (if it exists)
if( file_exists("$RFS_SITE_PATH/themes/$theme/t.header.php")) include("$RFS_SITE_PATH/themes/$theme/t.header.php");
// otherwise use the default header (this file)
else {
	lib_rfs_echo($RFS_SITE_DOC_TYPE);
	lib_rfs_echo($RFS_SITE_HTML_OPEN);
	lib_rfs_echo($RFS_SITE_HEAD_OPEN);
	
	lib_rfs_echo($RFS_SITE_META);
	
	// get keywords from any search engine queries and put them in the seo output
	$keywords=$_GET['query'];
	if(empty($keywords)) $keywords=$_GET['q'];
	$keywords.=$RFS_SITE_SEO_KEYWORDS;	
	echo "<meta name=\"description\" 	content=\"$keywords\">";
	echo "<meta name=\"keywords\" 		content=\"$keywords\">";
	lib_rfs_echo($RFS_SITE_TITLE);
	if(file_exists("$RFS_SITE_PATH/themes/$theme/t.css"))
		echo "<link rel=\"stylesheet\" href=\"$RFS_SITE_URL/themes/$theme/t.css\" type=\"text/css\">\n";
	echo "<link rel=\"canonical\" href=\"".lib_domain_canonical_url()."\" />";
	lib_rfs_echo($RFS_SITE_HEAD_CLOSE);	
	lib_rfs_echo($RFS_SITE_BODY_OPEN);	
	
	if($_SESSION['admin_show_top']!="hide") {	
		
		echo "<table border=0 width=100% cellspacing=0 cellpadding=0 class=\"toptexttd\">";
		echo "<tr><td class=toptd align=left >";
		if (lib_rfs_bool_true($RFS_THEME_TTF_TOP))  {
			$clr 	= lib_images_html2rgb($RFS_THEME_TTF_TOP_COLOR);
           $bclr	= lib_images_html2rgb($RFS_THEME_TTF_TOP_BGCOLOR);
			echo lib_images_text(
						$RFS_SITE_NAME,
						$RFS_THEME_TTF_TOP_FONT,						
						$RFS_THEME_TTF_TOP_FONT_SIZE,
						812,0,
						$RFS_THEME_TTF_TOP_FONT_X_OFFSET,
						$RFS_THEME_TTF_TOP_FONT_Y_OFFSET,
						$clr[0], $clr[1], $clr[2],
						$bclr[0], $bclr[1], $bclr[2],
						1,0 );
		}
		else if(lib_rfs_bool_true($RFS_THEME_TOP_LOGO)) {
			$base_srch="themes/$theme/t.top_image";
			$timg=0;
			if(file_exists("$RFS_SITE_PATH/$base_srch.jpg")) $timg=$base_srch.".jpg";
			if(file_exists("$RFS_SITE_PATH/$base_srch.gif")) $timg=$base_srch.".gif";
			if(file_exists("$RFS_SITE_PATH/$base_srch.png")) $timg=$base_srch.".png";
			if($timg) {
				echo "<img src=\"$RFS_SITE_URL/$timg\" align=\"left\" border=\"0\">";
			}
			else {
				echo "<div class=\"top_site_name\">$RFS_SITE_NAME</div>";
			}
		}
		echo "</td><td class=toptd> ";

		echo "<!-- $keywords --> ";
		echo "<font class=slogan>$RFS_SITE_SLOGAN</font>";
		echo "</td>";
		echo "<td class=toptd valign=bottom>";
		if(file_exists("$RFS_SITE_PATH/themes/$theme/t.bot_right_corner.gif")) {
			echo "<img src=\"$RFS_SITE_URL/themes/$theme/t.bot_right_corner.gif\" align=right valign=bottom>";
			echo "</td><td class=logged_in_td>";
		}
		else
			echo " &nbsp; ";
		if($_SESSION["logged_in"]!="true")    {
			lib_rfs_echo($RFS_SITE_LOGIN_FORM_CODE);
			echo "</td><td class=logged_in_td>";
		}
		else    {
			echo "</td>";
			echo "<td class=logged_in_td>";
			lib_rfs_echo($RFS_SITE_LOGGED_IN_CODE);
		}
		echo "</td>";		
		echo "</tr></table>";
		
		echo "<table border=0 width=100% class=rfs_top_menu_table cellpadding=0 cellspacing=0>";
		echo "<tr class=rfs_top_menu_table_td>";
		echo "<td class=rfs_top_menu_table_td valign=top>";
		
		echo "<table border=0 cellpadding=0 cellspacing=0 class=rfs_top_menu_table>";
		echo "<tr class=rfs_top_menu_table_td>";
		             
		lib_menus_draw($RFS_THEME_MENU_TOP_LOCATION); 
		//echo "<td align=right class=rfs_top_menu_table_td>";
		echo "<td class=rfs_top_menu_table_inner class=contenttd>";
		lib_forms_theme_select();		
		echo "</td>";
		echo "</tr></table>\n";
		//echo "</td></tr></table>";
		
		echo "<table border=0 width=100% class=rfs_top_menu_table cellpadding=0 cellspacing=0 align=center>";
		echo "<tr><td align=center>";
		
		if(!lib_rfs_bool_true($data->donated)) {
			lib_social_paypal();		
			lib_social_google_adsense($RFS_SITE_GOOGLE_ADSENSE);
		
		}
		echo "</td></tr></table>";
		
		// echo "</td></tr></table>";
		
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr>";
		echo "<td class=lefttd valign=top>";
    	lib_modules_draw("left");
		echo "</td>";		
		echo "<td valign=top class=midtd>";
		if(file_exists("$RFS_SITE_PATH/themes/$theme/t.top_left_corner.gif"))    {
			echo "<img src=\"$RFS_SITE_URL/themes/$theme/t.top_left_corner.gif\" align=left>";
		}
		
	} else {
	}
}

lib_ajax_javascript();
rfs_javascript();
lib_social_javascripts();

lib_rfs_echo($RFS_SITE_JS_MSDROPDOWN_THEME);
lib_rfs_echo($RFS_SITE_JS_JQUERY);
lib_rfs_echo($RFS_SITE_JS_COLOR);
lib_rfs_echo($RFS_SITE_JS_EDITAREA);
//lib_rfs_echo($RFS_SITE_JS_MSDROPDOWN);

lib_social_google_analytics();
lib_forms_system_message();
lib_rfs_do_action();


?>
