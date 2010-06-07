<?php
/*
	nasdevices/add.php

	access: radiusadmins only

	Allows the addition of a new NAS to the system configuration.
*/

class page_output
{
	var $obj_menu_nav;
	var $obj_form;


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
		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"])
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
			$structure["defaultvalue"]	= $GLOBALS["config"]["NAMEDMANAGER_DEFAULT_A"];
			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_ptr";
			$structure["type"]		= "checkbox";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_ptr");
			$structure["defaultvalue"]	= $GLOBALS["config"]["NAMEDMANAGER_DEFAULT_PTR"];
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
		$structure["defaultvalue"]	= $GLOBALS["config"]["DEFAULT_NAS_PASSWORD"];
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

		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"])
		{
			$this->obj_form->subforms["nas_dns"]	= array("nas_dns_record_na", "nas_dns_record_a", "nas_dns_record_ptr", "nas_dns_record_ptr_altip");
		}

		$this->obj_form->subforms["nas_auth"]		= array("nas_secret", "nas_ldapgroup");
		$this->obj_form->subforms["hidden"]		= array("id_nas");
		$this->obj_form->subforms["submit"]		= array("submit");



		// load data
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
