<?	
	/*$categories = $db->query("SELECT c2.id, c2.parent_id, c.name parent_name, c2.name FROM categories c 
LEFT JOIN categories c2 ON c.id = c2.parent_id
WHERE c.parent_id = 0");	*/
	$categories = $db->query("SELECT id, name FROM categories WHERE parent_id = '0'");
	$main_categories = $db->query("SELECT * FROM categories");
	$sub_categories = $db->query("SELECT * FROM categories WHERE parent_id != 0");
	
	$title = "Welcome!";
	include("head.php"); ?>

	<div id="content" style="margin-top: 90px">
		<div class="content-box">
			
			<div class="searchbar">
				<a href="/tools" class="blocklink calculators" style="position:absolute;top:-60px;left:-1px;font-weight:200">
					<img src="images/calculator.png"> Tools &amp; Calculators
				</a>
				<img src="images/fybt.png" style="position:absolute;top:-60px;right:10px;">
				<img src="images/fybtarrow.png" style="position:absolute;top:-35px;right:-30px;">
				<form action="listings" method="post">
				<div class="first-child sector filter-hover" id="select-location"><img src="images/location.png"><span class="title">Location</span> <img src="images/expand-down.png" style="margin-left:8px">
					
					<div id="location" class="dropdown" style="width:540px">
	                    <div class="content">
	                        <h5><span>us states</span> <span>(<a href="#" class="select-all" checks="state-checks">select all</a>)</span></h5>
	                            <ul>
	                                <? $i = 0; foreach($state_list as $abbrev => $state): $i++ ?>
	                                <li><input type="checkbox" class="state-checks" data="<?=$abbrev?>" name="states[<?=$abbrev?>]"<?=(isset_or($_REQUEST['states'][$abbrev])?" checked":"")?> /><?=$state?></li>
	                            <? if($i == 13): $i = 0; ?>
	                            </ul><ul>
	                            <? endif; ?>
	                                <? endforeach; ?>
	                            </ul>
	                    </div>
	
	                    <div class="bottombar floatright"><a href="#" class="button_green floatright last-child">OK</a></div>
	             </div>
	                
				</div><!-- sector first-child -->
				<div id="select-industry" class="sector filter-hover"><img src="images/industry.png"><span class="title">Industry</span> <img src="images/expand-down.png" style="margin-left:8px">
					<div id="location" class="dropdown" style="width:610px;">  
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
	
	                    <span style="float:right"><a href="#" class="button_green small offset cancel last-child">OK</a></span>
	                    
	                </div>
				</div><!-- sector industry -->
				<div class="last-child sector">
					<img src="images/price.png">Asking Price
					<div class="darkform autowidth small" style="width:390px;float:none;border:none;margin:0;padding:0px 10px;">
						<input type="text" name="min_price" placeholder="No Min">
						to
						<input type="text" name="max_price" placeholder="No Max">
						
						<a href="#" class="submitBtn button_green lightborders floatright" style="padding-top:5px;padding-bottom:5px">Search <img src="images/glass.png" style="margin-right:0;"></a>
					</div>
				</div><!-- sector last-child -->
				</form>
			</div>
			
			<div id="home_content" class=" hidden" style="padding-bottom:10px;padding-top:20px;background:url('images/hbg.png') no-repeat;background-position:0 50px">
				<? if(false): // hiding per nate ?>
				<img id="businessmap" src="images/businessmap.png" style="margin-left: 10px;margin-top: 50px;">
				
				<ul style="float:right;margin-top:20px;margin-right:20px;">
					<li><a href="#" class="state-link" id="mo">Montana</a></li>
					<li><a href="#" class="state-link" id="ne">Nebraska</a></li>
					<li><a href="#" class="state-link" id="nv">Nevada</a></li>
					<li><a href="#" class="state-link" id="nh">New Hampshire</a></li>
					<li><a href="#" class="state-link" id="nj">New Jersey</a></li>
					<li><a href="#" class="state-link" id="nm">New Mexico</a></li>
					<li><a href="#" class="state-link" id="ny">New York</a></li>
					<li><a href="#" class="state-link" id="nc">North Carolina</a></li>
					<li><a href="#" class="state-link" id="nd">North Dakota</a></li>
					<li><a href="#" class="state-link" id="oh">Ohio</a></li>
					<li><a href="#" class="state-link" id="ok">Oklahoma</a></li>
					<li><a href="#" class="state-link" id="or">Oregon</a></li>
					<li><a href="#" class="state-link" id="pa">Pennsylvania</a></li>
					<li><a href="#" class="state-link" id="ri">Rhode Island</a></li>
					<li><a href="#" class="state-link" id="sc">South Carolina</a></li>
					<li><a href="#" class="state-link" id="sd">South Dakota</a></li>
					<li><a href="#" class="state-link" id="tn">Tennessee</a></li>
					<li><a href="#" class="state-link" id="tx">Texas</a></li>
					<li><a href="#" class="state-link" id="ut">Utah</a></li>
					<li><a href="#" class="state-link" id="vt">Vermont</a></li>
					<li><a href="#" class="state-link" id="va">Virginia</a></li>
					<li><a href="#" class="state-link" id="wa">Washington</a></li>
					<li><a href="#" class="state-link" id="wv">West Virginia</a></li>
					<li><a href="#" class="state-link" id="wi">Wisconsin</a></li>
					<li><a href="#" class="state-link" id="wy">Wyoming</a></li>
				</ul>
				<ul style="float:right;margin-top:20px;margin-right:20px;">
					<li><a href="#" class="state-link" id="al">Alabama</a></li>
					<li><a href="#">Alaska</a></li>
					<li><a href="#" class="state-link" id="az">Arizona</a></li>
					<li><a href="#" class="state-link" id="ar">Arkansas</a></li>
					<li><a href="#" class="state-link" id="ca">California</a></li>
					<li><a href="#" class="state-link" id="co">Colorado</a></li>
					<li><a href="#" class="state-link" id="ct">Connecticut</a></li>
					<li><a href="#" class="state-link" id="de">Delaware</a></li>
					<li><a href="#">Washington DC</a></li>
					<li><a href="#" class="state-link" id="fl">Florida</a></li>
					<li><a href="#" class="state-link" id="ga">Georgia</a></li>
					<li><a href="#">Hawaii</a></li>
					<li><a href="#" class="state-link" id="id">Idaho</a></li>
					<li><a href="#" class="state-link" id="il">Illinois</a></li>
					<li><a href="#" class="state-link" id="in">India</a></li>
					<li><a href="#" class="state-link" id="ia">Iowa</a></li>
					<li><a href="#" class="state-link" id="ks">Kansas</a></li>
					<li><a href="#" class="state-link" id="ky">Kentucky</a></li>
					<li><a href="#" class="state-link" id="la">Louisiana</a></li>
					<li><a href="#" class="state-link" id="me">Maine</a></li>
					<li><a href="#" class="state-link" id="md">Maryland</a></li>
					<li><a href="#" class="state-link" id="ma">Massachusetts</a></li>
					<li><a href="#" class="state-link" id="mi">Michigan</a></li>
					<li><a href="#" class="state-link" id="mn">Minnesota</a></li>
					<li><a href="#" class="state-link" id="ms">Mississippi</a></li>
					<li><a href="#" class="state-link" id="mo">Missouri</a></li>
				</ul>
				<? endif; ?>
				
				<div class="aligncenter" style="width:50%;float:left;display:block;padding:25px">
					<h2 style="text-align:left;font-size:25px;">Proven Results</h2>
					
					<a href="/signup" class="button_blue huge last-child" style="float:none;margin-top:30px;"><img src="images/click.png" style="display:none;float:left;margin-left:-2px;margin-top:-2px;">List a Business For Sale <img src="images/rbars.png" style="float:right;display:none;"></a>
					
					<ul class="home_list" style="text-align:left;margin-left:80px;">
						<li><img src="images/home_icons/43.png"> Targeted listings<br />
							<span>Information on this can be found at</span>	
						</li>
						<li><img src="images/home_icons/191.png"> More qualified views<br />
							<span>More qualified viewers than any other listing site</span>
						</li>
						<li><img src="images/home_icons/273.png"> Ad Statistics<br />
							<span>Industry leading statistics for performance analysis</span>
						</li>
					</ul>
				</div>
				<div class="aligncenter" style="width:50%;float:right;display:block;padding:25px">
					<h2 style="text-align:right;font-size:25px;">Begin Your Journey</h2>
					
					<a href="/listings" class="button_green huge" style="float:none;margin-top:30px;">Find a Business to Buy</a>
					<ul class="home_list">
						<li><img src="images/home_icons/205.png"> Favorites list<br />
							<span>Keep track of listings that interest you</span>
						</li>
						<li><img src="images/home_icons/258.png"> More business photos<br />
							<span>Photos on nearly every listing</span>
						</li>
						<li><img src="images/home_icons/146.png"> Helpful tools for buyers<br />
							<span>Financing calculators and resources for buyers</span>
						</li>
						<li><img src="images/home_icons/99.png"> Search by locations<br />
							<span>Find your dream business in your dream location</span>
						</li>
					</ul>
					
				</div>
				<hr class="clear" style="width:940px;height:1px;display:block;padding-top:4px;padding-bottom:4px;border:0;border-bottom:1px solid #CCC;border-top:1px solid #CCC">
	        </div>
	        
	        <br class="clear">
		</div>
    </div>
    <script>
		$(document).ready(function() {
			$('#home_content').fadeIn(500);
			$(".state-checks").change(function() {
				var $this = $(this);
				if($(".state-checks").filter(':checked').length > 1) {
					$("#select-location").children(".title").html("Multi-Location");
				} else if($(".state-checks").filter(':checked').length == 0) {
					$("#select-location").children(".title").html("Location");
				} else {
					//$("#select-location").children(".title").html($(".state-checks").filter(':checked').parent().text());
					var $title = $(".state-checks").filter(':checked').parent().text()
					if($title.length > 10) {
						var $title = $title.substring(0,10) + "...";
					}
					$("#select-location").children(".title").html($title);
				}
			});
			$(".industry-checks").change(function() {
				var $this = $(this);
				if($(".industry-checks").filter(':checked').length > 1) {
					$("#select-industry").children(".title").html("Multi-Industry");
				} else if($(".industry-checks").filter(':checked').length == 0) {
					$("#select-industry").children(".title").html("Industry");
				} else {
					var $title = $(".industry-checks").filter(':checked').parent().text()
					if($title.length > 11) {
						var $title = $title.substring(0,11) + "...";
					}
					$("#select-industry").children(".title").html($title);
				}
			});
			$(".state-link").hover(
				function(event) {
					//$('#businessmap').fadeOut(function() {
					
					$("#businessmap").attr('src', "images/map_overlay/" + $(event.target).attr('id') + ".png").fadeIn('fast');
					//$('#businessmap-holder').stop().fadeOut('fast');
					//}).fadeIn();
				},
				function() {
					$("#businessmap").attr('src', "images/businessmap.png");
					//$('#businessmap-holder').fadeIn('fast');
					//$("#businessmap").attr('src', "images/map_overlay/holder.png").fadeOut('fast');
					//$('#map-container').css('background','url(images/businessmap.png) no-repeat');
				});
				
			$("#industries").change(function() {
			
				
				var options = [];
				<? while($category = $db->fetchNextObject($sub_categories)): ?>
				<? if(isset_or($cpid) != $category->parent_id): $cpid = $category->parent_id; ?>
					options[<?=$category->parent_id?>] += '<option value="">Select a Segment</option>';
				<? endif; ?>
					options[<?=$category->parent_id?>] += '<option value="<?=$category->id?>"><?=$category->name?></option>';
				<? endwhile; ?>
				$("#options").html(options[$("#industries").val()]);
				$("#options").removeAttr("disabled");
			});
		});
	</script>
    <? include("foot.php"); ?>