<?
/////////////////////////////////////////////////////////////////////////////////////////
// RFSCMS http://www.sethcoder.com/
/////////////////////////////////////////////////////////////////////////////////////////
$this_dir=getcwd();
include_once("session.php");
include_once("lib.div.php");
include_once("lib.log.php");
include_once("version.php");
include_once("lib.debug.php");
include_once("lib.access.php");
include_once("lib.mysql.php");
include_once("lib.string.php");
include_once("lib.file.php");
include_once("lib.flash.php");
include_once("lib.ajax.php");
include_once("lib.users.php");
include_once("lib.sitevars.php");
include_once("lib.helpers.php");
include_once("lib.forms.php");
include_once("lib.rfs.php");
include_once("lib.paypal.php");
include_once("lib.social.php");
include_once("lib.domain.php");
include_once("lib.menus.php");
include_once("lib.themes.php");
include_once("lib.buttons.php");
include_once("lib.images.php");
include_once("lib.genm.php");
include_once("lib.network.php");
include_once("lib.modules.php");
include_once("lib.tags.php");
/////////////////////////////////////////////////////////////////////////////////////////
if(isset($_SESSION['valid_user']))
$data=lib_users_get_data($_SESSION['valid_user']);
/////////////////////////////////////////////////////////////////////////////////////////
?>