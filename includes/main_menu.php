<?php
	
	$pages = array(	"Clients" => array(	"link" => "clients",
										"children" => array("New Client" => "client",
															"View All" => "clients")
									),
					"Users" => array( "link" => "users",
									  "children" => array("New User" => "user")
									  ),
					"Settings" => "settings");
	
	
	foreach ($pages as $display => $page) {
		if (!is_array($page)) {
			if (in_array($page, $u_permissions) || $u_all_access) {
				echo "<li><a href='{$page}'>{$display}</a></li>";
			}
		} else {
			$is_link = ($page['link']) ? 1 : 0;
			if ($u_all_access) {
				$access_to_parent = 1;
			} else {
				$access_to_parent = ($is_link) ? in_array($page['link'], $u_permissions) : 0;
			}
			$access_to_children = accessToChildren($page['children'], $u_permissions, $u_all_access);
			if ($access_to_parent || $access_to_children) {
				if ($access_to_parent) {
					echo "<li><a href='{$page['link']}'>{$display}</a>";
				} elseif ((!$access_to_parent && $access_to_children) || (!$is_link && $access_to_children)) { // what
					echo "<li><span>{$display}</span>";
				}
				if ($access_to_children) {
					echo "<ul>";
					displayChildren($page['children'], $u_permissions, $u_all_access);
					echo "</ul>";
				}
				echo "</li>";
			}
	
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	