<?
require("../includes/config.inc.php");
if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}
require("../includes/functions.php");
require("../includes/session.php");


require_once("../classes/class.Template.php");
$emailTemplate = new EmailTemplate(); // Create an instance for new user from contact form

$thread_id = mysql_real_escape_string($_REQUEST['id']);
$listing_id = mysql_real_escape_string($_REQUEST['listing_id']);
$reply_content = mysql_real_escape_string($_REQUEST['replyContent']);
$to_id = mysql_real_escape_string($_REQUEST['to_id']);

$seller_id = $db->queryUniqueValue("SELECT to_id FROM messages WHERE id = '{$thread_id}'");
$listing_headline = $db->queryUniqueValue("SELECT headline FROM listings WHERE id = '{$listing_id}'");
$to = $db->queryUniqueObject("SELECT first_name, last_name, email FROM users WHERE id= '{$to_id}'");

if($thread_id) {
	$result = $db->query("INSERT INTO messages (parent_id, from_id, to_id, listing_id, message, timestamp) VALUES ('{$thread_id}','{$u_id}','{$to_id}','{$listing_id}','{$reply_content}', NOW())");
	// send emails!
	$emailTemplate->SetParameter("user_name", $to->first_name . ' '. $to->last_name);

	$emailTemplate->SetParameter("listing_headline", $listing_headline);

	if($to_id == $seller_id) {
		$emailTemplate->SetParameter("inquiry_url", '<a href="http://businessfieds.com/myListing/'.$listing_id.'&inquiry_id='. $thread_id .'#inquiry">http://www.businessfieds.com/myListing/'.$listing_id.'&inquiry_id='. $thread_id .'#inquiry</a>');
	} else {
		$emailTemplate->SetParameter("inquiry_url", '<a href="http://businessfieds.com/myAccount&inquiry_id='. $thread_id .'#inquiry">http://www.businessfieds.com/myAccount&inquiry_id='. $thread_id .'#inquiry</a>');
	}
	$emailTemplate->SetTemplate($settings['inquiry_message_et']);
	$sendBuyerEmail = sendMail($to->email,$emailTemplate->Subject(),$emailTemplate->CreateBody()); // send other party an email
}
?>