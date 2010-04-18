<?php
/*
	servers/edit-process.php

	access:
		radiusadmins

	Updates or creates a new radius server entry.
*/


// includes
require("../include/config.php");
require("../include/amberphplib/main.php");
require("../include/application/main.php");


if (user_permissions_get('radiusadmins'))
{
	/*
		Form Input
	*/

	$obj_radius_server		= New radius_server;
	$obj_radius_server->id		= security_form_input_predefined("int", "id_radius_server", 0, "");


	// are we editing an existing server or adding a new one?
	if ($obj_radius_server->id)
	{
		if (!$obj_radius_server->verify_id())
		{
			log_write("error", "process", "The radius server you have attempted to edit - ". $obj_radius_server->id ." - does not exist in this system.");
		}
		else
		{
			// load existing data
			$obj_radius_server->load_data();
		}
	}

	// basic fields
	$obj_radius_server->data["server_name"]			= security_form_input_predefined("any", "server_name", 1, "");
	$obj_radius_server->data["server_description"]		= security_form_input_predefined("any", "server_description", 0, "");
	$obj_radius_server->data["api_auth_key"]		= security_form_input_predefined("any", "api_auth_key", 1, "");




	/*
		Verify Data
	*/

	// ensure the server name is unique
	if (!$obj_radius_server->verify_server_name())
	{
		log_write("error", "process", "The requested server name already exists, have you checked that the server you're trying to add doesn't already exist?");

		error_flag_field("server_name");
	}


	/*
		Process Data
	*/

	if (error_check())
	{
		if ($obj_radius_server->id)
		{
			$_SESSION["error"]["form"]["radius_server_edit"]	= "failed";
			header("Location: ../index.php?page=servers/view.php&id=". $obj_radius_server->id ."");
		}
		else
		{
			$_SESSION["error"]["form"]["radius_server_edit"]	= "failed";
			header("Location: ../index.php?page=servers/add.php");
		}

		exit(0);
	}
	else
	{
		// clear error data
		error_clear();


		/*
			Update radius server
		*/

		$obj_radius_server->action_update();


		/*
			Return
		*/

		header("Location: ../index.php?page=servers/view.php&id=". $obj_radius_server->id ."");
		exit(0);


	} // if valid data input
	
	
} // end of "is user logged in?"
else
{
	// user does not have permissions to access this page.
	error_render_noperms();
	header("Location: ../index.php?page=message.php");
	exit(0);
}


?>
