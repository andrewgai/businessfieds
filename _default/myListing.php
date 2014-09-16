<?  
	$listing_id = mysql_real_escape_string(route(1));
	
	//$listing = $db->queryUniqueObject("SELECT * FROM listings WHERE id = '{$listing_id}' AND user_id = '{$u_id}'");
	// process updates
	if($_REQUEST['updateListing']) {
		//foreach($listing_fields as $field) {
			//if(isset_or(mysql_real_escape_string($_REQUEST[$field])) != '') {
		//		$input_values[] = $field ."='".isset_or(mysql_real_escape_string($_REQUEST[$field]))."'";
			//}
		//}
		$_REQUEST['seller_financing'] = isset_or($_REQUEST['seller_financing'],0);
		$_REQUEST['home_based'] = isset_or($_REQUEST['home_based'],0);
		$_REQUEST['relocatable'] = isset_or($_REQUEST['relocatable'],0);
		$_REQUEST['franchise'] = isset_or($_REQUEST['franchise'],0);
		foreach($_REQUEST as $key => $value) {
			if(in_array($key, $listing_fields)) {
				$input_values[] = $key ."='".isset_or(mysql_real_escape_string(stripslashes($_REQUEST[$key])))."'";
			}
		}
		//$insert_names = implode(", ", $listing_fields);
		//$insert_values = implode("', '", $input_values);
		$insert_data = implode(",", $input_values);
		//$query = "UPDATE listings ({$insert_names}) VALUES ('{$insert_values}')";
		$query = "UPDATE listings SET {$insert_data} WHERE id = '{$listing_id}'";
		$result = $db->query($query);
		if($result) {
			$alerts[] = showAlert("Your listing has been updated!","alert_positive");
		} else {
			$alerts[] = showAlert("There was a problem updating your listing.","alert_negative");
		}
	}
	
	
	
    $listing = $db->queryUniqueObject("SELECT * FROM listings WHERE id = '{$listing_id}' AND user_id = '{$u_id}'");
	
	if($listing == null) {
		Header("Location: /myAccount");
	}
	
	
    $messages = $db->query("SELECT m.*, u.first_name, u.last_name,
    						CASE WHEN (SELECT m2.read FROM messages m2 WHERE m2.parent_id = m.id AND m2.to_id = '{$u_id}' ORDER BY m2.timestamp DESC LIMIT 1) = 0 THEN 1 ELSE 0 END new_message,
    						(SELECT m2.timestamp FROM messages m2 WHERE m2.parent_id = m.id ORDER BY m2.timestamp DESC LIMIT 1) last_update
    						
    						FROM messages m
    						LEFT JOIN users u ON m.from_id = u.id
    						WHERE parent_id = 0 AND listing_id = '{$listing_id}'");

    
	
	$business_types = $db->query("SELECT c.id parent_id, c.name parent_name, c2.id child_id, c2.name child_name
									FROM  categories c
									LEFT JOIN categories c2 ON c.id = c2.parent_id
									WHERE c.parent_id = 0");
	
	
	while($business_type = $db->fetchNextObject($business_types)) {
		$categories[$business_type->parent_id]['name'] = $business_type->parent_name;
		$categories[$business_type->parent_id]['sub_cat'][$business_type->child_id] = $business_type->child_name;
	}
	
	$photos = $db->query("SELECT * FROM photos WHERE listing_id = '{$listing_id}'");


	
    $stylesheets = array("css/validationEngine.jquery.css", "styles/bottom.css");
    $scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js", "js/jquery.form.js", "js/jquery.pikachoose.js", "js/jquery.jcarousel.min.js");
    
    $title = "My Listing - ".$listing->headline;
    include("head.php"); ?>
	<div id="imgViewer" class="modal" style="width:600px; max-width: 600px; max-height: 600px; overflow:hidden; margin-left:-300px;text-align:center">
		<a href="#" class="close"><img src="images/x.png"></a>

        <br />
       	<img id="bigImage" src="" style="max-width:600px;">
	</div><!-- #imgViewer .modal -->
	
    <div id="content">
        <div class="content-menu side-nav floatleft" style="width:220px">
            <ul class="first">
            	<li class="active">Manage Listing</li>
                <li class="constant"><a href="#inquiries">Inquiries</a></li>
                <li class="constant"><a href="#edit_listing">Edit Listing</a></li>
                <li class="constant"><a href="#manage_photos">Manage Photos</a></li>
                <li class="constant"><a href="#statistics">Statistics</a></li>
            </ul>
            <ul class="hidden">
	    		<li><a href="#inquiry">Inquiry</a></li>
	    	</ul>
        </div>
        <div class="content-box floatright panels" style="width:760px">
            <div id="inquiries" class="panel">
                <h2>Inquiries</h2>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? if($db->numRows($messages) == 0): ?>
                        <tr>
                            <td colspan="2" style="text-align:center">You do not have any inquiries.</td>
                        </tr>
                        <? else: 
                        while($message = $db->fetchNextObject($messages)): ?>
                        <tr <?=($message->new_message)? 'style="font-weight: bold"':''?>>
                            <td><a href="#" tab="inquiry" class="nuclear-link" msg="<?=$message->id?>"><?=$message->first_name?> <?=$message->last_name?></a></td>
                            <td class="aligncenter"><?=formatDateTime($message->last_update)?></td>
                        </tr>
                        <? endwhile; 
                        endif; ?>
                    </tbody>
                </table>
                
            </div>
            <div id="inquiry" class="panel">
    			
    			
    			<br style="clear:both">
    		</div><!-- #inquiry .panel -->
    		<div id="edit_listing" class="panel">
    			<div id="edit_response"></div>
    			<div class="message"></div>
    			<form action="/ajax/forms.php" method="post" id="listingForm">
    			<input type="hidden" name="doUpdateListing" value="true">
    			<input type="hidden" name="listing_id" value="<?=$listing_id?>">
    			
    			<h2>Edit Listing: <?=$listing->headline?> <a href="#" class="submitBtn blocklink floatright last-child" data="listingForm"><img src="images/disk.png">Save</a></h2>
    			
    			<div class="lightform fullwidth" style="overflow:auto">
		        	<table class="datatable noborders" style="margin-left:0px;width:100%">
		        		<tr>
		        			<td style="width:190px" class="bold">Headline:</td>
		        			<td><input type="text" name="headline" style="width:400px" class="validate[required]" value="<?=$listing->headline?>"></td>
		        		</tr>
		        		<tr>
		        			<td class="bold" style="vertical-align:top;padding-top:15px;">Description:</td>
		        			<td>
		        				<span class="countdown">4000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:200px" name="description" class="count_text validate[required]" limit="4000"><?=nl2br($listing->description)?></textarea>
		        				</td>
		        		</tr>
		        		<tr>
		        			<td class="bold">Type of Business:</td>
		        			<td><select id="biz-type" name="category_id" class="validate[required] autowidth">
		        					<option value="">-- Choose an Industry --</option>
		        					<? foreach($categories as $key => $value): ?>
		        					<option value="<?=$key?>" <?=($listing->category_id == $key)?'selected="selected"':''?>><?=$value['name']?></option>
		        					<? endforeach;?>
		        				</select></td>
		        		</tr>
		        		<tr>
		        			<td class="bold">Business Sub-Type:</td>
		        			<td>
		        				<div class="subtype">
		        					<select class="validate[required] autowidth">
		        						<option value="">- Choose Segment -</option>
		        					</select>
		        				</div>
		        				<? foreach($categories as $id => $category): ?>
		        				<div class="subtype<?=$id?> hidden">
			        				<select name="sub_category_id" class="subtypes autowidth">
			        					<option value="">- Choose Segment -</option>
			        					<? foreach($category['sub_cat'] as $key => $value):  ?>
			        					<option value="<?=$key?>" <?=($listing->sub_category_id == $key)?'selected="selected"':''?>><?=$value?></option>
			        					<? endforeach; ?>
			        				</select>
		        				</div>
		        				<? endforeach; ?>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td class="bold">Country:</td>
		        			<td>
		        				<select id="biz-country" name="country" class="validate[required] autowidth">
								    <option value="" selected>-- Choose a Country --</option>
								    <? foreach($countries as $abb => $name): ?>
								    <option value="<?=$abb?>" <?=($abb == $listing->country)?'selected="selected"':''?>><?=$name?></option>
								    <? endforeach; ?>
								</select>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td class="bold">State/Province</td>
		        			<td>
		        				<div class="statena">
			        				<select class="autowidth">
			        					<option>Not Applicable</option>
			        				</select>
		        				</div>
		        				<div class="stateus hidden">
		        					<select name="state" id="state" class="autowidth">
		        						<option value="">-- Choose State --</option>
		        						<? foreach($state_list as $abb => $name): ?>
		        						<option value="<?=$abb?>" <?=($listing->state == $abb)?'selected="selected"':''?>><?=$name?></option>
										<? endforeach; ?>
		        					</select>
		        				</div>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td class="bold">County:</td>
		        			<td>
		        				<div class="county-">
		        					<select class="autowidth">
		        						<option>Not Applicable</option>
		        					</select>
		        				</div>
		        				<? foreach($counties as $state => $county): ?>
								<div class="county-<?=$state?> hidden">
									<select name="county" class="counties autowidth" disabled="disabled">
										<option value="">-- Select County --</option>
									<? foreach($county as $key => $name): ?>
										<option value="<?=$name?>" <?=($listing->county == $name)?'selected="selected"':''?>><?=$name?></option>
									<? endforeach; ?>
									</select>
								</div>
								<? endforeach; ?> 
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>City:</td>
		        			<td><input type="text" name="city" value="<?=$listing->city?>"></td>
		        		</tr>
		        		<tr>
		        			<td>Asking Price:</td>
		        			<td>
		        				<input type="text" name="asking_price" value="<?=$listing->asking_price?>">
		        				
		        				<input type="checkbox" name="seller_financing" value="1" style="vertical-align:middle;margin-top:-2px;margin-left:20px;margin-right:10px" <?=($listing->seller_financing == 1)?'checked':''?>>Seller financing available
		        			</td>
		        		</tr>
		        		
		        	</table>
		        </div>
		        <br class="clear">
		        
		    	<p class="indent" style="margin-bottom:0px;padding-bottom:0px;">The information in this section is optional, however, we recommend that you include as much information as possible as doing so will generally lead to a higher buyer response rate.</p>
		    	
		    	<div class="lightform fullwidth" style="overflow:auto">
		    		<table class="datatable noborders" style="margin-left:0px;margin-top:20px;width:100%">
		    			<tr>
		    				<td style="width:190px">Gross Revenue:</td>
		    				<td><input type="text" name="gross_rev" value="<?=$listing->gross_rev?>"></td>
		    			</tr>
		    			<tr>
		    				<td>Gross Rev. Comments:</td> 
		    				<td><input type="text" name="gross_rev_comments" value="<?=$listing->gross_rev_comments?>" style="width:400px;"></td>
		    			</tr>
		    			<tr>
		    				<td>Cash Flow:</td>
		    				<td><input type="text" name="cash_flow" value="<?=$listing->cash_flow?>"></td>
		    			</tr>
		    			<tr>
		    				<td>Cash Flow Comments:</td>
		    				<td><input type="text" name="cash_flow_comments" value="<?=$listing->cash_flow_comments?>" style="width:400px;"></td>
		    			</tr>
		    			<tr>
		    				<td>Value of Inventory:</td>
		    				<td>
		    					<input type="text" name="inventory_value" value="<?=$listing->inventory_value?>">
		    					<small>Included in Asking Price? <input type="radio" name="inventory_included" value="1" <?=($listing->inventory_included == 1)?'checked="checked"':''?>> Yes <input type="radio" name="inventory_included" value="0" <?=($listing->inventory_included != 1)?'checked="checked"':''?>> No</small>
		    				</td>
		    			</tr>
		    			<tr>
		    				<td>Value of FF&amp;E:</td>
		    				<td>
		    					<input type="text" name="ffe_value" value="<?=$listing->ffe_value?>">
		    					<small>Included in Asking Price? <input type="radio" name="ffe_included" value="1" <?=($listing->ffe_included == 1)?'checked="checked"':''?>> Yes <input type="radio" name="ffe_included" value="0" <?=($listing->ffe_included != 1)?'checked="checked"':''?>> No</small>
		    				</td>
		    			</tr>
		    			<tr>
		    				<td>Value of Real Estate:</td>
		    				<td>
		    					<input type="text" name="realestate_value" value="<?=$listing->realestate_value?>">
		    					<small>Included in Asking Price? <input type="radio" name="realestate_included" value="1" <?=($listing->realestate_included == 1)?'checked="checked"':''?>> Yes <input type="radio" name="realestate_included" value="0" <?=($listing->realestate_included != 1)?'checked="checked"':''?>> No</small>
		    				</td>
		    			</tr>
		    			<tr>
		        			<td>Secondary Type:</td>
		        			<td><select id="biz-type2" name="second_category_id" class="autowidth">
		        					<option value="0">-- Choose an Industry --</option>
		        					<? foreach($categories as $key => $value): ?>
		        					<option value="<?=$key?>" <?=($listing->second_category_id == $key)?'selected="selected"':''?>><?=$value['name']?></option>
		        					<? endforeach;?>
		        				</select></td>
		        		</tr>
		        		<tr>
		        			<td>Secondary Sub-Type:</td>
		        			<td>
		        				<div class="subtype2-0">
		        					<select class="autowidth">
		        						<option>- Choose Segment -</option>
		        					</select>
		        				</div>
		        				<? foreach($categories as $id => $category): ?>
		        				<div class="subtype2-<?=$id?> hidden">
			        				<select name="second_sub_category_id" class="subtypes2 autowidth">
			        					<option value="0">- Choose Segment -</option>
			        					<? foreach($category['sub_cat'] as $key => $value):  ?>
			        					<option value="<?=$key?>" <?=($listing->second_sub_category_id == $key)?'selected="selected"':''?>><?=$value?></option>
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
		        				<textarea style="width:400px;height:60px;" class="count_text" limit="2000" name="seller_financing_desc"><?=nl2br($listing->seller_financing_desc)?></textarea>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>Year Established:</td>
		        			<td><input type="text" name="year_established" style="width:120px" value="<?=$listing->year_established?>"></td>
		        		</tr>
		        		<tr>
		        			<td>Number of Employees:</td>
		        			<td><input type="text" name="num_employees" style="width:120px" value="<?=$listing->num_employees?>"> (e.g. 8 FTE; 4 PTE)
		        		</tr>
		        		<tr>
		        			<td></td>
		        			<td>
		        				<input type="checkbox" name="relocatable" value="1" <?=($listing->relocatable == 1)?'checked':''?>> Relocatable
		        				<input type="checkbox" name="franchise" value="1" style="margin-left:40px;" <?=($listing->franchise == 1)?'checked':''?>> Franchise
		        				<input type="checkbox" name="home_based" value="1" style="margin-left:40px" <?=($listing->home_based == 1)?'checked':''?>> Home Based
		        			</td>
		        		</tr>
		        		<tr>
		        			<td style="vertical-align:top;padding-top:15px">Management Training/Sup.:</td>
		        			<td>
		        				<span class="countdown">2000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:60px;" class="count_text" name="mgmt_training" limit="2000"><?=nl2br($listing->mgmt_training)?></textarea>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td style="vertical-align:top;padding-top:15px">Reason for Selling:</td>
		        			<td>
		        				<span class="countdown">2000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:60px;" class="count_text" name="sell_reason" limit="2000"><?=nl2br($listing->sell_reason)?></textarea>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td style="vertical-align:top;padding-top:15px">Facilities:</td>
		        			<td>
		        				<span class="countdown">2000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:60px;" class="count_text" name="facilities" limit="2000"><?=nl2br($listing->facilities)?></textarea>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td style="vertical-align:top;padding-top:15px">Market Outlook/Competition:</td>
		        			<td>
		        				<span class="countdown">2000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:60px;" class="count_text" name="market_outlook" limit="2000"><?=nl2br($listing->market_outlook)?></textarea>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td style="vertical-align:top;padding-top:15px">Keywords:</td>
		        			<td>
		        				<span class="countdown">2000 characters remaining.</span><br>
		        				<textarea style="width:400px;height:60px;" class="count_text" name="keywords" limit="2000"><?=nl2br($listing->keywords)?></textarea>
		        			</td>
		        		</tr>
		        		
		    		</table>
		    	</div>
    			<br class="clear">
    			
    			
    			
  			</form>
  			</div><!-- panel #edit_listing -->
  			
  			<div id="manage_photos" class="panel">
  				<h2>Manage Photos <a href="#" id="addPhoto" class="blocklink floatright last-child"><img src="images/picture_add.png">Add Photo</a></span></h2>
  				
  				
				<div class="photos pika-big-thumbs">
					
				</div>
  				<br class="clear">
  				

				<iframe name="uploadFormResults" id="uploadiFrame" class="hidden"></iframe>
				<form id="uploadForm" target="uploadFormResults" action="/ajax/photoUpload.php" method="post" enctype="multipart/form-data">
	                <input id="fileInput" name="files[]" style="position:absolute;visibility:hidden;margin:0;padding:0;z-index:-1" type="file" multiple="multiple" />
	                <input type="hidden" name="listing_id" value="<?=$listing->id?>" />
	                <input type="hidden" name="submit" value="true" />
	                <input type="submit" id="uploadSubmit" style="position:absolute;visibility:hidden;z-index:-1;" />
	            </form>
  			</div><!-- panel #manage_photos -->
    	</div> <!-- panels -->
        <br style="clear:both">
    </div>
    <script>
	

        $(document).ready(function() {
        	
        	<?=insertFormAjax('listingForm')?>
        	
			$('.photos').load("/ajax/photos.php?listing_id=<?=$listing_id?>");
			
			$('.photos').on('click', '.clip', function(event) {
				var imgsrc = $(event.target).attr('src');
				$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
				$('#fade').css({'filter' : 'alpha(opacity=60)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies 
				$('#bigImage').attr('src',imgsrc);
				$("#imgViewer").slideDown(100);
				return false;
			});
			
			$('.photos').on('click', '.deletePhoto', function(event) {
				var id = $(event.target).parents('div').siblings('.clip').children('img').attr('data');

					$.get("/ajax/photos.php?delete=true&photo_id=" + id);
					$(this).parents('li').animate({width: -10}).fadeOut('fast');
					return false;
					//$(this).closest()
			});

			
			$('.photos').on('click', '.makePrimary', function(event) {
					// going to get a page to change this
					var id = $(event.target).parents('div').siblings('.clip').children('img').attr('data');
					$.get("/ajax/photos.php?primary=true&photo_id=" + id);
					$('.primaryLinks').not($(this).parent()).html('<a href="#" class="makePrimary">Make Primary</a>');
					$(this).parent('span').html('Primary');
					return false;
			});
			
			
        	var newHeight = $('.photos').closest('.panels')[0].scrollHeight;
        	//$('.photos').load('/ajax/photos.php?listing_id=<?=$listing->id?>').closest('.panels').animate({height: newHeight});
        	$("#addPhoto").click(function() {
				$("#fileInput").click();
				return false;
			});
			$("#fileInput").change(function() {
				var ext = $(this).val().split('.').pop().toLowerCase();
				if($.inArray(ext, ['png','jpg','jpeg','gif']) == -1) {
					alert('Sorry, we can only accept picture formats');
				} else {
					$("#uploadSubmit").click();
					$("#uploadiFrame").load(function() {
						$(".photos").load("/ajax/photos.php?listing_id=<?=$listing->id?>").fadeIn();
					});
				}
			});
        	
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
            
            $('.count_text').bind('keyup change', function() {
    			var limit = $(this).attr('limit');
    			var remaining = limit - $(this).val().length;
    			$(this).parent('td').children('.countdown').html(remaining + ' characters remaining.');
    			//$('.countdown').html(remaining + ' characters remaining.');
    		}).trigger('change');
    		$('#state').change(function() {
    			$('.county-').parent('td').children('div').hide();
    			$('.counties').attr("disabled","disabled");
    			$('.county-'+ $(this).val()).show();
    			$('.county-'+ $(this).val()).children('select').removeAttr("disabled").addClass('validate[required]');
    			
    		}).trigger('change');
    		$('#biz-type').change(function() {
    			$('.subtypes').attr("disabled","disabled");
    			$('.subtype').parent('td').children('div').hide().children('select').removeClass('validate[required]');
    			$('.subtype'+$(this).val()).show().children('select').removeAttr("disabled").addClass('validate[required]');
    		}).trigger('change');
    		$('#biz-type2').change(function() {
    			$('.subtypes2').attr("disabled","disabled");
    			$('.subtype2-0').parent('td').children('div').hide().children('select').removeClass('validate[required]');
    			$('.subtype2-'+$(this).val()).show().children('select').removeAttr("disabled").addClass('validate[required]');
    		}).trigger('change');
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