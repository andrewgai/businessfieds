<?	

if(isset_or($_REQUEST['x'])) {
	$email = decode($_REQUEST['x'],"businessfieds");
	$existing_user = $db->queryUniqueValue("SELECT id FROM users WHERE email='{$email}' AND active=0");
	if($existing_user != null) {
		$query = "UPDATE users SET active=1 WHERE email = '{$email}'";
		$db->execute($query);
		
		$message = '<p style="margin:20px;font-size: 15px"><strong>Thank you!</strong>
					<br><br>Your account has been activated. <a href="/login">Sign In &raquo;</a></p>';
	} else {
		$message = '<p style="margin:20px;font-size: 15px">This link is either invalid or the account is already active.</p>';
	}
}
	
	$title = "Activation";
	include("head.php"); ?>

	<div id="content">
    	<div class="content-box login" style="margin:auto;width:500px;margin-top:30px;padding:0;overflow:visible">
    		<div class="graybar top"><div class="sector last-child" style="font-size:17px">Activation Complete</div></div>
    		<?=isset_or($message)?>
	    	<br style="clear:both">
	    </div>
    	
    </div>
    <script>
		$(document).ready(function() {
			
		});
	</script>
    <? include("foot.php"); ?>