<?php
/*
	nasdevices/view.php

	access:
		radiusadmins

	Displays all the details of the selected NAS and allows it to be adjusted.
*/

class page_output extends ui_nas_device
{
	var $obj_nas_device;
	var $obj_menu_nav;
	var $obj_form;


	function page_output()
	{
		// include custom scripts and/or logic
		$this->requires["javascript"][]	= "include/javascript/nasdevices.js";

		// initate object
		$this->obj_nas_device		= New nas_device;

		// fetch variables
		$this->obj_nas_device->id	= security_script_input('/^[0-9]*$/', $_GET["id"]);


		// define the navigiation menu
		$this->obj_menu_nav = New menu_nav;

		$this->obj_menu_nav->add_item("Adjust NAS Configuration", "page=nasdevices/view.php&id=". $this->obj_nas_device->id ."", TRUE);
		$this->obj_menu_nav->add_item("View NAS-Specific Logs", "page=nasdevices/logs.php&id=". $this->obj_nas_device->id ."");
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
		else
		{
			if ($this->obj_nas_device->load_data())
			{
				// load general data
				$this->obj_form->structure["nas_hostname"]["defaultvalue"]			= $this->obj_nas_device->data["nas_hostname"];
				$this->obj_form->structure["nas_shortname"]["defaultvalue"]			= $this->obj_nas_device->data["nas_shortname"];
				$this->obj_form->structure["nas_type"]["defaultvalue"]				= $this->obj_nas_device->data["nas_type"];
				$this->obj_form->structure["nas_description"]["defaultvalue"]			= $this->obj_nas_device->data["nas_description"];
				$this->obj_form->structure["nas_secret"]["defaultvalue"]			= $this->obj_nas_device->data["nas_secret"];
				$this->obj_form->structure["nas_station_0_ldapgroup"]["defaultvalue"]		= $this->obj_nas_device->data["nas_ldapgroup"];

				if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"])
				{
					if ($this->obj_nas_device->data["nas_dns_record_a"])
					{
						$this->obj_form->structure["nas_dns_record_a"]["defaultvalue"]		= 1;
					}

					if ($this->obj_nas_device->data["nas_dns_record_ptr"])
					{
						$this->obj_form->structure["nas_dns_record_ptr"]["defaultvalue"]	= 1;
					}

					$this->obj_form->structure["nas_dns_record_ptr_altip"]["defaultvalue"]	= $this->obj_nas_device->data["nas_dns_record_ptr_altip"];
				}

				// determine address type
				//	- single IP (ipv4_single)
				//	- subnet with CIDR notation (ipv4_range)
				//	- hostname (hostname)

				if (preg_match("/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/", $this->obj_nas_device->data["nas_address"]))
				{
					// single IP
					$this->obj_form->structure["nas_address_type"]["defaultvalue"]		= "ipv4_single";
					$this->obj_form->structure["nas_address_ipv4"]["defaultvalue"]		= $this->obj_nas_device->data["nas_address"];
				}
				elseif (preg_match("/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}\/[0-9]*$/", $this->obj_nas_device->data["nas_address"]))
				{
					// CIDR notation
					$this->obj_form->structure["nas_address_type"]["defaultvalue"]		= "ipv4_range";
					$this->obj_form->structure["nas_address_ipv4_range"]["defaultvalue"]		= $this->obj_nas_device->data["nas_address"];
				}
				elseif (preg_match("/^[a-zA-Z][a-zA-Z0-9.-]*$/", $this->obj_nas_device->data["nas_address"]))
				{
					// hostname
					$this->obj_form->structure["nas_address_type"]["defaultvalue"]		= "hostname";
					$this->obj_form->structure["nas_address_host"]["defaultvalue"]		= $this->obj_nas_device->data["nas_address"];
				}


				// load NAS Called-Station-IDs
				if (is_array($this->obj_nas_device->data["stationids"]))
				{
					$i = 0;

					foreach ($this->obj_nas_device->data["stationids"] as $nas_device)
					{
						$i++;

						$this->obj_form->structure["nas_station_". $i ."_stationid"]["defaultvalue"]		= $nas_device["stationid"];
						$this->obj_form->structure["nas_station_". $i ."_ldapgroup"]["defaultvalue"]		= $nas_device["ldapgroup"];
					}
				}
			}
		}

	}



	function render_html()
	{
		// title + summary
		print "<h3>NAS CONFIGURATION</h3><br>";
		print "<p>This page allows you to view and adjust the NAS configuration.</p>";

	
		// display the form
		$this->obj_form->render_form();
	}

}

?>
