<?php
chdir("../../");
include("include/lib.all.php");

$data=lib_users_get_data($_SESSION['valid_user']);
$count=lib_log_count($data->name);

if($debug=="on") $_SESSION['debug_msgs']=true;
if($debug=="off") $_SESSION['debug_msgs']=false;

load_vw($data);

if($w>0) {	$_SESSION['wi']=$w; $w=0;  save_vw(); }
if($h>0) {	$_SESSION['he']=$h; $h=0;  save_vw(); }

if(!empty($cat)) $_SESSION['cat']=$cat;
if(empty($cat))  $cat=$_SESSION['cat'];
if(empty($cat)) $cat='Live Stream';

if($edzor=='1')   $_SESSION['edzors']=true;
if($edzor=='0')  $_SESSION['edzors']=false;

if(!empty($across)) { $_SESSION['cols']=$across; $across=''; save_vw(); }
if(!empty($down))   { $_SESSION['rows']=$down;   $down='';   save_vw(); }

function save_vw(){
	$data=lib_users_get_data($_SESSION['valid_user']);
	$vw =$_SESSION['cols']."|";
	$vw.=$_SESSION['rows']."|";
	$vw.=$_SESSION['wi']."|";
	$vw.=$_SESSION['he']."|";
	if($data->id) {
		for($darx=0;$darx<10;$darx++){
			for($dary=0;$dary<10;$dary++){			
				$vw.="$darx,$dary,".$_SESSION['darr'][$darx][$dary]['id']."|";
			}
		}
		lib_mysql_query("update `users` set videowall = '$vw' where id='$data->id'");
	}
}

function load_vw($data){
		
	if(!empty($data->videowall)) {
		$_SESSION['darr']='';
	
		$_SESSION['darr'][$darx][$dary]= array();
		
		$vars=explode("|",$data->videowall);
		$_SESSION['cols']=$vars[0];
		$_SESSION['rows']=$vars[1];
		$_SESSION['wi']=$vars[2];
		$_SESSION['he']=$vars[3];
		
		for($vi=4;$vi<count($vars);$vi++){
			$pv=explode(",",$vars[$vi]);
			$vs=lib_mysql_fetch_one_object("select * from videos where id='$pv[2]'");
			$_SESSION['darr'][$pv[0]][$pv[1]]['id']=$vs->id;
			$_SESSION['darr'][$pv[0]][$pv[1]]['sname']=$vs->sname;
			$_SESSION['darr'][$pv[0]][$pv[1]]['embed_code']=$vs->embed_code;		
		}
	}
}

if( (empty($_SESSION['darr'])) ||
	($action=="resetmatrix") )	{
	$rr=lib_mysql_query("select * from videos where category = '$cat' order by id asc");
	for($darx=0;$darx<10;$darx++){
		for($dary=0;$dary<10;$dary++){
			$vs=$rr->fetch_object();
			$_SESSION['darr'][$darx][$dary]= array();
			$_SESSION['darr'][$darx][$dary]['id']=$vs->id;
			$_SESSION['darr'][$darx][$dary]['sname']=$vs->sname;
			$_SESSION['darr'][$darx][$dary]['embed_code']=$vs->embed_code;
		}
	}
	save_vw();
}



////////////////////////////////////////////////////////////////////////

if(empty($_SESSION['rows']))  $_SESSION['rows']=2;
if(empty($_SESSION['cols']))  $_SESSION['cols']=3;

if(empty($_SESSION['wi'])) { $_SESSION['wi']=400; }
if(empty($_SESSION['he'])) { $_SESSION['he']=300; }

$w=$_SESSION['wi'];
$h=$_SESSION['he'];

if($_SESSION['edzors']==1) $edit=true;

echo "<html><head>";

