<?php
/*
	logs/logs.php

	access:
		radiusdmins

	Fetch the logs from the radius servers in the database.
*/


class page_output
{
	var $obj_table;
	var $requires;


	function page_output()
	{
		$this->requires["javascript"][] = "include/javascript/logs.js";
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
		// establish a new table object
		$this->obj_table = New table;

		$this->obj_table->language	= $_SESSION["user"]["lang"];
		$this->obj_table->tablename	= "logs";

		// define all the columns and structure
		$this->obj_table->add_column("timestamp", "timestamp", "");
		$this->obj_table->add_column("standard", "server_name", "radius_servers.server_name");
		$this->obj_table->add_column("standard", "log_type", "");
		$this->obj_table->add_column("standard", "log_contents", "");

		// defaults
		$this->obj_table->columns		= array("timestamp", "server_name", "log_type", "log_contents");

		$this->obj_table->sql_obj->prepare_sql_settable("logs");
		$this->obj_table->sql_obj->prepare_sql_addjoin("LEFT JOIN radius_servers ON radius_servers.id = logs.id_server");
		$this->obj_table->sql_obj->prepare_sql_addorderby_desc("logs.id");

		// acceptable filter options
		$structure = NULL;
		$structure["fieldname"] = "searchbox";
		$structure["type"]	= "input";
		$structure["sql"]	= "(server_name LIKE '%value%' OR log_type LIKE '%value%' OR log_contents LIKE '%value%')";
		$this->obj_table->add_filter($structure);

		$structure = NULL;
		$structure["fieldname"] 	= "num_logs_rows";
		$structure["type"]		= "input";
		$structure["sql"]		= "";
		$structure["defaultvalue"]	= "1000";
		$this->obj_table->add_filter($structure);


		$structure = form_helper_prepare_dropdownfromdb("id_radius_server", "SELECT id, server_name as label FROM radius_servers ORDER BY server_name");
		$structure["type"]	= "dropdown";
		$structure["sql"]	= "id_server='value'";
		$this->obj_table->add_filter($structure);

		$structure = NULL;
		$structure["fieldname"] 	= "log_debug";
		$structure["type"]		= "checkbox";
		$structure["sql"]		= "log_type!='Debug'";
		$structure["defaultvalue"]	= "on";
		$this->obj_table->add_filter($structure);




		// load options
		$this->obj_table->load_options_form();


		// generate SQL
		$this->obj_table->generate_sql();

		// load limit filter
		$this->obj_table->sql_obj->string .= "LIMIT ". $this->obj_table->filter["filter_num_logs_rows"]["defaultvalue"];

		// load data
		$this->obj_table->load_data_sql();

	}


	function render_html()
	{
		// title + summary
		print "<h3>RADIUS SERVER LOGS</h3>";
		print "<p>This page displays logs collected from the radius servers.</p>";

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

	
		// hidden field to hold update interval
		$update_interval = sql_get_singlevalue("SELECT value FROM config WHERE name = \"LOG_UPDATE_INTERVAL\"");
		print "<input type=\"hidden\" value=\"" .$update_interval. "\" id=\"update_interval\" name=\"update_interval\">";

		// hidden field to hold highest id
		$highest_id = sql_get_singlevalue("SELECT id AS value FROM logs ORDER BY id DESC LIMIT 1");
		print "<input type=\"hidden\" value=\"" .$highest_id. "\" id=\"highest_id\" name=\"highest_id\">";
		
		if ($_SESSION["form"]["logs"]["columns"])
		{
			$columns = implode(",", $_SESSION["form"]["logs"]["columns"]);
		}
		else
		{
			$columns = implode(",", $this->obj_table->columns);
		}
		print "<input type=\"hidden\" value=\"" .$columns. "\" id=\"columns\" name=\"columns\">";

	}

}


?>
