<?	
// new email template
if(isset_or($_REQUEST['new_template'])) {
	$name = mysql_real_escape_string($_REQUEST['email_name']);
	$subject = mysql_real_escape_string($_REQUEST['email_subject']);
	$body = mysql_real_escape_string(stripslashes($_REQUEST['email_body']));
	
	$result = $db->query("INSERT INTO email_templates (name,subject,body) VALUES('{$name}','{$subject}','{$body}')");
	if($result) {
		$alerts[] = showAlert("New email template added successfully!", "alert_positive");
	} else {
		$alerts[] = showAlert("There was a problem adding your new email template. An alert has been dispatched to the system administrator.", "alert_negative");
		// sysadmin contact function pending
		
	}
}

// edit email template
if(isset_or($_REQUEST['edit_template'])) {
	$name = mysql_real_escape_string($_REQUEST['email_name']);
	$subject = mysql_real_escape_string($_REQUEST['email_subject']);
	$body = mysql_real_escape_string(stripslashes($_REQUEST['email_body']));
	
	$template_id = mysql_real_escape_string($_REQUEST['template_id']);
	$result = $db->query("UPDATE email_templates SET name = '{$name}', subject = '{$subject}', body = '{$body}' WHERE id = '{$template_id}'");
	if($result) {
		$alerts[] = showAlert("Email template edited successfully!", "alert_positive");
	} else {
		$alerts[] = showAlert("There was a problem editing your email template. An alert has been dispatched to the system administrator.", "alert_negative");
		// sysadmin contact function pending
		
	}
}

$template_id = mysql_real_escape_string(route(1));

if($template_id) {
	$editing = $db->queryUniqueObject("SELECT * FROM email_templates WHERE id = '{$template_id}'");
}

$emails = $db->query("SELECT * FROM email_templates");

	
$stylesheets = array("css/validationEngine.jquery.css");
$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js");

$title = "Email Templates";
include("head.php"); 

