<?
require("../includes/config.inc.php");
if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}
require("../includes/functions.php");
require("../includes/session.php");

if(isset($_GET['id'])) {
	$template_id = $_GET['id'];
	
	if($_GET['action'] == 'del') {
		$db->query("DELETE FROM email_templates WHERE id = '{$template_id}'");
	}
	
}

?>