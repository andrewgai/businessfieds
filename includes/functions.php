<?php

if(empty($db)) {
	require_once("../classes/class.Database.php");
	$db = new DB($db_name,$db_host,$db_user,$db_password);
}

function protectPage() {
	global $_SESSION;
	if($_SESSION['logged_in'] != 'true') {
		Header("Location: /login");
		exit;
	} 
}

function objectToArray2($object) {
	$array=array();
	foreach($object as $member=>$data)
	{
		$array[$member]=$data;
	}
	return $array;
}

function reloadSettings() {
	global $db;
	$result = $db->query("SELECT * FROM settings");
	while ($row = $db->fetchNextObject($result)) {
		$settings[$row->name] = $row->value;
	}
	return $settings;
}

function showAlert($message, $classes = "") {
	return "<div class='alert {$classes}'>
				{$message}
				<a href='#' class='close floatright'><img src='/images/x.png'></a>
			</div>";
}

function isset_or(&$check, $alternate = NULL) { 
    return (isset($check)) ? (empty($check) ? $alternate : $check) : $alternate; 
} 

function stripAllSlashes($input) {
	if (is_array($input)) {
		$output = array();
		foreach ($input as $key => $value) {
			$output[$key] = stripAllSlashes($value);
		}
		return $output;
	} else {
		$output = stripslashes($input);
		return $output;
	}
}

function route($i) {
	global $route_array;
	if (!isset($route_array[$i])) {
		return false;
	} else {
		return mysql_real_escape_string($route_array[$i]);
	}
}

function truncate($string, $max = 55, $replacement = '...') {
	if (strlen($string) <= $max) {
		return $string;
	}
	$leave = $max - strlen ($replacement);
	return substr_replace($string, $replacement, $leave);
}

function formatDate($date, $shortyear=0) {
	if ($date == "0000-00-00" or $date == "0000-00-00 00:00:00" or $date == "") {
		return "";
	} else {
		if ($shortyear == 3) {
			return date("n/j", strtotime($date));
		} elseif ($shortyear == 2) {
			return date("n/j/y", strtotime($date));
		} elseif ($shortyear == 1) {
			return date("m/d/y", strtotime($date));
		} else {
			return date("m/d/Y", strtotime($date));
		}
	}
}


function formatDatetime($datetime) {
	$timestamp = strtotime($datetime);
	return date("m/d/y g:ia", $timestamp);
}

function prepareDate($date) {
	if ($date == "none" || $date == "" || $date == "0000-00-00") {
		return "0000-00-00";
	} else {
		return date("Y-m-d",strtotime($date));
	}
}

function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}

function getNameFromId($table, $id, $field) {
	if ($field == null || $field == "") {
		$field = "name";
	}
	return getOneCell("SELECT {$field} FROM {$table} WHERE id = '{$id}'");
}

function unWatch($listing_id) {
	global $u_id, $db; // get user id
	$listing_id = mysql_real_escape_string($listing_id);
	$query = "DELETE FROM watchlist WHERE user_id = '{$u_id}' AND listing_id = '{$listing_id}'"; 
	if($db->query($query)) { // kill the db entry
		return true;
	} else {
		return false;
	}
}

