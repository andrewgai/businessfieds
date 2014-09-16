<?		
	$business_types = $db->query("SELECT c.id parent_id, c.name parent_name, c2.id child_id, c2.name child_name
									FROM  categories c
									LEFT JOIN categories c2 ON c.id = c2.parent_id
									WHERE c.parent_id = 0");
	
	while($business_type = $db->fetchNextObject($business_types)) {
		$categories[$business_type->parent_id]['name'] = $business_type->parent_name;
		$categories[$business_type->parent_id]['sub_cat'][$business_type->child_id] = $business_type->child_name;
	}
	
	
	
	if(route(1) == 'step2') {

		foreach ($_POST as $key => $value) {
			$$key = mysql_real_escape_string($value);
		}
		$seller_financing = (isset_or($seller_financing) == '1')?:'0';
		$relocatable = (isset_or($relocatable) == '1')?: '0';
		$franchise = (isset_or($franchise) == '1')?: '0';
		$home_based = (isset_or($home_based) == '1')?:'0';
		$type = $_REQUEST['ad_type'];
		$spotlight = ($type == 'spotlight')? '1': '0';
		
		$price = $type .'_price';
		$price = 2 * $db->queryUniqueValue("SELECT value FROM settings WHERE name = '{$price}'");
		
		$query = "INSERT INTO listings (user_id, country, state, county, city, type, spotlight, category_id, sub_category_id, headline, description, asking_price, seller_financing, gross_rev, gross_rev_comments, cash_flow, cash_flow_comments, inventory_value, inventory_included, ffe_value, ffe_included, realestate_value, realestate_included, second_category_id, second_sub_category_id, seller_financing_desc, year_established, num_employees, relocatable, franchise, home_based, mgmt_training, sell_reason, facilities, market_outlook, keywords, active)
					VALUES ('{$u_id}','{$country}','{$state}','{$county}','{$city}','{$type}','{$spotlight}','{$category_id}','{$sub_category_id}','{$headline}','{$description}','{$asking_price}','{$seller_financing}','{$gross_rev}','{$gross_rev_comments}','{$cash_flow}','{$cash_flow_comments}','{$inventory_value}','{$inventory_included}','{$ffe_value}','{$ffe_included}','{$realestate_value}','{$realestate_included}','{$second_category_id}','{$second_sub_category_id}','{$seller_financing_desc}','{$year_est}','{$num_emp}','{$relocatable}','{$franchise}','{$home_based}','{$training}','{$sell_reason}','{$facilities}','{$competition}','{$keywords}','0')";
						
		//$db->query($query);
			
	}
	
	switch(route(1)) {
		case 'standard':
			$ad_type = 'standard';
			break;
		case 'featured':
			$ad_type = 'featured';
			break;
		case 'spotlight':
			$ad_type = 'spotlight';
			break;
		default:
			$ad_type = '';
			break;
	}
	
	require_once("includes/arrays.php");
	
	$stylesheets = array("css/validationEngine.jquery.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js");
	//print_r($categories);

	$title = "Sign Up!";
	include("head.php"); ?>
	<div id="content">
		
		<? if(route(1) == null) :?>
		<div class="content-box">
	    	<h1 style="margin:20px;">List Your Business For Sale on BusinessFieds.com TODAY!</h1>
	        <div style="width:250px;display:inline-block;float: left;margin-left:20px;height:300px;">
	        	<h2>All of our packages include</h2>
	            <ul class="tick24">
	            	<li>Feature 1</li>
	                <li>Feature 2</li>
	                <li>Feature 3</li>
	                <li>Feature 4</li>
	                <li>Feature 5</li>
	                <li>Feature 6</li>
	            </ul>
	        </div>
	        <div class="package blue" style="height:320px;">
	        	
	        	<div class="title">Standard</div>
	        	<div class="price">$59</div>

            	<ul>
                	<li>Standard listing</li>
                    <li>60 Day term</li>
                </ul>

	            
	            <div class="big-price">$59</div>
	            <a href="/signup/standard" class="button_green" style="position:absolute;bottom:0;margin-bottom:15px;left:50%;margin-left:-38px;z-index:2;">Choose</a>
	        </div>
	        <div class="package green" style="height:320px;">
	        	
	        	<div class="title">Featured</div>
	        	<div class="price">$99</div>

            	<ul>
                	<li>Search highlight</li>
                    <li>60 Day term</li>
                    <li>Statistics</li>
                </ul>

	            
	            <div class="big-price">$99</div>
	            <a href="/signup/featured" class="button_green" style="position:absolute;bottom:0;margin-bottom:15px;left:50%;margin-left:-44px;z-index:2;">Choose</a>
	        </div>
	        <div class="package orange" style="height:320px;">
	        	
	        	<div class="title">Spotlight</div>
	        	<div class="price">$159</div>

	        	<ul>
	            	<li>Top of search results</li>
	                <li>90 Day term</li>
	                <li>Extended Statistics</li>
	                <li>Email to matched buyers</li>
	            </ul>

	            
	            <div class="big-price">$159</div>
	            <a href="/signup/spotlight" class="button_green" style="position:absolute;bottom:0;margin-bottom:15px;left:50%;margin-left:-44px;z-index:2;">Choose</a>
	        </div>
	        <div style="width:100%;overflow:auto;margin-top:30px;padding-top:30px;">
	        	<hr color="#efefef">
	            <h1 style="margin:20px;">More Information</h1>
	            <p class="bigtext" style="margin:20px;">
	            	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam est nibh, elementum eu volutpat ut, scelerisque adipiscing mauris. Nam adipiscing cursus nunc, vitae porta justo ornare vel. Pellentesque hendrerit ipsum vitae orci auctor auctor. Fusce vestibulum nulla vitae massa scelerisque venenatis. Quisque tellus lacus, sollicitudin vitae ultricies et, ullamcorper at sem. Quisque imperdiet nulla nec odio fermentum quis pharetra nunc consectetur. Proin condimentum nulla eu turpis mattis elementum. Proin id justo non urna porta rhoncus. Mauris euismod, ligula a faucibus condimentum, nisl risus sagittis dui, a iaculis ligula massa rhoncus quam. Donec commodo facilisis justo eget ultrices. Fusce in est risus. Donec cursus elementum erat non pharetra. Praesent pulvinar elementum dignissim.
	            </p>
	        </div>
        </div>
        
        <? elseif(route(1) == 'step2'): ?>
        <div class="content-box">
	        <form action="/signup/complete" method="post" id="billingForm">
	        <h1 style="margin:20px;">Purchase Your Business For Sale Ad</h1>
	        <h2 style="margin:20px;margin-top:40px">Step 2 of 2: Enter Payment Information</h2>
	        <p style="margin:20px">Enter your credit card information below. All information is encrypted and transmitted over a secure connection. 
	        	<br />We accept Visa, MasterCard, American Express, and Discover.</p>
	        <h3 class="bar">Package Information <span class="hint">You can edit your listing at any time with your login</span></h3>
	        
	        <div class="gridform big floatleft" style="margin-bottom:20px;width:520px">
	        	<table style="margin-left:40px;margin-top:20px;width:650px">
	        		<tr>
	        			<td style="width:190px" class="bold">Product Description:</td>
	        			<td style="height:46px;color:#8eb124;font-weight:600;">BusinessFieds <?=ucwords($type)?> Listing</td>
	        		</tr>
	        		<tr>
	        			<td style="width:190px" class="bold">Listing Term:</td>
	        			<td style="height: 46px;color:#8eb124;font-weight:600;"> <?=formatDate('now')?> to <?=formatDate('now + 2 months')?></td>
	        		</tr>
	        		<tr>
	        			<td class="bold">Price:</td>
	        			<td style="color:#8eb124;font-weight:600;">$<?=$price?></td>
	        		</tr>
	        	</table>
	        </div>
	        <br class="clear">
	        
	        <h3 class="bar">Payment Info <span class="hint">Your address on file must match the billing address for the card entered here</span></h3>
	        <div class="lightform floatleft" style="margin-bottom:20px;width:520px">
	        	<table style="margin-left:40px;margin-top:20px;width:650px">
	        		<tr>
	        			<td class="bold" style="vertical-align:top;padding-top:15px;width:190px;">
	        				<img src="/images/lock.png">
	        			</td>
	        			<td>
	        				<div class="credit-card">
	        					<span class="title">Payment Details</span><img src="/images/visa.png" class="floatright visalogo cardlogo hidden"><img src="/images/mastercard.png" class="floatright mastercardlogo cardlogo hidden"><img src="/images/discover.png" class="floatright discoverlogo cardlogo hidden"><img src="/images/amex.png" class="floatright amexlogo cardlogo hidden">
	        					<div class="field-label">Card Number</div>
	        					<input type="text" name="card_number" class="card-info validate[required,creditCard]" style="width:325px">
	        					<div class="bottombar"></div>
	        					
	        					<div class="field-label">Expiration <span class="floatright">Cvc</span></div>
	        					
	        					<select class="card-info" style="width: 125px;margin-right:20px">
	        						<option value="01">January (01)</option>
	        						<option value="02">February (02)</option>
	        						<option value="03">March (03)</option>
	        						<option value="04">April (04)</option>
	        						<option value="05">May (05)</option>
	        						<option value="06">June (06)</option>
	        						<option value="07">July (07)</option>
	        						<option value="08">August (08)</option>
	        						<option value="09">September (09)</option>
	        						<option value="10">October (10)</option>
	        						<option value="11">November (11)</option>
	        						<option value="12">December (12)</option>
	        					</select>
	        					<select class="card-info" style="width: 80px">
	        						<? for($i=0; $i < 10; $i++): ?>
	        						
	        						<option value="<?=date("Y", strtotime("now +".$i." years"))?>"><?=date("Y", strtotime("now +".$i." years"))?></option>
	        						<? endfor; ?>
	        					</select>
	        					<input type="text" name="card_cvc" class="card-info floatright validate[required,min[3],max[4],custom[integer]]" style="width:80px">
	        					<div class="accent"></div>
	        				</div>
	        			</td>
	        		</tr>
	        	</table>
	        </div>
	        <br class="clear">
	        
	        <h3 class="bar">Billing Address</h3>
	        <div class="lightform margins floatleft" style="margin-bottom:20px;width:520px">
	        	<table style="margin-left:40px;margin-top:20px;width:650px">
	        		<tr>
	        			<td class="bold" style="padding-top:15px;width:190px;">
	        				Address:
	        			</td>
	        			<td >
	        				<input type="text" name="billing_address" class="validate[required]">
	        			</td>
	        		</tr>
	        		<tr>
	        			<td class="bold"></td>
	        			<td style="padding-bottom:10px"><input type="text" name="billing_address2"></td>
	        		</tr>
	        		<tr>
	        			<td class="bold">City:</td>
	        			<td><input type="text" name="billing_city" class="validate[required]"></td>
	        		</tr>
	        		<tr>
	        			<td class="bold">State:</td>
	        			<td>
	        				<select name="billing_state" class="validate[required]">
	        					<option value="">-- Choose State --</option>
	        				<? foreach($state_list as $abb => $name): ?>
	        					<option value="<?=$abb?>"><?=$name?></option>
	        				<? endforeach?> 
	        				</select>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td class="bold">Zip:</td>
	        			<td><input type="text" name="billing_zip" class="validate[required]"></td>
	        		</tr>
	        	</table>
	        </div>
	        
	        <br class="clear">
	        
	        </form>
	        <a href="#" class="button_green floatright completePurchase" style="margin-right:20px;margin-bottom:30px">Complete Purchase <span style="padding-left: 5px">&#9658;</span></a>
	        <br class="clear">
      	</div><!-- .content-box -->
        <? elseif($ad_type != ''): ?>
        <form action="/signup/step2" method="post" id="newAdForm">
        <div class="content-box">
	        <input type="hidden" name="ad_type" value="<?=$ad_type?>">
	        <h1 style="margin:20px;">Create Your Business For Sale Ad <a href="#" class="button_green floatright last-child submitBtn">Continue <span style="padding-left: 5px">&#9658;</span></a><a href="#" class="button_black floatright previewBtn">Preview <span style="padding-left: 5px">&#9658;</span></a></h1>
	        <h2 style="margin:20px;margin-top:40px">Step 1 of 2: Enter Account and Listing Information</h2>
	        <h3 class="bar">1. Create an Account or Login <span class="hint">If you have an account simply enter your current email address and password.</span></h3>
	        <? if($_SESSION['logged_in']): ?>
	        <p class="indent">
	        	You are logged in as <strong><?=$u_email?></strong>
	        	<input type="hidden" name="validLogin" value="true">
	        </p>
	        <? else: ?>
	        
	        <div class="lightform big floatleft autowidth" style="margin-left:20px;margin-bottom:20px;width:520px">
	        	<table style="margin-left:20px;margin-top:20px;width:650px">
	        		<tr>
	        			<td style="width:190px" class="bold">Email:</td>
	        			<td><input type="text" name="new_user_email" class="validate[required,custom[email]]"></td>
	        		</tr>
	        		<tr>
	        			<td style="width:190px" class="bold">Choose Password:</td>
	        			<td><input type="password" name="new_user_password" class="validate[required]"> <span style="color:#aaa;font-weight:100">(6 character min.)</span></td>
	        		</tr>
	        	</table>
	        </div>
	        <div class="loginResult floatleft" style="margin-top:20px;margin-left:20px"></div>
	        <br class="clear">
	        <? endif; ?>
	        
	        <h3 class="bar">2. Enter Basic Info <span class="hint">You can edit your listing at any time by using your login</span></h3>
	        <div class="floatright hint-block" style="width:250px;margin:20px">
	        	<h4>About Headlines</h4>
	        	Your headline highlights your business's best features in the search results. Keep it short and simple, but be sure mention the type of business and one or two good qualities
				<br><br>
				<p style="margin-bottom:20px;font-style:italic">&raquo; Coffee Shop/Restaurant in busy downtown area. Great cash flow!</p>
				<h4>About Descriptions</h4>
				Your description is where you communicate your business's best features in detail. The best descriptions are detailed and yet easy to read for busy online buyers.
				<br><br>
				Bulleted lists or short paragraphs are generally the best way to lay out your ad for easy reading. HTML is not permitted
	        </div>
	        <div class="lightform big autowidth" style="overflow:visible;margin-left:20px">
	        	<table style="margin-left:20px;margin-top:20px;width:650px">
	        		<tr>
	        			<td style="width:190px" class="bold">Headline:</td>
	        			<td><input type="text" name="headline" style="width:400px" class="validate[required]"></td>
	        		</tr>
	        		<tr>
	        			<td class="bold" style="vertical-align:top;padding-top:15px;">Description:</td>
	        			<td>
	        				<span class="countdown">4000 characters remaining.</span><br>
	        				<textarea style="width:400px;height:200px" name="description" class="count_text validate[required]" limit="4000"></textarea>
	        				</td>
	        		</tr>
	        		<tr>
	        			<td class="bold">Type of Business:</td>
	        			<td><select id="biz-type" name="category_id" class="validate[required]">
	        					<option value="">-- Choose an Industry --</option>
	        					<? foreach($categories as $key => $value): ?>
	        					<option value="<?=$key?>"><?=$value['name']?></option>
	        					<? endforeach;?>
	        				</select></td>
	        		</tr>
	        		<tr>
	        			<td class="bold">Business Sub-Type:</td>
	        			<td>
	        				<div class="subtype">
	        					<select class="validate[required]">
	        						<option value="">- Choose Segment -</option>
	        					</select>
	        				</div>
	        				<? foreach($categories as $id => $category): ?>
	        				<div class="subtype<?=$id?> hidden">
		        				<select name="sub_category_id" class="subtypes">
		        					<option value="">- Choose Segment -</option>
		        					<? foreach($category['sub_cat'] as $key => $value):  ?>
		        					<option value="<?=$key?>"><?=$value?></option>
		        					<? endforeach; ?>
		        				</select>
	        				</div>
	        				<? endforeach; ?>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td class="bold">Country:</td>
	        			<td>
	        				<select id="biz-country" name="country" class="validate[required]">
							    <option value="" selected>-- Choose a Country --</option>
							    <option value="US" selected='selected'>United States</option>
							    <option value="AF">Afghanistan</option>
							    <option value="AL">Albania</option>
							    <option value="DZ">Algeria</option>
							    <option value="AS">American Samoa</option>
							    <option value="AD">Andorra</option>
							    <option value="AO">Angola</option>
							    <option value="AI">Anguilla</option>
							    <option value="AQ">Antarctica</option>
							    <option value="AG">Antigua and Barbuda</option>
							    <option value="AR">Argentina</option>
							    <option value="AM">Armenia</option>
							    <option value="AW">Aruba</option>
							    <option value="AU">Australia</option>
							    <option value="AT">Austria</option>
							    <option value="AZ">Azerbaijan</option>
							    <option value="BS">Bahamas</option>
							    <option value="BH">Bahrain</option>
							    <option value="BD">Bangladesh</option>
							    <option value="BB">Barbados</option>
							    <option value="BY">Belarus</option>
							    <option value="BE">Belgium</option>
							    <option value="BZ">Belize</option>
							    <option value="BJ">Benin</option>
							    <option value="BM">Bermuda</option>
							    <option value="BT">Bhutan</option>
							    <option value="BO">Bolivia</option>
							    <option value="BA">Bosnia and Herzegowina</option>
							    <option value="BW">Botswana</option>
							    <option value="BV">Bouvet Island</option>
							    <option value="BR">Brazil</option>
							    <option value="IO">British Indian Ocean Territory</option>
							    <option value="BN">Brunei Darussalam</option>
							    <option value="BG">Bulgaria</option>
							    <option value="BF">Burkina Faso</option>
							    <option value="BI">Burundi</option>
							    <option value="KH">Cambodia</option>
							    <option value="CM">Cameroon</option>
							    <option value="CA">Canada</option>
							    <option value="CV">Cape Verde</option>
							    <option value="KY">Cayman Islands</option>
							    <option value="CF">Central African Republic</option>
							    <option value="TD">Chad</option>
							    <option value="CL">Chile</option>
							    <option value="CN">China</option>
							    <option value="CX">Christmas Island</option>
							    <option value="CC">Cocos (Keeling) Islands</option>
							    <option value="CO">Colombia</option>
							    <option value="KM">Comoros</option>
							    <option value="CG">Congo</option>
							    <option value="CD">Congo, the Democratic Republic of the</option>
							    <option value="CK">Cook Islands</option>
							    <option value="CR">Costa Rica</option>
							    <option value="CI">Cote d'Ivoire</option>
							    <option value="HR">Croatia (Hrvatska)</option>
							    <option value="CU">Cuba</option>
							    <option value="CY">Cyprus</option>
							    <option value="CZ">Czech Republic</option>
							    <option value="DK">Denmark</option>
							    <option value="DJ">Djibouti</option>
							    <option value="DM">Dominica</option>
							    <option value="DO">Dominican Republic</option>
							    <option value="TP">East Timor</option>
							    <option value="EC">Ecuador</option>
							    <option value="EG">Egypt</option>
							    <option value="SV">El Salvador</option>
							    <option value="GQ">Equatorial Guinea</option>
							    <option value="ER">Eritrea</option>
							    <option value="EE">Estonia</option>
							    <option value="ET">Ethiopia</option>
							    <option value="FK">Falkland Islands (Malvinas)</option>
							    <option value="FO">Faroe Islands</option>
							    <option value="FJ">Fiji</option>
							    <option value="FI">Finland</option>
							    <option value="FR">France</option>
							    <option value="FX">France, Metropolitan</option>
							    <option value="GF">French Guiana</option>
							    <option value="PF">French Polynesia</option>
							    <option value="TF">French Southern Territories</option>
							    <option value="GA">Gabon</option>
							    <option value="GM">Gambia</option>
							    <option value="GE">Georgia</option>
							    <option value="DE">Germany</option>
							    <option value="GH">Ghana</option>
							    <option value="GI">Gibraltar</option>
							    <option value="GR">Greece</option>
							    <option value="GL">Greenland</option>
							    <option value="GD">Grenada</option>
							    <option value="GP">Guadeloupe</option>
							    <option value="GU">Guam</option>
							    <option value="GT">Guatemala</option>
							    <option value="GN">Guinea</option>
							    <option value="GW">Guinea-Bissau</option>
							    <option value="GY">Guyana</option>
							    <option value="HT">Haiti</option>
							    <option value="HM">Heard and Mc Donald Islands</option>
							    <option value="VA">Holy See (Vatican City State)</option>
							    <option value="HN">Honduras</option>
							    <option value="HK">Hong Kong</option>
							    <option value="HU">Hungary</option>
							    <option value="IS">Iceland</option>
							    <option value="IN">India</option>
							    <option value="ID">Indonesia</option>
							    <option value="IR">Iran (Islamic Republic of)</option>
							    <option value="IQ">Iraq</option>
							    <option value="IE">Ireland</option>
							    <option value="IL">Israel</option>
							    <option value="IT">Italy</option>
							    <option value="JM">Jamaica</option>
							    <option value="JP">Japan</option>
							    <option value="JO">Jordan</option>
							    <option value="KZ">Kazakhstan</option>
							    <option value="KE">Kenya</option>
							    <option value="KI">Kiribati</option>
							    <option value="KP">Korea, Democratic People's Republic of</option>
							    <option value="KR">Korea, Republic of</option>
							    <option value="KW">Kuwait</option>
							    <option value="KG">Kyrgyzstan</option>
							    <option value="LA">Lao People's Democratic Republic</option>
							    <option value="LV">Latvia</option>
							    <option value="LB">Lebanon</option>
							    <option value="LS">Lesotho</option>
							    <option value="LR">Liberia</option>
							    <option value="LY">Libyan Arab Jamahiriya</option>
							    <option value="LI">Liechtenstein</option>
							    <option value="LT">Lithuania</option>
							    <option value="LU">Luxembourg</option>
							    <option value="MO">Macau</option>
							    <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
							    <option value="MG">Madagascar</option>
							    <option value="MW">Malawi</option>
							    <option value="MY">Malaysia</option>
							    <option value="MV">Maldives</option>
							    <option value="ML">Mali</option>
							    <option value="MT">Malta</option>
							    <option value="MH">Marshall Islands</option>
							    <option value="MQ">Martinique</option>
							    <option value="MR">Mauritania</option>
							    <option value="MU">Mauritius</option>
							    <option value="YT">Mayotte</option>
							    <option value="MX">Mexico</option>
							    <option value="FM">Micronesia, Federated States of</option>
							    <option value="MD">Moldova, Republic of</option>
							    <option value="MC">Monaco</option>
							    <option value="MN">Mongolia</option>
							    <option value="MS">Montserrat</option>
							    <option value="MA">Morocco</option>
							    <option value="MZ">Mozambique</option>
							    <option value="MM">Myanmar</option>
							    <option value="NA">Namibia</option>
							    <option value="NR">Nauru</option>
							    <option value="NP">Nepal</option>
							    <option value="NL">Netherlands</option>
							    <option value="AN">Netherlands Antilles</option>
							    <option value="NC">New Caledonia</option>
							    <option value="NZ">New Zealand</option>
							    <option value="NI">Nicaragua</option>
							    <option value="NE">Niger</option>
							    <option value="NG">Nigeria</option>
							    <option value="NU">Niue</option>
							    <option value="NF">Norfolk Island</option>
							    <option value="MP">Northern Mariana Islands</option>
							    <option value="NO">Norway</option>
							    <option value="OM">Oman</option>
							    <option value="PK">Pakistan</option>
							    <option value="PW">Palau</option>
							    <option value="PA">Panama</option>
							    <option value="PG">Papua New Guinea</option>
							    <option value="PY">Paraguay</option>
							    <option value="PE">Peru</option>
							    <option value="PH">Philippines</option>
							    <option value="PN">Pitcairn</option>
							    <option value="PL">Poland</option>
							    <option value="PT">Portugal</option>
							    <option value="PR">Puerto Rico</option>
							    <option value="QA">Qatar</option>
							    <option value="RE">Reunion</option>
							    <option value="RO">Romania</option>
							    <option value="RU">Russian Federation</option>
							    <option value="RW">Rwanda</option>
							    <option value="KN">Saint Kitts and Nevis</option> 
							    <option value="LC">Saint LUCIA</option>
							    <option value="VC">Saint Vincent and the Grenadines</option>
							    <option value="WS">Samoa</option>
							    <option value="SM">San Marino</option>
							    <option value="ST">Sao Tome and Principe</option> 
							    <option value="SA">Saudi Arabia</option>
							    <option value="SN">Senegal</option>
							    <option value="SC">Seychelles</option>
							    <option value="SL">Sierra Leone</option>
							    <option value="SG">Singapore</option>
							    <option value="SK">Slovakia (Slovak Republic)</option>
							    <option value="SI">Slovenia</option>
							    <option value="SB">Solomon Islands</option>
							    <option value="SO">Somalia</option>
							    <option value="ZA">South Africa</option>
							    <option value="GS">South Georgia and the South Sandwich Islands</option>
							    <option value="ES">Spain</option>
							    <option value="LK">Sri Lanka</option>
							    <option value="SH">St. Helena</option>
							    <option value="PM">St. Pierre and Miquelon</option>
							    <option value="SD">Sudan</option>
							    <option value="SR">Suriname</option>
							    <option value="SJ">Svalbard and Jan Mayen Islands</option>
							    <option value="SZ">Swaziland</option>
							    <option value="SE">Sweden</option>
							    <option value="CH">Switzerland</option>
							    <option value="SY">Syrian Arab Republic</option>
							    <option value="TW">Taiwan, Province of China</option>
							    <option value="TJ">Tajikistan</option>
							    <option value="TZ">Tanzania, United Republic of</option>
							    <option value="TH">Thailand</option>
							    <option value="TG">Togo</option>
							    <option value="TK">Tokelau</option>
							    <option value="TO">Tonga</option>
							    <option value="TT">Trinidad and Tobago</option>
							    <option value="TN">Tunisia</option>
							    <option value="TR">Turkey</option>
							    <option value="TM">Turkmenistan</option>
							    <option value="TC">Turks and Caicos Islands</option>
							    <option value="TV">Tuvalu</option>
							    <option value="UG">Uganda</option>
							    <option value="UA">Ukraine</option>
							    <option value="AE">United Arab Emirates</option>
							    <option value="GB">United Kingdom</option>
							    <option value="UM">United States Minor Outlying Islands</option>
							    <option value="UY">Uruguay</option>
							    <option value="UZ">Uzbekistan</option>
							    <option value="VU">Vanuatu</option>
							    <option value="VE">Venezuela</option>
							    <option value="VN">Viet Nam</option>
							    <option value="VG">Virgin Islands (British)</option>
							    <option value="VI">Virgin Islands (U.S.)</option>
							    <option value="WF">Wallis and Futuna Islands</option>
							    <option value="EH">Western Sahara</option>
							    <option value="YE">Yemen</option>
							    <option value="YU">Yugoslavia</option>
							    <option value="ZM">Zambia</option>
							    <option value="ZW">Zimbabwe</option>
							</select>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td class="bold">State/Province</td>
	        			<td>
	        				<div class="statena">
		        				<select>
		        					<option>Not Applicable</option>
		        				</select>
	        				</div>
	        				<div class="stateus hidden">
	        					<select name="state" id="state">
	        						<option value="">-- Choose State --</option>
	        						<? foreach($state_list as $abb => $name): ?>
	        						<option value="<?=$abb?>"><?=$name?></option>
									<? endforeach; ?>
	        					</select>
	        				</div>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td class="bold">County:</td>
	        			<td>
	        				<div class="county-">
	        					<select>
	        						<option>Not Applicable</option>
	        					</select>
	        				</div>
	        				<? foreach($counties as $state => $county): ?>
							<div class="county-<?=$state?> hidden">
								<select name="county" class="counties" disabled="disabled">
									<option value="">-- Select County --</option>
								<? foreach($county as $key => $name): ?>
									<option value="<?=$name?>"><?=$name?></option>
								<? endforeach; ?>
								</select>
							</div>
							<? endforeach; ?> 
	        			</td>
	        		</tr>
	        		<tr>
	        			<td>City:</td>
	        			<td><input type="text" name="city"></td>
	        		</tr>
	        		<tr>
	        			<td>Asking Price:</td>
	        			<td>
	        				<input type="text" name="asking_price">
	        				<small><input type="checkbox" name="seller_financing" value="1" style="margin-right:10px;margin-left:10px;margin-top:0;margin-bottom:0"></input>Seller financing available</small>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td></td>
	        			<td><a href="#" class="button_black previewBtn">Preview <span style="padding-left: 5px">&#9658;</span></a><a href="#" class="button_green submitBtn">Continue <span style="padding-left: 5px">&#9658;</span></a></td>
	        		</tr>
	        	</table>
	        	<br class="clear">
	        </div>
	        <br class="clear">
	        <h3 class="bar clear" style="margin-top: 20px">3. Add Other Listing Details <small>(Optional)</small><span class="hint">You will be able to add a photo to your listing after checkout (featured &amp; spotlight only)</span></h3>
	        <div class="hint-block floatright" style="width:250px;margin:20px">
	    		<p>The information in this section is optional, however, we recommend that you include as much information as possible as doing so will generally lead to a higher buyer response rate.</p>
	    	</div><!-- hint-block -->
	    	
	    	<div class="lightform big marginbottom autowidth" style="overflow:auto;margin-left:20px">
	    		<table style="margin-left:20px;margin-top:20px;width:650px;">
	    			<tr>
	    				<td style="width:190px">Gross Revenue:</td>
	    				<td><input type="text" name="gross_rev"></td>
	    			</tr>
	    			<tr>
	    				<td>Gross Rev. Comments:</td> 
	    				<td><input type="text" name="gross_rev_comments"></td>
	    			</tr>
	    			<tr>
	    				<td>Cash Flow:</td>
	    				<td><input type="text" name="cash_flow"></td>
	    			</tr>
	    			<tr>
	    				<td>Cash Flow Comments:</td>
	    				<td><input type="text" name="cash_flow_comments"></td>
	    			</tr>
	    			<tr>
	    				<td>Value of Inventory:</td>
	    				<td>
	    					<input type="text" name="inventory_value">
	    					<small>Included in Asking Price? <input type="radio" name="inventory_included" value="1"> Yes <input type="radio" name="inventory_included" value="0"> No</small>
	    				</td>
	    			</tr>
	    			<tr>
	    				<td>Value of FF&amp;E:</td>
	    				<td>
	    					<input type="text" name="ffe_value">
	    					<small>Included in Asking Price? <input type="radio" name="ffe_included" value="1"> Yes <input type="radio" name="ffe_included" value="0"> No</small>
	    				</td>
	    			</tr>
	    			<tr>
	    				<td>Value of Real Estate:</td>
	    				<td>
	    					<input type="text" name="realestate_value">
	    					<small>Included in Asking Price? <input type="radio" name="realestate_included" value="1"> Yes <input type="radio" name="realestate_included" value="0"> No</small>
	    				</td>
	    			</tr>
	    			<tr>
	        			<td>Secondary Type:</td>
	        			<td><select id="biz-type2" name="second_category_id">
	        					<option value="0">-- Choose an Industry --</option>
	        					<? foreach($categories as $key => $value): ?>
	        					<option value="<?=$key?>"><?=$value['name']?></option>
	        					<? endforeach;?>
	        				</select></td>
	        		</tr>
	        		<tr>
	        			<td>Secondary Sub-Type:</td>
	        			<td>
	        				<div class="subtype2-0">
	        					<select>
	        						<option>- Choose Segment -</option>
	        					</select>
	        				</div>
	        				<? foreach($categories as $id => $category): ?>
	        				<div class="subtype2-<?=$id?> hidden">
		        				<select name="second_sub_category_id" class="subtypes2">
		        					<option value="0">- Choose Segment -</option>
		        					<? foreach($category['sub_cat'] as $key => $value):  ?>
		        					<option value="<?=$key?>"><?=$value?></option>
		        					<? endforeach; ?>
		        				</select>
	        				</div>
	        				<? endforeach; ?>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align: top;padding-top:15px;">Seller Financing:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" limit="2000" name="seller_financing_desc"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td>Year Established:</td>
	        			<td><input type="text" name="year_est" style="width:120px"></td>
	        		</tr>
	        		<tr>
	        			<td>Number of Employees:</td>
	        			<td><input type="text" name="num_emp" style="width:120px"> (e.g. 8 FTE; 4 PTE)
	        		</tr>
	        		<tr>
	        			<td></td>
	        			<td>
	        				<input type="checkbox" name="relocatable" value="true"> Relocatable
	        				<input type="checkbox" name="franchise" value="true" style="margin-left:40px;"> Franchise
	        				<input type="checkbox" name="home_based" value="true" style="margin-left:40px"> Home Based
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align:top;padding-top:15px">Management Training/Sup.:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" name="training" limit="2000"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align:top;padding-top:15px">Reason for Selling:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" name="sell_reason" limit="2000"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align:top;padding-top:15px">Facilities:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" name="facilities" limit="2000"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align:top;padding-top:15px">Market Outlook/Competition:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" name="competition" limit="2000"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td style="vertical-align:top;padding-top:15px">Keywords:</td>
	        			<td>
	        				<span class="countdown">2000 characters remaining.</span><br>
	        				<textarea style="width:400px" class="count_text" name="keywords" limit="2000"></textarea>
	        			</td>
	        		</tr>
	        		<tr>
	        			<td colspan="2">
	        				
	        			</td>
	        		</tr>
	    		</table>
	    	</div>
	    	<br class="clear">
	    	<br class="clear">
	    	<div style="display:block;width:100%">
	    	<a href="#" class="button_green floatright submitBtn" style="margin-right:20px;margin-bottom:30px">Continue <span style="padding-left: 5px">&#9658;</span></a><a href="#" class="button_black floatright previewBtn">Preview <span style="padding-left: 5px">&#9658;</span></a>
	    	</div>
	    	<br class="clear">
		</div><!-- content-box -->
		
        <br style="clear:both">
    </div>
    </form>
    <div id="preview" class="modal" style="width:670px;margin-left:-335px;top:70px; bottom:30px;overflow: hidden">
    	<a href="#" class="close"><img src="images/x.png"></a>
		<h3>Your Standard Listing on BusinessFieds will look like this:</h3>
		<div id="listing" style="position: absolute;top: 40px;margin-left:0px;bottom:5px;overflow:auto;right:0px;padding-right:10px;left:10px;">
        	<h1 class="headline">Cardio Barre Franchise</h1>
            <h3 class="location">La Jolla, California (San Diego County)</h3>
            <h6><a href="#" class="biz_cat">Building & Const. Services</a> - <a href="#" class="biz_subcat">Auto & Automotive</a></h6>
            <h6 class="2ndcat"></h6>
                        <div style="overflow: auto">
            <img src="images/nopic_thm.png" width="300" style="float:left;display:block;margin-top:10px;margin-right:20px">
            <div style="width:300px; float:right; margin-top: 10px;">
                <table class="datatable details" style="margin-bottom:10px">
                    <tr class="head">
                        <td>Asking Price:</td>
                        <td class="asking_price">$9,000,000</td>
                    </tr>
                    <tr>
                        <td>Gross Revenue:</td>
                        <td class="gross_rev">$2,000,000</td>
                    </tr>
                    <tr>
                        <td>Cash Flow:</td>
                        <td class="cash_flow">$20,000</td>
                    </tr>
                    <tr>
                        <td>FF&amp;E:</td>
                        <td class="ffe_value">N/A</td>
                    </tr>
                    <tr>
                        <td>Inventory:</td>
                        <td class="inventory">$250,000</td>
                    </tr>
                    <tr>
                        <td>Real Estate:</td>
                        <td class="real_estate">N/A</td>
                    </tr>
                </table>
                <h6>** not included in asking price</h6>
            </div>
            </div>
            <br>
        	<h2 style="margin-top:30px;">Business Description</h2>
            <p class="biz_desc"></p>
            <h2 style="margin-top:30px;">About the Business</h2>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Year Established:</span> <span class="year_est"></span></p>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Number of Employees:</span> <span class="employees"></span></p>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Facilities:</span> <span class="facilities"></span></p>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Market Outlook / Competition:</span> <span class="market_outlook"></span></p>
            <p class="hidden relocatable"><span style="font-weight:bold;padding-right:8px;display:inline-block;">Relocatable:</span> Yes</p>
            <p class="hidden franchise"><span style="font-weight:bold;padding-right:8px;display:inline-block;">Franchise:</span> Yes</p>
            <p class="hidden homebased"><span style="font-weight:bold;padding-right:8px;display:inline-block;">Home Based:</span> Yes</p>
            
            <h2 style="margin-top:30px;">About the Sale</h2>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Reason for Selling:</span> <span class="sell_reason"></span></p>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Training / Support:</span> <span class="training"></span></p>
            <p class="cf hidden"><span style="font-weight:bold;padding-right:8px;display:inline-block;">Cash Flow Comments:</span> <span class="cf_comments"></span></p>
            <p class="gr hidden"><span style="font-weight:bold;padding-right:8px;display:inline-block;">Gross Revenue Comments:</span> <span class="gr_comments"></span></p>
            <p><span style="font-weight:bold;padding-right:8px;display:inline-block;">Seller Financing:</span> <span class="seller_fin_comments"></span></p>
            
            <h6 class="keywords"></h6>
        </div>
		
        <br />
		<br>
    </div><!-- preview -->
    <? endif; ?>
    <script>
    	$(document).ready(function() {
    		$('#billingForm').validationEngine();
    		$('input[name=card_number]').bind('keyup change', function() {
    			var firstChar = $(this).val().substr(0,1);
    			if(firstChar == '3') {
    				$('.credit-card').removeClass("visa mastercard discover").addClass("amex");
    				$('.cardlogo').not('amexlogo').hide();
    				$('.amexlogo').show();
    			} else if(firstChar == '4') {
    				$('.credit-card').removeClass("amex mastercard discover").addClass("visa");
    				$('.cardlogo').not('visalogo').hide();
    				$('.visalogo').show();
    			} else if(firstChar == '5') {
    				$('.credit-card').removeClass("visa amex discover").addClass("mastercard");
    				$('.cardlogo').not('.mastercardlogo').hide();
    				$('.mastercardlogo').show();
    			} else if(firstChar == '6') {
    				$('.credit-card').removeClass("visa mastercard amex").addClass("discover");
    				$('.cardlogo').not('.discoverlogo').hide();
    				$('.discoverlogo').show();
    			} else {
    				$('.credit-card').removeClass("amex visa mastercard discover");
    				$('.cardlogo').hide();
    			}
    		});
    		$('input[name=user_email]').blur(function() {
    			if($('input[name=user_password]').val() != '') {
    				// load the user checker
    				$('.loginResult').load('ajax/checkLogin.php?email=' + $('input[name=user_email]').val() + '&password=' + $('input[name=user_password]').val());
    			}
    		});
    		$('input[name=user_password]').blur(function() {
    			if($('input[name=user_email]').val() != '') {
    				// load the user checker

    				$('.loginResult').load('ajax/checkLogin.php?email=' + $('input[name=user_email]').val() + '&password=' + $('input[name=user_password]').val());
    			}
    		});
    		
    		//$('#newAdForm').validationEngine();
    		
    		$('.submitBtn').click(function() {
    			$('#newAdForm').submit();
    			return false;
    		});
    		$(".previewBtn").click(function() {
    			if($('#newAdForm').validationEngine('validate')) {
					$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
					$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
					$('#listing .headline').html($('input[name=headline]').val());
					var location = '';
					//var city = ;
					if($('input[name=city]').val().length > 0) {
						location = $('input[name=city]').val() + ', ';
					}
					if($('select[name=country] option:selected').val() == 'US') {
						location += $('select[name=state] option:selected').text() + ' (' + $('select[name=county]').parent('div:visible').find('select option:selected').val() + ' County)';
					} else {
						location = $('select[name=country] option:selected').text();
					}
					
					$('#listing .location').html(location);
					$('#listing .biz_cat').html($('select[name=category_id] option:selected').text());
					$('#listing .biz_subcat').html($('select[name=sub_category_id]').parent('div:visible').find('select option:selected').text());
	
					if($('select[name=second_category_id] option:selected').val() != 0 && $('select[name=second_sub_category_id]').parent('div:visible').find('select option:selected').val() != 0) {
						$('#listing .2ndcat').html('<a href="#" class="2ndbiz_cat">' + $('select[name=second_category_id] option:selected').text() + '</a> - <a href="#" class="2ndbiz_subcat">' + $('select[name=second_sub_category_id]').parent('div:visible').find('select option:selected').text() + '</a>');
					}
					
					$('#listing .asking_price').html($('input[name=asking_price]').val()).currency({decimals: 0});
					$('#listing .gross_rev').html($('input[name=gross_rev]').val()).currency({decimals: 0});
					$('#listing .cash_flow').html($('input[name=cash_flow]').val()).currency({decimals: 0});
					$('#listing .ffe_value').html($('input[name=ffe_value]').val()).currency({decimals: 0});
					$('#listing .inventory').html($('input[name=inventory_value]').val()).currency({decimals: 0});
					$('#listing .real_estate').html($('input[name=realestate_value]').val()).currency({decimals: 0});
					$('#listing .year_est').html($('input[name=year_est]').val());
					$('#listing .employees').html($('input[name=num_emp]').val());
					$('#listing .biz_desc').html($('textarea[name=description]').val());
					
					$('#listing .facilities').html($('textarea[name=facilities]').val());
					$('#listing .market_outlook').html($('textarea[name=competition]').val());
					$('#listing .sell_reason').html($('textarea[name=sell_reason]').val());
					$('#listing .training').html($('textarea[name=training]').val());
					
					$('#listing .seller_fin_comments').html($('textarea[name=seller_financing_desc]').val());
					
					if($('input[name=cash_flow_comments]').val().length > 0) {
						$('#listing .cf_comments').html($('input[name=cash_flow_comments]').val());
						$('#listing .cf').show();
					}
					
					if($('input[name=gross_rev_comments]').val().length > 0) {
						$('#listing .gr_comments').html($('input[name=gross_rev_comments]').val());
						$('#listing .gr').show();
					}
					
					if($('input[name=inventory_included]:checked').val() == '1') {
						$('#listing .inventory').append(' **');
					}
					if($('input[name=ffe_included]:checked').val() == '1') {
						$('#listing .ffe_value').append(' **');
					}
					if($('input[name=realestate_included]:checked').val() == '1') {
						$('#listing .real_estate').append(' **');
					}
					
					if($('input[name=relocatable]').is(':checked')) {
						$('#listing .relocatable').show();
					}
					if($('input[name=franchise]').is(':checked')) {
						$('#listing .franchise').show();
					}
					if($('input[name=home_based]').is(':checked')) {
						$('#listing .homebased').show();
					}
					
					$('#listing .keywords').html($('textarea[name=keywords]').val());
					
					$("#preview").fadeIn(200);
					
				}
				return false;
			});
    		$('.count_text').bind('keyup change', function() {
    			var limit = $(this).attr('limit');
    			var remaining = limit - $(this).val().length;
    			$(this).parent('td').children('.countdown').html(remaining + ' characters remaining.');
    			//$('.countdown').html(remaining + ' characters remaining.');
    		});
    		$('#state').change(function() {
    			$('.county-').parent('td').children('div').hide();
    			$('.counties').attr("disabled","disabled");
    			$('.county-'+ $(this).val()).show();
    			$('.county-'+ $(this).val()).children('select').removeAttr("disabled").addClass('validate[required]');
    			
    		})
    		$('#biz-type').change(function() {
    			$('.subtypes').attr("disabled","disabled");
    			$('.subtype').parent('td').children('div').hide().children('select').removeClass('validate[required]');
    			$('.subtype'+$(this).val()).show().children('select').removeAttr("disabled").addClass('validate[required]');
    		});
    		$('#biz-type2').change(function() {
    			$('.subtypes2').attr("disabled","disabled");
    			$('.subtype2-0').parent('td').children('div').hide().children('select').removeClass('validate[required]');
    			$('.subtype2-'+$(this).val()).show().children('select').removeAttr("disabled").addClass('validate[required]');
    		});
    		$('#biz-country').change(function() {
    			if($('#biz-country').val() == 'US') {
    				$('.statena').hide();
    				$('.stateus').show().children('select').addClass('validate[required]');
    			} else {
    				$('.stateus').hide().children('select').removeClass('validate[required]');
    				$('.statena').show();
    			}
    		}).trigger('change');
    	});
    </script>
    <? include("foot.php"); ?>