<?php
/*
	servers/delete-process.php

	access:
		radiusadmins

	Deletes an unwanted server.
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


	// for error return if needed
	@security_form_input_predefined("any", "server_name", 1, "");
	@security_form_input_predefined("any", "server_description", 0, "");

	// confirm deletion
	@security_form_input_predefined("any", "delete_confirm", 1, "You must confirm the deletion");




	/*
		Verify Data
	*/


	// verify the selected server exists
	if (!$obj_radius_server->verify_id())
	{
		log_write("error", "process", "The server you have attempted to delete - ". $obj_radius_server->id ." - does not exist in this system.");
	}




	/*
		Process Data
	*/

	if (error_check())
	{
		$_SESSION["error"]["form"]["radius_server_delete"]	= "failed";
		header("Location: ../index.php?page=servers/delete.php&id=". $obj_radius_server->id ."");

		exit(0);
	}
	else
	{
		// clear error data
		error_clear();



		/*
			Delete server
		*/

		$obj_radius_server->action_delete();



		/*
			Return
		*/

		header("Location: ../index.php?page=servers/servers.php");
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
