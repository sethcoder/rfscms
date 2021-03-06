<?php
/////////////////////////////////////////////////////////////////////////////////////////
// RFSCMS http://www.rfscms.org/
/////////////////////////////////////////////////////////////////////////////////////////
// MEMES CORE MODULE
/////////////////////////////////////////////////////////////////////////////////////////
include_once("include/lib.all.php");

$RFS_ADDON_NAME="memes";
$RFS_ADDON_VERSION="1.0.0";
$RFS_ADDON_SUB_VERSION="0";
$RFS_ADDON_RELEASE="";
$RFS_ADDON_DESCRIPTION="RFSCMS Memes";
$RFS_ADDON_REQUIREMENTS="";
$RFS_ADDON_COST="";
$RFS_ADDON_LICENSE="";
$RFS_ADDON_DEPENDENCIES="";
$RFS_ADDON_AUTHOR="Seth T. Parson";
$RFS_ADDON_AUTHOR_EMAIL="seth.parson@rfscms.org";
$RFS_ADDON_AUTHOR_WEBSITE="http://rfscms.org/";
$RFS_ADDON_IMAGES="";
$RFS_ADDON_FILE_URL="";
$RFS_ADDON_GIT_REPOSITORY="";
$RFS_ADDON_URL=lib_modules_get_base_url_from_file(__FILE__);

lib_menus_register("Memes","$RFS_SITE_URL/modules/core_memes/memes.php");
///////////////////////////////////////////////////////////////////////////////////////
// PANELS
function m_panel_memes($x) { eval(lib_rfs_get_globals());
	echo "<h2>Last $x Memes</h2>";
	$r=lib_mysql_query("select * from meme where `private`!='yes' and `status` = 'SAVED' order by time desc limit $x");
	if($r)
	for($i=0;$i<$x;$i++) {
		$m=$r->fetch_object();
		if($m) {
			echo "<div id=$m->id style=\"float: left;\">";
			rfs_show1minimeme($m->id);
			echo "</div>";
		}
	}
	echo "<br style='clear: both;'>";
}

function rfs_show1meme($inmeme_id) {
	eval(lib_rfs_get_globals());
	$m=lib_mysql_fetch_one_object("select * from meme where id='$inmeme_id'");
	$t=$m->name."-".time();
	echo "<div id='fl_$inmeme_id' class=\"memebox\"
	style=\"vertical-align:meme_iddle; text-align:center\"
	>"; // display:table-cell;position:absolute; width:100%; height:100% \"
	echo "<div class=\"memepic\">";
	echo "<a href='$RFS_SITE_URL/include/generate.image.php/$t.png?meme_id=$m->id&owidth=$meme_fullsize&forcerender=1' target=_blank>
	<img src='$RFS_SITE_URL/include/generate.image.php/$t.png?meme_id=$m->id&oheight=$meme_thumbwidth&forcerender=1' border=0></a>"; // owidth=$meme_thumbwidth&
	echo "</div>";
	$muser=lib_users_get_data($m->poster); if(empty($muser->name)) $muser->name="anonymous";
	// echo "<hr>";
	lib_images_text("Rating:".lib_string_number_to_text($m->rating), "OCRA.ttf", 15, 78,24,   0,0, 1,255,1, 0, 55,0, 1,0   );
	echo "<a href='$RFS_SITE_URL/modules/core_memes/memes.php?action=muv&meme_id=$m->id'><img src='$RFS_SITE_URL/images/icons/thumbup.png'   border=0 width=24></a>";
	echo "<a href='$RFS_SITE_URL/modules/core_memes/memes.php?action=mdv&meme_id=$m->id'><img src='$RFS_SITE_URL/images/icons/thumbdown.png' border=0 width=24></a>";
	echo "<hr>";
	lib_buttons_make_button("$RFS_SITE_URL/modules/core_memes/memes.php?action=showmemes&onlyshow=$m->name","$m->name");
	lib_buttons_make_button("$RFS_SITE_URL/modules/core_memes/memes.php?action=memegenerate&basepic=$m->basepic&name=$m->name","New Caption");
	echo "<br>";
	if(lib_access_check("memes","edit")) {
		lib_buttons_make_button("$RFS_SITE_URL/modules/core_memes/memes.php?action=memeedit&meme_id=$m->id","Edit");
	}
	if(lib_access_check("memes","delete")) {
		lib_buttons_make_button("$RFS_SITE_URL/modules/core_memes/memes.php?action=meme_delete&meme_id=$m->id","Delete");
		echo "<br>";
	}
	echo "</div>";
}
function rfs_show1minimeme($inmeme_id) {
	eval(lib_rfs_get_globals());
	$meme_fullsize=512;
	$meme_thumbwidth=160;
	$m=lib_mysql_fetch_one_object("select * from meme where id='$inmeme_id' and `status`='SAVED'");
	$t=$m->name."-".time();
	echo "<div id='fl_$inmeme_id' class=\"memeboxmini\">";
	echo "<div class=\"memepic\">";
	echo "<a href='$RFS_SITE_URL/include/generate.image.php/$t.png?meme_id=$m->id&oheight=$meme_fullsize&forcerender=1' target=_blank><img src='$RFS_SITE_URL/include/generate.image.php/$t.png?meme_id=$m->id&owidth=$meme_thumbwidth&forcerender=1' border=0></a>";
	echo "</div>";
	$muser=lib_users_get_data($m->poster); if(empty($muser->name)) $muser->name="anonymous";
	lib_images_text("Rating:".lib_string_number_to_text($m->rating), "OCRA.ttf", 12, 78,24,   0,0, 1,255,1, 0, 55,0, 1,0   );
	echo "<a href='$RFS_SITE_URL/modules/core_memes/memes.php?action=muv&meme_id=$m->id'><img src='$RFS_SITE_URL/images/icons/thumbup.png'   border=0 width=24></a>";
	echo "<a href='$RFS_SITE_URL/modules/core_memes/memes.php?action=mdv&meme_id=$m->id'><img src='$RFS_SITE_URL/images/icons/thumbdown.png' border=0 width=24></a>";
	echo "<hr>";
	echo "</div>";
}

