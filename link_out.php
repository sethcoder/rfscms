<?
// link out
include("config/config.php");
include("include/lib.mysql.php");
include("include/lib.domain.php");
$link_out=$_REQUEST['link'];
echo $link_out;
$result=sc_query("select * from link_bin where `link` like '%$link_out%'");
if(mysql_num_rows($result)>0) {
	$link=mysql_fetch_object($result); $link->clicks=$link->clicks+1;
	sc_query("update link_bin set `clicks` = '$link->clicks' where `id` = '$link->id'");
}
if(empty($link_out)) $link_out=$site_url;
echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=http://$link_out\">";
?>