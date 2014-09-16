<?	

if(isset_or($_REQUEST['updateContactSettings'])) {
	foreach ($_REQUEST['setting'] as $key => $value) {
		$key = mysql_real_escape_string($key);
		$value = mysql_real_escape_string($value);
		$result[] = $db->query("UPDATE settings SET value = '{$value}' WHERE name = '{$key}'");
	}
	if(!in_array('0',$result)) {
		$alerts[] = showAlert("Settings updated successfully!", "alert_positive");
	} else {
		$alerts[] = showAlert("There was a problem updating settings", "alert_negative");
	}
}

$groups = $db->query("SELECT group_id,name FROM user_groups");
if(isset_or($_REQUEST['permSave'])) {
	foreach($_REQUEST['permissions'] as $group_id => $whatever) {
		//echo $group_id;
		// drop the groups shit
		$db->query("DELETE FROM user_groups_permissions WHERE group_id = '{$group_id}'");
		foreach($_REQUEST['permissions'][$group_id] as $permission_id => $active) {
			//echo $group_id.">".$permission_id ."\n";
			$result = $db->query("INSERT INTO user_groups_permissions (group_id, permission_id) VALUES ('{$group_id}','{$permission_id}')");
		}
		
	}
	if($result) {
		$alerts[] = showAlert("Permissions updated","alert_positive");
	}
}

$permission_files = $db->query("SELECT name FROM permissions");
while($filename = $db->fetchNextObject($permission_files)) {
	$perm_files[] = $filename->name;
}

$files['_default'] = scandir("./_default");
$allfiles = array();
$allfiles = array_merge($allfiles, $files['_default']);
$db->resetFetch($groups);
while($group = $db->fetchNextObject($groups)) {

	if(is_dir("./".$group->name)) {
		$files[$group->name] = scandir("./".$group->name);
		$allfiles = array_merge($allfiles, $files[$group->name]);
	}
	$group_queries[] = "CASE WHEN (SELECT name FROM user_groups ug
								LEFT JOIN user_groups_permissions ugp ON ugp.group_id = ug.group_id
								WHERE ugp.permission_id = p.id AND ug.group_id = '{$group->group_id}' LIMIT 1) IS NOT NULL THEN 1 ELSE 0 END {$group->name}_access";
}

array_walk($allfiles, "subit");

function subit(&$var) {
	$var = substr($var, 0,strlen($var)-4);
}

$newfiles = array_filter(array_diff($allfiles, $perm_files));
foreach($newfiles as $file) {	
	$db->query("INSERT INTO permissions (name) VALUES('{$file}')");
}
$orphans = array_filter(array_diff($perm_files, $allfiles));
foreach($orphans as $orphan) {
	$db->query("DELETE FROM permissions WHERE name = '{$orphan}'");
}


$group_query = implode(",", $group_queries);

