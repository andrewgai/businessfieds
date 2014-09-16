<?
require("../includes/config.inc.php");
if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}
require("../includes/functions.php");
require("../includes/session.php");


$following_btn = '<a href="/myAccount#watchlist" class="watchingbtn"><img src="images/book.png" style="padding-right:3px;vertical-align:bottom;">Watching</a>';
$follow_btn = '<a href="#" class="watchbtn"><img src="images/book_add.png" style="padding-right:3px;vertical-align:bottom;">Add to Watchlist</a>';

if(isset($_GET['id'])) {
	$listing_id = $_GET['id'];
	
	if($_GET['action'] == 'check') {
		// check the following status
		if(checkWatch($listing_id)) {
			// following is a go! send in the unfollow button
			echo $following_btn;
		} else {
			// no sir, you are not in fact following this. would you like to?
			echo $follow_btn;
		}
	}
	
	if($_GET['action'] == 'watch') {
		if(watch($listing_id)) {
			// you have successfully followed the element!
			echo $following_btn;
		} else {
			// well something went wrong... we should probably put in some sort of notification but the user will never know
			echo $follow_btn;
		}
	}
	
	if($_GET['action'] == 'unwatch') {
		if(unWatch($listing_id)) {
			// we are sad to see you go :(
			echo $follow_btn;
		} else {
			// hmm problem unfollowing...sorry charlie! 
			echo $following_btn;	
		}
	}
}

?>