<?php
/*
	servers/servers.php

	access:
		radiusdmins

	Interface to view and manage what FreeRadius servers are managed by this interface. The main reason
	for this interface is to put a view onto what is being recorded to allow the API to function and
	make it easier to get reports on a per-server basis.
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
		$this->obj_table->tablename	= "radius_server";

		// define all the columns and structure
		$this->obj_table->add_column("standard", "server_name", "");
		$this->obj_table->add_column("standard", "server_description", "");
		$this->obj_table->add_column("standard", "sync_status_config", "NONE");
		$this->obj_table->add_column("standard", "sync_status_log", "NONE");

		// defaults
		$this->obj_table->columns		= array("server_name", "server_description", "sync_status_config", "sync_status_log");
		$this->obj_table->columns_order		= array("server_name");
		$this->obj_table->columns_order_options	= array("server_name");

		$this->obj_table->sql_obj->prepare_sql_settable("radius_servers");
		$this->obj_table->sql_obj->prepare_sql_addfield("id", "");
		$this->obj_table->sql_obj->prepare_sql_addfield("api_sync_config", "");
		$this->obj_table->sql_obj->prepare_sql_addfield("api_sync_log", "");

		// load data
		$this->obj_table->generate_sql();
		$this->obj_table->load_data_sql();


		// check sync status
		$sync_status_config = sql_get_singlevalue("SELECT value FROM config WHERE name='SYNC_STATUS_CONFIG'");

		for ($i=0; $i < $this->obj_table->data_num_rows; $i++)
		{
			if ($sync_status_config != $this->obj_table->data[$i]["api_sync_config"])
			{
				$this->obj_table->data[$i]["sync_status_config"] = "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span>";
			}
			else
			{
				$this->obj_table->data[$i]["sync_status_config"] = "<span class=\"table_highlight_open\">". lang_trans("status_synced") ."</span>";
			}

			if ((time() - $this->obj_table->data[$i]["api_sync_log"]) > 86400)
			{
				$this->obj_table->data[$i]["sync_status_log"] = "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span>";
			}
			else
			{
				$this->obj_table->data[$i]["sync_status_log"] = "<span class=\"table_highlight_open\">". lang_trans("status_synced") ."</span>";
			}
		}

	}


	function render_html()
	{
		// title + summary
		print "<h3>RADIUS SERVERS</h3>";
		print "<p>This page allows you to define and monitor all the radius servers that are pulling configuration and pushing logs to phpfreeradius - an entry is needed for each FreeRadius server in the system.</p>";

		// table data
		if (!$this->obj_table->data_num_rows)
		{
			format_msgbox("important", "<p>There are currently no FreeRadius servers configured - you will need to define at least one FreeRadius server to manage.</p>");
		}
		else
		{
			// details link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_details", "servers/view.php", $structure);

			// logging link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_logs", "servers/logs.php", $structure);

			// delete link
			$structure = NULL;
			$structure["id"]["column"]	= "id";
			$this->obj_table->add_link("tbl_lnk_delete", "servers/delete.php", $structure);


			// display the table
			$this->obj_table->render_table_html();

		}

		// add link
		print "<p><a class=\"button\" href=\"index.php?page=servers/add.php\">Add New Server</a></p>";

	}

}


?>
