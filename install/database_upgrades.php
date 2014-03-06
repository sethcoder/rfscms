<?
// Interim Database Changes. These changes will be rotated out into the install script
// INITIAL UPDATES

$a=intval($RFS_SITE_DATABASE_UPGRADE);
$b=intval($RFS_BUILD);
//echo "[$a][$b]<br>";

if(empty($RFS_SITE_DATABASE_UPGRADE)) {
sc_database_add("rfsauth","name","text","NOT NULL");
sc_database_add("rfsauth","enabled","text","NOT NULL");
sc_database_add("rfsauth","value","text","NOT NULL");
sc_database_add("rfsauth","value2","text","NOT NULL");

$id =	sc_database_data_add("rfsauth","name","EBSR",0);
		sc_database_data_add("rfsauth","enabled","true",$id);
		sc_database_data_add("rfsauth","value","",$id);
		
$id =	sc_database_data_add("rfsauth","name","FACEBOOK",0);
		sc_database_data_add("rfsauth","enabled","false",$id);
		sc_database_data_add("rfsauth","value","",$id);
		sc_database_data_add("rfsauth","value2","",$id);
		
$id =	sc_database_data_add("rfsauth","name","OPENID",0);
		sc_database_data_add("rfsauth","enabled","false",$id);
		sc_database_data_add("rfsauth","value","",$id);
		
sc_database_add("users","downloads", "text", "NOT NULL");
sc_database_add("users","uploads", "text", "NOT NULL");
sc_database_add("users","donated", "text", "NOT NULL");
sc_query("ALTER TABLE `site_vars` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
sc_query("ALTER TABLE `site_vars` ADD `desc` TEXT");
sc_query("ALTER TABLE `site_vars` ADD `type` TEXT");
sc_query("ALTER TABLE `menu_top` ADD `access_method` TEXT");
sc_query("ALTER TABLE `menu_top` ADD `other_requirement` TEXT");
sc_query("ALTER TABLE `menu_top` DROP `access`");
sc_query("ALTER TABLE `menu_top` DROP `other_requirements`");
sc_query("update `menu_top` set `access_method`='admin,access' where `name`='Admin'");
sc_query("update `menu_top` set `other_requirement`='loggedin=true' where `name`='Profile'");
sc_database_add("categories","worksafe", "text", "NOT NULL");
sc_database_data_add("categories","name","unsorted",0);
// MD5 hash
sc_database_add("files","md5", "text", "NOT NULL");
sc_database_add("files","tags","text", "NOT NULL");
sc_database_add("files","ignore","text", "NOT NULL");
// Duplicates table
sc_database_add("file_duplicates", "loc1", "text", "NOT NULL");
sc_database_add("file_duplicates", "size1", "text", "NOT NULL");
sc_database_add("file_duplicates", "loc2", "text", "NOT NULL");
sc_database_add("file_duplicates", "size2", "text", "NOT NULL");
sc_database_add("file_duplicates", "md5", "text", "NOT NULL");
sc_database_add("wiki","name",   		"text","NOT NULL");
sc_database_add("wiki","revision",		"int",	"NOT NULL");
sc_database_add("wiki","revised_by",	"text","NOT NULL");
sc_database_add("wiki","revision_note","text","NOT NULL");
sc_database_add("wiki","author", 		"text","NOT NULL");
sc_database_add("wiki","text",   		"text","NOT NULL");
sc_database_add("wiki","tags",   		"text","NOT NULL");
sc_database_add("wiki","updated",		"timestamp","ON UPDATE CURRENT_TIMESTAMP NOT NULL");
sc_touch_dir("$RFS_SITE_PATH/images/wiki");
sc_touch_dir("$RFS_SITE_PATH/images/news");
sc_database_add("news","name",		"text",	"NOT NULL");
sc_database_add("news","headline",	"text",	"NOT NULL");
sc_database_add("news","message",	"text",	"NOT NULL");
sc_database_add("news","category1","text",	"NOT NULL");
sc_database_add("news","submitter","int",		"NOT NULL DEFAULT '0'");
sc_database_add("news","time",		"timestamp","NOT NULL");
sc_database_add("news","lastupdate","timestamp","ON UPDATE CURRENT_TIMESTAMP NOT NULL");
sc_database_add("news","image_url","text",	"NOT NULL");
sc_database_add("news","image_link","text",	"NOT NULL");
sc_database_add("news","image_alt","text",	"NOT NULL");
sc_database_add("news","topstory",	"text",	"NOT NULL");
sc_database_add("news","published","text",	"NOT NULL");
sc_database_add("news","views",		"int",		"NOT NULL DEFAULT '0'");
sc_database_add("news","rating",	"text",	"NOT NULL");
sc_database_add("news","sfw",		"text",	"NOT NULL");
sc_database_add("news","page",		"int",		"NOT NULL");
sc_database_add("news","wiki",		"text",	"NOT NULL");
sc_query( "CREATE TABLE IF NOT EXISTS `pmsg` (`id` int(11) NOT NULL AUTO_INCREMENT,`to` text NOT NULL, `from` text NOT NULL, `subject` text NOT NULL, `message` text NOT NULL, `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',`read` text NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=149 ; ");
}

if($a<889) {
	sc_database_add("site_vars","type","text","not null");
	sc_database_add("menu_top","access_method","text","not null");
}
if($a<890) {
	sc_database_add("menu_top","access_method","text","not null");
	sc_database_add("menu_top","other_requirement","text","not null");
}
if($a<891) {
	sc_database_add("site_vars","desc","text","not null");
}
if($a<901) {
	sc_access_method_add("debug", "view");
}

if($a<903) {
	sc_database_add("todo_list","name","text","not null");
	sc_database_add("todo_list","description","text","not null");
	sc_database_add("todo_list","assigned_to","text","not null");
	sc_database_add("todo_list","assigned_to_group","text","not null");
	sc_database_add("todo_list","public","text","not null");
	sc_database_add("todo_list","owner","text","not null");
	sc_database_add("todo_list","type","text","not null");

	sc_database_add("todo_list_task","name","text","not null");
	sc_database_add("todo_list_task","list","text","not null");
	sc_database_add("todo_list_task","priority","text","not null");
	sc_database_add("todo_list_task","description","text","not null");
	sc_database_add("todo_list_task","resolve_action","text","not null");
	sc_database_add("todo_list_task","step","text","not null");
	sc_database_add("todo_list_task","status","text","not null");
	sc_database_add("todo_list_task","opened","timestamp","DEFAULT CURRENT_TIMESTAMP");
	sc_database_add("todo_list_task","opened_by","text","not null");
	sc_database_add("todo_list_task","due","timestamp","not null");
	sc_database_add("todo_list_task","closed","timestamp","not null");
	sc_database_add("todo_list_task","closed_by","text","not null");	

	sc_database_add("todo_list_status","name","text","not null");
	sc_database_data_add("todo_list_status","name","Open","");
	sc_database_data_add("todo_list_status","name","In Progress","");
	sc_database_data_add("todo_list_status","name","Resolved","");
	sc_database_data_add("todo_list_status","name","Closed","");

	sc_database_add("todo_list_type","name","text","not null");
	sc_database_data_add("todo_list_type","name","Personal","");
	sc_database_data_add("todo_list_type","name","Bug","");
	sc_database_data_add("todo_list_type","name","Task","");

	sc_access_method_add("todo_list", "add");	
}

if($a < $b) {
	$RFS_SITE_DATABASE_UPGRADE=intval($RFS_BUILD);
	$dbu=mfo1("select * from site_vars where name='database_upgrade'");
	if(empty($dbu->id)) sc_query("insert into site_vars (`name`,`value`) values('database_upgrade','$RFS_SITE_DATABASE_UPGRADE');");
	else sc_query("update site_vars set `value` = '$RFS_SITE_DATABASE_UPGRADE' where `name`='database_upgrade'");
	echo "Added interim database changes $RFS_SITE_DATABASE_UPGRADE<br>";
}


?>
