<?
chdir("../../");
$RFS_LITTLE_HEADER=true;
include("header.php");
function videos_buttons() {
	eval(lib_rfs_get_globals());
	echo "<br>";
	lib_buttons_make_button("$RFS_SITE_URL/modules/core_videos/videos.php?action=random","Random Video");
	if(lib_access_check("videos","submit"))
		lib_buttons_make_button("$RFS_SITE_URL/modules/core_videos/videos.php?action=submitvid","Submit Video");
	echo "<br>";
}
function videos_pagefinish() {
	eval(lib_rfs_get_globals());
	include("footer.php");
	exit();
}
function videos_action_modifyvideo() {
	eval(lib_rfs_get_globals());
	if( lib_access_check("videos","edit") ) {
		$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
		echo "<p align=center>";
		echo "$video->embed_code<br>";
		echo "<form enctype=application/x-www-form-URLencoded method=post action=videos.php>";
		echo "<table border=0>";
		echo "<input type=hidden name=action value=modifygo>";
		echo "<input type=hidden name=id value=\"$video->id\">";
				
		echo "<tr><td>Short Name</td><td><input name=sname size=100 value=\"$video->sname\"></td></tr>";
		echo "<tr><td>Description</td><td><textarea rows=10 cols=80 name=\"description\">$video->description</textarea></td></tr>";
		echo "<tr><td>URL</td><td><textarea rows=10 cols=80 name=\"vurl\">$video->url</textarea></td></tr>";
		
		echo "<tr>";
		echo "<td>Safe For Work </td>";
		echo "<td> <select name=sfw>";
		if(!empty($video->sfw)) echo "<option>$video->sfw";
		echo "<option>yes<option>no</select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>Hidden</td>";
		echo "<td><select name=hidden>";
		if(!empty($video->hidden)) echo "<option>$video->hidden";
		echo "<option>no<option>yes</select></td>";
		echo "</tr>";
		echo "<tr>";

		echo "<td>Category:</td>";
		echo "<td><select name=category>";
		echo "<option>$video->category";
		$result2=lib_mysql_query("select * from `categories` order by `name` asc");
		while($cat=$result2->fetch_object())
			echo "<option>$cat->name";
		echo "</select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>Embed Code:</td>";
		echo "<td><textarea rows=10 cols=100 name=vembed_code>$video->embed_code</textarea></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>&nbsp;</td><td>";
		echo "<input type=submit name=go value=go>";
		echo "</td></tr>";
		echo "</table>";
		echo "</form>";
		echo "</p>";
	} else {
		echo "You can't edit videos.";
	}
	videos_pagefinish();
}
function videos_action_modifygo() {
	eval(lib_rfs_get_globals());
	$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	$vc=lib_users_get_data($video->contributor);
	if(lib_access_check("videos","edit")) {
		if((!lib_access_check("videos","editothers"))  &&
		        ($data->id!=$video->contributor)) {
			echo "This isn't your video.";
		} else {
			$sname=addslashes($sname);
			$vembed_code=addslashes($vembed_code);
			$description=addslashes($description);
			lib_mysql_query("update `videos` set `category`='$category' where `id`='$id'");			
			lib_mysql_query("update `videos` set `sname`='$sname' where `id`='$id'");
			lib_mysql_query("update `videos` set `description`='$description' where `id`='$id'");
			lib_mysql_query("update `videos` set `sfw`='$sfw' where `id`='$id'");
			lib_mysql_query("update `videos` set `hidden`='$hidden' where `id`='$id'");
			lib_mysql_query("update `videos` set `url`='$vurl' where `id`='$id'");
			lib_mysql_query("update `videos` set `embed_code`='$vembed_code' where `id`='$id'");
		}
	}
	videos_action_view($id);
}


