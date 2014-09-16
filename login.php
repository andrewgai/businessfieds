<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html class="login">
	<head>
		<title>TPAC</title>
		<base href="<?=BASE_URL?>">
		<link rel="stylesheet" type="text/css" href="css/style.css" media = "all">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
        <style>
		body {
			padding-top: 150px;
			background-image: url('/images/bg.png'); 
			background-attachment: initial; 
			background-origin: initial; 
			background-clip: initial; 
			background-color: initial; 
			background-position: initial initial; 
			background-repeat: initial initial;
		}
		</style>
	</head>
	<body>
		<div id="login_box">
			<?=$message?>
			<form action="/" method="post">
				<div class="gridform">
					<label for="user_email">Email</label>
					<input type="text" name="user_email" id="user_email">
					
					<label for="user_password">Password</label>
					<input type="password" name="user_password" id="user_password">
				
				</div><!-- gridform -->
				<input type="submit" class="button_green offset wide marginbottom clear" name="login_submit" value="Submit">
			</form>
		</div><!-- login_box -->
	</body>
</html>