function watch($listing_id) {
	global $u_id, $db; // get user id
	$listing_id = mysql_real_escape_string($listing_id);
	$check = $db->queryUniqueValue("SELECT count(user_id) FROM watchlist WHERE user_id = '{$u_id}' AND listing_id = '{$listing_id}'");
	if($check == 0) { // we aren't already following so let's follow!
		$query = "INSERT INTO watchlist (user_id,listing_id) VALUES ('{$u_id}','{$listing_id}')";	
		if($db->query($query)) { // insert following reference
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function checkWatch($listing_id, $user_id = FALSE) {
	global $u_id, $db; // get user id
	$user_id = ($user_id)? $user_id : $u_id;
	$listing_id = mysql_real_escape_string($listing_id);
	$check = $db->queryUniqueValue("SELECT count(user_id) FROM watchlist WHERE user_id = '${user_id}' AND listing_id = '{$listing_id}'");
	if($check == 0) {
		// not following
		return false;
	} else {
		// we are following!
		return true;
	}
}

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function linktohtml($url,$referalize = TRUE,$trunc = FALSE) {
	if(is_array($url)) {
		$url = $url[0];
	}
	$href = addhttp($url);
	$url = preg_replace( "{http://}", "", $url );
	(($trunc)? $displayUrl = truncate($url,$trunc): $displayUrl = $url);
	if($referalize) {
		return '<a href="http://jsindustries.net/?y=2012&x='.$href.'" target="_blank">'.$displayUrl.'</a>';
	} else {
		return '<a href="'.$href.'" target="_blank">'.$displayUrl.'</a>';
	}
}

function links_clickable($string) {
	//$regex = "/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig";
	$string = preg_replace( "{ www.}", " http://www.", $string );
	//$regex = "(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)";
	$regex = "/(http:\/\/[^\s]+)/";
	$clickable = preg_replace_callback($regex, 'linktohtml', $string);
	return $clickable;	
}

function file_exists_2($dir, $file) { 
	$ret = exec("ls ".$dir." | grep ".$file); 
	return (!empty($ret)); 
} 

function sanitizeUrl($url) {
	$url = strtolower($url);
	$url = str_replace("http://", "", $url);
	$url = str_replace("https://", "", $url);
	$url = str_replace("www.", "", $url);
	$url = trim($url,"/");
	return $url;
}

function accessToChildren($children, $permissions, $allow_all) {
	$access = 0;
	foreach ($children as $child) {
		if (in_array($child, $permissions) || $allow_all) {
			$access = 1;
		}
	}
	return $access;
}

function displayChildren($children, $permissions, $allow_all) {
	foreach ($children as $display => $page) {
		if (in_array($page, $permissions) || $allow_all) {
			echo "<li><a href='{$page}'>{$display}</a></li>";
		}
	}
}

function encode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$hash = "";
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if (isset_or($j) == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}

function decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
	$hash = "";
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if (isset_or($j) == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}

function createRandomPassword() { 
    $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $pass = '' ; 
    while ($i <= 7) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $pass = $pass . $tmp; 
        $i++; 
    }
    return $pass; 
} 

function objectToArray($object) {
	if(!is_object($object) && !is_array($object)) {
		return $object;
	}
	if(is_object($object)) {
		$object = get_object_vars($object);
	}
	return array_map('objectToArray', $object);
}

function sendMail($to, $subject, $body, $attachment = null) {		
	
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Additional headers
	$headers .= 'From: BusinessFieds <no-reply@businessfieds.com>' . "\r\n";

	// Mail it
	$result = mail($to, $subject, $body, $headers);
	
	return $result;
	/*
	require_once("Mail.php");
	require_once("Mail/mime.php");
	$username = "tpac.tracker@gmail.com";
	$password = "tpac2012";
	
	$headers = array ('From' => "TPAC Tracker <".$username.">", 'To' => $to, 'Subject' => $subject);

	$mime = new Mail_mime("\n");
	$mime->setTXTBody(utf8_decode($message));
	$mime->setHTMLBody("<html><body>" . utf8_decode(nl2br($message)) . "</body></html>");
	if ($attachment) {
		$mime->addAttachment($attachment, 'application/msword');
	}
	$body = $mime->get();
	$headers = $mime->headers($headers);

	$smtp = Mail::factory('smtp', array ('host' => "ssl://smtp.gmail.com", 'port' => "465", 'auth' => true, 'username' => $username, 'password' => $password));
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)) {
		return false;
	} else {
		return true;
	}*/
}

function exportToCSV($filename = "export.csv", $headers = array(), $data = array(), $fields = array()) {
	$fp = fopen($filename, 'w') or die("cant create file");
	fputcsv($fp, $headers, ',', '"') or die("cant write to file");
	foreach ($data as $row) {
		$insert_row = array();
		if (!empty($fields)) {
			foreach ($fields as $field) {
				$insert_row[] = $row[$field];
			}
			fputcsv($fp, $insert_row, ',', '"') or die("cant write to file");
		} else {
			fputcsv($fp, $row, ',', '"') or die("cant write to file");
		}
	}
	fclose($fp);
	
	header("Content-Disposition: attachment; filename=\"{$filename}\"");
	header("Content-Type: application/force-download");
	header("Content-Length: " . filesize($filename));
	header("Connection: close");
	readfile($filename);
	unlink($filename);
	die();
}

function drawLineChart($element_id, $data, $headers, $fields, $colors = array(), $height = 220) {
	?>
	<script>
		google.load('visualization', '1', {packages: ['corechart']});
		function draw_<?=$element_id?>() {
			// Create and populate the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'x');
			<? foreach ($headers as $header): ?>
				data.addColumn('number', '<?=$header?>');
			<? endforeach; ?>
			<? foreach ($data as $row): ?>
				data.addRow([
					<? foreach ($fields as $key => $field): ?>
						<? if ($key == 0): ?>
							"<?=$row[$field]?>", 
						<? else: ?>
							<?=$row[$field]?>, 
						<? endif; ?>
					<? endforeach; ?>
				]);
			<? endforeach; ?>
			new google.visualization.LineChart(document.getElementById('<?=$element_id?>')).
				draw(data, {
					vAxis: {textPosition: 'in', viewWindowMode: 'maximized', minValue: 0},
					chartArea: {left:0,top:10,width:"100%",height:"60%"},
					<? if (!empty($colors)): ?>
						colors:['<?=implode("', '", $colors)?>'],
					<? endif; ?>
					curveType: "function",
					fontSize: 10,
					height: <?=$height?>, 
					legend: 'bottom', 
					pointSize: 4,
			 });
		}
		google.setOnLoadCallback(draw_<?=$element_id?>);
	</script>
	<?
}

function insertFormAjax($element_id, $clearform = "false") {
	?>
	$('#<?=$element_id?>').validationEngine();
    $('#<?=$element_id?>').submit(function() {
    	if($('#<?=$element_id?>').validationEngine('validate')) {
    		$('.content-box').append('<div class="screen"><div class="spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div><div class="bar9"></div><div class="bar10"></div><div class="bar11"></div><div class="bar12"></div></div></div>');
    		var $form = $(this);
    		var t = setTimeout(function() { $form.ajaxSubmit({target: $form.siblings('.message'), 
    														  success: function(){ $('.screen').fadeOut().remove(); var newHeight = $(this).closest('.panels')[0].scrollHeight;
																				   $(this).closest('.panels').animate({height: newHeight}); },
															  clearForm: <?=$clearform?>
															 }) }, 800);
    		
    		$form.siblings('.message').on('click', '.close', function(event) {
    			$(this).parents(".alert").slideUp("fast");
				return false;
    		});
    	}
    	return false;
    }); 
	<?
}
?>