<?
require("../includes/config.inc.php");
require("../includes/session.php");
require("../includes/functions.php");
// Create database class instance and connect to db
require_once("../classes/class.Database.php");
$db = new DB($db_name,$db_host,$db_user,$db_password);


// check for taken email
if(isset_or($_REQUEST['checkEmail'])) {
	$email = mysql_real_escape_string($_REQUEST['checkEmail']);
	$exists = $db->queryUniqueValue("SELECT COUNT(id) FROM users WHERE email = '{$email}'");
	if($exists > 0) {
		?>
		<span class="emailTaken" style="color:red;">Email taken. <a href="/passwordRecovery">Forgot Password?</a></span>
		<?
	} else {
		?>
		<span class="hidden emailTaken" style="color:red;">Email taken. <a href="/passwordRecovery">Forgot Password?</a></span>
		<?
	}
}


// register the user!
if(isset_or($_REQUEST['doRegister'])) {
	foreach ($_POST as $key => $value) {
		$$key = mysql_real_escape_string($value);
	}
	$password = md5($password);
	$existing_user = $db->queryUniqueValue("SELECT id FROM users WHERE email='{$email}'");
	if($existing_user == null) {
		$query = "INSERT INTO users (id,group_id,first_name,last_name,email,password,active,created) VALUES(0,2,'{$fname}','{$lname}','{$email}','{$password}',0,NOW())";
		$db->execute($query);
		?>
		<div class="graybar top"><div class="sector last-child" style="font-size:17px">Thanks for registering!</div></div>
		<p style="margin:20px;font-size: 15px">You will receive an email shortly with a link to activate your account.</p>
		<p style="margin:20px;font-size:15px;text-align:right;color:#666666">-BusinessFieds</p>
		<?

		$subject = 'BusinessFieds Activation Email';
		$code = encode($email,"businessfieds");		

		$body = '
		<html>
		<head>
		  <title>BusinessFieds Activation Email</title>
		</head>
		<body>
		  <p>Thank you for signing up at BusinessFieds.com please click the activation link below or copy and paste it into your browser:</p>
		  <a href="http://businessfieds.com/activation&x='. $code .'">http://www.businessfieds.com/activation&x='. $code .'</a>
		  <p>Thank you again,<br>BusinessFieds</p>
		</body>
		</html>
		';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'From: BusinessFieds <no-reply@businessfieds.com>' . "\r\n";

		// Mail it
		mail($email, $subject, $body, $headers);

	} else {
		$message = '<p><strong>A user already exists with that email.</strong> Did you forget your password? <a href="#">Recover it here</a></p>';
	}
}


// user edits their profile
if(isset_or($_REQUEST['doEditProfile'])) {
	foreach($_POST['userdata'] as $name => $value) {
		$insert_data[] = $name."='".mysql_real_escape_string($value)."'";
	}
	$query = "UPDATE users SET ".implode(",",$insert_data)."WHERE id='{$u_id}'";
	if(mysql_query($query)) {
		$new_email = mysql_real_escape_string(trim($_POST['userdata']['email']));
		if($_SESSION['email'] != $new_email) {
			$_SESSION['email'] = $new_email;
		}
		echo showAlert("Your profile has been updated!","positive");
	} else {
		echo showAlert("Your profile has NOT been updated","negative");
	}
}

// user changes password
if(isset_or($_REQUEST['doEditPassword'])) {
	$cur_pass = md5($_POST['oldpassword']);
	$new_pass = md5($_POST['newpassword']);
	$new_pass_conf = md5($_POST['newpasswordconfirm']);
	
	if($new_pass != $new_pass_conf) { // make sure they confirmed new pass
		echo showAlert("Entered passwords do not match!","negative");
		exit;
	} else {
		if(!$db->queryUniqueValue("SELECT count(id) FROM users WHERE id='{$u_id}' AND password='{$cur_pass}'")) { //make sure current password entered matches account!
			echo showAlert("Current password does not match what is on file","negative");
		} else {
			$update_pass = mysql_query("UPDATE users SET password='{$new_pass}' WHERE id='{$u_id}'");
			if($update_pass) {
				$_SESSION['password'] = $new_pass;
				echo showAlert("Your password has been updated!","positive");
			} else {
				echo showAlert("There was a problem updating your password","negative");
			}
		}
	}
}

// user updates listing
if(isset_or($_REQUEST['doUpdateListing'])) {
	require_once("../includes/arrays.php");
	$_POST['seller_financing'] = isset_or($_POST['seller_financing'],0);
	$_POST['home_based'] = isset_or($_POST['home_based'],0);
	$_POST['relocatable'] = isset_or($_POST['relocatable'],0);
	$_POST['franchise'] = isset_or($_POST['franchise'],0);
	$listing_id = mysql_real_escape_string($_POST['listing_id']);
	foreach($_POST as $key => $value) {
		if(in_array($key, $listing_fields)) {
			$input_values[] = $key ."='".isset_or(mysql_real_escape_string(stripslashes($_POST[$key])))."'";
		}
	}
	$insert_data = implode(",", $input_values);
	$query = "UPDATE listings SET {$insert_data} WHERE id = '{$listing_id}'";
	$result = $db->query($query);
	if($result) {
		echo showAlert("Your listing has been updated!","positive");
	} else {
		echo showAlert("There was a problem updating your listing.","negative");
	}
}

