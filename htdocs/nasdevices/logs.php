<?php
/*
	nasdevices/logs.php

	access:
		radiusdmins

	TODO: write me
*/


class page_output
{
	var $obj_nas_device;
	var $obj_menu_nav;
	var $obj_form;


	function page_output()
	{

		// initate object
		$this->obj_nas_device		= New nas_device;

		// fetch variables
		$this->obj_nas_device->id	= security_script_input('/^[0-9]*$/', $_GET["id"]);


		// define the navigiation menu
		$this->obj_menu_nav = New menu_nav;

		$this->obj_menu_nav->add_item("Adjust NAS Configuration", "page=nasdevices/view.php&id=". $this->obj_nas_device->id ."");
		$this->obj_menu_nav->add_item("View NAS-Specific Logs", "page=nasdevices/logs.php&id=". $this->obj_nas_device->id ."", TRUE);
		$this->obj_menu_nav->add_item("Delete NAS", "page=nasdevices/delete.php&id=". $this->obj_nas_device->id ."");
	}


	function check_permissions()
	{
		return user_permissions_get("radiusadmins");
	}


	function check_requirements()
	{
		// make sure the NAS is valid
		if (!$this->obj_nas_device->verify_id())
		{
			log_write("error", "page_output", "The requested NAS (". $this->obj_nas_device->id .") does not exist - possibly the NAS has been deleted?");
			return 0;
		}

		return 1;
	}

	function execute()
	{
		// nothing todo
		return 1;
	}


	function render_html()
	{
		// title + summary
		print "<h3>NAS DEVICES LOG</h3>";
		print "<p>This feature has yet to be implemented.</p>";
	}

}


?>