function memes_create_meme($name,$poster,$basepic,$texttop) {
	global $mysql_id;
	$q="insert into meme ( `name`,`poster`, `basepic`,`texttop`,`status`) VALUES('$name','$poster', '$basepic',  '$texttop', 'EDIT');";
    lib_mysql_query($q);
	return $mysql_id;
}
function memes_update_meme($meme_id,$poster,$name,$basepic,$texttop,$texttop_color,$textbottom,$textbottom_color,$rating,$font,$text_size,$text_color,$text_bg_color,$time,$private,$datborder,$status) {
	if(!empty($poster)) 	lib_mysql_query("update meme set `poster`   	= '$poster'     	 where id='$meme_id'");
	if(!empty($name))		lib_mysql_query("update meme set `name`  		= '$name'   	     where id='$meme_id'");	
							lib_mysql_query("update meme set `texttop`     	= '$texttop'    	 where id='$meme_id'");
	if(!empty($texttop_color))	lib_mysql_query("update meme set `texttop_color`		= '$texttop_color'    where id='$meme_id'");
							lib_mysql_query("update meme set `textbottom`  	= '$textbottom' 	 where id='$meme_id'");
	if(!empty($textbottom_color)) lib_mysql_query("update meme set `textbottom_color` = '$textbottom_color'    where id='$meme_id'");
	if(!empty($rating))		lib_mysql_query("update meme set `rating`	    = '$rating'       where id='$meme_id'");
	if(!empty($font))		lib_mysql_query("update meme set `font`	     	= '$font'       where id='$meme_id'");
	if(!empty($text_size))	lib_mysql_query("update meme set `text_size`		= '$text_size'     where id='$meme_id'");
	if(!empty($text_color))	lib_mysql_query("update meme set `text_color`		= '$text_color'    where id='$meme_id'");	
	if(!empty($text_bg_color)) lib_mysql_query("update meme set `text_bg_color`	= '$text_bg_color' where id='$meme_id'");
	if(!empty($time))       lib_mysql_query("update meme set `time`	= '$time' where id='$meme_id'");
	if(!empty($private))	lib_mysql_query("update meme set `private`		= '$private'       where id='$meme_id'");
	if(!empty($datborder))	lib_mysql_query("update meme set `datborder`		= '$datborder'   	  where id='$meme_id'");
	if(!empty($status))		lib_mysql_query("update meme set `status`		= '$status'   	  where id='$meme_id'");
	
}

?>
