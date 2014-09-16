<?		
	$state_list = array('AL'=>"Alabama",  
			'AK'=>"Alaska",  
			'AZ'=>"Arizona",  
			'AR'=>"Arkansas",  
			'CA'=>"California",  
			'CO'=>"Colorado",  
			'CT'=>"Connecticut",  
			'DE'=>"Delaware",  
			'DC'=>"Dist. Of Columbia",  
			'FL'=>"Florida",  
			'GA'=>"Georgia",  
			'HI'=>"Hawaii",  
			'ID'=>"Idaho",  
			'IL'=>"Illinois",  
			'IN'=>"Indiana",  
			'IA'=>"Iowa",  
			'KS'=>"Kansas",  
			'KY'=>"Kentucky",  
			'LA'=>"Louisiana",  
			'ME'=>"Maine",  
			'MD'=>"Maryland",  
			'MA'=>"Massachusetts",  
			'MI'=>"Michigan",  
			'MN'=>"Minnesota",  
			'MS'=>"Mississippi",  
			'MO'=>"Missouri",  
			'MT'=>"Montana",
			'NE'=>"Nebraska",
			'NV'=>"Nevada",
			'NH'=>"New Hampshire",
			'NJ'=>"New Jersey",
			'NM'=>"New Mexico",
			'NY'=>"New York",
			'NC'=>"North Carolina",
			'ND'=>"North Dakota",
			'OH'=>"Ohio",  
			'OK'=>"Oklahoma",  
			'OR'=>"Oregon",  
			'PA'=>"Pennsylvania",  
			'RI'=>"Rhode Island",  
			'SC'=>"South Carolina",  
			'SD'=>"South Dakota",
			'TN'=>"Tennessee",  
			'TX'=>"Texas",  
			'UT'=>"Utah",  
			'VT'=>"Vermont",  
			'VA'=>"Virginia",  
			'WA'=>"Washington",  
			'WV'=>"West Virginia",  
			'WI'=>"Wisconsin",  
			'WY'=>"Wyoming");
	
	
	$order_array = array("timestamp","views","asking_price","cash_flow","state");
	$hash_array = array("q=1");
	// Handle Order By
	if (in_array(isset_or($_REQUEST['order']),$order_array)) {
		$dir = (strtoupper($_REQUEST['dir']) == "ASC" OR strtoupper($_REQUEST['dir']) == "DESC")?strtoupper($_REQUEST['dir']):"";
		$order = "ORDER BY l.spotlight DESC, " . mysql_real_escape_string($_REQUEST['order']) . " " . mysql_real_escape_string($dir);
		$hash_array[] = "order=".mysql_real_escape_string($_REQUEST['order']);
		$hash_array[] = "dir=".mysql_real_escape_string($dir);
	} else {
		$order = "ORDER BY l.spotlight DESC";
	}
	// Pagination variable
	$page = (isset_or($_REQUEST['page'])?$_REQUEST['page']:1);
	$start = (intval($page)*10);
	if($start == 0 || $start == 10) {
		$limit = "LIMIT 0, 10";
		$start = 0;
	} else {
		$start = $start-10;
		$limit = "LIMIT ".$start.", 10";
	}
	
	// Takes all the input from the Filter Form, or from Links, and creates the WHERE query for the database, as well as the Query String (hash) for Links.
	$where_array = array();
	
	
	$hash_array[] = "page=".$page;
	$fields = array(
				array("l.asking_price", " >= ", "min_price"),
				array("l.asking_price", " <= ", "max_price"),
				array("l.gross_rev", " >= ", "min_gross"),
				array("l.gross_rev", " <= ", "max_gross"),
				array("l.cash_flow", " >= ", "min_cflow"),
				array("l.cash_flow", " <= ", "max_cflow")
			);
	foreach ($fields as $field) {
		if (isset($_REQUEST[$field[2]]) && isset_or($_REQUEST[$field[2]]) != "") {
			if(intval($_REQUEST[$field[2]]) != 0) {
				if ($field[1] == " LIKE ") {
					$where_array[] = $field[0] . $field[1] . "'%" . trim(mysql_real_escape_string($_REQUEST[$field[2]])) . "%'";
				} else {
					$where_array[] = $field[0] . $field[1] . mysql_real_escape_string($_REQUEST[$field[2]]);
				}
				$hash_array[] = $field[2] . "=" . $_REQUEST[$field[2]];
			}
		}
	}
	
	if(isset_or($_REQUEST['states'])) {
		foreach ($_REQUEST['states'] as $state => $active) {
			$states[] = "'".$state."'";
			$hash_array[] = "states[".$state."]=1";
		}
		$where_array[] = "l.state IN (". implode(",",$states).")";
	}
	if(isset_or($_REQUEST['industry'])) {
		foreach ($_REQUEST['industry'] as $industry => $active) {
			$industries[] = "'".$industry."'";
			$hash_array[] = "industry[".$industry."]=1";
		}
		$where_array[] = "(l.category_id IN (". implode(",",$industries).") OR l.sub_category_id IN (". implode(",",$industries)."))";
	}
	
	if (count($hash_array) > 1) {
		$hash = "&" . implode("&",$hash_array);
	}
	$where = "";
	$where_array[] = "active = '1'";
	if (count($where_array) > 0) {
		$where = "WHERE " . implode(" AND ",$where_array);
	}
	
	//print_r($where_array);
	$listings = $db->query("SELECT * FROM listings l
							{$where} {$order}
							");
	$total_listings = $db->numRows();
	$number_of_pages = ceil($total_listings / 10);
	$query = "SELECT * FROM listings l {$where} {$order} {$limit}";
	$listings = $db->query("SELECT l.*, p.filename photo FROM listings l
							LEFT JOIN photos p ON p.id = l.primary_photo
							{$where} {$order} {$limit}
							");
	$listing_count = $db->numRows();
	if($listing_count == 0) { $start = -1; $end = 0; } else { $end = $start + $listing_count; }
	$categories = $db->query("SELECT id, name FROM categories WHERE parent_id = '0'");
	
	// Put hash array into session for return to search results links
	$_SESSION['search'] = $hash;

	$title = "Business Listings";
	include("head.php"); ?>

	<div id="content">
		<div class="content-box">
			<div class="sidebar floatleft">
	        	<form action="listings" method="post">
				<h2>Filters</h2>
				<div class="lightform small filters autowidth">
					<div><a href="#" id="select-location" class="filter fullwidth"><span class="title">Select Location</span> <span style="float:right">&#9660;</span></a></div>
					<div id="location" class="dropdown" style="width: 540px">
						<div class="content">
							<h5><span>us states</span> <span>(<a href="#" class="select-all" checks="state-checks">select all</a>)</span></h5>
								<ul>
	                            	<? $i = 0; foreach($state_list as $abbrev => $state): $i++ ?>
	                                <li><input type="checkbox" class="state-checks" name="states[<?=$abbrev?>]"<?=(isset_or($_REQUEST['states'][$abbrev])?" checked":"")?> /><?=$state?></li>
	                            <? if($i == 13): $i = 0; ?>
	                            </ul><ul>
	                            <? endif; ?>
	                            	<? endforeach; ?>
	                            </ul>
						</div>
	
						<span style="float:right"><a href="#" class="button_black">Cancel</a><input type="submit" value="OK" class="button_green last-child" style="color:white"></span>
					</div>
					<div><a href="#" id="select-industry" class="filter fullwidth"><span class="title">Select Industry</span> <span style="float:right">&#9660;</span></a></div>
					<div id="industry" class="dropdown" style="width: 650px">
	                	
						<div class="content">
							<h5><span>Categories</span></h5>
	                        <ul>
	                        	<? $i = 0; while($category = $db->fetchNextObject($categories)): $i++ ?>
	                            	
	                            	<li><input type="checkbox" class="industry-checks" name="industry[<?=$category->id?>]"<?=(isset_or($_REQUEST['industry'][$category->id])?" checked":"")?> /><?=$category->name?></li>
	                                <? if($i == 10): $i = 0; ?>
	                                	</ul><ul>
	                                <? endif; ?>
	                        	<? endwhile; ?>
	                        </ul>
	                    </div>
	
						<span style="float:right"><a href="#" class="button_black">Cancel</a><input type="submit" value="OK" class="button_green last-child" style="color:white"></span>
	                    
					</div>
					<br /><br />
					<h5 style="margin-bottom: 4px;"><span>Price</span></h5>
					<span class="aligncenter" style="width:200px;display:inline-block;line-height:23px;">
						<input type="text" name="min_price" class="quick-clear floatleft" value="<?=isset_or($_REQUEST['min_price'])?>" placeholder="No Min" style="width: 40%"> to 
						<input type="text" name="max_price" class="quick-clear floatright" value="<?=isset_or($_REQUEST['max_price'])?>" placeholder="No Max" style="width: 40%">
					</span>
	                <br /><br />
	                <a href="#" class="inline-filter"><h5 style="margin-bottom: 6px;margin-top: 8px;"><span>Gross Income</span><span class="arrow hidden">&#9660;</span><span class="arrow">&#9658;</span></h5></a>
	                
					<div class="inline-dropdown hidden aligncenter" style="width:200px;line-height:23px;">
						<input type="text" name="min_gross" class="quick-clear floatleft" value="<?=isset_or($_REQUEST['min_gross'])?>" placeholder="No Min" style="width: 40%"> to 
						<input type="text" name="max_gross" class="quick-clear floatright" value="<?=isset_or($_REQUEST['max_gross'])?>" placeholder="No Max" style="width: 40%"></div>
					<br />
	                <a href="#" class="inline-filter"><h5 style="margin-bottom: 6px;margin-top: 8px;"><span>Cash Flow</span><span class="arrow hidden">&#9660;</span><span class="arrow">&#9658;</span></h5></a>
					<div class="inline-dropdown hidden aligncenter" style="width:200px;line-height:23px;">
						<input type="text" name="min_cflow" class="quick-clear floatleft" value="<?=isset_or($_REQUEST['min_cflow'])?>" placeholder="No Min" style="width: 40%"> to 
						<input type="text" name="max_cflow" class="quick-clear floatright" value="<?=isset_or($_REQUEST['max_cflow'])?>" placeholder="No Max" style="width: 40%"></div>
				</div>
				<span style="float:right;margin-top: 10px;"><input type="submit" value="Search!" class="blocklink last-child"></span>
	            
	            <br style="clear:both">
	            </form>
			</div><!-- sidebar -->
			
			<div id="listings">
				<div id="listings-header">Showing <?=$start+1?> - <?=$end?> of <b><?=$total_listings?></b> listing(s)</div>
				<div id="listings-thead"><div style="display: inline-block"><a href="#" class="sorter" style="float:none;margin-left:10px;">Sort By <span>&#9660;</span></a></div>
					<div class="dropdown custom" style="width:180px;margin-left:10px;padding:10px 5px;">
						<ul class="padded">    
							<li><a href="listings/<?=isset_or($hash)?>&order=date&dir=DESC" title="">Newest to Oldest</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=date&dir=ASC" title="">Oldest to Newest</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=asking_price&dir=ASC" title="">Asking Price: Low to High</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=asking_price&dir=DESC" title="">Asking Price: High to Low</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=cash_flow&dir=ASC" title="">Cash Flow: Low to High</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=cash_flow&dir=DESC" title="">Cash Flow: High to Low</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=state&dir=ASC" title="">State: A-Z</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=state&dir=DESC" title="">State: Z-A</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=county&dir=ASC" title="">County: A-Z</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=county&dir=DESC" title="">County: Z-A</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=city&dir=ASC" title="">City: A-Z</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=city&dir=DESC" title="">City: Z-A</a></li>
		                    <li><a href="listings/<?=isset_or($hash)?>&order=views&dir=DESC" title="">Most Viewed</a></li>
						</ul>
						
					</div>
					<a href="listings/<?=isset_or($hash)?>&order=location&dir=<?=((isset_or($_REQUEST['order']) == 'location' && isset_or($_REQUEST['dir'])=='asc')?"desc":"asc")?>" class="column">Location</a>
					<a href="listings/<?=isset_or($hash)?>&order=cash_flow&dir=<?=((isset_or($_REQUEST['order']) == 'cash_flow' && isset_or($_REQUEST['dir'])=='asc')?"desc":"asc")?>" class="column">Cash Flow</a>
					<a href="listings/<?=isset_or($hash)?>&order=asking_price&dir=<?=((isset_or($_REQUEST['order']) == 'asking_price' && isset_or($_REQUEST['dir'])=='asc')?"desc":"asc")?>" class="column">Price</a>
				</div>
				<ul class="list">
	            	<? $run = 0; 
	            		while($listing = $db->fetchNextObject($listings)): ?>
	            	<? if((isset_or($last_type) == 'spotlight' && $listing->type != 'spotlight') || (isset_or($last_type) == '' && $listing->type != 'spotlight' && $page == 1 && $run != 0)): ?>
	            	<li class="last"><span>&#9652;</span> SPOTLIGHT <span>&#9652;</span></li>
	            	<? endif; ?>
					<li class="<?=$listing->type?><?=(isset_or($last_type) == 'spotlight')?' smush':' first'?>">
						<div class="snapshot">
						<? if($listing->type != 'standard' && $listing->photo != ''): ?>
							<img src="user_content/<?=$listing->photo?>">
						<? else: ?>
							<img src="images/forsale5.png">
						<? endif; ?>
						</div>
						<div class="<?=$listing->type?>">
						<h3><a href="listing/<?=$listing->id?>"><?=$listing->headline?></a></h3>
						<span><?=substr($listing->city, 0, 10)?><?=strlen($listing->city) > 11?'...':'';?>, <?=$listing->state?></span><span>$<?=number_format($listing->cash_flow)?></span><span>$<?=number_format($listing->asking_price)?></span>
						<p><?=$listing->description?></p>
						</div>
					</li>
	                <? $last_type = $listing->type; $run++; endwhile; ?>
				</ul>
				<div class="listing">
				</div>
				<div>&nbsp;</div>
				<div id="listings-footer">Showing <?=$start+1?> - <?=$end?> of <b><?=$total_listings?></b> listing(s)
					<span class="floatright">
						Page:
						<? for ($i = 1; $i <= $number_of_pages; $i++): ?>
	                    <a href="/listings/<?=isset_or($hash)?>&page=<?=$i?>" class="blocklink<?=((isset_or($page)==$i)?" active":"")?><?=(($i==$number_of_pages)?" last-child":"")?>"><?=$i?></a>
	                   	<? endfor; ?>
	                </span>
				</div>
				<br style="clear:both">
			</div><!-- listings -->
			<br style="clear:both">
		</div><!-- content-box -->
	</div><!-- content -->
	<script>
		$(".state-checks").change(function() {
			var $this = $(this);
			if($(".state-checks").filter(':checked').length > 1) {
				$("#select-location").children(".title").html("Multiple Selected");
			} else if($(".state-checks").filter(':checked').length == 0) {
				$("#select-location").children(".title").html("Select Location");
			} else {
				$("#select-location").children(".title").html($(".state-checks").filter(':checked').parent().text());
			}
		});
		$(".industry-checks").change(function() {
			var $this = $(this);
			if($(".industry-checks").filter(':checked').length > 1) {
				$("#select-industry").children(".title").html("Multiple Selected");
			} else if($(".industry-checks").filter(':checked').length == 0) {
				$("#select-industry").children(".title").html("Select Industry");
			} else {
				var $title = $(".industry-checks").filter(':checked').parent().text()
				if($title.length > 23) {
					var $title = $title.substring(0,23) + "...";
				}
				$("#select-industry").children(".title").html($title);
			}
		});
	</script>
	<? include("foot.php"); ?>