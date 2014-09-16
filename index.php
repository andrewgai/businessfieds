<?
$route_array = array();
if (isset($_GET['rt'])) {
	$route_array = explode("/",$_GET['rt']);
}

// Toggle for test environment
$debug = TRUE;

// Get the config file
require_once("includes/config.inc.php");

// Bring in the functions and session handlers
require_once("includes/functions.php");
require_once("includes/session.php");

// email template parse class
require_once("classes/class.Template.php");


// If we're here then we are authenticated and need to figure out what page to display

//(($u_type == 1)?$u_type = '_admin':$u_type = '_user'); // Set the user type folders

$u_all_access = $db->queryUniqueValue("SELECT allow_all FROM user_groups WHERE group_id = '{$u_group_id}'");
$u_group_name = $db->queryUniqueValue("SELECT name FROM user_groups WHERE group_id = '{$u_group_id}'");

// Long-ish variable names so as not to interfere with existing var names in Permissions pages.
$permissions = $db->query("SELECT up.permission_id, p.name
									FROM user_groups_permissions up
									LEFT JOIN permissions p ON up.permission_id = p.id
									WHERE up.group_id = '{$u_group_id}'");
									
while($result = $db->fetchNextObject($permissions)) {
	$permissions_array[$result->permission_id] = array("name" => $result->name);
}

if(!empty($permissions_array)) { 
	foreach ($permissions_array as $permissions_array_single) {
		$u_permissions[] = $permissions_array_single['name'];
	}
} else {
	$u_permissions = ''; // Empty 	
}

if($u_all_access != 1) {
	foreach ($permissions_array as $permissions_array_single) {
		$u_permissions[] = $permissions_array_single['name'];
	}
} else {
	$u_permissions[] = "admin";	
}

require_once("includes/arrays.php");
			
// email template fields array
$template_fields = array(
					"activation_url" => "url for new users to activate their accounts",						
					"listing_headline" => "headline for the business listing",
					"inquiry_message" => "message",
					"inquiry_url" => "link to view message",
					"inquirer_name" => "name of the person who filled out the contact form"
					);
if(isset_or($u_id)) {
	$new_messages = $db->queryUniqueValue("SELECT count(id) FROM messages WHERE to_id = '{$u_id}' AND `read` ='0'");
}
if (isset($route_array[0]) && $route_array[0] != "") {
	if (in_array($route_array[0], $u_permissions) || $u_all_access == 1) {
		if (file_exists($u_group_name . "/" . $route_array[0] . ".php")) {
			include($u_group_name . "/" . $route_array[0] . ".php");
		} else {
			include("_default/" . $route_array[0] . ".php");
		}
	} elseif($_SESSION['logged_in']) {
		include("_default/no_access.php");
	} else {
		
		if(isset_or($_GET['inquiry_id'])) {
			$_SESSION['referrer'] = isset_or($_GET['rt']);
			$_SESSION['referrer'] .= '&inquiry_id='.$_GET['inquiry_id'].'#inquiry';
		}
		Header("Location: /login");
	}
} else {
	include("_default/home.php");
}



?>