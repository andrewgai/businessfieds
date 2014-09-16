<?
require("../includes/config.inc.php");
if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}
require("../includes/functions.php");
require("../includes/session.php");

protectPage();

$thread_id = mysql_real_escape_string($_REQUEST['id']);

if($thread_id) {
	$first_message = $db->queryUniqueObject("SELECT m.*, l.headline, l.user_id, u.first_name, u.last_name FROM messages m 
										LEFT JOIN listings l ON m.listing_id = l.id
										LEFT JOIN users u ON u.id = m.from_id
										WHERE m.id = '{$thread_id}' AND (from_id = '{$u_id}' OR to_id = '{$u_id}')");
	$messages = $db->query("SELECT * FROM messages m LEFT JOIN users u ON m.from_id = u.id WHERE parent_id = '{$thread_id}' AND (from_id = '{$u_id}' OR to_id = '{$u_id}') ORDER BY timestamp ASC");
	
	$message_count = $db->numRows($messages);
	// mark the messages to me read when I load the page
	$db->query("UPDATE messages SET `read` = '1' WHERE to_id = '{$u_id}' AND (parent_id = '{$thread_id}' OR id = '{$thread_id}')");
}



?>

<h2>Inquiry: <?=$first_message->headline?> <a href="#" id="add_reply" class="blocklink floatright last-child"><img src="images/comment_add.png">Reply</a></h2>
<table class="datatable nostripe">
	<thead>
		<th style="width:170px;">Author</th>
		<th>Message</th>
	</thead>
	<tbody>
		
		<tr class="<?=($message_count > 2)?'collapse':''?><?=($first_message->from_id == $u_id)? ' me':' green'?>">
			<td style="width:120px;vertical-align:top">
				<strong><?=($first_message->from_id == $u_id)? 'Me': $first_message->first_name?>:</strong><br>
				<span><?=formatDatetime($first_message->timestamp)?></span>
			</td>
			<td><?=nl2br($first_message->message)?></td>
		</tr>
		<? $i = 0; while($message = $db->fetchNextObject($messages)): $i++; ?>
		<? if($i == ($message_count - 2)):?>
		<tr>
			<td colspan="2" style="background:#F5F5F6"><a href="#" class="expander"><h5 style="margin:0px;padding:5px 0px;border-top:1px solid #E3E3E5;border-bottom:1px solid #E3E3E5" class="aligncenter"><span style="background:#F5F5F6;padding-left:5px;font-weight:400"><?=$message_count-2?> Older Messages</span></h5></a></td>
		</tr>
		<? endif; ?>
		<tr class="<?=($i < ($message_count - 2))?'collapse':''?><?=($message->from_id == $u_id)? ' me':' green'?>">
			<td style="vertical-align:top"><strong><?=($message->from_id == $u_id)? 'Me': $message->first_name?>:</strong><br>
				<span><?=formatDatetime($message->timestamp)?></span></td>
			<td><?=nl2br($message->message)?></td>
			
		</tr>
		<? endwhile; ?>
		<tr id="reply" class="hidden silver">
			<td><strong>Me:</strong><br>
				<span id="timestamp"><?=formatDateTime('now')?></span></td>
			<td id="replyBlock">
				<div class="lightform small" style="width:100%">
					<textarea id="replyContent" style="height:100px;width:100%;resize:none;"></textarea>
				</div>
				<input type="hidden" id="listing_id" value="<?=$first_message->listing_id?>">
				<input type="hidden" id="thread_id" value="<?=$first_message->id?>">
				<input type="hidden" id="to_id" value="<?=($first_message->from_id == $u_id)? $first_message->to_id: $first_message->from_id?>">
				<input type="submit" name="submit" id="submitReply" value="Send" class="blocklink last-child floatright">
				
			</td>
		</tr>
		
		
		
		
	</tbody>
</table>

<script>
	$('.expander').click(function() {
		$(this).parents('tr').hide().siblings('.collapse').show();
		var newHeight = $(this).closest('.panels')[0].scrollHeight;
		$(this).closest('.panels').animate({height: newHeight});
		return false;
	});
	$("#submitReply").click(function() {
		var dataString = 'id=' + $('#thread_id').val() + '&listing_id=' + $('#listing_id').val() + '&to_id=' + $('#to_id').val() + '&replyContent=' + $('#replyContent').val();
		alert(dataString);
		$.ajax({
			type: "POST",
			url: "ajax/inquiryReply.php",
			data: dataString,
			success: function() {
				//$('#replyBlock').fadeOut(function() {
					//var newHeight = $('#replyBlock').closest('.panels')[0].scrollHeight;
					//alert(newHeight);
					$('#replyBlock').text($('#replyContent').val()).html(function(index, old) { return old.replace(/\n/g, '<br />')});
					var newHeight = $('#replyBlock').closest('.panel').height() + 20;
					//alert(newHeight);
					$('#replyBlock').closest('.panels').animate({height: newHeight });
				//});
			}
		});
		$('#add_reply').fadeOut();
		return false;
	});
</script>