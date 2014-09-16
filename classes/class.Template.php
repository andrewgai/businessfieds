<?php # Script 2.5

// This class reads in a template, sets the different values, and sends it to the browser.

class EmailTemplate extends DB {

	// Set the attributes.
	var $template_id;
	var $html;
	var $parameters = array();

	function EmailTemplate () { // This function sets which template will be used.
		// $emailObj = $this->queryUniqueObject("SELECT subject, body FROM email_templates WHERE id = '{$template_id}'");
		// $this->template = $emailObj->body;
		// $this->subject = $emailObj->subject;
		// //$this->html = implode ("", $this->template); // Read the template into an array, then create a string.
		// $this->html = $this->template;
	}

	function SetTemplate ($template_id) {
		$emailObj = $this->queryUniqueObject("SELECT subject, body FROM email_templates WHERE id = '{$template_id}'");
		$this->html = $emailObj->body;
		$this->subject = $emailObj->subject;
	}


	function SetParameter ($variable, $value) { // This function sets the particular values.
		$this->parameters[$variable] = $value;
	}
	
	function CreateBody () { // This function does the bulk of the work.
		foreach ($this->parameters as $key => $value) { // Loop through all the parameters and set the variables to values.
			$template_name = '%' . $key . '%';
			$this->html = str_replace ($template_name, $value, $this->html);
		}    
		return nl2br($this->html);
	}
	
	function Subject () {
		return $this->subject;
	}
}
?>