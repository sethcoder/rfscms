<?php
/////////////////////////////////////////////////////////////////////////////////////////
// RFSCMS http://www.rfscms.org/
/////////////////////////////////////////////////////////////////////////////////////////
// VIDEOS CORE MODULE
/////////////////////////////////////////////////////////////////////////////////////////
include_once("include/lib.all.php");

$RFS_ADDON_NAME="videos";
$RFS_ADDON_VERSION="1.0.0";
$RFS_ADDON_SUB_VERSION="0";
$RFS_ADDON_RELEASE="";
$RFS_ADDON_DESCRIPTION="RFSCMS Videos";
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

lib_menus_register("Videos","$RFS_SITE_URL/modules/core_videos/videos.php");
////////////////////////////////////////////////////////////////////////////////////////////////////////
// PANELS
function m_panel_videos($x) { eval(lib_rfs_get_globals());
    echo "<h2>Last $x Videos</h2>";
    $res2=lib_mysql_query("select * from `videos` order by time desc limit 0,$x");
	echo "<table border=0 cellspacing=0 cellpadding=0>";
    while($video=$res2->fetch_object()) {
        if($video->sfw=="no") $video->embed_code="$RFS_SITE_URL/files/videos/NSFW.gif";
		$vlink="<a href=\"$RFS_SITE_URL/modules/core_videos/videos.php?action=view&id=$video->id\"
		alt=\"$video->sname\"
		text=\"$video->sname\"
		title=\"$video->sname\"
		>";
        echo "<tr><td class=contenttd>";
		// echo "<table border=0 cellspacing=0 cellpadding=0><tr><td>";
		echo $vlink;
		echo "<img src=\"".videos_get_thumbnail($video)."\" width=100 class='rfs_thumb' title=\"$video->sname $video->description\">";
		echo "</a>";
		echo "<br>";//"</td><td style='padding: 10px;'>";
		echo $vlink;
		$vname=lib_string_truncate($video->sname,18);
        	echo "$vname</a>";
        	//echo lib_string_truncate($video->description,20);
		//echo "</td><tr></table>";
        echo "</td></tr>";
    }
	echo "</table>";
//	echo "<tr><td class=contenttd></td><td class=contenttd>";
    echo "(<a href=$RFS_SITE_URL/modules/core_videos/videos.php?action=random class=a_cat>Random video</a>)<br>";
    echo "(<a href=$RFS_SITE_URL/modules/core_videos/videos.php class=a_cat>More...</a>)";
//	echo "</td></tr>";
//	echo "</table>";
}

function videos_action_submitvid_embedform() {
	global $RFS_SITE_URL;
	$id=$_REQUEST['id']; 
	if(!empty($id)) {
		$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	}
	echo "<div class=''>\n";
	echo "<form enctype=application/x-www-form-URLencoded method=post action=\"$RFS_SITE_URL/modules/core_videos/videos.php\">\n";
	echo "<table border=0>\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"submitvidgo\">\n";	
	echo "<tr><td>Title</td><td><input size=160 name=\"sname\"></td></tr>\n";
	echo "<tr><td>Link</td><td><input size=160 name=\"link\"></td></tr>\n";
	echo "<tr><td>URL</td><td><input size=160 name=\"vurl\"></td></tr>\n";
	echo "<tr><td>Description</td><td><textarea rows=10 cols=80 name=\"description\"></textarea></td></tr>\n";
	echo "<tr><td>Embed Code</td><td><textarea rows=10 cols=80 name=\"vembed_code\"></textarea></td></tr>\n";
	echo "<tr><td>Safe For Work</td><td><select name=sfw>";
	// if(!empty($video->sfw)) echo "<option>$video->sfw";
	echo "<option>yes<option>no</select></td></tr>\n";
	$res=lib_mysql_query("select * from `categories` order by name asc");
	echo "<tr><td>Category</td><td><select name=category>";
	if(!empty($video->category)) echo "<option>$video->category";
	while($cat=$res->fetch_object()) {
		echo "<option>$cat->name";
	}
	echo "</select></td></tr>\n";
	echo "<tr><td>&nbsp; </td><td><input type=\"submit\" value=\"Add Video\"></td></tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "</div>";	
}
function videos_action_submitvid_urlform() {
	
	global $RFS_SITE_URL;
	$id=$_REQUEST['id']; 
	if(!empty($id)) {
		$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	}
	echo "<div class=''>\n";
	echo "<form enctype=application/x-www-form-URLencoded method=post action=\"$RFS_SITE_URL/modules/core_videos/videos.php\">\n";
	echo "<table border=0>\n";		
	echo "<input type=\"hidden\" name=\"action\" value=\"submitvid_internet_go\">\n";
	echo "<tr><td>Enter URL</td><td><input size=160 name=\"url\"></td>\n";
	echo "<td>Safe For Work</td><td><select name=sfw>";
	echo "<option>yes<option>no</select></td>\n";
	$res=lib_mysql_query("select * from `categories` order by name asc");
	echo "<td>Category</td><td><select name=category>";
	if(!empty($video->category)) echo "<option>$video->category";
	while($cat=$res->fetch_object()) echo "<option>$cat->name";
	echo "</select></td>\n";
	echo "<td>&nbsp; </td><td><input type=\"submit\" value=\"Add Video\"></td>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "</div>\n";
}

