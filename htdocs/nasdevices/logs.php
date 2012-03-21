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
		// make sure logging is enabled
		if (!$GLOBALS["config"]["FEATURE_LOGS_ENABLE"])
		{
			log_write("error", "page_output", "Logging functionality is disabled, adjust FEATURE_LOGS_ENABLE on the configuration page to fix.");
			return 0;
		}

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
		// establish a new table object
		$this->obj_table = New table;

		$this->obj_table->language	= $_SESSION["user"]["lang"];
		$this->obj_table->tablename	= "logs_nas_devices";

		// define all the columns and structure
		$this->obj_table->add_column("timestamp", "timestamp", "");
		$this->obj_table->add_column("standard", "server_name", "radius_servers.server_name");
		$this->obj_table->add_column("standard", "log_type", "");
		$this->obj_table->add_column("standard", "log_contents", "");

		// defaults
		$this->obj_table->columns		= array("timestamp", "server_name", "log_type", "log_contents");

		$this->obj_table->sql_obj->prepare_sql_settable("logs");
		$this->obj_table->sql_obj->prepare_sql_addjoin("LEFT JOIN radius_servers ON radius_servers.id = logs.id_server");
		$this->obj_table->sql_obj->prepare_sql_addwhere("id_nas='". $this->obj_nas_device->id ."'");
		$this->obj_table->sql_obj->prepare_sql_addorderby_desc("timestamp");

		// acceptable filter options
		$structure = NULL;
		$structure["fieldname"] 	= "searchbox";
		$structure["type"]		= "input";
		$structure["sql"]		= "(server_name LIKE '%value%' OR log_type LIKE '%value%' OR log_contents LIKE '%value%')";
		$this->obj_table->add_filter($structure);

		$structure = NULL;
		$structure["fieldname"] 	= "num_logs_rows";
		$structure["type"]		= "input";
		$structure["sql"]		= "";
		$structure["defaultvalue"]	= "1000";
		$this->obj_table->add_filter($structure);


		// load options
		$this->obj_table->add_fixed_option("id", $this->obj_nas_device->id);
		$this->obj_table->load_options_form();


		// generate SQL
		$this->obj_table->generate_sql();

		// load limit filter
		$this->obj_table->sql_obj->string .= "LIMIT ". $this->obj_table->filter["filter_num_logs_rows"]["defaultvalue"];

		// load data from DB
		$this->obj_table->load_data_sql();

	}


	function render_html()
	{
		// title + summary
		print "<h3>NAS DEVICE</h3>";
		print "<p>This page displays all log entries that were matched against the selected NAS device. This collection of logs might not be perfect, there could be some entries that haven't been displayed if phpfreeradius was unable to associate them with the device, in which case use the main logs page to view all logs.</p>";

		// display options form
		$this->obj_table->render_options_form();

		// table data
		if (!count($this->obj_table->columns))
		{
			format_msgbox("important", "<p>Please select some valid options to display.</p>");
		}
		elseif (!$this->obj_table->data_num_rows)
		{
			format_msgbox("info", "<p>No log records that match your options were found.</p>");
		}
		else
		{

			// display the table
			$this->obj_table->render_table_html();

		}

	}


}


?>
