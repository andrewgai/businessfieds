<?
session_start();
if(empty($route_array)) {
	$route_array = array();
}

if(isset($_POST['user_email'])) {
	$u_email = mysql_real_escape_string(trim($_POST['user_email']));
} elseif(isset($_SESSION['email'])) {
	$u_email = $_SESSION['email'];	
} else {
	$u_email = "";	
}
if(isset($_POST['user_password'])) {
	$u_password = md5($_POST['user_password']);
} elseif(isset($_SESSION['password'])) {
	$u_password = $_SESSION['password'];
} else {
	$u_password = "";	
}

function killsession($message = "") {
	unset($_SESSION['email']);
	unset($_SESSION['password']);
	unset($_SESSION['logged_in']);
	unset($user);
	session_destroy();
	header("Location: /login&message=".$message);
	exit;
}

if ($u_email == "" || $u_password == "") { // Initial State. If no email or password is being passed.
	$_SESSION['logged_in'] = false;
	$u_group_id = 0;

} else {
	if (in_array("logout",$route_array)) { // If 'logout' is contained in URL.
		killsession("<b>You've successfully logged out.</b>");  
	}
	$user_data = $db->queryUniqueObject("SELECT * FROM `users` WHERE active='1' AND email = '{$u_email}' AND password = '{$u_password}'");

	if ($user_data == NULL) { // If incorrect email/password
		killsession("<b><font color=red>Incorrect Username / Password Combination.</font></b>");
	} else {

		// Else.. we're all good! set session variables and continue.
		$_SESSION['logged_in'] = true;
		$_SESSION['email'] = $u_email;
		$_SESSION['password'] = $u_password;
		$u_id = $user_data->id;
		$u_name = $user_data->first_name." ".$user_data->last_name;
		$user = array(
						"id" => $user_data->id,
						"first_name" => $user_data->first_name,
						"last_name" => $user_data->last_name,
						"name" => $user_data->first_name." ".$user_data->last_name,
						"email" => $user_data->email,
						"phone" => $user_data->phone,
						"address" => $user_data->address,
						"address2" => $user_data->address2,
						"city" => $user_data->city,
						"state" => $user_data->state,
						"zip" => $user_data->zip,
						"company" => $user_data->company
						);
		$u_group_id = $user_data->group_id;

	}
}

?>