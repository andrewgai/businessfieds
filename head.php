<?
$total_alerts = count(isset_or($alerts));
?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?=(isset($title)) ? stripslashes($title) . " - BUSINESSfieds" : "BUSINESSfieds"?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<base href="<?=BASE_URL?>">
	<link rel="stylesheet" type="text/css" href="css/maxout.css?r=<?=rand(1,999999)?>" media = "all">
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media = "all">
	<? if (!empty($stylesheets)): foreach ($stylesheets as $stylesheet): ?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet?>" media = "all">
	<? endforeach; endif; ?>	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/sorttable.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<? if (!empty($scripts)): foreach ($scripts as $script): ?>
		<script type="text/javascript" src="<?=$script?>"></script>
	<? endforeach; endif; ?>
	<script>
		$(document).ready(function() {
			$(".filter, .sorter").click(function() {
				if(!$(this).parent().next(".dropdown").is(":visible")) {
					$(".dropdown").hide();
				}
				$(this).parent().next(".dropdown").slideToggle();
				return false;
			});
			
			$(".filter-hover").hover(function() {
				//$(this).children(".dropdown").stop().hide().slideDown();
				$(this).children(".dropdown").stop(function() { $(this).hide(); }).slideDown();
			}, function() {
				//$(this).children(".dropdown").stop().slideUp(function() { $(this).css("height", "auto"); });
				$(this).children(".dropdown").stop().hide(0,function() { $(this).css("height", "auto"); });
			});
			
			$(".inline-filter").click(function() {
				$(this).next(".inline-dropdown").slideToggle('fast');
				$(this).children().children(".arrow").toggle();
				return false;
			});
			
			$(".cancel").click(function() {
				$(this).closest(".dropdown").slideToggle();
				return false;
			})
			$(".quick-clear").click(function() {
				this.select();
			});
			$("#lwu").click(function() {
				window.location = 'signup';
				//$("#db").slideToggle();
			});
			$(".select-all").click(function() {
				if($("."+$(this).attr("checks")).filter(':checked').length == $("."+$(this).attr("checks")).length) {
					$("."+$(this).attr("checks")).prop("checked", false);
				} else {
					$("."+$(this).attr("checks")).prop("checked", true);
				}
				$("."+$(this).attr("checks")).change();
				return false;
			});
			$(".alert").slideDown("slow");
		});
		function fcCalculate() {
			
			if( isNumber($('#fc_saleprice').val()) && isNumber($('#fc_rate').val()) && isNumber($('#fc_years').val()) ) {

				var salePrice = $('#fc_saleprice').val();

				if($('#fc_downpayment').val() > 0) {
					var amtFinanced = salePrice - $('#fc_downpayment').val()*salePrice;
				} else {
					var amtFinanced = salePrice;
				}
				
				
				
				var rate = $('#fc_rate').val()/100;
				var adjRate = rate / 12;
				var months = $('#fc_years').val()*12;
				var Paymt = (amtFinanced*adjRate) / ( 1 - Math.pow((1 + adjRate),(-months)) );
				
				$('#fc_payment').val("$" + Paymt.toFixed(2));
			}
		}
		function isNumber(n) {
		  return !isNaN(parseFloat(n)) && isFinite(n);
		}
	</script>
</head>
<body <?=($u_all_access==1)?'class="admin"':''?>>
<div id="bg">
</div><!-- bg -->
	<? if($u_all_access == 1): ?>
	<div id="admin-bar">
		<ul>
			<li><a href="admin">Admin Home</a></li>
			<li><a href="">Statistics</a>
				<ul>
					<li><a href="writers">Listings</a></li>
					<li><a href="financials">Users</a></li>
					<li><a href="outreach">Filler Link</a></li>
					<li><a href="inventory">Fille Link 2</a></li>
				</ul>
			</li>
			<li><a href="/settings">Settings</a>
				<ul>
					<li><a href="/admin#permissions">Permissions</a></li>
					<li><a href="/admin#contact_settings">Contact Settings</a></li>
					<li><a href="/emailTemplates">Email Templates</a></li>
				</ul>
			</li>
		</ul>
	</div>
	<? endif; ?>
	<? if(!empty($alerts)) {
		foreach($alerts as $alert) {
			echo $alert;
		}
	}
	?>
<div id="container">
	<div id="header">
		<div class="doc_width">
			<div id="logo">
				<a href="http://businessfieds.com"><img src="images/logo.png" alt="BusinessFieds"></a>
			</div>
			<div class="account"><span style="font-weight:500"><?=((isset_or($_SESSION['logged_in']))?"Welcome, ":"Not Logged In")?></span> <?=((isset_or($_SESSION['logged_in']))?$u_name:"")?> 
			<? if(isset_or($_SESSION['logged_in'])): ?> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="/logout">Sign Out</a><? else: ?><a href="/login">Sign In</a><? endif; ?>
			</div>
			<div id="navigation">
				<ul>
					<li><a href="listings" <?=(route(0)=='listings')?'class="active"':''?>>Listings</a></li>
					<li><a href="about" <?=(route(0)=='about')?'class="active"':''?>>About Us</a></li>
					<li><a href="/blog">Blog</a></li>
					<li><a href="signup" <?=(route(0)=='signup')?'class="active"':''?>>List With Us</a></li>
					<li><a href="myAccount" <?=(route(0)=='myAccount')?'class="active"':''?>>My Account<? if($new_messages > 0): ?><span class="notification"><?=$new_messages?></span><? endif; ?></a></li>

				</ul>
			</div>
		</div>
	</div><!-- header -->
	<div id="divider"></div><!-- divider -->
	
    <div id="lwu">
    	<img src="/images/lwu.png" alt="List With Us!">
    </div>
    <div class="modal hidden" id="db">
    	<?=nl2br(isset_or($query))?>
    </div>
