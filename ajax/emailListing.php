<?php

require_once("../includes/config.inc.php");
require_once("../includes/functions.php");

// Create database class instance and connect to db
require_once("../classes/class.Database.php");
$db = new DB($db_name,$db_host,$db_user,$db_password);

// Pull captcha functions
$cryptinstall="../crypt/cryptographp.fct.php";
require_once($cryptinstall);  

$email_listing_id = mysql_real_escape_string($_REQUEST['email_listing_id']);
$from = $_REQUEST['y_email'];
$email = $_REQUEST['d_email'];

if(isset_or($_REQUEST['share_listing']) && $email_listing_id != "" && $from != "" && $email != "" && isset_or($_REQUEST['captcha']) != "") {
	
	$listing_title = $db->queryUniqueValue("SELECT headline FROM listings WHERE id='{$email_listing_id}'");

	if (chk_crypt($_REQUEST['captcha'])) {
		$subject = 'BusinessFieds: '.$listing_title;	
	
		$body = '
		<html>
		<head>
		  <title>BusinessFieds Shared Listing</title>
		</head>
		<body>
		  <p>'. $_REQUEST['y_email'].' has forwarded you this <a href="http://www.businessfieds.com" target="_blank">BusinessFieds.com</a> listing.</p>
		  <p>Please see below for more information</p>
		  <p>Visit the posting at <a href="http://www.businessfieds.com/listing/'. $_REQUEST['email_listing_id'] .'" target="_blank">http://www.businessfieds.com/listing/'.$_REQUEST['email_listing_id'] .'</a>
		  to contact the seller</p>
		  <p>Thank you,<br>BusinessFieds</p>
		</body>
		</html>
		';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'From: BusinessFieds <no-reply@businessfieds.com>' . "\r\n";
	
		// Mail it
		$success = mail($email, $subject, $body, $headers);

	} else {
		$success = FALSE; // captcha was wrong
	}
} else {
	$success = FALSE; // missing fields
}

if ($success):
?>
<div style="text-align:center;padding-top:30px;padding-bottom:30px;">
	<p><strong>This listing has been shared with <?=$email?>.</strong></p>
</div>
<? else: ?>
<form action="ajax/emailListing.php" method="post" id="emailListingForm">
<div class="gridform fullwidth marginbottom">
	<p><font color="red">The captcha text was entered incorrectly. Please try again</font></p>
	<div class="floatleft">
    	<div style="margin-right:30px;">
        <label for="email">Your Email</label>
		<input type="email" name="y_email" style="width:170px"<?=(($_SESSION['logged_in'])?' value="'.$_SESSION['email'].'" disabled':'value="'.$from.'"')?> class="validate[required,custom[email]]">
		</div>
		<div>
		<label for="d_email">Destination Email</label>
		<input type="email" name="d_email" style="width:170px" class="validate[required,custom[email]]" value="<?=$email?>">
		</div>
	</div>
	<div class="floatright" style="padding-top:30px;padding-right:30px">
		<?=dsp_crypt(0,1);?>
					
		<label for="captcha" style="margin-top:7px">Captcha Text</label>
		<input type="text" name="captcha" style="width:100px" class="validate[required,custom[onlyLetterNumber]]">
	</div>
</div>
<br>
<? if($_SESSION['logged_in']): ?>
<input type="hidden" name="y_email" value="<?=$_SESSION['email']?>" />
<? endif; ?>
<input type="hidden" name="share_listing" value="true" />
<input type="hidden" name="email_listing_id" value="<?=$email_listing_id?>" />
<input type="submit" class="button_green floatright last-child" value="Share Listing">
</form>
<? endif; ?>