echo "<META NAME=\"ROBOTS\" CONTENT=\"INDEX,FOLLOW\">\n";
echo "<meta http-equiv=\"Content-Language\" content=\"en-us\">\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">\n";
echo "<meta name=\"GENERATOR\" content=\"Notepad\">\n";
echo "<meta name=\"ProgId\" content=\"Notepad\">\n";
$keywords=$_GET['query']; if(empty($keywords)) $keywords=$_GET['q'];
if(empty($keywords))
$keywords=" $RFS_SITE_URL Video Wall";
echo "<meta name=\"description\" content=\"$keywords\">\n";
echo "<meta name=\"keywords\" content=\"$keywords\">\n";
echo "<title>$RFS_SITE_URL Video Wall</title>\n";

echo "<link rel=\"canonical\" href=\"$RFS_SITE_URL/modules/core_video_wall/v.php\" />";

$theme=$data->theme;
if(empty($theme)) $theme="white";
echo "<link rel=\"stylesheet\" href=\"$RFS_SITE_URL/themes/$theme/t.css\" type=\"text/css\">\n";
			
echo "</head>\n";

echo "<body style=\" margin:0; \">\n";

lib_social_google_analytics();
	
echo "<center>";

if($action=="fixall") {
	$r=lib_mysql_query("select * from videos where category='$cat'");
	for($i=0;$i<$r->num_rows;$i++){
		$video=$r->fetch_object();
		$video->sname=str_replace("(BROKEN)","",$video->sname);
		lib_mysql_query("update videos set sname='$video->sname' where id='$video->id'");
	}
}
if($action=="report") {
	$v=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	$dd="<form action=\"$RFS_SITE_URL/modules/core_video_wall/v.php\" method=post>
	Confirm broken video: $v->sname
	<input type=submit name=report value=Report>
	<input type=hidden name=action value=report_go>
	<input type=hidden name=id value=$id>
	</form>";	
	lib_forms_info($dd,"red","white");
}
if($action=="report_go"){
	$video=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	$email=$RFS_SITE_ADMIN_EMAIL;
	$message="The video $video->sname (id:$video->id) has been reported as broken";
	if(!empty($data->name))
		$message.=" by $data->name";
	$message.=".";
	$subject="BROKEN VIDEO: $video->sname (id:$video->id)";
	mailgo($email,$message,$subject);
	$video->sname=str_replace("(BROKEN)","",$video->sname);
	$video->sname.="(BROKEN)";
	lib_mysql_query("update videos set sname='$video->sname' where id='$video->id'");	
	lib_forms_info("Thank you for reporting the video $video->sname.","red","white");
}

if($act=="chg") {
	$vs=lib_mysql_fetch_one_object("select * from videos where id='$sname'");
	$_SESSION['darr'][$dx][$dy]['sname']=$vs->sname;
	$_SESSION['darr'][$dx][$dy]['embed_code']=$vs->embed_code;
	$_SESSION['darr'][$dx][$dy]['id']=$vs->id;
	echo "<table border=0 width=100% style=' background-color:black'><tr>
	<td  style=' color: #000000; background-color:green'>
	BVW MATRIX MODIFIED $dx, $dy = $vs->sname
	</td></tr></table>";
	save_vw();
}

if($action=="rmconfirmed"){
	lib_mysql_query("delete from videos where id='$id' limit 1");
	
	// update videos set category='$cat' where id='$id' limit 1");
	echo "<table border=0 width=100% style=' background-color:#000000'><tr><td  style=' color: #000000; background-color:#ff0000'>
		REMOVED $id
		</td></tr></table>";
}

