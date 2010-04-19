<?php
/*
	servers/logs.php

	access:
		radiusdmins

	Fetch all the logs for the selected radius server.
*/


class page_output
{
	var $obj_radius_server;
	var $obj_menu_nav;
	var $obj_form;


	function page_output()
	{

		// initate object
		$this->obj_radius_server		= New radius_server;

		// fetch variables
		$this->obj_radius_server->id		= security_script_input('/^[0-9]*$/', $_GET["id"]);


		// define the navigiation menu
		$this->obj_menu_nav = New menu_nav;

		$this->obj_menu_nav->add_item("Adjust Server Configuration", "page=servers/view.php&id=". $this->obj_radius_server->id ."");
		$this->obj_menu_nav->add_item("View Server-Specific Logs", "page=servers/logs.php&id=". $this->obj_radius_server->id ."", TRUE);
		$this->obj_menu_nav->add_item("Delete Server", "page=servers/delete.php&id=". $this->obj_radius_server->id ."");
	}


	function check_permissions()
	{
		return user_permissions_get("radiusadmins");
	}


	function check_requirements()
	{
		// make sure the server is valid
		if (!$this->obj_radius_server->verify_id())
		{
			log_write("error", "page_output", "The requested server (". $this->obj_radius_server->id .") does not exist - possibly the server has been deleted?");
			return 0;
		}

		return 1;
	}



	function execute()
	{
		// establish a new table object
		$this->obj_table = New table;

		$this->obj_table->language	= $_SESSION["user"]["lang"];
		$this->obj_table->tablename	= "logs_radius";

		// define all the columns and structure
		$this->obj_table->add_column("timestamp", "timestamp", "");
		$this->obj_table->add_column("standard", "server_name", "radius_servers.server_name");
		$this->obj_table->add_column("standard", "log_type", "");
		$this->obj_table->add_column("standard", "log_contents", "");

		// defaults
		$this->obj_table->columns		= array("timestamp", "server_name", "log_type", "log_contents");

		$this->obj_table->sql_obj->prepare_sql_settable("logs");
		$this->obj_table->sql_obj->prepare_sql_addjoin("LEFT JOIN radius_servers ON radius_servers.id = logs.id_server");
		$this->obj_table->sql_obj->prepare_sql_addwhere("id_server='". $this->obj_radius_server->id ."'");
		$this->obj_table->sql_obj->prepare_sql_addorderby_desc("timestamp");

		// acceptable filter options
		$structure = NULL;
		$structure["fieldname"] = "searchbox";
		$structure["type"]	= "input";
		$structure["sql"]	= "(server_name LIKE '%value%' OR log_type LIKE '%value%' OR log_contents LIKE '%value%')";
		$this->obj_table->add_filter($structure);


		// load options
		$this->obj_table->load_options_form();

	
		// load data
		$this->obj_table->generate_sql();
		$this->obj_table->load_data_sql();

	}


	function render_html()
	{
		// title + summary
		print "<h3>RADIUS SERVER LOGS</h3>";
		print "<p>This page displays logs collected from the selected radius server.</p>";

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