?>

	<div id="content">
		<div style="overflow:visible;position:relative;width:1000px;height:120px;">
    		<span class="header-explode" style="margin-right:20px;">Email Templates</span>
    	</div>
    	<div class="side-nav hidden">
			<ul class="first">
				<a href="#emails"><li>Email Templates</li></a>
				<a href="#new_email"><li>New Template</li></a>
			</ul>
			<ul>
				<a href="#edit_email"><li>Edit Template</li></a>
			</ul>

    	</div>
    	<div class="panels fullwidth">
	    	<div id="emails" class="panel">
	    		<span class="floatleft" style="margin: 10px 20px;"><a href="/admin"><img src="images/arrow_left.png" valign="absmiddle" style="margin-bottom:-3px;margin-right:5px">Back to Admin</a></span>
	    		<span class="floatright" style="margin: 10px 20px;"><a href="#new_email" tab="new_email" class="nuclear-link"><img src="images/add.png" class="icon-link">Add Template</a></span>
		    	<table class="nakedtable margin fullwidth">
					<thead>
						<tr>
							<th>Name</th>
							<th>Subject</th>
							<!--<th>Body</th>-->
							<th style="border-left:0"></th>
						</tr>
					</thead>
					<tbody>
						<? if(empty($emails)): ?>
						<tr>
							<td colspan="4" style="text-align:center">You do not have any templates.</td>
						</tr>
						<? else: 
						while($email = $db->fetchNextObject($emails)): ?>
						<tr>
							<td><a href="/emailTemplates/<?=$email->id?>#edit_email"><?=$email->name?></a></td>
							<td><?=$email->subject?></td>
							<!--<td><?=nl2br(truncate($email->body, 150))?></td>-->
							<td class="alignright"><a href="#" class="del" id="<?=$email->id?>"><img src="images/delete.png" height="15" alt="Delete"></a></td>
						</tr>
						<? endwhile; 
						endif; ?>
					</tbody>
				</table>
			</div>
			<div id="new_email" class="panel">
				<h2>New Email Template <span style="margin-top:3px; margin-right:10px;"><a href="#" style="margin-right:15px"><img src="images/page_save.png" class="icon-link">Save</a><a href="#emails" tab="emails" class="nuclear-link"><img src="images/delete.png" class="icon-link" height="15">Cancel</a></span></h2>
				<div class="gridform big">
				<form action="/emailTemplates" method="post">
				<table class="nakedtable" style="width: 670px">
					<tr>
						<td>Name</td>
						<td><input type="text" name="email_name" class="floatleft" style="width:250px"></td>
					</tr>
					<tr>
						<td>Subject</td>
						<td><input type="text" name="email_subject" style="width:500px"></td>
					</tr>
				
					<tr>
						<td style="vertical-align:top">Body</td>
						<td><textarea name="email_body" style="width: 500px;height: 400px"></textarea></td>
					</tr>
    			</table>
    				
    				
    				<input type="hidden" name="new_template" value="true">
    				<input type="submit" class="button_green clear offset floatright last-child" style="padding: 10px 25px;" value="Save">
    			</form>
    			</div>
    			<div class="green-block floatright">
    				<h3>Tag Guidelines</h3>
    				<table>
    					<? foreach($template_fields as $field_name => $field_desc): ?>
    					<tr>
    						<td class="paddingbottom"><strong><?=$field_name?></strong> <div style="padding-left:20px"><?=$field_desc?></div></td>
    					</tr>
    					<? endforeach; ?>
    				</table>
    			</div>
    			<br style="clear:both">
			</div>
			<div id="edit_email" class="panel">
				<h2>Editing: <?=isset_or($editing->name)?> <span style="margin-top:3px; margin-right:10px;"><a href="#" style="margin-right:15px"><img src="images/page_save.png" class="icon-link">Save</a><a href="#emails" tab="emails" class="nuclear-link"><img src="images/delete.png" class="icon-link" height="15">Cancel</a></span></h2>
				<div class="gridform big">
				<form action="/emailTemplates" method="post">
				<table class="nakedtable" style="width: 670px">
					<tr>
						<td>Name</td>
						<td><input type="text" name="email_name" class="floatleft" value="<?=isset_or($editing->name)?>" style="width:250px"></td>
					</tr>
					<tr>
						<td>Subject</td>
						<td><input type="text" name="email_subject" value="<?=isset_or($editing->subject)?>" style="width:500px"></td>
					</tr>
				
					<tr>
						<td style="vertical-align:top">Body</td>
						<td><textarea name="email_body" style="width: 500px;height: 400px"><?=isset_or($editing->body)?></textarea></td>
					</tr>
    			</table>
    			
    				
    				
    				<input type="hidden" name="edit_template" value="true">
    				<input type="hidden" name="template_id" value="<?=isset_or($editing->id)?>">
    				<input type="submit" class="button_green clear offset floatright last-child" style="padding: 10px 25px;" value="Save">
    			</form>
    			</div>
    			<div class="green-block floatright">
    				<h4>Tag Guidelines</h4>
    				<table>
    					<? foreach($template_fields as $field_name => $field_desc): ?>
    					<tr>
    						<td class="paddingbottom"><strong><?=$field_name?></strong> <div style="padding-left:20px"><?=$field_desc?></div></td>
    					</tr>
    					<? endforeach; ?>
    				</table>
    			</div>
				<br style="clear:both">
			</div>
			
			
    	</div>
    	<br style="clear:both">
    </div>
    <script>
		$(document).ready(function() {
			$('.del').click(function() {
				var listing_id = $(this).attr("id");
				$.get("ajax/emailTemplates.php?action=del&id=" + listing_id);
				$(this).closest("tr").hide();
				return false;
			});
			$(".nuclear-link").click(function() {
				var hash = '#' + $(this).attr("tab");
				$('.side-nav').find('a').filter('[href=' + hash + ']').click();
				window.location.hash = '';
				return false;
			});
			$('.side-nav li').each(function () {
    			$('<img src="images/rlink-arrow.png" class="hidden" style="position: absolute;left:10px;margin-top:1px" height="10" valign="top">').prependTo(this);
  				}).hover(function () {
  				//$(this).stop().addClass('selected',250);
			    $(this).stop().animate({
			      'padding-left' : 25
			    }, 250, function() { $('img', this).fadeIn(250); });
			    
			    
			  }, function () {
				if(!$(this).hasClass("selected")) {
			  	$('img', this).stop().fadeOut('fast');
				    $(this).stop().animate({
				      'padding-left' : 10
				    }, 250);
			    }
			    
			    
			  });
			  
			   $('.side-nav').each(function () {
				    var $links = $(this).find('a'),
				      panelIds = $links.map(function() { return this.hash; }).get().join(","),
				      $panels = $(panelIds),
				      $panelwrapper = $panels.filter(':first').parent(),
				      $lis = $(this).find('li'),
				      delay = 500,
				      heightOffset = 20; // we could add margin-top + margin-bottom + padding-top + padding-bottom of $panelwrapper
				      
				    $panels.hide();
				    
				 
				    
				    $links.click(function () {
				      var link = this, 
				        $link = $(this);
				        $li = $(this).children('li');
				      
				      // ignore if already visible
				      if ($link.is('.selected')) {
				        return false;
				      }
				      
				      $lis.not($li).children('img').hide(1,function() {

				      //$li.siblings().removeAttr("style");
				      $lis.not($li).removeAttr("style");
				      });

				      $links.removeClass('selected');
				      $link.addClass('selected');
				      
				      $lis.removeClass('selected');
				      $li.addClass('selected');
				      $li.children('img').show();
				      
				      //document.title = 'jQuery look: Tim Van Damme - ' + $link.text();
				              
				      if ($.support.opacity) {
				        $panels.stop().animate({opacity: 0 }, delay);
				      }
				      
				      $panelwrapper.stop().animate({
				        height: 0
				      }, delay, function() {
				        var height = $panels.hide().filter(link.hash).css('opacity', 1).show().height() + heightOffset;
				        
				        $panelwrapper.animate({
				          height: height
				        }, delay);
				        
				        
				      });
				      if($(this).attr('href').substring(0,1) != '#') {
				      	return true;
				      } else {
				      return false;
				      }
				    });
				    
				    //$links.filter(window.location.hash ? '[href=' + window.location.hash + ']' : ':first').click();
				    //$panels.filter(':first').show();
				    $links.filter(window.location.hash ? '[href=' + window.location.hash + ']' : ':first').parent("li").addClass('selected').children("img").show();
				    $panels.filter(window.location.hash ? window.location.hash : ':first').show();

				  });
		});
	</script>
    <? include("foot.php"); ?>