if($action=="edgo") {
	$v=lib_mysql_fetch_one_object("select * from videos where id='$id'");
	if(  ($data->access==255) ||
		($data->id==$v->contributor) ) {
	for($ci=0;$ci<999;$ci++){
			$vembed_code=str_replace("width=\"$ci\"","width=\"400\"",$vembed_code);
			$vembed_code=str_replace("height=\"$ci\"","height=\"300\"",$vembed_code);
			$vembed_code=str_replace("width='$ci'","width='400'",$vembed_code);
			$vembed_code=str_replace("height='$ci'","height='300'",$vembed_code);
			$vembed_code=str_replace("width=$ci ","width=400 ",$vembed_code);
			$vembed_code=str_replace("height=$ci ","height=300 ",$vembed_code);
			$vembed_code=str_replace("width:$ci ","width:400 ",$vembed_code);
			$vembed_code=str_replace("height:$ci ","height:300 ",$vembed_code);
		}
		$vembed_code=addslashes($vembed_code);				
		lib_mysql_query("update videos set embed_code='$vembed_code' where id='$id'");
		lib_mysql_query("update videos set sname='$sname' where id='$id'");
		echo "<table border=0 width=100% style=' background-color:#000000'><tr><td  style=' color: #000000; background-color:#ff0000'>
		Modified</td></tr></table>";
	}
	else{
		echo "<table border=0 width=100% style=' background-color:#000000'><tr><td  style=' color: #000000; background-color:#ff0000'>
			You can not into other people's videos. If there is a problem with one, please report it.
		</td></tr></table>";

	}
}

if($act=="add") {

	if(!empty($embed_code)) {

		$time=date("Y-m-d H:i:s");
		for($ci=0;$ci<5299;$ci++){
			$embed_code=str_replace("width=\"$ci\"","width=\"400\"",$embed_code);
			$embed_code=str_replace("height=\"$ci\"","height=\"300\"",$embed_code);
			$embed_code=str_replace("width='$ci'","width='400'",$embed_code);
			$embed_code=str_replace("height='$ci'","height='300'",$embed_code);
			$embed_code=str_replace("width=$ci ","width=400 ",$embed_code);
			$embed_code=str_replace("height=$ci ","height=300 ",$embed_code);
			$embed_code=str_replace("width:$ci ","width:400 ",$embed_code);
			$embed_code=str_replace("height:$ci ","height:300 ",$embed_code);

		}
		$embed_code=addslashes($embed_code);
		$cont=$data->id; if(!$cont) $cont=999;
		lib_mysql_query("insert into videos  (`contributor`, `sname`,`embed_code` , `time`, `category`)
			     					 VALUES('$cont', '$name','$embed_code', '$time', '$cat')" );
	}
	echo "<table border=0 width=100% style=' background-color:#000000'><tr><td  style=' color: #000000; background-color:#ff0000'> VIDEO ADDED </td></tr></table>";
}

$tr_m=$_SESSION['cols'];

echo "<table border=0 cellspacing=0 cellpadding=3 width=100%><tr>";

echo "<td>";
echo "<a href=$RFS_SITE_URL><img src=$RFS_SITE_URL/images/navigation/back.gif></a>";
echo "</td><td>";
echo "</td><td>";
echo "VIDEO WALL";
echo "</td><td>";
echo "$count hits";
echo "</td><td>";
echo "</td><td>";
	//lib_social_twitter_follow();
echo "</td><td>";
echo "</td><td>";
	//lib_social_tweet("$RFS_SITE_URL/modules/core_video_wall/v.php","Video Wall", "Check out this page, it lets you view a bunch of different live streams at one time. ");
echo "</td><td>";
echo "</td><td>";	
	//lib_social_facebook_like("$RFS_SITE_URL/modules/core_video_wall/v.php");	
echo "</td><td>";
echo "</td><td>";
	//lib_social_google_plus("$RFS_SITE_URL/modules/core_video_wall/v.php");
echo "</td><td>";
if(empty($data->donated))
	//lib_social_paypal_small();
echo "</td><td>";

if($edit==true){
			
	echo" [<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?edzor=0>Stop Customizing</a>]<br>";
	echo "</td>";
	echo "</tr></table>";
	echo "<br>";
   
   ECHO "<table border=0 cellspacing=0 cellpadding=0><tr><td>";
	if($_SESSION["logged_in"]!="true")    {
		echo "Register to save your custom settings, or to edit embedded code.";
		lib_rfs_echo($RFS_SITE_LOGIN_FORM_CODE);
	}
	else    {
		
		echo "</td><td class=contenttd>&nbsp;</td><td class=contenttd>";
		echo $_SESSION["valid_user"];
		echo " (<a href=$RFS_SITE_URL/login.php?action=logout&outpage=modules/core_video_wall/v.php>logout</a>)<BR>";
	}
	echo "</td></tr></table>";
						
	echo "<table border=0 cellspacing=0><tr><td valign=top>";

	echo "If there is a glitch<br>in the matrix...<br>[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?action=resetmatrix>RESET EVERYTHING</a>]";
	echo "</td><td>";

	echo "Matrix Size:<br>";
	echo "Presets<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?across=1&down=1>1x1</a>]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?across=2&down=2>2x2</a>]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?across=3&down=2>3x2</a>]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?across=4&down=3>4x3</a>]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?across=5&down=4>5x4</a>]<br>";
	lib_forms_build_quick("SHOW_TEXT_across=".$_SESSION['cols'].$RFS_SITE_DELIMITER.
			"SHOW_TEXT_down=".$_SESSION['rows'].$RFS_SITE_DELIMITER."act=mtxs","Matrix Size");
	
	echo "</td><td>";

	echo "Stream size:<br><br>";
	echo "Presets<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?w=300&h=200>Small</a> (300x200)]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?w=400&h=300>Medium</a> (400x300)]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?w=500&h=400>HUGE</a> (500x400)]<br>";
	echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?w=800&h=600>MEGA</a> (800x600)]<br>";

	lib_forms_build_quick("SHOW_TEXT_w=".$_SESSION['wi'].$RFS_SITE_DELIMITER.
			"SHOW_TEXT_h=".$_SESSION['he'].$RFS_SITE_DELIMITER."act=size","Stream Size");

	echo "</td></tr></table>";
	echo "<hr>";

	}
	else {
	
	echo " [<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?edzor=1>Customize</a>]";
	echo "</td>";
	echo "</tr></table>";
}


