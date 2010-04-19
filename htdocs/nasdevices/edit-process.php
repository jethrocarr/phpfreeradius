<?php
/*
	nasdevices/edit-process.php

	access:
		radiusadmins

	Updates or creates a NAS based on the input given to it.
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


	// are we editing an existing NAS or adding a new one?
	if ($obj_nas_device->id)
	{
		if (!$obj_nas_device->verify_id())
		{
			log_write("error", "process", "The NAS you have attempted to edit - ". $obj_nas_device->id ." - does not exist in this system.");
		}
		else
		{
			// load existing data
			$obj_nas_device->load_data();
		}
	}

	// basic fields
	$obj_nas_device->data["nas_hostname"]			= security_form_input_predefined("any", "nas_hostname", 1, "");
	$obj_nas_device->data["nas_address"]			= security_form_input_predefined("any", "nas_address", 1, "");
	$obj_nas_device->data["nas_type"]			= security_form_input_predefined("int", "nas_type", 1, "");
	$obj_nas_device->data["nas_description"]		= security_form_input_predefined("any", "nas_description", 0, "");
	$obj_nas_device->data["nas_secret"]			= security_form_input_predefined("any", "nas_secret", 1, "");
	$obj_nas_device->data["nas_ldapgroup"]			= security_form_input_predefined("any", "nas_ldapgroup", 1, "");




	/*
		Verify Data
	*/


	// ensure the name/address of this NAS has not been used before
	if (!$obj_nas_device->verify_nas_hostname())
	{
		log_write("error", "process", "The requested hostname is already in use by another NAS, perhaps you are trying to add a NAS that has already been configured?");

		error_flag_field("nas_hostname");
	}

	if (!$obj_nas_device->verify_nas_address())
	{
		log_write("error", "process", "The requested address is already in use by another NAS, perhaps you are trying to add a NAS that has already been configured?");

		error_flag_field("nas_address");
	}


	// verify that a valid LDAP group has been selected
	if (!$obj_nas_device->verify_nas_ldapgroup())
	{
		log_write("error", "process", "The requested ldapgroup ". $obj_nas_device->data["nas_ldapgroup"] ." does not seem to exist in this system");

		error_flag_field("nas_ldapgroup");
	}


	// check the address format - can be one of three types:
	//	- single IP
	//	- subnet with CIDR notation
	//	- hostname

	$expressions	= array();
	$expressions[]	= "/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/";			// single IP
	$expressions[]	= "/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}\/[0-9]*$/";		// CIDR notation subnet
	$expressions[]	= "/^[a-zA-Z][a-zA-Z0-9.-]*$/";											// hostname

	$match		= 0;

	foreach ($expressions as $regex)
	{
		if (preg_match($regex, $obj_nas_device->data["nas_address"]))
		{
			$match = 1;
		}
	}

	if (!$match)
	{
		// address did not match any known format
		log_write("error", "process", "The supplied address is invalid - either a single IP (127.0.0.1), subnet (192.168.0.0/24) or hostname (host1.example.com) must be supplied as an address");

		error_flag_field("nas_address");
	}



	/*
		Process Data
	*/

	if (error_check())
	{
		if ($obj_nas_device->id)
		{
			$_SESSION["error"]["form"]["nas_device_edit"]	= "failed";
			header("Location: ../index.php?page=nasdevices/view.php&id=". $obj_nas_device->id ."");
		}
		else
		{
			$_SESSION["error"]["form"]["nas_device_edit"]	= "failed";
			header("Location: ../index.php?page=nasdevices/add.php");
		}

		exit(0);
	}
	else
	{
		// clear error data
		error_clear();


		/*
			Update NAS Device
		*/

		$obj_nas_device->action_update();


		/*
			Return
		*/

		header("Location: ../index.php?page=nasdevices/view.php&id=". $obj_nas_device->id ."");
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
