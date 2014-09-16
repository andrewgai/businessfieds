<?php

date_default_timezone_set('America/Los_Angeles');

// Errors are emailed here.
$contact_email = 'andrew.gai@gmail.com'; 

// Assume debugging is off. 
if (!isset($debug)) {
	$debug = FALSE;
}

define("BASE_URL", "http://businessfieds.com/");
define("BASE_PATH", dirname(__FILE__).'/..');


ini_set("auto_detect_line_endings", "1");

# **************************** #
# ***** ERROR MANAGEMENT ***** #

// Create the error handler.
function my_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) {

	global $debug, $contact_email;
	
	// Build the error message.
	$message = "An error occurred in script '$e_file' on line $e_line: \n<br />$e_message\n<br />";
	
	// Add the date and time.
	$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n<br />";
	
	// Append $e_vars to the $message.
	$message .= "<pre>" . print_r ($e_vars, 1) . "</pre>\n<br />";
	
	if ($debug) { // Show the error.
	
		echo '<p class="error">' . $message . '</p>';
		
	} else { 
	
		// Log the error:
		error_log ($message, 1, $contact_email); // Send email.
		
		// Only print an error message if the error isn't a notice or strict.
		if ( ($e_number != E_NOTICE) && ($e_number < 2048)) {
			echo '<p class="error">A system error occurred. We apologize for the inconvenience.</p>';
		}
		
	} // End of $debug IF.

} // End of my_error_handler() definition.

// Use my error handler:
//set_error_handler ('my_error_handler'); // turned error handling off because we're lazy and the sensitivity is too high

# ***** ERROR MANAGEMENT ***** #
# **************************** #




# ************************** #
# ***** DATABASE STUFF ***** #

// Database Settings
$db_host = 'internal-db.s147439.gridserver.com';
$db_user = 'db147439_bfieds';
$db_password = 'spreadCA2012';
$db_name = 'db147439_bfieds';

# ***** DATABASE STUFF ***** #
# ************************** #

$timestamp = date( 'Y-m-d H:i:s' );

$encode_key = "businessfieds";

// Create database class instance and connect to db
require_once(BASE_PATH."/classes/class.Database.php");
$db = new DB($db_name,$db_host,$db_user,$db_password);

// Get any Settings from the DB and format it as $settings['name'] = value
$result = $db->query("SELECT * FROM settings");
while ($row = $db->fetchNextObject($result)) {
	$settings[$row->name] = $row->value;
}
?>