function videos_action_submitvid_internet_go() {
	eval(lib_rfs_get_globals());
	$x=strtolower($source);
	$e="videos_action_submitvid_$x"."_go();";
	eval($e);
}
function videos_action_submitvid_liveleak_go() {
	eval(lib_rfs_get_globals());	
	if(lib_access_check("videos","submit")) {
		
		$html_raw = file_get_contents($url);
		$html = new DOMDocument();
		@$html->loadHTML($html_raw);
		foreach($html->getElementsByTagName('meta') as $meta) {
			$ax=$meta->getAttribute('property');
			$bx=$meta->getAttribute('content');
			switch($ax){ 			
				case "og:title": 		$sname = str_replace("LiveLeak.com - ","",$bx); break;	
				case "og:description": 	$description=$bx; break;
				case "og:image": 		$image=$bx; break;
			}
		}	

		$ec=explode("/",$image); $ed=explode("_",$ec[-1]); $embed_code=$ed[0];
		$vembed_code = "<iframe width=\"853\" height=\"480\" src=\"http://www.liveleak.com/ll_embed?f=$embed_code\" frameborder=\"0\" allowfullscreen></iframe>";
		$cont		 = $data->id;
		$time		 = date("Y-m-d H:i:s");
		$sname		 = addslashes($sname);
		$url	 	 = addslashes($url);
		$description = addslashes($description);
		
		$q=" INSERT INTO `videos` (`contributor`, `sname`, `description`, `embed_code`,  `url`,       `time`, `bumptime`, `category`,    `hidden`,  `sfw`)
						   VALUES ('$cont',      '$sname', '$description', '$vembed_code' , '$youtube' ,'$time',    '$time','$category', '0', 		'$sfw');";
			
		echo $q;			   
		lib_mysql_query($q);
		$q="select * from videos order by time desc limit 1";
		$vid=lib_mysql_fetch_one_object($q);
		$_GLOBALS['id']=$vid->id;
		videos_action_view($vid->id);
	}
	
}

function videos_action_submitvid_youtube_go() {
	eval(lib_rfs_get_globals());
	if(lib_access_check("videos","submit")) {
		
		$html_raw = file_get_contents($url);
		$html = new DOMDocument();
		@$html->loadHTML($html_raw);
		foreach($html->getElementsByTagName('meta') as $meta) {
			$ax=$meta->getAttribute('property');
			$bx=$meta->getAttribute('content');
			switch($ax){ 			
				case "og:title": 		$sname = str_replace("LiveLeak.com - ","",$bx); break;	
				case "og:description": 	$description=$bx; break;
				case "og:image": 		$image=$bx; break;
				case "og:url": 			$ex=explode("=",$bx); $ytcode=$ex[1]; break;				
			}
		}		
		
		$vembed_code = "<iframe width=\"853\" height=\"480\" src=\"//www.youtube.com/embed/$ytcode\" frameborder=\"0\" allowfullscreen></iframe>";
		$cont		 = $data->id;
		$time		 = date("Y-m-d H:i:s");
		$sname		 = addslashes($sname);
		$url		 = addslashes($url);
		$description = addslashes($description);
		
		$q=" INSERT INTO `videos` (`contributor`, `sname`, `description`, `embed_code`,  `url`,       `time`, `bumptime`, `category`,    `hidden`,  `sfw`)
						   VALUES ('$cont',      '$sname', '$description', '$vembed_code' , '$youtube' ,'$time',    '$time','$category', '0', 		'$sfw');";
		lib_mysql_query($q);
		$q="select * from videos order by time desc limit 1";
		$vid=lib_mysql_fetch_one_object($q);
		$_GLOBALS['id']=$vid->id;
		videos_action_view($vid->id);
	}
}