if(isset_or($_REQUEST['doListingContact'])) {
	// get the listing id and the user_id of person who listed it
	$listing_id = mysql_real_escape_string($_REQUEST['listing_id']);
	$name = mysql_real_escape_string($_REQUEST['name']);
	$email = mysql_real_escape_string($_REQUEST['email']);
	$phone = mysql_real_escape_string($_REQUEST['phone']);
	$message = mysql_real_escape_string($_REQUEST['message']);
	$listing_info = $db->queryUniqueObject("SELECT l.headline, u.* FROM listings l
											LEFT JOIN users u ON u.id = l.user_id
											WHERE l.id = '{$listing_id}'");
	$user_exists = $db->queryUniqueValue("SELECT id FROM users WHERE email = '{$email}'");
	
	$emailTemplate = new EmailTemplate(); // Create an instance for new user from contact form
	
	if($user_exists == null) {
		// user doesn't exist so add them
		if(strpos($name, " ") != false) {
			list($fname, $lname) = explode(" ", $name);
		} else {
			$fname = $name;
			$lname = "";
		}
		$password = encode($name,$encode_key);
		$db->query("INSERT INTO users (group_id,first_name,last_name,email,password,active,created) VALUES(2,'{$fname}','{$lname}','{$email}','{$password}',0,NOW())");
		$new_u_id = $db->lastInsertedId();
		
		// put message into the database
		$db->query("INSERT INTO messages (parent_id,listing_id,from_id,to_id,message,timestamp) VALUES(0,'{$listing_id}','{$new_u_id}','{$listing_info->id}','{$message}',NOW())");
		$inquiry_id = $db->lastInsertedId();
		
		// send activation email to new user
		
		$code = encode($email,"businessfieds");	// encrypt email to make code
		// set template fields
		$emailTemplate->SetParameter("activation_url", '<a href="http://www.businessfieds.com/activation&x='. $code .'">http://www.businessfieds.com/activation&x='. $code .'</a>');
		$emailTemplate->SetParamter("inquirer_name", $name);
		
		$emailTemplate->SetTemplate($settings['new_user_contact_et']);
		$sendNewUEmail = sendMail($email,$email->Subject(),$email->CreateBody()); // send email
		
		// send message notifications to inquirer and seller
		$emailTemplate->SetParameter("seller_name", $listing_info->first_name." ".$listing_info->last_name);
		$emailTemplate->SetParameter("listing_headline", $listing_info->headline);
		$emailTemplate->SetParameter("inquiry_message", $message);
		$emailTemplate->SetParameter("inquiry_url", '<a href="http://www.businessfieds.com/inquiries/'. $inquiry_id .'">http://www.businessfieds.com/inquiry/'. $inquiry_id .'</a>');
		
		$emailTemplate->SetTemplate($settings['new_inquiry_buyer_et']);
		$sendBuyerEmail = sendMail($email,$email->Subject(),$email->CreateBody()); // send buyer email
		
		$emailTemplate->SetTemplate($settings['new_inquiry_seller_et']);
		$sendSellerEmail = sendMail($listing_info->email, $email->Subject(), $email->CreateBody()); // send seller email			
	} else {
		$db->query("INSERT INTO messages (parent_id,listing_id,from_id,to_id,message,timestamp) VALUES(0,'{$listing_id}','{$user_exists}','{$listing_info->id}','{$message}',NOW())");
		$inquiry_id = $db->lastInsertedId();
		// send an email to both parties
		// send message notifications to inquirer and seller
		
		$emailTemplate->SetParameter("inquirer_name", $name);
		$emailTemplate->SetParameter("seller_name", $listing_info->first_name);
		$emailTemplate->SetParameter("listing_headline", $listing_info->headline);
		$emailTemplate->SetParameter("inquiry_message", $message);
		$emailTemplate->SetParameter("inquiry_url", '<a href="http://www.businessfieds.com/inquiries/'. $inquiry_id .'">http://www.businessfieds.com/inquiry/'. $inquiry_id .'</a>');
		
		$emailTemplate->SetTemplate($settings['new_inquiry_buyer_et']);
		$sendBuyerEmail = sendMail($email,$emailTemplate->Subject(),$emailTemplate->CreateBody()); // send buyer email
		
		$emailTemplate->SetTemplate($settings['new_inquiry_seller_et']);
		$sendSellerEmail = sendMail($listing_info->email, $emailTemplate->Subject(), $emailTemplate->CreateBody()); // send seller email	
	}

	// Send the user a message
	?>
	<div class="aligncenter" style="font-weight:bold;margin-top:30px;margin-bottom:30px">
		Thank you! Your Inquiry Has Been Sent
		
		You can view this inquiry in <a href="/myAccount">My Account</a>
		
	</div>
	<?
}

?>