$permission_table = $db->query("SELECT *,
								{$group_query}
								FROM permissions p
								ORDER BY name ASC
								");

$groups = $db->query("SELECT ug.*, count(u.id) user_count FROM `user_groups` ug
LEFT JOIN users u ON u.group_id = ug.group_id
GROUP BY ug.group_id");

$users = $db->query("SELECT u.*, count(l.id) listings FROM users u
LEFT JOIN listings l ON u.id = l.user_id
GROUP BY u.id");
while($user = $db->fetchNextObject($users)) {
	$group_users[$user->group_id][] = objectToArray($user);
}

$emailTemplates = $db->query("SELECT * FROM email_templates");
$settings = reloadSettings();
	
$stylesheets = array("css/validationEngine.jquery.css");
$scripts = array("js/jquery.validationEngine.js", "js/jquery.validationEngine-en.js");

$title = "Administration";
include("head.php"); 

?>

	<div id="content">
		<div style="overflow:visible;position:relative;width:1000px;height:120px;">
    		<span class="header-explode" style="margin-right:20px;">Administration</span>
    	</div>
    	<div class="side-nav floatleft">
			<ul class="first">
				<li><a href="#overview">Admin Overview</a></li>
			</ul>
			<ul>
				<li><a href="#groups">User Groups</a></li>
				<li><a href="#permissions">Permissions</a></li>
				<li><a href="#users">Manage Users</a></li>
			</ul>
			<ul>
				<li><a href="#contact_settings">Contact Settings</a></li>
				<li><a href="/emailTemplates">Email Templates</a></li>
			</ul>
			<ul class="hidden">
				<? while($group = $db->fetchNextObject($groups)) : ?>
					<li><a href="#group_<?=$group->name?>"><?=$group->name?></a></li>
				<? endwhile; ?>
			</ul>
    	</div>
    	<div class="panels">
	    	<div id="overview" class="panel">
	    		<h2>Admin Overview</h2>
	    	</div>
	    	
	    	<!-- Group panels -->
	    	<? $db->resetFetch($groups); while($group = $db->fetchNextObject($groups)): ?>
	    	<div id="group_<?=$group->name?>" class="panel">
	    		<h2>Group: <?=$group->name?></h2>
	    		
	    		<table class="nakedtable margin width715">
	    			<thead>
	    				<th>Active</th>
	    				<th>Name</th>
	    				<th>Listings</th>
	    				<th>Registered</th>
	    			</thead>
	    			
	    			<tbody>
	    				<? foreach($group_users[$group->group_id] as $user):  
	    				 ?>
	    				
	    				<tr>
	    					<td class="aligncenter"></td>
	    					<td class="aligncenter"><?=$user['first_name']?> <?=$user['last_name']?></td>
	    					<td class="aligncenter"><?=$user['listings']?></td>
	    					<td class="aligncenter"><?=$user['created']?></td>
	    				</tr>
	    				<? endforeach; ?>
	    			</tbody>
	    		</table>
	    	</div>
	    	<? endwhile; ?>
	    	<!-- end group panels -->
	    	
	    	
	    	<div id="users" class="panel">
	    		<h2>Manage Users</h2>
	    		
	    	</div>
	    	<div id="groups" class="panel">
	    		<h2>User Groups <span><a href="#" class="addGroup"><img src="images/add.png" class="icon-link">Add Group</a> <a href="#" class="submitBtn" data="permissionForm"><img src="images/page_save.png" class="icon-link">Save</a></span></h2>
	    		<table class="nakedtable margin width715">
	    			<thead>
	    				<th>Name</th>
	    				<th>All Access</th>
	    				<th>Users</th>
	    			</thead>
	    			<tbody>
	    				<? $db->resetFetch($groups); while($group = $db->fetchNextObject($groups)): ?>
	    				<tr>
	    					<td><a href="#" class="nuclear-link" tab="group_<?=$group->name?>"><?=$group->name?></a></td>
	    					<td class="aligncenter"><input type="checkbox" name="all_access" <?=($group->allow_all)?'checked="checked"':''?>></td>
	    					<td class="aligncenter"><?=$group->user_count?></td>
	    				</tr>
	    				<? endwhile; ?>
	    			</tbody>
	    		</table>
	    		
	    		
	    		<br class="clear">
	    	</div>
	    	
			<div id="permissions" class="panel">
	    		<h2>Permissions <span style="margin-top:3px; margin-right:10px;"><a href="#" class="submitBtn" data="permissionForm"><img src="images/page_save.png" class="icon-link">Save</a></span></h2>
	    		<form action="admin#permissions" id="permissionForm" method="post">
	    		<table class="nakedtable margin width715">
    				<thead>
    					<tr>
    						<th></th>
    						<? $db->resetFetch($groups); while($group = $db->fetchNextObject($groups)): ?>
    						<th><?=$group->name?></th>
    						<? endwhile; ?>
    					</tr>
    				</thead>
    				<tbody> 
    					<? while($permission = $db->fetchNextObject($permission_table)): ?>
    					<tr>
    						<td><?=$permission->name?></a></td>
    						<? $db->resetFetch($groups); while($group = $db->fetchNextObject($groups)): ?>
    						<td class="aligncenter"><input type="checkbox" name="permissions[<?=$group->group_id?>][<?=$permission->id?>]" <?=($permission->{$group->name.'_access'})?'checked':''?>></td>
							<? endwhile; ?>
    					</tr>
    					<? endwhile; ?>
    				</tbody>
    			</table>
    			<input type="hidden" name="permSave" value="true">
    			</form>
	    	</div>
			<div id="contact_settings" class="panel">
	    		<h2>Contact Settings</h2>
	    		<div class="gridform big" style="width:675px;margin-top:20px;margin-left:20px;">
	    			<form action="/admin#contact_settings" method="post">
	    			<h3 style="margin-left:0">New User</h3>
	    			<table class="nakedtable marginbottom">
	    				<tr>
	    					<td>Signup Form</td>
		    				<td class="alignright">
			    				<select name="setting[new_user_form_et]" class="sidebyside" style="margin:5px">
			    					<option value="0">-- Select Template --</option>
			    				<? while($emailTemplate = $db->fetchNextObject($emailTemplates)): ?>
									<option value="<?=$emailTemplate->id?>" <?=(($settings['new_user_form_et'] == $emailTemplate->id)? 'selected':'')?>><?=$emailTemplate->name?></option>
								<? endwhile; ?>
			    				</select>
		    				</td>
    					</tr>
    					<tr>
    						<td>Contact Form</td>
		    				<td class="alignright">
			    				<select name="setting[new_user_contact_et]" class="sidebyside" style="margin:5px">
			    					<option value="0">-- Select Template --</option>
			    				<? $db->resetFetch($emailTemplates); while($emailTemplate = $db->fetchNextObject($emailTemplates)): ?>
									<option value="<?=$emailTemplate->id?>" <?=(($settings['new_user_contact_et'] == $emailTemplate->id)? 'selected':'')?>><?=$emailTemplate->name?></option>
								<? endwhile; ?>
			    				</select>
			    			</td>
    					</tr>
    				</table>
    				
	    			<h3 style="margin-left:0">Listing Inquiries</h3>
	    			<table class="nakedtable marginbottom">
	    				<tr>
	    					<td>New Inquiry Buyer Email</td>
	    					<td class="alignright">
	    						<select name="setting[new_inquiry_buyer_et]" class="sidebyside" style="margin:5px">
			    					<option value="0">-- Select Template --</option>
			    				<? $db->resetFetch($emailTemplates); while($emailTemplate = $db->fetchNextObject($emailTemplates)): ?>
									<option value="<?=$emailTemplate->id?>" <?=(($settings['new_inquiry_seller_et'] == $emailTemplate->id)? 'selected':'')?>><?=$emailTemplate->name?></option>
								<? endwhile; ?>
			    				</select>
	    					</td>
	    				</tr>
	    				<tr>
	    					<td>New Inquiry Seller Email</td>
	    					<td class="alignright">
	    						<select name="setting[new_inquiry_seller_et]" class="sidebyside" style="margin:5px">
			    					<option value="0">-- Select Template --</option>
			    				<? $db->resetFetch($emailTemplates); while($emailTemplate = $db->fetchNextObject($emailTemplates)): ?>
									<option value="<?=$emailTemplate->id?>" <?=(($settings['new_inquiry_buyer_et'] == $emailTemplate->id)? 'selected':'')?>><?=$emailTemplate->name?></option>
								<? endwhile; ?>
			    				</select>
	    					</td>
	    				</tr>
	    				<tr>
	    					<td>Inquiry Message</td>
	    					<td class="alignright">
	    						<select name="setting[inquiry_message_et]" class="sidebyside" style="margin:5px">
			    					<option value="0">-- Select Template --</option>
			    				<? $db->resetFetch($emailTemplates); while($emailTemplate = $db->fetchNextObject($emailTemplates)): ?>
									<option value="<?=$emailTemplate->id?>" <?=(($settings['inquiry_message_et'] == $emailTemplate->id)? 'selected':'')?>><?=$emailTemplate->name?></option>
								<? endwhile; ?>
			    				</select>
	    					</td>
	    				</tr>
	    			</table>
    				
    				
    				
    				
    				<input type="hidden" name="updateContactSettings" value="true">
    				<input type="submit" class="button_green clear offset floatright" style="padding: 10px 25px;margin-right:4px" value="Save">
    				</form>
    			</div>
    			<br style="clear:both">
	    	</div>
    	</div>
    	<br style="clear:both">
    </div>
    <script>
		$(document).ready(function() {
			$('.submitBtn').click(function(e) {
				e.preventDefault();
				var formId = $(this).attr("data");
				$("#"+formId).submit();
			});
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
		});
	</script>
    <? include("foot.php"); ?>