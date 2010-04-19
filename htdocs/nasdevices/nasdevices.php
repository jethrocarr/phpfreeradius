<?php
/*
	nasdevices/nasdevices.php

	access:
		radiusdmins

	Interface to view, edit or delete NAS devices
*/


class page_output
{
	var $obj_table;


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
		$this->obj_table->tablename	= "nas_devices";

		// define all the columns and structure
		$this->obj_table->add_column("standard", "nas_hostname", "");
		$this->obj_table->add_column("standard", "nas_address", "");
		$this->obj_table->add_column("standard", "nas_type", "nas_types.nas_type");
		$this->obj_table->add_column("standard", "nas_ldapgroup", "");
		$this->obj_table->add_column("standard", "nas_description", "");

		// defaults
		$this->obj_table->columns		= array("nas_hostname", "nas_address", "nas_type", "nas_ldapgroup", "nas_description");
		$this->obj_table->columns_order		= array("nas_hostname");
		$this->obj_table->columns_order_options	= array("nas_hostname", "nas_address", "nas_type", "nas_ldapgroup");

		$this->obj_table->sql_obj->prepare_sql_settable("nas_devices");
		$this->obj_table->sql_obj->prepare_sql_addjoin("LEFT JOIN nas_types ON nas_types.id = nas_devices.nas_type");
		$this->obj_table->sql_obj->prepare_sql_addfield("id", "nas_devices.id");

		// acceptable filter options
		$structure = NULL;
		$structure["fieldname"] = "searchbox";
		$structure["type"]	= "input";
		$structure["sql"]	= "(nas_hostname LIKE '%value%' OR nas_address LIKE '%value%')";
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
		print "<h3>NETWORK ACCESS SERVERS</h3>";
		print "<p>This page allows you to define and administrate the Network Access Servers (NAS) which are configured for Free Radius</p>";

		// display options form
		$this->obj_table->render_options_form();

		// table data
		if (!count($this->obj_table->columns))
		{
			format_msgbox("important", "<p>Please select some valid options to display.</p>");
		}
		elseif (!$this->obj_table->data_num_rows)
		{
			format_msgbox("info", "<p>No NASes that match your options were found.</p>");
		}
		else
		{
			// details link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_details", "nasdevices/view.php", $structure);

			// logging link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_logs", "nasdevices/logs.php", $structure);

			// delete link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_delete", "nasdevices/delete.php", $structure);


			// display the table
			$this->obj_table->render_table_html();

		}

		// add link
		print "<p><a class=\"button\" href=\"index.php?page=nasdevices/add.php\">Add New NAS</a></p>";

	}

}


?>