function videos_get_url_from_code($code) {
	$youtube="";
	if(stristr($code,"youtube")) {
		$ytx=explode("\"",$code);
		for($yti=0;$yti<count($ytx);$yti++) {
			if(stristr($ytx[$yti],"youtube")) {
				$ytx2=explode("/",$ytx[$yti]);
				$youtube=$ytx2[count($ytx2)-1];
			}
		}
	}
	if(!empty($youtube)) {
		$url="http://www.youtube.com/watch?v=$youtube";
	}
	return $url;
}

function videos_get_original_image($video) {
	if(empty($video)) return;
	if(empty($video->original_image)) {
		$image="";
		$html_raw = file_get_contents($video->url);
		$html = new DOMDocument();
		@$html->loadHTML($html_raw);
		foreach($html->getElementsByTagName('meta') as $meta) {		
			switch(strtolower($meta->getAttribute('property'))) {
				case "og:image":
					$video->original_image=$meta->getAttribute('content');
					if(!empty($video->original_image)) {
						$x=addslashes($video->original_image);
						lib_mysql_query("update `videos` set `original_image` = '$x' where `id`='$video->id'");
					}
					break;
			}
		}
	}
	return;
}

function videos_get_thumbnail($video) {
	eval(lib_rfs_get_globals());
	
	$ytturl="$RFS_SITE_URL/modules/core_videos/cache/oops.png";
	$ytthumb="";
	
	if(!empty($video->image)) {
		$vicheck=str_replace($RFS_SITE_URL,$RFS_SITE_PATH,$video->image);
		
		if(file_exists($vicheck)) {
			return $video->image;
		}
		
		$t=lib_string_generate_uid(time());
		
		$thmpath="$RFS_SITE_PATH/modules/core_videos/cache/$t.jpg";
		$thmurl="$RFS_SITE_URL/modules/core_videos/cache/$t.jpg";
		$ch = curl_init($video->original_image);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$thmb = curl_exec($ch);
		curl_close($ch);
		$x=file_put_contents($thmpath, $thmb);
		if($x) {
			lib_mysql_query("update videos set `image`='$thmurl' where `id`='$video->id'");
			return $thmurl;
		}
		return $video->image;
	}
	
	if( (stristr($video->embed_code,"youtube")) || 
		(stristr($video->embed_code,"youtu.be")) ) {
			$ytx=explode("\"",$video->embed_code);
			for($yti=0;$yti<count($ytx);$yti++) {
				if(stristr($ytx[$yti],"youtube")) { 
					$ytx2=explode("/",$ytx[$yti]); 
					$ytthumb=$ytx2[count($ytx2)-1];
			} 
		}
		if($ytthumb) {
			$yttlocal="$RFS_SITE_PATH/modules/core_videos/cache/$ytthumb.jpg";
			$ytturl="$RFS_SITE_URL/modules/core_videos/cache/$ytthumb.jpg";
			if(!file_exists($yttlocal)) {
				$ch = curl_init("http://i1.ytimg.com/vi/$ytthumb/mqdefault.jpg");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$ytt = curl_exec($ch);
				curl_close($ch);
				$x=file_put_contents($yttlocal, $ytt);
				if($x)
					lib_mysql_query("update videos set `image`='$ytturl' where `id`='$video->id'");
			}
			if(!file_exists($yttlocal)) {
				$ytturl="$RFS_SITE_URL/modules/core_videos/cache/oops.png";
			}
		}
	}
	return $ytturl;
}

function videos_convert_embed_size($embed_code,$w,$h) {
	for($ci=0;$ci<5299;$ci++){
		$embed_code=str_replace("width=\"$ci\"","width=\"$w\"",$embed_code);
		$embed_code=str_replace("height=\"$ci\"","height=\"$h\"",$embed_code);
		$embed_code=str_replace("width='$ci'","width='$w'",$embed_code);
		$embed_code=str_replace("height='$ci'","height='$h'",$embed_code);
		$embed_code=str_replace("width=$ci ","width=$w ",$embed_code);
		$embed_code=str_replace("height=$ci ","height=$h ",$embed_code);
		$embed_code=str_replace("width:$ci ","width:$w ",$embed_code);
		$embed_code=str_replace("height:$ci ","height:$h ",$embed_code);
	}
	return $embed_code;
}

?>