function videos_action_submitvidgo() {
	eval(lib_rfs_get_globals());
	if(lib_access_check("videos","submit")) {
		$cont=$data->id;
		$time=date("Y-m-d H:i:s");
		$url=addslashes($url);
		$description=addslashes($description);
		// echo "	SUBMITTING VIDEO: <br>contributor $cont <br>sname $sname <br>url $vembed_code <br>time $time <br>btime $time <br>category $category<br>	sfw $sfw <br>";
		lib_mysql_query(" INSERT INTO `videos` (`contributor`,`sname`,`description`,`embed_code`, `url`,`time`,`bumptime`,`category`,`hidden`,`sfw`)
										VALUES ('$cont','$sname', '$description', '$vembed_code','$vurl','$time','$time','$category','0','$sfw');");
		$q="select * from videos order by time desc limit 1";
		$vid=lib_mysql_fetch_one_object($q);
		videos_action_view($vid->id);
	}
}
function videos_action_submitvid() {
	eval(lib_rfs_get_globals());
	
	
	if(lib_access_check("videos","submit")) {
		echo "\n\n\n\n";
		echo "<h1>Submit new video</h1>\n";
		echo "<div class='forum_box'>\n";
		
		echo "<h1>From Web</h1>\n";
		
		echo "<form enctype=application/x-www-form-URLencoded method=post action=\"$RFS_SITE_URL/modules/core_videos/videos.php\">\n";
		echo "<table border=0>\n";

		
		echo "<input type=\"hidden\" name=\"action\" value=\"submitvid_internet_go\">\n";
		
		echo "<select name=\"source\">
			<option>YouTube
			<option>LiveLeak
			</select>";
		
		
		echo "<tr><td>URL</td><td><input size=160 name=\"url\"></td></tr>\n";
		
		
		echo "<tr><td>Safe For Work</td><td><select name=sfw>";
		if(!empty($video->sfw)) echo "<option>$video->sfw";
		echo "<option>yes<option>no</select></td></tr>\n";
		$res=lib_mysql_query("select * from `categories` order by name asc");
		echo "<tr><td>Category</td><td><select name=category>";
		if(!empty($category_in)) echo "<option>$category_in";
		while($cat=$res->fetch_object()) echo "<option>$cat->name";
		echo "</select></td></tr>\n";
		echo "<tr><td>&nbsp; </td><td><input type=\"submit\" value=\"Add Video\"></td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "</div>\n";
		echo "\n\n\n\n";

		echo "<div class='forum_box'>\n";
		echo "<h1>From Embedded Code</h1>\n";
		echo "<form enctype=application/x-www-form-URLencoded method=post action=\"$RFS_SITE_URL/modules/core_videos/videos.php\">\n";
		echo "<table border=0>\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"submitvidgo\">\n";
		echo "<tr><td>Title</td><td><input size=160 name=\"sname\"></td></tr>\n";
		echo "<tr><td>Link</td><td><input size=160 name=\"link\"></td></tr>\n";
		echo "<tr><td>URL</td><td><input size=160 name=\"vurl\"></td></tr>\n";
		echo "<tr><td>Description</td><td><textarea rows=10 cols=80 name=\"description\"></textarea></td></tr>\n";
		echo "<tr><td>Embed Code</td><td><textarea rows=10 cols=80 name=\"vembed_code\"></textarea></td></tr>\n";
		echo "<tr><td>Safe For Work</td><td><select name=sfw>";
		if(!empty($video->sfw)) echo "<option>$video->sfw";
		echo "<option>yes<option>no</select></td></tr>\n";
		$res=lib_mysql_query("select * from `categories` order by name asc");
		echo "<tr><td>Category</td><td><select name=category>";
		if(!empty($category_in)) echo "<option>$category_in";
		while($cat=$res->fetch_object()) {
			echo "<option>$cat->name";
		}
		echo "</select></td></tr>\n";
		echo "<tr><td>&nbsp; </td><td><input type=\"submit\" value=\"Add Video\"></td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "</div>";
	}
}
function videos_action_removego() {
	eval(lib_rfs_get_globals());
	if(lib_access_check("videos","delete")) {
		$video=lib_mysql_fetch_one_object("select * from `videos` where `id`='$id'");
		lib_mysql_query("delete from `videos` where `id`='$id'");
		echo "<p>Removed $video->sname from the database...</p>";
	}
	videos_action_();
}
function videos_action_removevideo() {
	eval(lib_rfs_get_globals());
	if(lib_access_check("videos","delete")) {
		$video=lib_mysql_fetch_one_object("select * from `videos` where `id`='$id'");
		echo "<table border=0>\n";
		echo "<form enctype=application/x-www-form-URLencoded action=$RFS_SITE_URL/modules/core_videos/videos.php method=post>\n";
		echo "<input type=hidden name=action value=removego>\n";
		echo "<input type=hidden name=id value=\"$id\">\n";
		echo "<tr><td>Are you sure you want to delete [$video->sname]???</td>";
		echo "<td><input type=submit name=submit value=\"Yes\"></td></tr>\n";
		echo "</form></table>\n";
		video_pagefinish();
	}
}
function videos_action_view($id) {
	eval(lib_rfs_get_globals());
	videos_buttons();
	$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	$vc=lib_users_get_data($video->contributor);
	echo "<div class=forum_message > <center> ";
	echo "<h1>$video->category videos</h1>";
	
	$res2=lib_mysql_query("select * from `videos` where `category`='$category' and `hidden`!='yes' order by `sname` asc");
	$linkprev="";
	$linknext="";
	
	while($video2=$res2->fetch_object()) {
		if($video2->id==$video->id) {
			$video2=$res2->fetch_object();
			if(!empty($video2->id)) {
				$linknext="[<a href=videos.php?action=view&id=$video2->id>Next</a>]";
				if(!empty($video3->id))
					$linkprev="[<a href=videos.php?action=view&id=$video3->id>Previous</a>]";
				break;
			}
		} else {
			$video3=$video2;
		}
	}
	if(empty($linknext))
		if(!empty($video3->id))
			$linkprev="[<a href=videos.php?action=view&id=$video3->id>Previous</a>]";
	echo "<p>$video->sname</p>"; 
	echo "<p>$video->description</p>";
	if($video->sfw=="yes") {
		echo "$video->embed_code<br>";
	} else {
		if($viewsfw=="yes") echo "$video->embed_code<br>";
		else echo "<a href=videos.php?action=view&id=$video->id&viewsfw=yes><img src=\"$RFS_SITE_URL/images/icons/NSFW.png\" border=0></a><BR>";
	}
	echo "<br><br>";
	echo $linkprev;
	if(lib_access_check("videos","edit"))   lib_buttons_make_button("$RFS_SITE_URL/modules/core_videos/videos.php?action=modifyvideo&id=$video->id","Edit");
	if(lib_access_check("videos","delete")) lib_buttons_make_button("$RFS_SITE_URL/modules/core_videos/videos.php?action=removevideo&id=$video->id","Delete");
	echo $linknext;
	echo "</div>";
	videos_action_viewcat($video->category);
	videos_action_view_cats();
	videos_pagefinish();
}
function videos_action_viewcat($cat) {
	eval(lib_rfs_get_globals());
	videos_buttons();
	$res2=lib_mysql_query("select * from `videos` where `category`='$cat' and `hidden`!='yes' order by `sname` asc");
	while($vid=$res2->fetch_object()) {
		echo "<div style='margin: 5px; border: 1px; float: left; width: 100px; height: 170px;'>";
		echo "<a href=\"videos.php?action=view&id=$vid->id\" title=\"$vid->sname\">";
		$ytturl=videos_get_thumbnail($vid);
		echo "<img src=\"$ytturl\" width=100 class=rfs_thumb><br>";

		echo "$vid->sname</a>";
		echo "</div>";
	}
	echo "<br style='clear: both;'>";
}
function videos_action_random() {
	eval(lib_rfs_get_globals());
	$res=lib_mysql_query("select * from `videos` where `hidden`!='yes'");
	$num=$res->num_rows;
	if($num==0) {
		videos_buttons();
		echo "<p>There are no videos.</p>";
	} else {
		$vid=rand(1,$num)-1;
		mysqli_data_seek($res,$vid);
		$video=$res->fetch_object();
		$vc=lib_users_get_data($video->contributor);
		$id=$video->id;
		videos_action_view($id);
	}
}
function videos_action_view_cats() {
	eval(lib_rfs_get_globals());
	$numcols=0;
	echo "<table border=0><tr>";
	$res=lib_mysql_query("select * from `categories` order by name asc");
	while($cat=$res->fetch_object()) {
		$res2=lib_mysql_query("select * from `videos` where `category`='$cat->name' and `hidden`!='yes' order by `sname` asc");
		$numvids=$res2->num_rows;
		if(!empty($cat->name))
			if($numvids>0) {
				echo "<td>";
				echo "<table border=0 cellspacing=0 cellpadding=3 width=100%><tr><td>";
				echo "<table border=0 cellspacing=0 cellpadding=0 width=100% ><tr>";
				echo "<td class=td_cat valign=top width=220>";
				echo "<h1>
				<a href=\"$RFS_SITE_URL/modules/core_videos/videos.php?action=viewcat&cat=$cat->name\">
				$cat->name videos ($numvids)
				</a>
				</h1><br>";
				echo "</td></tr><tr>";
				echo "<td class=td_cat valign=top height=200 width=220>";
				if($numvids>5) $numvids=5;
				for($i=0; $i<$numvids; $i++) {
					$video=$res2->fetch_object();
					if($video->sfw=="no")
						$video->embed_code="$RFS_SITE_URL/images/icons/NSFW.gif";
					echo "<table border=0>";
					echo "<tr><td valign=top>";
					echo "<a href=videos.php?action=view&id=$video->id>";
					
					echo "<img src=\"".videos_get_thumbnail($video)."\" width=100 class=rfs_thumb><br>";
					
					echo "$video->sname</a><br>";
					echo "</td></tr>";
					echo "</table>";
				}
				echo "</td></tr></table>";
				echo "</td></tr></table>";
				echo "</td>";
				$numcols++;
				if($numcols>3) {
					echo "</tr><tr>";
					$numcols=0;
				}
			}
	}
	echo "</tr>";
	echo "</table>";
}
function videos_action_() {
	eval(lib_rfs_get_globals());
	echo "Videos</h1>";
	videos_action_random();
	videos_pagefinish();
}

?>
