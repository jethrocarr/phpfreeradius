<?php
/*
	nasdevices/delete-process.php

	access:
		radiusadmins

	Deletes an unwanted NAS device
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

	$obj_nas_device			= New nas_device;
	$obj_nas_device->id		= security_form_input_predefined("int", "id_nas", 0, "");


	// for error return if needed
	@security_form_input_predefined("any", "nas_hostname", 1, "");
	@security_form_input_predefined("any", "nas_address", 1, "");
	@security_form_input_predefined("any", "nas_description", 0, "");

	// confirm deletion
	@security_form_input_predefined("any", "delete_confirm", 1, "You must confirm the deletion");




	/*
		Verify Data
	*/


	// verify the selected NAS exists
	if (!$obj_nas_device->verify_id())
	{
		log_write("error", "process", "The NAS you have attempted to delete - ". $obj_nas_device->id ." - does not exist in this system.");
	}




	/*
		Process Data
	*/

	if (error_check())
	{
		$_SESSION["error"]["form"]["nas_device_delete"]	= "failed";
		header("Location: ../index.php?page=nasdevices/delete.php&id=". $obj_nas_device->id ."");

		exit(0);
	}
	else
	{
		// clear error data
		error_clear();



		/*
			Delete NAS Device
		*/

		$obj_nas_device->action_delete();



		/*
			Return
		*/

		header("Location: ../index.php?page=nasdevices/nasdevices.php");
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
