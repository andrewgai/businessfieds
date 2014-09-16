<?	
	$cryptinstall="./crypt/cryptographp.fct.php";
 	require_once($cryptinstall);  
	
	$listing_id = mysql_real_escape_string(route(1));
	
	// if watch was set on request let's do it!
	if(isset_or($_REQUEST['watch'])) {
		watch($listing_id);
	}
	
	if(isset_or($_REQUEST['share_listing']) && isset_or($_REQEUST['email_listing_id']) != "" && isset_or($_REQUEST['y_email']) != "" && isset_or($_REQUEST['d_email']) != "") {
		$email_listing_id = $_REQUEST['email_listing_id'];
		$email = $_REQUEST['d_email'];
		$listing_title = $db->queryUniqueValue("SELECT title FROM listings WHERE id='{$email_listing_id}'");
		$subject = 'BusinessFieds: '.$listing_title;	

		$body = '
		<html>
		<head>
		  <title>BusinessFieds Shared Listing</title>
		</head>
		<body>
		  <p>'. $_REQUEST['y_email'].' has forwarded you this <a href="http://www.businessfieds.com" target="_blank">BusinessFieds.com</a> listing.</p>
		  <p>Please see below for more information</p>
		  <p>Visit the posting at <a href="http://www.businessfieds.com/listing/'. $_REQUEST['email_listing_id'] .'" target="_blank">http://www.businessfieds.com/listing/'.$_REQUEST['email_listing_id'] .'
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
		mail($email, $subject, $body, $headers);
	}
	$query = "SELECT c.name industry, c2.name sub_industry, c3.name secondary_industry, c4.name secondary_sub_industry, l.* FROM listings l
									LEFT JOIN categories c ON l.category_id = c.id
									LEFT JOIN categories c2 ON l.sub_category_id = c2.id
									LEFT JOIN categories c3 ON l.second_category_id = c3.id
									LEFT JOIN categories c4 ON l.second_sub_category_id = c4.id
									LEFT JOIN watchlist w ON w.listing_id = l.id AND w.user_id = '{$u_id}'
									WHERE l.id='{$listing_id}'";
	$listing = $db->queryUniqueObject("SELECT c.name industry, c2.name sub_industry, c3.name secondary_industry, c4.name secondary_sub_industry, l.*,
									(SELECT COUNT(w.listing_id) FROM watchlist w WHERE w.listing_id = l.id) watching
									FROM listings l
									LEFT JOIN categories c ON l.category_id = c.id
									LEFT JOIN categories c2 ON l.sub_category_id = c2.id
									LEFT JOIN categories c3 ON l.second_category_id = c3.id
									LEFT JOIN categories c4 ON l.second_sub_category_id = c4.id
									LEFT JOIN watchlist w ON w.listing_id = l.id AND w.user_id = '{$u_id}'
									WHERE l.id='{$listing_id}'
							");
	$views = $listing->views + 1;						
	$counter = mysql_query("UPDATE listings SET views = '{$views}' WHERE id = '{$listing_id}'");
	$categories = $db->query("SELECT id, name FROM categories WHERE parent_id = '0'");
	
	
	$photos = $db->query("SELECT * FROM photos WHERE listing_id = '{$listing_id}'");
	$has_photos = $db->numRows($photos);
	
	$stylesheets = array("css/validationEngine.jquery.css", "styles/bottom.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js", "js/jquery.form.js", "js/jquery.pikachoose.js", "js/jquery.jcarousel.min.js");
	
	$title = $listing->headline;
	include("head.php"); ?>
	<div id="imgViewer" class="modal" style="width:600px; max-width: 600px; max-height: 600px; overflow:hidden; margin-left:-300px;text-align:center">
		<a href="#" class="close"><img src="images/x.png"></a>

        <br />
       	<img id="bigImage" src="" style="max-width:600px;">
	</div><!-- #imgViewer .modal -->
	<div id="loginForm" class="modal">
		<a href="#" class="close"><img src="images/x.png"></a>
		<h3>Log In</h3>
        <br />
        <form action="/listing/<?=$listing_id?>" method="post">
        <div class="lightform fullwidth marginbottom">
        	<div class="floatleft" style="margin-right:30px;">
            <label for="email">Email</label>
    		<input type="email" id="uemail" name="user_email" style="width:170px" class="validate[required,custom[email]]">
    		</div>
    		<div class="floatright">
    		<label for="password">Password</label>
    		<input type="password" id="upass" name="user_password" style="width:170px" class="validate[required]">
    		</div>
		</div>
		
		<br>
		<input type="hidden" name="watch" value="true">
        <input type="submit" value="Login" class="button_green floatright last-child">
        </form>
	</div><!-- loginForm -->
	
	<div id="emailListing" class="modal" style="width:500px;margin-left:-250px">
		<div class="top-border"></div>
		<a href="#" class="close"><img src="images/x.png"></a>
		<h3>Email this Listing</h3>
        <br />
        <div id="emailListingContent">
	        <form action="ajax/emailListing.php" method="post" id="emailListingForm">
	        <div class="lightform grid fullwidth">
	        	<div class="floatleft" style="width:330px">
		        	<div style="">
		            <label for="email" class="sidebyside">Your Email</label>
		    		<input type="email" name="y_email" style="width:170px"<?=(($_SESSION['logged_in'])?' value="'.$u_email.'" disabled':'')?> class="validate[required,custom[email]] sidebyside">

		    		<label for="d_email">Destination Email</label>
		    		<input type="email" name="d_email" style="width:170px" class="validate[required,custom[email]]">
		    		</div>
		    	</div>
	    		<div class="floatright" style="">
	    			<?=dsp_crypt(0,1);?>
	    			
	    			<input type="text" name="captcha" placeholder="captcha" style="width:100px;margin-top:16px;" class="validate[required,custom[onlyLetterNumber]]">
	    		</div>
			</div>
			<br>
			<? if($_SESSION['logged_in']): ?>
			<input type="hidden" name="y_email" value="<?=$u_email?>" />
			<? endif; ?>
			<input type="hidden" name="share_listing" value="true" />
			<input type="hidden" name="email_listing_id" value="<?=$listing_id?>" />
	        <input type="submit" class="blocklink floatright last-child" value="Share Listing">
	        </form>
	    </div>
	</div><!-- emailListing -->
	
	<div id="content">
		<div class="content-box floatleft" style="width:680px;margin-right:20px">
			<? if($listing->seller_financing == 1): ?>
			<img src="/images/sfa.png" style="position:absolute;right:30px;top:0px;">
			<!--<div class="inverted-tab" style="font-weight:200;text-align:center;text-transform:uppercase;">
				Seller Financing Available
				<div class="main"></div>
			</div>-->
			<? endif; ?>
			<div id="listing">
	        	<h1><?=$listing->headline?></h1>
	            <h3><?=$listing->city?>, <?=$state_list[$listing->state]?> (<?=$listing->county?> County)</h3>
	            <h6><a href="#"><?=$listing->industry?></a> - <a href="#"><?=$listing->sub_industry?></a></h6>
	            <? if($listing->secondary_industry && $listing->secondary_sub_industry): ?>
	            <h6><a href="#"><?=$listing->secondary_industry?></a> - <a href="#"><?=$listing->secondary_sub_industry?></a></h6>
	            <? endif; ?>
	            <div style="overflow: auto">
	            <!--<img src="images/nopic_thm.png" width="300" style="float:left;display:block;margin-top:10px;">-->
	            <div class="pikachoose floatleft" style="margin-top: 10px;">
						<ul id="pikame" class="jcarousel-skin-pika">
						
						<? if($has_photos > 0): while($photo = $db->fetchNextObject($photos)):  ?>
						<li><img src="/user_content/<?=$photo->filename?>"></li>
						<? endwhile; 
						else:  ?>
						<li><img src="/images/nopix.png"></li>
						<? endif; ?>
						</ul>
					</div>
	            <div style="width:270px; float:right; margin-top: <?=($listing->seller_financing == 1)? '0px':'10px'?>;">
	                <table class="datatable details" style="margin-top:10px;margin-bottom:10px">
	                    <tr class="head">
	                        <td>Asking Price:</td>
	                        <td>$<?=number_format($listing->asking_price)?></td>
	                    </tr>
	                    <tr>
	                        <td>Gross Revenue:</td>
	                        <td>$<?=number_format($listing->gross_rev)?></td>
	                    </tr>
	                    <tr>
	                        <td>Cash Flow:</td>
	                        <td>$<?=number_format($listing->cash_flow)?></td>
	                    </tr>
	                    <tr>
	                        <td>FF&amp;E:</td>
	                        <td><?=($listing->ffe_value > 0)?"$".number_format($listing->ffe_value).' '.(($listing->ffe_included == 1)? '':'**'):"N/A"?></td>
	                    </tr>
	                    <tr>
	                        <td>Inventory:</td>
	                        <td><?=($listing->inventory_value > 0)?"$".number_format($listing->inventory_value).' '.(($listing->inventory_included == 1)? '':'**'):"N/A"?></td>
	                    </tr>
	                    <tr>
	                        <td>Real Estate:</td>
	                        <td><?=($listing->realestate_value > 0)?"$".number_format($listing->realestate_value).' '.(($listing->realestate_included == 1)? '':'**'):"N/A"?></td>
	                    </tr>
	                    
	                </table>
	                <h6>** not included in asking price</h6>
	            </div>
	            </div>
	            <br>
	        	<h2>Business Description</h2>
	            <p><?=$listing->description?></p>
	            
	            <h2>About the Business</h2>
	            <? if(isset_or($listing->year_established)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Year Established:</span><?=$listing->year_established?></p>
	            <? endif; ?>
	            <? if(isset_or($listing->num_employees)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Number of Employees:</span><?=$listing->num_employees?></p>
	            <? endif; ?>
	            <? if(isset_or($listing->facilities)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Facilities:</span><?=$listing->facilities?></p>
	            <? endif; ?>
	            <? if(isset_or($listing->market_outlook)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Market Outlook / Competition:</span><?=$listing->market_outlook?></p>
	            <? endif; ?>
	            <? if(isset_or($listing->relocatable) == 1): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Relocatable:</span>Yes</p>
	            <? endif; ?>
	            <? if(isset_or($listing->franchise) == 1): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Franchise:</span>Yes</p>
	            <? endif; ?>
	            <? if(isset_or($listing->home_based) == 1): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Home Based:</span>Yes</p>
	            <? endif; ?>
	            
	            <h2>About the Sale</h2>
	            
	            <? if(isset_or($listing->sell_reason)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Reason for Selling:</span><?=$listing->sell_reason?></p>
	            <? endif; ?>
	            
	            <? if(isset_or($listing->mgmt_training)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Training / Support:</span><?=$listing->mgmt_training?></p>
	            <? endif; ?>
	            
	            <? if(isset_or($listing->cash_flow_comments)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Cash Flow Comments:</span><?=$listing->cash_flow_comments?></p>
	            <? endif; ?>
	            
	            <? if(isset_or($listing->gross_rev_comments)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Gross Revenue Comments:</span><?=$listing->gross_rev_comments?></p>
	            <? endif; ?>
	            
	            <? if(isset_or($listing->seller_financing_desc)): ?>
	            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Seller Financing:</span><?=$listing->seller_financing_desc?></p>
	            <? endif; ?>
	        </div>
	        
	        <br style="clear:both">
	    </div>
	    
	    
	    <div id="contact" class="content-menu floatleft last-child" style="width:300px">
        	<ul>
        		<li>Listing Tools</li>
        		<li class="constant">
        			<span id="watch" class="floatleft">
        				<? if($listing->watching == 1): ?>
        				<a href="/myAccount#watchlist" class="watchingbtn"><img src="images/book.png" style="padding-right:3px;vertical-align:bottom;">Watching</a>
        				<? else: ?>
        				<a href="#" class="watchbtn"><img src="images/book_add.png" style="padding-right:3px;vertical-align:bottom;">Add to Watchlist</a>
        				<? endif; ?>
        			</span>
            		<span style="float:right"><a href="#" id="emailLink" style="padding-left:0"><img src="images/email.png" style="padding-right:3px;vertical-align:bottom;">Email Listing</a></span>
        			<br class="clear">
        		</li>
        	</ul>
        	
        	<ul>
        		<li class="active">Contact Seller</li>
        		<li class="constant padded noarrow">
        			<div class="message"></div>
        			<form action="/ajax/forms.php" id="contact_form" method="post">
        			<input type="hidden" name="doListingContact" value="true">
        			<div class="lightform grid fullwidth" style="margin-top:10px">
	                	
	                    <input type="text" name="name" style="width:100%" placeholder="Your Name" value="<?=isset_or($user['name'])?>" <?=(($_SESSION['logged_in'])? 'readonly="readonly"': '' )?> class="contact validate[required,custom[onlyLetterSp]]">

	                    <input type="text" name="email" style="width:100%;" placeholder="Your Email" value="<?=isset_or($user['email'])?>" <?=(($_SESSION['logged_in'])? 'readonly="readonly"': '' )?> class="contact validate[required,custom[email]]">

	               		<input type="text" name="phone" style="width:100%" placeholder="Your Phone" value="<?=isset_or($user['phone'])?>" class="contact validate[required,custom[phone]]">

	                    <textarea name="message" placeholder="Your Message..." style="width:100%;height:100px" class="validate[required]"></textarea>
	                    
	                    <input type="hidden" name="listing_id" value="<?=$listing_id?>">
	                    <input type="hidden" name="contact" value="true">
	                    
	                    <input type="submit" id="contactSubmit" class="blocklink floatright last-child clear" style="margin: 10px 0px;" value="Submit!">
	                
	                <br style="clear:both">
	                </div>
        			</form>
        		</li>
        	</ul>
        	
         	<? if($listing->broker_name != null): ?>
         	<ul>
            	<li>Broker</li>
            	<li class="constant padded noarrow"><strong><?=$listing->broker_name?></strong><br />
            	<?=$listing->broker_email?><br />
            	<?=$listing->broker_phone?>
            	</li>
          	</ul><!-- green-block broker details -->
            <? endif; ?>
        </div>
        <br class="clear">
	</div>
	<script>
		$(document).ready(function() {
			
			$("#contact_form").validationEngine({scroll: false});
		    $('#contact_form').submit(function() {
		    	if($('#contact_form').validationEngine('validate')) {
		    		$('#contact_form').children('.lightform').append('<div class="screen"><div class="spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div><div class="bar9"></div><div class="bar10"></div><div class="bar11"></div><div class="bar12"></div></div></div>');
		    		var $form = $(this);
		    		var t = setTimeout(function() { $form.ajaxSubmit({target: $form.siblings('.message'), 
		    														  success: function(){ $('#contact_form').children('.lightform').slideUp();
		    														  					   $children('.screen').fadeOut().remove().siblings('.message').fadeIn(); },
																	  clearForm: true
																	 }) }, 800);
		    	}
		    	return false;
		    }); 
			
			$("#pikame").PikaChoose({carousel:true,autoPlay:false,carouselOptions:{wrap:'circular'}});
			
			$('.pika-stage img').click(function(event) {
				var imgsrc = $(event.target).attr('src');
				$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
				$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
				$('#bigImage').attr('src',imgsrc);
				$("#imgViewer").slideDown(100);
				return false;
			});
			$("#watch").load("ajax/watchlist.php?action=check&id=<?=$listing_id?>");
			$("#watch").click(function(event) {
				
				<? if($_SESSION['logged_in']): ?>
				if($(event.target).is(".watchbtn")) {
					event.preventDefault();
					$("#watch").load("ajax/watchlist.php?action=watch&id=<?=$listing_id?>");	
				}
				if($(event.target).is(".watchingbtn")) {
					//event.preventDefault();
					//$("#watch").load("ajax/watchlist.php?action=watch&id=<?=$listing_id?>");	
				}
				<? else: ?>
				$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
				$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
				$("#loginForm").slideDown(100);
				return false;
				<? endif; ?>
			});
			
			$("#emailListingForm").validationEngine({promptPosition : "bottomRight", scroll: false});
			$("#emailListingForm").validationEngine('attach');
			
			var options = { 
			target:        '#emailListingContent',   // target element(s) to be updated with server response 
			beforeSubmit:  showRequest,  // pre-submit callback 
			success:       showResponse  // post-submit callback 
			}; 
        
			// bind form using 'ajaxForm' 
			$('#emailListingForm').ajaxForm(options); 
		
			// pre-submit callback 
			function showRequest(formData, jqForm, options) { 
				//Slide Up!
				$('#emailListingContent').fadeOut();
				return true; 
			} 
		 
			// post-submit callback 
			function showResponse(responseText, statusText, xhr, $form)  { 
				$('#emailListingContent').fadeIn();
				var options = { 
				target:        '#emailListingContent',   // target element(s) to be updated with server response 
				beforeSubmit:  showRequest,  // pre-submit callback 
				success:       showResponse  // post-submit callback 
				}; 
				// bind form using 'ajaxForm' 
				$("#emailListingForm").validationEngine({promptPosition : "bottomRight", scroll: false});
				$("#emailListingForm").validationEngine('attach');
				$('#emailListingForm').ajaxForm(options);
			}
			

			$("#contactSubmit").click(function() {
				if($("#contact_form").validationEngine('validate')) {
					// <? if(isset_or($_SESSION['logged_in'])): ?>
					// return true;
					// <? else: ?>
					// $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
					// $('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
					// $("#loginForm").slideDown(100);
					// return true;
					// <? endif; ?>
					return true;
				}
			});
			$("#login_contact").click(function() {
				$(".contact").append('<input type="hidden" name="user_email" value="'+$("#uemail").val()+'"><input type="hidden" name="user_password" value="'+$("#upass").val()+'">');
				$("#contact_form").submit();
				return false;
			});
			$("#emailLink").click(function() {
				$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
				$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
				$("#emailListing").slideDown(100);
				return false;
			});
			
		});
	</script>
    <? include("foot.php"); ?>