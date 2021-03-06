<?php
/*
	servers/view.php

	access:
		radiusadmins

	Displays all the details and configuration options for a specific radius server.
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

		$this->obj_menu_nav->add_item("Adjust Server Configuration", "page=servers/view.php&id=". $this->obj_radius_server->id ."", TRUE);

		if ($GLOBALS["config"]["FEATURE_LOGS_ENABLE"])
		{
			$this->obj_menu_nav->add_item("View Server-Specific Logs", "page=servers/logs.php&id=". $this->obj_radius_server->id ."");
		}

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
		/*
			Define form structure
		*/
		$this->obj_form			= New form_input;
		$this->obj_form->formname	= "radius_server_edit";
		$this->obj_form->language	= $_SESSION["user"]["lang"];

		$this->obj_form->action		= "servers/edit-process.php";
		$this->obj_form->method		= "post";

		// general
		$structure = NULL;
		$structure["fieldname"] 	= "server_name";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$this->obj_form->add_input($structure);
							
		$structure = NULL;
		$structure["fieldname"]		= "server_description";
		$structure["type"]		= "textarea";
		$this->obj_form->add_input($structure);


		// api
		$structure = NULL;
		$structure["fieldname"]		= "api_auth_key";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_api_auth_key");
		$this->obj_form->add_input($structure);


		// sync status
		$structure = NULL;
		$structure["fieldname"]			= "sync_status_config";
		$structure["type"]			= "text";
		$structure["options"]["nohidden"]	= "hide";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]			= "sync_status_log";
		$structure["type"]			= "text";
		$structure["options"]["nohidden"]	= "hide";
		$this->obj_form->add_input($structure);


		// hidden section
		$structure = NULL;
		$structure["fieldname"] 	= "id_radius_server";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $this->obj_radius_server->id;
		$this->obj_form->add_input($structure);
			
		// submit section
		$structure = NULL;
		$structure["fieldname"] 	= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "Save Changes";
		$this->obj_form->add_input($structure);
		
		
		// define subforms
		$this->obj_form->subforms["server_details"]	= array("server_name", "server_description");
		$this->obj_form->subforms["server_api"]		= array("api_auth_key");
		$this->obj_form->subforms["server_status"]	= array("sync_status_config", "sync_status_log");
		$this->obj_form->subforms["hidden"]		= array("id_radius_server");
		$this->obj_form->subforms["submit"]		= array("submit");


		// import data
		if (error_check())
		{
			$this->obj_form->load_data_error();
		}
		else
		{
			if ($this->obj_radius_server->load_data())
			{
				$this->obj_form->structure["server_name"]["defaultvalue"]		= $this->obj_radius_server->data["server_name"];
				$this->obj_form->structure["server_description"]["defaultvalue"]	= $this->obj_radius_server->data["server_description"];
				$this->obj_form->structure["api_auth_key"]["defaultvalue"]		= $this->obj_radius_server->data["api_auth_key"];

				if ($this->obj_radius_server->data["sync_status_config"])
				{
					if ($this->obj_radius_server->data["sync_status_config"] == 1)
					{
						$this->obj_form->structure["sync_status_config"]["defaultvalue"]		= "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span> Configuration has never been synced!";
					}
					else
					{
						$this->obj_form->structure["sync_status_config"]["defaultvalue"]	= "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span> Last synced on ". time_format_humandate($this->obj_radius_server->data["api_sync_config"]) ." ". date("H:i:s", $this->obj_radius_server->data["api_sync_log"]) ."";
					}
				}
				else
				{
					$this->obj_form->structure["sync_status_config"]["defaultvalue"]	= "<span class=\"table_highlight_open\">". lang_trans("status_synced") ."</span>";
				}


				if ($this->obj_radius_server->data["sync_status_log"])
				{
					if ($this->obj_radius_server->data["sync_status_log"] == 1)
					{
						$this->obj_form->structure["sync_status_log"]["defaultvalue"]		= "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span> No logs exist for this server!";
					}
					else
					{
						$this->obj_form->structure["sync_status_log"]["defaultvalue"]		= "<span class=\"table_highlight_important\">". lang_trans("status_unsynced") ."</span> Logging appears stale, last synced on ". time_format_humandate($this->obj_radius_server->data["api_sync_log"]) ." ". date("H:i:s", $this->obj_radius_server->data["api_sync_log"]) ."";
					}
				}
				else
				{
					$this->obj_form->structure["sync_status_log"]["defaultvalue"]		= "<span class=\"table_highlight_open\">". lang_trans("status_synced") ."</span> Last log message delivered on ". time_format_humandate($this->obj_radius_server->data["api_sync_log"]) ." ". date("H:i:s", $this->obj_radius_server->data["api_sync_log"]) ."";
				}
			}
		}
	}


	function render_html()
	{
		// title + summary
		print "<h3>SERVER CONFIGURATION</h3><br>";
		print "<p>This page allows you to view and adjust the server configuration.</p>";

	
		// display the form
		$this->obj_form->render_form();
	}

}

?>
