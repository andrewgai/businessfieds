<?
require("../includes/config.inc.php");
require("../includes/session.php");
require("../includes/functions.php");
// Create database class instance and connect to db
require_once("../classes/class.Database.php");
$db = new DB($db_name,$db_host,$db_user,$db_password);

require("../classes/class.simpleImage.php");

//prin_r($_FILES);

if(isset($_POST['submit']) && isset($_POST['listing_id'])) {
	$listing_id = mysql_real_escape_string($_POST['listing_id']);
	if (in_array(0, $_FILES["files"]["error"])) {
		foreach($_FILES['files']['name'] as $key => $value) {
			
			if($_FILES['files']['error'][$key] == 0) {
				$ext = pathinfo($_FILES["files"]["name"][$key], PATHINFO_EXTENSION); // get files extension
				// propose a filename
				$exists = TRUE;
				while($exists) {
					$filename = uniqid(rand(), true) .".". $ext;
					if(file_exists("../user_content/" . $filename)) {
						$exists = TRUE;
					} else {
						$exists = FALSE;
					}
				}
				
				move_uploaded_file($_FILES["files"]["tmp_name"][$key], "../user_content/" . $filename); // copy the uploaded file to proper directory
				if (file_exists("../user_content/" . $filename)) {
					$image = new SimpleImage();
   					$image->load('../user_content/' . $filename);
					if($image->getWidth() > $image->getHeight()) {
   						$image->resizeToWidth(80);
					} else {
						$image->resizeToHeight(80);
					}
   					$image->save('../user_content/thumbs/' . $filename);
					$db->query("INSERT INTO photos (listing_id, filename) VALUES ('{$listing_id}','{$filename}')");	// insert db association
				}
				//echo "<a href=\"/uploads/articles/{$_POST['article_id']}.{$ext}\" target=\"_blank\">View Word Document</a>"; // is never shown to the user, debug purpose
			}
		}
	}

} 


if(isset($_GET['id'])) {
	if (file_exists("../uploads/articles/" . $_GET['id'].".doc")) { // if file exists then show the link
		echo '<a href="#" id="deleteAttachment"><img src="images/delete.png" style="vertical-align:text-top"></a> <a href="/uploads/articles/'.$_GET['id'].'.doc" target="_blank">View Word Document</a>';
	} elseif (file_exists("../uploads/articles/" . $_GET['id'].".docx")) { // if file exists then show the link
		echo '<a href="#" id="deleteAttachment"><img src="images/delete.png" style="vertical-align:text-top"></a> <a href="/uploads/articles/'.$_GET['id'].'.docx" target="_blank">View Word Document</a>';
	}
}
if(isset($_GET['delete']) && isset($_GET['id'])) {
	if (file_exists("../uploads/articles/" . $_GET['id'].".doc")) { // if we already have an article file then delete it
		unlink("../uploads/articles/" . $_GET['id'].".doc");
	} elseif (file_exists("../uploads/articles/" . $_GET['id'].".docx")) { // if we already have an article file then delete it
		unlink("../uploads/articles/" . $_GET['id'].".docx");
	}
	mysql_query("UPDATE articles SET has_attachment='0' WHERE id='{$_GET['id']}'");
}
?>