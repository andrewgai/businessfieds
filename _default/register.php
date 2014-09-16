<?	


	
	$stylesheets = array("css/validationEngine.jquery.css");
	$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js", "js/jquery.form.js");
	
	$title = "Register";
	include("head.php"); ?>

	<div id="content">
		<div class="content-box" style="margin-left:200px;width:600px;">
			<div class="graybar top"><div class="sector last-child" style="font-size:17px">Register for your free account</div></div>
    		<?=isset_or($message)?>
    		<? if(!isset_or($registered)): ?>
    		<form action="/ajax/forms.php" id="registrationForm" method="post">
	    	<div class="lightform big" style="margin-left:30px;margin-top:15px;width:260px;">
	 			
	    		<input type="text" name="fname" placeholder="First Name" class=" validate[required,custom[onlyLetterSp]]" style="width:120px;">
	    		<input type="text" name="lname" placeholder="Last Name" class=" validate[required,custom[onlyLetterSp]]" style="width: 120px;float:right">
	    		
	    		
	    		<input type="email" name="email" placeholder="Email Address" class=" validate[required,custom[email]]" style="width: 260px;margin-bottom:0px">
	    		<div id="emailCheck" style="width:260px;height:40px;line-height:40px;padding-left:10px;display:block;">
	    			<span class="hidden emailTaken" style="color:red;">Email taken. <a href="#">Forgot Password?</a></span>
	    		</div>
	    		<input type="password" name="password" placeholder="Password" class=" validate[required]" style="width:260px">
	    		
	    		
	    		<input type="password" name="confirm-password" placeholder="Confirm Password" class=" validate[required]" style="width:260px">
	    		
				<input type="hidden" name="doRegister" value="true">
				
	    	</div>
	    	<div class="infobox" style="width:270px;position:absolute;top: 60px;right:0px;font-size:14px;">
	    		<h6 style="font-size:15px">Start using these <strong>free</strong> features today!</h6>
	    		<br />
	    		<ul class="tickmark" style="margin-left:20px;">
                	<li>Save your searches</li>
                    <li>Watch listings</li>
                    <li>Contact Businesses for Sale</li>
                    <li>Random other things</li>
                    <li>Weekly email digest</li>
                </ul>
	    	</div>
	    	
	    	<input type="submit" name="submit" value="Register NOW!" class="blocklink clear floatright last-child" style="position:absolute;bottom:25px;right:30px;height:40px;padding-left: 20px;padding-right:20px">
	    	
	    	</form>
	    	<? endif; ?>
	    	<br style="clear:both">

    	</div>
    </div>
    <script>
		$(document).ready(function() {
			$('input[name=email]').blur(function() {
				$('#emailCheck').load('/ajax/forms.php?checkEmail='+$('input[name=email]').val());
			});
			$('#registrationForm').validationEngine();
		    $('#registrationForm').submit(function() {
		    	if($('#registrationForm').validationEngine('validate') && $('.emailTaken').is(':hidden')) {
		    		$('.content-box').append('<div class="screen"><div class="spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div><div class="bar9"></div><div class="bar10"></div><div class="bar11"></div><div class="bar12"></div></div></div>');
		    		var $form = $(this);
		    		var t = setTimeout(function() { $form.ajaxSubmit({target:'.content-box'}) }, 800);
		    		
		    	}
		    	return false;
		    }); 
			
		});

	</script>
    <? include("foot.php"); ?>