echo "<table border=0 width=100% ><tr><td align=center>";
if(empty($data->donated))
	lib_social_google_adsense($RFS_SITE_GOOGLE_ADSENSE);
	
echo "</td></tr></table>";

echo "<table border=0 cellspacing=0 cellpadding=0>\n";
//if($aaa=="test"){
for($darx=0;$darx<$_SESSION['rows'];$darx++){
	echo "<tr>";
	for($dary=0;$dary< $_SESSION['cols'] ;$dary++){
		echo "<td>";
		
		$embed_code=$_SESSION['darr'][$darx][$dary]['embed_code'];		
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
		
		/*
		$vid=str_replace("w=\"400\"","w=\"$w\"",$vid);
		$vid=str_replace("h=\"300\"","h=\"$h\"",$vid);
		$vid=str_replace("width:400 ","width:$w",   $vid);
		$vid=str_replace("height:300 ","height:$h",   $vid);
		$vid=str_replace("width='400'","width='$w'",   $vid);
		$vid=str_replace("height='300'","height='$h'", $vid);
		$vid=str_replace("width=\"400\"","width=\"$w\"",   $vid);
		$vid=str_replace("height=\"300\"","height=\"$h\"", $vid);				
		$vid=str_replace("width=400 ","width=$w ",     $vid);
		$vid=str_replace("height=300 ","height=$h ",   $vid);
		 */

		  if($_SESSION['edzors']==1){					  

			lib_forms_optionize( "$RFS_SITE_URL/modules/core_video_wall/v.php",
						"act=chg".$RFS_SITE_DELIMITER.
						"dx=$darx".$RFS_SITE_DELIMITER.
						"dy=$dary".$RFS_SITE_DELIMITER.
						"include=category:$cat",
						"videos",
						"sname",
						1,
						$_SESSION['darr'][$darx][$dary]['sname'],
						1
						);
		}
		
		else
			echo $embed_code;

		echo "</td>";
	}
	echo "</tr>";
}
echo "</table>\n";

