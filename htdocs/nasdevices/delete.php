<?php
/*
	nasdevices/delete.php

	access:
		radiusadmins

	Allows the selected NAS device to be deleted.
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

		if ($GLOBALS["config"]["FEATURE_LOGS_ENABLE"])
		{
			$this->obj_menu_nav->add_item("View NAS-Specific Logs", "page=nasdevices/logs.php&id=". $this->obj_nas_device->id ."");
		}

		$this->obj_menu_nav->add_item("Delete NAS", "page=nasdevices/delete.php&id=". $this->obj_nas_device->id ."", TRUE);
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
		/*
			Define form structure
		*/
		$this->obj_form			= New form_input;
		$this->obj_form->formname	= "nas_device_delete";
		$this->obj_form->language	= $_SESSION["user"]["lang"];

		$this->obj_form->action		= "nasdevices/delete-process.php";
		$this->obj_form->method		= "post";



		// general
		$structure = NULL;
		$structure["fieldname"] 	= "nas_hostname";
		$structure["type"]		= "text";
		$this->obj_form->add_input($structure);
							
		$structure = NULL;
		$structure["fieldname"]		= "nas_address";
		$structure["type"]		= "text";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_description";
		$structure["type"]		= "text";
		$this->obj_form->add_input($structure);


		// hidden section
		$structure = NULL;
		$structure["fieldname"] 	= "id_nas";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $this->obj_nas_device->id;
		$this->obj_form->add_input($structure);
			

		// confirm delete
		$structure = NULL;
		$structure["fieldname"] 	= "delete_confirm";
		$structure["type"]		= "checkbox";
		$structure["options"]["label"]	= "Yes, I wish to delete this NAS and realise that once deleted the data can not be recovered.";
		$this->obj_form->add_input($structure);

		// submit
		$structure = NULL;
		$structure["fieldname"] 	= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "delete";
		$this->obj_form->add_input($structure);
		
		
		// define subforms
		$this->obj_form->subforms["nas_details"]	= array("nas_hostname", "nas_address","nas_description");
		$this->obj_form->subforms["hidden"]		= array("id_nas");
		$this->obj_form->subforms["submit"]		= array("delete_confirm", "submit");


		// import data
		if (error_check())
		{
			$this->obj_form->load_data_error();
		}
		else
		{
			if ($this->obj_nas_device->load_data())
			{
				$this->obj_form->structure["nas_hostname"]["defaultvalue"]		= $this->obj_nas_device->data["nas_hostname"];
				$this->obj_form->structure["nas_address"]["defaultvalue"]		= $this->obj_nas_device->data["nas_address"];
				$this->obj_form->structure["nas_description"]["defaultvalue"]		= $this->obj_nas_device->data["nas_description"];
			}
		}
	}


	function render_html()
	{
		// title + summary
		print "<h3>DELETE NAS</h3><br>";
		print "<p>This page allows you to delete an unwanted NAS device - take care to make sure you are deleting the NAS that you intend to, this action is not reversable.</p>";

	
		// display the form
		$this->obj_form->render_form();
	}

}

?>
