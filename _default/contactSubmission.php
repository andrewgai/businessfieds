<?	
	if(isset($_REQUEST['contact'])) {
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
			if(strpos($name, " ") != flase) {
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
		
	} else {
		Header("Location: /listings");
	}
	$stylesheets = array("css/validationEngine.jquery.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js");
	
	$title = "Business for Sale Inquiry";
	include("head.php"); ?>

	<div id="content">
		<div style="overflow:visible;position:relative;width:1000px;height:120px;">
    		<span class="header-explode" style="margin-right:20px;">Thank You</span>

    	</div>
    	<h1 style="margin:20px 20px 0px 20px;">Thank you! Your Inquiry Has Been Sent</h1>
    	<div id="listing" style="line-height:150%">
    		<a href="/listing/<?=$_REQUEST['listing_id']?>"><img src="images/arrow_left.png" valign="absmiddle" style="margin-bottom:-3px;margin-right:5px">Back to the listing</a>
    		<br>
    		<a href="/listings<?=isset_or($_SESSION['search'])?>"><img src="images/arrow_left.png" style="margin-bottom:-3px;margin-right:5px"><?=(isset($_SESSION['search'])?'Back to search results':'Start a new search')?></a>
    		<br><br>
    		For your convenience, this inquiry has been saved in your account under your<br>
			<strong>My Account &raquo; For Sale Inquiries</strong> section under <strong><?=isset_or($_REQUEST['email'])?></strong>
			<br><br>
			<hr color="#efefef">
			<h1 style="margin-top:20px">Other businesses in that category</h1>
    	</div>
    	<? if(!$_SESSION['logged_in']): ?>
    	<div id="contact" style="margin-top:-30px">
    		
	    	<div class="green-block">
		    	<h3>Complete Your Free Account Registration</h3>
		    	We have created a free account for you based on your email address. Your account will allow you to:
		    	<br><br>
		    	<ul class="tickmark spaced" style="margin-left:5px;margin-right:5px">
                	<li>Keep track of all the businesses you have inquired on</li>
                    <li>Create watchlists of businesses you are interested in</li>
                    <li>Save searches so you can came back to them later</li>
                    <li>Be alerted of new listings made available</li>
                    <li>And more!</li>
                </ul>
                <br>
                To complete the free account registration, please choose a password below.
                <br><br>
                <form>
                <div class="gridform contact">
                	<label for="email">Email Address</label>
                	<span class="contact" style="float:right;margin-top:2px;"><?=isset_or($_REQUEST['email'])?></span>
                	
                	<label for="password">Password</label>
                	<input type="password" name="password" class="contact">
                	
                	
              	</div>
               	<input type="submit" class="button_green floatright last-child" value="Submit">
	            </form>
		    </div>
		</div><!-- contact -->
		<? endif; ?>
		<br style="clear:both">
    </div>
    <script>
	
	</script>
    <? include("foot.php"); ?>