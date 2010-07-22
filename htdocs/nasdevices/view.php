<?php
/*
	nasdevices/view.php

	access:
		radiusadmins

	Displays all the details of the selected NAS and allows it to be adjusted.
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
			Define form structure
		*/
		$this->obj_form			= New form_input;
		$this->obj_form->formname	= "nas_device_edit";
		$this->obj_form->language	= $_SESSION["user"]["lang"];

		$this->obj_form->action		= "nasdevices/edit-process.php";
		$this->obj_form->method		= "post";



		// general
		$structure = NULL;
		$structure["fieldname"] 	= "nas_hostname";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_hostname");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] 	= "nas_address_type";
		$structure["type"]		= "radio";
		$structure["values"]		= array("ipv4_single", "ipv4_range", "hostname");
		$structure["options"]["req"]	= "yes";
		$structure["defaultvalue"]	= "ipv4_single";
		$this->obj_form->add_input($structure);
							

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_ipv4_range";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_ipv4_range");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_host";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_host");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_ipv4";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_ipv4");
		$this->obj_form->add_input($structure);


		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_ipv4_range", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_host", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_ipv4", "show");

		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_na", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_a", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_ptr", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_ptr_altip", "show");

		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_ipv4_range", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_host", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_ipv4", "hide");

		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_na", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_a", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_ptr", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_ptr_altip", "hide");

		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_ipv4_range", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_host", "show");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_ipv4", "hide");
	
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_na", "show");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_a", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_ptr", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_ptr_altip", "hide");


		$structure = form_helper_prepare_dropdownfromdb("nas_type", "SELECT id, nas_type as label FROM nas_types ORDER BY nas_type");
		$structure["options"]["req"]	= "yes";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_description";
		$structure["type"]		= "textarea";
		$this->obj_form->add_input($structure);


		// DNS
		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"] == "enabled")
		{
			$structure = NULL;
			$structure["fieldname"]			= "nas_dns_record_na";
			$structure["type"]			= "message";
			$structure["defaultvalue"]		= "<i>". lang_trans("help_nas_dns_record_na") ."</i>";

// TODO: why does this CSS break the javascript show/hide stuff?
//			$structure["options"]["css_row_class"]	= "table_highlight_info";

			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_a";
			$structure["type"]		= "checkbox";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_a");
			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_ptr";
			$structure["type"]		= "checkbox";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_ptr");
			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_ptr_altip";
			$structure["type"]		= "input";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_ptr_altip");
			$this->obj_form->add_input($structure);
		}


		// authentication
		$structure = NULL;
		$structure["fieldname"] 	= "nas_secret";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$this->obj_form->add_input($structure);
	


		// ldap groups
		$structure = NULL;

		$obj_ldap = New ldap_auth_lookup;
		$obj_ldap->list_groups();

		foreach ($obj_ldap->data as $group_name)
		{
			$structure["values"][]	= $group_name;
		}

		$structure["fieldname"] 	= "nas_ldapgroup";
		$structure["type"]		= "dropdown";
		$structure["options"]["req"]	= "yes";
		$this->obj_form->add_input($structure);
		
			

		// hidden section
		$structure = NULL;
		$structure["fieldname"] 	= "id_nas";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $this->obj_nas_device->id;
		$this->obj_form->add_input($structure);
			
		// submit section
		$structure = NULL;
		$structure["fieldname"] 	= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "Save Changes";
		$this->obj_form->add_input($structure);
		
		
		// define subforms
		$this->obj_form->subforms["nas_details"]	= array("nas_hostname", "nas_address_type", "nas_address_ipv4", "nas_address_host", "nas_address_ipv4_range", "nas_type", "nas_description");

		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"] == "enabled")
		{
			$this->obj_form->subforms["nas_dns"]	= array("nas_dns_record_na", "nas_dns_record_a", "nas_dns_record_ptr", "nas_dns_record_ptr_altip");
		}

		$this->obj_form->subforms["nas_auth"]		= array("nas_secret", "nas_ldapgroup");
		$this->obj_form->subforms["hidden"]		= array("id_nas");
		$this->obj_form->subforms["submit"]		= array("submit");


		// import data
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
				$this->obj_form->structure["nas_type"]["defaultvalue"]				= $this->obj_nas_device->data["nas_type"];
				$this->obj_form->structure["nas_description"]["defaultvalue"]			= $this->obj_nas_device->data["nas_description"];
				$this->obj_form->structure["nas_secret"]["defaultvalue"]			= $this->obj_nas_device->data["nas_secret"];
				$this->obj_form->structure["nas_ldapgroup"]["defaultvalue"]			= $this->obj_nas_device->data["nas_ldapgroup"];

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
