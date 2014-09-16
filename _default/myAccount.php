<?	
	if(!isset_or($_SESSION['logged_in'])) {
		header("Location: /login");
	}
	$listings = $db->query("SELECT * FROM watchlist w 
							LEFT JOIN listings l ON w.listing_id = l.id
							WHERE w.user_id = '{$u_id}'
							");
	$listing_count = $db->numRows();
	
	$myListings = $db->query("SELECT l.id, 
							CASE WHEN (SELECT m.read FROM messages m WHERE m.listing_id = l.id ORDER BY m.timestamp DESC LIMIT 1) = 0 THEN 1 ELSE 0 END new_message,
							l. headline,
							l.asking_price
							FROM `listings` l
							WHERE user_id = '{$u_id}'");
	$myListingsCount = $db->numRows();
	
	$messages = $db->query("SELECT m.*, u.first_name, u.last_name, l.headline, 
							CASE WHEN (SELECT m2.read FROM messages m2 WHERE m2.parent_id = m.id AND m2.to_id = '{$u_id}' ORDER BY m2.timestamp DESC LIMIT 1) = '0' THEN 1 ELSE 0 END new_message
							FROM messages m
	
							LEFT JOIN listings l ON l.id = m.listing_id
							LEFT JOIN users u ON m.to_id = u.id
	 						WHERE parent_id = 0 AND from_id = '{$u_id}'");
	
	$stylesheets = array("css/validationEngine.jquery.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js", "js/jquery.form.js");
	
	$title = "My Account";
	include("head.php"); ?>

	<div id="content">
		
		<div class="content-menu side-nav floatleft" style="width:220px">
			<ul class="first">
				<li class="link active"><a href="#dashboard">Dashboard</a></li>
				<li class="constant"><a href="#watchlist">Watchlist</a></li>
				<!--<li class="constant"><a href="#manage_searches">Manage Searches</a></li>-->
			</ul>
			<ul>
				<li>Listings</li>
				<li class="constant"><a href="/signup">Add Listing</a></li>
				<? if($myListingsCount > 0): ?>
				<li class="constant"><a href="#my_listings">My Listings</a></li>
				<? endif; ?>
			</ul>
	
			<ul>
				<li class="link"><a href="#edit_profile">Edit Profile</a></li>
				<li><a href="#password">Change Password</a></li>
	    	</ul>
	    	<ul class="hidden">
	    		<li><a href="#inquiry">Inquiry</a></li>
	    	</ul>
    	</div>
    	<div class="content-box floatright panels" style="width:760px;">
    		<div id="dashboard" class="panel">
    			<h2>My Inquiries</h2>
    			<table class="datatable">
    				<thead>
    					<tr>
    						<th>Listing Headline</th>
							<th>Date</th>
    					</tr>
    				</thead>
    				<tbody>
    					<? if(empty($messages)): ?>
    					<tr>
    						<td colspan="4" style="text-align:center">You do not have any inquiries.</td>
    					</tr>
    					<? else: 
    					while($message = $db->fetchNextObject($messages)): ?>
    					<tr <?=($message->new_message == '1')? 'style="font-weight: bold"':''?>>
    						<td><a href="#" tab="inquiry" class="nuclear-link" msg="<?=$message->id?>"><?=$message->headline?></a></td>
    						<td style="text-align:center"><?=formatDate($message->timestamp)?></td>
    					</tr>
    					<? endwhile; 
    					endif; ?>
    				</tbody>
    			</table>
    			
    		</div>
    		<? if($myListingsCount > 0): ?>
    		<div id="my_listings" class="panel">
    			<h2>My Listings</h2>
    			<table class="datatable">
    				<thead>
    					<tr>
    						<th>Listing Headline</th>
    						<th>Asking Price</th>
    						
    					</tr>
    				</thead>
    				<tbody>
    					<? 
    					while($myListing = $db->fetchNextObject($myListings)): ?>
    					<tr>
    						
    						<td><a href="/myListing/<?=$myListing->id?>"><?=$myListing->headline?><?=($myListing->new_message == 1)? '<img src="images/email.png" class="icon-link" style="padding-left:10px">':'' ?></a></td>
    						<td class="aligncenter">$<?=number_format($myListing->asking_price)?></td>
    						
    					</tr>
    					<? endwhile; ?>
    				</tbody>
    			</table>
    			
    		</div>
    		<? endif; ?>
    		
    		<!--<div id="manage_searches" class="panel">
    			<h2>Manage Searches</h2>
    			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    		</div>-->
    		
    		<div id="watchlist" class="panel">
    			<h2>Watchlist</h2>
    			<table class="datatable">
    				<thead>
    					<tr>
    						<th>Listing Headline</th>
    						<th>Asking Price</th>
    						<th>Gross Revenue</th>
    						<th>Cash Flow</th>
    						<th></th>
    					</tr>
    				</thead>
    				<tbody>
    					<? if(empty($listings)): ?>
    					<tr>
    						<td colspan="4" style="text-align:center">You do not have any businesses in your watchlist. To watch a business listing click the option under listing tools when you view any listing.</td>
    					</tr>
    					<? else: 
    					while($listing = $db->fetchNextObject($listings)): ?>
    					<tr>
    						<td><a href="/listing/<?=$listing->id?>"><?=$listing->headline?></a></td>
    						<td class="aligncenter">$<?=number_format($listing->asking_price)?></td>
    						<td class="aligncenter">$<?=number_format($listing->gross_rev)?></td>
    						<td class="aligncenter">$<?=number_format($listing->cash_flow)?></td>
    						<td class="aligncenter"><a href="#" class="del" id="<?=$listing->id?>"><img src="images/del.png" height="15" alt="Delete"></a></td>
    					</tr>
    					<? endwhile; 
    					endif; ?>
    				</tbody>
    			</table>
    			
    		</div>
    		<div id="edit_profile" class="panel">
    			<div class="message"></div>
    			<form action="/ajax/forms.php" id="formUserInfo" method="post">
    			<input type="hidden" name="doEditProfile" value="true">
    			<h2>Edit Profile <a href="#" class="submitBtn blocklink floatright last-child"><img src="images/disk.png">Save</a></h2>
    			<div class="lightform big" style="margin-left:100px;">
    				<label for="userdata[email]" class="floatleft sidebyside">User Email</label>
    				<input type="text" name="userdata[email]" class="sidebyside validate[required]" value="<?=$u_email?>">
    
    				<label for="userdata[first_name]" class="floatleft sidebyside">First Name</label>
    				<input type="text" class="sidebyside validate[required]" name="userdata[first_name]" value="<?=$user['first_name']?>">
    				
    				<label for="userdata[last_name]" class="floatleft sidebyside">Last Name</label>
    				<input type="text" class="sidebyside validate[required]" name="userdata[last_name]" value="<?=$user['last_name']?>">
    				
    				<label for="userdata[company]" class="floatleft sidebyside">Company</label>
    				<input type="text" class="sidebyside" name="userdata[company]" value="<?=$user['company']?>">
    				
    				<label for="userdata[address]" class="floatleft sidebyside">Address</label>
    				<input type="text" class="sidebyside" name="userdata[address]" value="<?=$user['address']?>">
    				
    				<label for="userdata[address2]" class="floatleft sidebyside">Address 2</label>
    				<input type="text" class="sidebyside" name="userdata[address2]" value="<?=$user['address2']?>">
    				
    				<label for="userdata[city]" class="floatleft sidebyside">City</label>
    				<input type="text" class="sidebyside" name="userdata[city]" value="<?=$user['city']?>">
    				
    				<label for="userdata[state]" class="floatleft sidebyside">State</label>
    				<select name="userdata[state]">
    					<option value="">Select a State</option>
    					<? foreach($state_list as $abb => $name): ?>
    					<option value="<?=$abb?>" <?=($abb == $user['state'])?'selected="selected"':''?>><?=$name?></option>
    					<? endforeach; ?>
    				</select>
    				
    				<label for="userdata[zip]" class="floatleft sidebyside">Zip</label>
    				<input type="text" class="sidebyside" name="userdata[zip]" value="<?=$user['zip']?>">
    				
    				<label for="userdata[phone]" class="floatleft sidebyside">Phone</label>
    				<input type="text" class="sidebyside" name="userdata[phone]" value="<?=$user['phone']?>">
    				
 
    				
    				
    			</div>
    			<br style="clear:both">
    			</form>
    		</div>
    		<div id="password" class="panel">
    			<div class="message"></div>
    			<form action="/ajax/forms.php" id="formPassword" method="post">
    			<input type="hidden" name="doEditPassword" value="true">
    			<h2>Password <a href="#" class="submitBtn blocklink floatright last-child"><img src="/images/disk.png">Save</a></h2>
    			<div class="lightform big" style="margin-left:100px;">
    				<label for="oldpassword" class="floatleft sidebyside">Current Password</label>
    				<input type="password" name="oldpassword" class="floatleft sidebyside marginbottom">
    				
    				<label for="newpassword" class="floatleft sidebyside">New Password</label>
    				<input type="password" class="sidebyside" name="newpassword">
    				
    				<label for="newpasswordconfirm" class="floatleft sidebyside">Confirm Password</label>
    				<input type="password" class="sidebyside" name="newpasswordconfirm">
    				
    				
    			</div>
    			
    			<br style="clear:both">
    			</form>
    		</div>
    		<div id="inquiry" class="panel">
    			
    			
    			<br style="clear:both">
    		</div>
    	</div>
    	<br style="clear:both">
    </div>
    <script>
		$(document).ready(function() {
			
			<?=insertFormAjax('formUserInfo')?>
		    
		    <?=insertFormAjax('formPassword','true')?>
		    
		    
			
			
			$('li.active').siblings().show();
			$('.del').click(function() {
				var listing_id = $(this).attr("id");
				$.get("ajax/watchlist.php?action=unwatch&id=" + listing_id);
				$(this).closest("tr").hide();
				return false;
			});
			$(".nuclear-link").click(function() {
				var hash = '#' + $(this).attr("tab");
				$('.side-nav').find('a').filter('[href=' + hash + ']').click();
				window.location.hash = '';
				if($(this).attr("msg")) {
					$('#inquiry').load("ajax/inquiry.php?id="+$(this).attr("msg"), function() {
						$('#add_reply').click(function() {
							
							
							$('#reply').slideDown();
							var newHeight = $('#reply').closest('.panels')[0].scrollHeight;
							$('#reply').closest('.panels').animate({height: newHeight});
							
							return false;
						});
					});
				}
				return false;
			});
			$('#add_reply').click(function() {
				alert('hi!');
				$('#reply').fadeIn();
				return false;
			});
			<? if(isset_or($_REQUEST['inquiry_id'])) : ?>
			$('#inquiry').load("ajax/inquiry.php?id=<?=$_REQUEST['inquiry_id']?>", function() {
				$('#add_reply').click(function() {
					
					
					$('#reply').slideDown();
					var newHeight = $('#reply').closest('.panels')[0].scrollHeight;
					$('#reply').closest('.panels').animate({height: newHeight});
					
					return false;
				});
			});
			<? endif; ?>
		});
	</script>
    <? include("foot.php"); ?>