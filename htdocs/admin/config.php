<?php
/*
	admin/config.php
	
	access: radiusadmins only

	Allows administrators to change system-wide settings stored in the config table that affect
	the key operation of the application.
*/

class page_output
{
	var $obj_form;


	function check_permissions()
	{
		return user_permissions_get("radiusadmins");
	}

	function check_requirements()
	{
		// nothing to do
		return 1;
	}


	function execute()
	{
		/*
			Define form structure
		*/
		
		$this->obj_form = New form_input;
		$this->obj_form->formname = "config";
		$this->obj_form->language = $_SESSION["user"]["lang"];

		$this->obj_form->action = "admin/config-process.php";
		$this->obj_form->method = "post";


/*
		// security options
		$structure = NULL;
		$structure["fieldname"]				= "BLACKLIST_ENABLE";
		$structure["type"]				= "checkbox";
		$structure["options"]["label"]			= "Enable to prevent brute-force login attempts";
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]				= "BLACKLIST_LIMIT";
		$structure["type"]				= "input";
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);
*/

		// default optins
		$structure = NULL;
		$structure["fieldname"]				= "DEFAULT_NAS_PASSWORD";
		$structure["type"]				= "input";
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);



		// date/time configuration
		$structure = form_helper_prepare_timezonedropdown("TIMEZONE_DEFAULT");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"]				= "DATEFORMAT";
		$structure["type"]				= "radio";
		$structure["values"]				= array("yyyy-mm-dd", "mm-dd-yyyy", "dd-mm-yyyy");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);



		// namedmanager options
		$structure = NULL;
		$structure["fieldname"]				= "NAMEDMANAGER_FEATURE";
		$structure["type"]				= "radio";
		$structure["values"]				= array("enabled", "disabled");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$structure["defaultvalue"]			= "disabled";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]				= "NAMEDMANAGER_API_URL";
		$structure["type"]				= "input";
		$structure["options"]["label"]			= " ". lang_trans("help_namedmanager_api_url");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]				= "NAMEDMANAGER_API_KEY";
		$structure["type"]				= "input";
		$structure["options"]["label"]			= " ". lang_trans("help_namedmanager_api_key");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"]				= "NAMEDMANAGER_DEFAULT_A";
		$structure["type"]				= "checkbox";
		$structure["options"]["label"]			= " ". lang_trans("help_namedmanager_default_a");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]				= "NAMEDMANAGER_DEFAULT_PTR";
		$structure["type"]				= "checkbox";
		$structure["options"]["label"]			= " ". lang_trans("help_namedmanager_default_ptr");
		$structure["options"]["no_translate_fieldname"]	= "yes";
		$this->obj_form->add_input($structure);


		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "disabled", "NAMEDMANAGER_API_URL", "hide");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "disabled", "NAMEDMANAGER_API_KEY", "hide");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "disabled", "NAMEDMANAGER_DEFAULT_A", "hide");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "disabled", "NAMEDMANAGER_DEFAULT_PTR", "hide");

		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "enabled", "NAMEDMANAGER_API_URL", "show");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "enabled", "NAMEDMANAGER_API_KEY", "show");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "enabled", "NAMEDMANAGER_DEFAULT_A", "show");
		$this->obj_form->add_action("NAMEDMANAGER_FEATURE", "enabled", "NAMEDMANAGER_DEFAULT_PTR", "show");


		// miscellaneous configurations
		$structure = NULL;
		$structure["fieldname"]					= "LOG_UPDATE_INTERVAL";
		$structure["type"]					= "input";
		$structure["options"]["no_translate_fieldname"]		= "yes";
		$structure["options"]["label"]				= " seconds";
		$this->obj_form->add_input($structure);


		// submit section
		$structure = NULL;
		$structure["fieldname"]					= "submit";
		$structure["type"]					= "submit";
		$structure["defaultvalue"]				= "Save Changes";
		$this->obj_form->add_input($structure);
		
		
		// define subforms
//		$this->obj_form->subforms["config_security"]		= array("BLACKLIST_ENABLE", "BLACKLIST_LIMIT");
		$this->obj_form->subforms["config_defaults"]		= array("DEFAULT_NAS_PASSWORD");
		$this->obj_form->subforms["config_dateandtime"]		= array("DATEFORMAT", "TIMEZONE_DEFAULT");
		$this->obj_form->subforms["config_namedmanager"]	= array("NAMEDMANAGER_FEATURE", "NAMEDMANAGER_API_URL", "NAMEDMANAGER_API_KEY", "NAMEDMANAGER_DEFAULT_A", "NAMEDMANAGER_DEFAULT_PTR");
		$this->obj_form->subforms["config_miscellaneous"]	= array("LOG_UPDATE_INTERVAL");
		$this->obj_form->subforms["submit"]			= array("submit");

		if (error_check())
		{
			// load error datas
			$this->obj_form->load_data_error();
		}
		else
		{
			// fetch all the values from the database
			$sql_config_obj		= New sql_query;
			$sql_config_obj->string	= "SELECT name, value FROM config ORDER BY name";
			$sql_config_obj->execute();
			$sql_config_obj->fetch_array();

			foreach ($sql_config_obj->data as $data_config)
			{
				$this->obj_form->structure[ $data_config["name"] ]["defaultvalue"] = $data_config["value"];
			}

			unset($sql_config_obj);
		}
	}



	function render_html()
	{
		// Title + Summary
		print "<h3>CONFIGURATION</h3><br>";
		print "<p>Use this page to adjust phpfreeradius's configuration to suit your requirements.</p>";
	
		// display the form
		$this->obj_form->render_form();
	}

	
}

?>