if($_SESSION['edzors']==1){
	
	echo "<hr>";
	
	echo "Submit a new live feed for the wall... enter embed code below<BR> ";

lib_forms_build(  "$RFS_SITE_URL/modules/core_video_wall/v.php",
			"SHOW_TEXT_10#120#name=".$RFS_SITE_DELIMITER.
			"SHOW_TEXTAREA_20#120#embed_code=".$RFS_SITE_DELIMITER."act=add"
			, "", "", "", "", "", "", 20, "Add new stream");

			
	echo "<hr>";
			
	echo "All videos contributed...<br>";
	echo "(Note: Videos must be placed in the '$cat' category to be listed here)<br>";
	
	$q="select * from videos where category='$cat' order by sname asc";		
	$r=lib_mysql_query($q);
	
	echo "<table border=0 cellspacing=0 cellpadding=3>";
	
	echo "<tr>";
	echo "<td>Name</td>";
	echo "<td>Contributor</td>";
	echo "<td>Date Added</td>";
	echo "<td></td>";
	echo "</tr>";
	
	
	
		
		while($v=$r->fetch_object()){
			
			echo "<tr>";
			
			$v->sname=str_replace("(BROKEN)","<font style='color:white; background-color:red;'>(BROKEN)</font>",$v->sname);
			echo "<td><a id='EDIT$v->id'></a> $v->sname </td>";
			$x=lib_users_get_data($v->contributor)->name; if(empty($x)) $x="anonymous";
			if($data->name==$x) $x="<font style='background-color:blue; color:white;'>$x</font>";
			echo "<td>$x</td>";			
			echo "<td> $v->time </td>";
			echo "<td> [<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?action=report&id=$v->id>Report broken</a>] ";
			
			if( ($data->id == $v->contributor) || (lib_access_check("video_wall","editothers")) )
				echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?action=ed&id=$v->id#EDIT$v->id>Edit</a>] ";
			if( ($data->id == $v->contributor) || (lib_access_check("video_wall","deleteothers")) )				
				echo "[<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?action=rm&id=$v->id#EDIT$v->id>Remove</a>] ";
					
			echo "</td>";
			echo "</tr>";
			
			if(($action=="ed")  && $v->id==$id) {
				if( ($data->id == $v->contributor) || (lib_access_check("video_wall","editothers")) ) {
					echo "<tr><td valign=top><form action=$RFS_SITE_URL/modules/core_video_wall/v.php method=post><input name=sname value='$v->sname'></td>";
					echo "<td><input type=hidden name=action value=edgo><input type=hidden name=id value=$id><textarea cols=50 rows=10 name=vembed_code>$v->embed_code</textarea></td>";
					echo "<td><input type=submit></form></td><td></td></tr>";
				}
			}
			if( ($action=="rm") && $v->id==$id) {
				if( ($data->id == $v->contributor) || (lib_access_check("video_wall","deleteothers")) ) {
					$v=lib_mysql_fetch_one_object("select * from videos where id='$id'");	
					echo "<tr><td><font color=red>REMOVE:</font></td>";
					echo "<td>";
					echo "<FONT color=red>REMOVE FEED: $v->name Are you sure?</font> ";
					echo "</td>";
					echo "<td> [<a href=$RFS_SITE_URL/modules/core_video_wall/v.php?action=rmconfirmed&id=$id>YES</a>] </td><td></td></tr>";
				}
			}
		}
		
		echo "</table>";
		
		for($jg=0;$jg<50;$jg++) echo "<br>";
	}
	
	save_vw();
	
echo "</center>";

lib_debug_footer(0);

echo "</body>";
echo "</html>";





?>

