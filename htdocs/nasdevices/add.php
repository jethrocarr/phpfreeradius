<?php
/*
	nasdevices/add.php

	access: radiusadmins only

	Allows the addition of a new NAS to the system configuration.
*/

class page_output extends ui_nas_device
{
	var $obj_menu_nav;
	var $obj_form;


	function page_output()
	{
		// include custom scripts and/or logic
		$this->requires["javascript"][]	= "include/javascript/nasdevices.js";
	}

	function check_permissions()
	{
		return user_permissions_get("radiusadmins");
	}


	function check_requirements()
	{
		// nothing todo
		return 1;
	}


	function execute()
	{
		/*
			Load Form
		*/

		$this->ui_form();


		/*
			Define page-specific form structure
		*/
		
		$this->obj_form->formname	= "nas_device_edit";
		$this->obj_form->language	= $_SESSION["user"]["lang"];

		$this->obj_form->action		= "nasdevices/edit-process.php";
		$this->obj_form->method		= "post";


		/*
			Load Form Data
		*/

		if (error_check())
		{
			$this->obj_form->load_data_error();
		}
	}

	function render_html()
	{
		// title + summary
		print "<h3>ADD NETWORK ACCESS SERVER</h3><br>";
		print "<p>This page allows you to add a new NAS device.</p>";

	
		// display the form
		$this->obj_form->render_form();
	}

}

?>
