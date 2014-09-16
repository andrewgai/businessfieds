<?	

	if(isset_or($_SESSION['logged_in'])) {
		if(isset_or($_SESSION['referrer'])) {
			header("Location: ".$_SESSION['referrer']);
		} else {
			header("Location: /myAccount");
		}
	}
	
	$stylesheets = array("css/validationEngine.jquery.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js");
	
	$title = "Login";
	include("head.php"); ?>

	<div id="content">
		
    	<div class="content-box login" style="margin:auto;width:500px;margin-top:30px;padding:0;overflow:visible">
    		<? if(isset_or($_REQUEST['message'])): ?><h6 style="margin-left:30px;margin-top:20px;margin-bottom:-15px"><?=stripslashes($_REQUEST['message'])?></h6><? endif; ?>
    		<form action="<?=((isset_or($_SESSION['referrer']))?: '/login')?>" id="loginForm" method="post">
	    	<div class="lightform autowidth big" style="margin:30px;padding-bottom:60px;display:block;">
	 

		    		<input type="email" name="user_email" placeholder="E-mail" class="validate[required,custom[email]]">

		    		<input type="password" name="user_password" placeholder="Password" class="floatright validate[required]">
		    		<br style="clear:both">
		    		<input type="hidden" name="referrer" value="<?=$_SESSION['referrer']?>">
					<input type="submit" name="submit" value="Log In" class="blocklink last-child floatright" style="margin-top: 20px;margin-bottom:15px;margin-right:8px;padding: 10px 14px">
	    	</div>
	    	
	    	<div class="graybar bottom">
	    		<div class="sector first-child width66">
		    		Don't have an account?
		    		<a href="/register">Sign up now!</a>
	    		</div>
	    		<div class="sector last-child" style="width:33%">
	    			<a href="#">Forgot password?</a>
	    		</div>
	    	</div>
	    	
	    	</form>
	    	<br style="clear:both">
	    </div>
    	
    </div>
    <script>
		$(document).ready(function() {
			$("#loginForm").validationEngine();
		});
	</script>
    <? include("foot.php"); ?>