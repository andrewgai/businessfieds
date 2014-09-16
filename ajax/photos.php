<?
require("../includes/config.inc.php");
require("../includes/session.php");
require("../includes/functions.php");
// Create database class instance and connect to db
require_once("../classes/class.Database.php");
$db = new DB($db_name,$db_host,$db_user,$db_password);

if(isset($_REQUEST['delete']) && isset($_REQUEST['photo_id'])) {
	$photo_id = mysql_real_escape_string($_REQUEST['photo_id']);
	$owner_user_id = $db->queryUniqueValue("SELECT l.user_id FROM photos p LEFT JOIN listings l ON p.listing_id = l.id WHERE p.id = '{$photo_id}'");
	//$owner_user_id = $db->queryUniqueValue("SELECT user_id FROM listings WHERE id = '{$listing_id}'");
	if($owner_user_id == $u_id) {
		// make sure we own the photo we are trying to delete
		$db->query("DELETE FROM photos WHERE id = '{$photo_id}'");
	}
	
}
if(isset($_REQUEST['primary']) && isset($_REQUEST['photo_id'])) {
	$photo_id = mysql_real_escape_string($_REQUEST['photo_id']);
	$listing = $db->queryUniqueObject("SELECT l.user_id, l.id FROM photos p LEFT JOIN listings l ON p.listing_id = l.id WHERE p.id = '{$photo_id}'");
	//$owner_user_id = $db->queryUniqueValue("SELECT user_id FROM listings WHERE id = '{$listing_id}'");
	if($listing->user_id == $u_id) {
		// make sure we own the photo we are trying to delete
		$db->query("UPDATE listings SET primary_photo = '{$photo_id}' WHERE id = '{$listing->id}'");
	}
	
}

if(isset($_REQUEST['listing_id'])) {
	$listing_id = mysql_real_escape_string($_REQUEST['listing_id']);
	$photos = $db->query("SELECT * FROM photos WHERE listing_id = '{$listing_id}'");
	$primary_photo = $db->queryUniqueValue("SELECT primary_photo FROM listings WHERE id = '{$listing_id}'");

}
?>
<? if($db->numRows($photo) == 0): ?>
<div style="position:absolute;top: 80px;width:200px;text-align:center;left:50%;margin-left:-100px;color: #b2bbc7">
						There are no photos currently.
					</div>
<? endif; ?>
<ul>
	
<? while($photo = $db->fetchNextObject($photos)):  ?>



		<li>
			<div class="clip">
				<img src="/user_content/<?=$photo->filename?>" style="display:inline;width:100%;opacity:1;" class="listing-pic" data="<?=$photo->id?>">
			</div>
			<div class="pic-ops">
				<span class="primaryLinks" style="margin-top:10px;margin-left:5px">
					<? if($primary_photo == $photo->id): ?>
					Primary 
					<? else: ?>
					<a href="#" class="makePrimary">Make Primary</a>
					
					<? endif; ?>
				</span>
				<span style="margin-top:-2px;" class="floatright">
					<a href="#" class="deletePhoto"><img src="/images/trashcan.png"></a>
				</span>
			</div>
		</li>


<? endwhile; ?>
</ul>