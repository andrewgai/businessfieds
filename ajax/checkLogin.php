<?
require("../includes/config.inc.php");
if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}
require("../includes/functions.php");
require("../includes/session.php");

$u_email = mysql_real_escape_string($_REQUEST['email']);
$u_password = md5(mysql_real_escape_string($_REQUEST['password']));
$user_data = $db->queryUniqueObject("SELECT id, group_id, first_name, last_name, email, phone FROM `users` WHERE active='1' AND email = '{$u_email}' AND password = '{$u_password}'");

if($user_data != null) :
?>
<input type="hidden" name="validLogin" value="true">
<input type="hidden" name="user_email" value="<?=$u_email?>">
<input type="hidden" name="user_password" value="<?=$_REQUEST['password']?>">
 
<? else: ?>
<input type="hidden" name="validLogin" value="false">

<? endif; ?>