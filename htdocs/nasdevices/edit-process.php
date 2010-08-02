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
	$obj_nas_device->data["nas_shortname"]			= security_form_input_predefined("any", "nas_shortname", 0, "");
	$obj_nas_device->data["nas_type"]			= security_form_input_predefined("int", "nas_type", 1, "");
	$obj_nas_device->data["nas_description"]		= security_form_input_predefined("any", "nas_description", 0, "");
	$obj_nas_device->data["nas_secret"]			= security_form_input_predefined("any", "nas_secret", 1, "");
	$obj_nas_device->data["nas_ldapgroup"]			= security_form_input_predefined("any", "nas_ldapgroup", 1, "");


	// shortname
	if (empty($obj_nas_device->data["nas_shortname"]))
	{
		// auto-generate by taking the host portion of the name

		$tmp = explode(".", $obj_nas_device->data["nas_hostname"]);

		$obj_nas_device->data["nas_shortname"] = $tmp["0"];
	}


	// address field
	$obj_nas_device->data["nas_address_type"]		= security_form_input_predefined("any", "nas_address_type", 1, "");

	switch ($obj_nas_device->data["nas_address_type"])
	{
		case "ipv4_single":
			$obj_nas_device->data["nas_address"]	= security_form_input_predefined("ipv4", "nas_address_ipv4", 1, "");
		break;

		case "ipv4_range":
			$obj_nas_device->data["nas_address"]	= security_form_input_predefined("ipv4_cidr", "nas_address_ipv4_range", 1, "");
		break;

		case "hostname":
			$obj_nas_device->data["nas_address"]	= security_form_input_predefined("dns_fqdn", "nas_address_ipv4_host", 1, "");
		break;

		default:
			log_write("error", "process", "Invalid address type supplied, this is most likely an application bug.");

			error_flag_field("nas_address_type");
		break;
	}


	// DNS data
	if ($obj_nas_device->data["nas_address_type"] == "ipv4_single")
	{
		$obj_nas_device->data["nas_dns_record_a"]			= security_form_input_predefined("any", "nas_dns_record_a", 0, "");
		$obj_nas_device->data["nas_dns_record_ptr"]			= security_form_input_predefined("any", "nas_dns_record_ptr", 0, "");
		$obj_nas_device->data["nas_dns_record_ptr_altip"]		= security_form_input_predefined("ipv4", "nas_dns_record_ptr_altip", 0, "");
	}


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
		
		switch ($obj_nas_device->data["nas_address_type"])
		{
			case "ipv4_single":
				error_flag_field("nas_address_ipv4");
			break;

			case "ipv4_range":
				error_flag_field("nas_address_ipv4_range");
			break;

			case "hostname":
				error_flag_field("nas_address_host");
			break;
		}
	}


	// ensure that the shortname is valid
	if (!$obj_nas_device->verify_nas_shortname())
	{
		log_write("error", "process", "The requested shortname is already in use by another host");

		error_flag_field("nas_shortname");
	}


	// verify that a valid LDAP group has been selected
	if (!$obj_nas_device->verify_nas_ldapgroup())
	{
		log_write("error", "process", "The requested ldapgroup ". $obj_nas_device->data["nas_ldapgroup"] ." does not seem to exist in this system");

		error_flag_field("nas_ldapgroup");
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


		/*
			Update NAS Device
		*/

		$obj_nas_device->action_update();
		$obj_nas_device->action_update_namedmanager();



		/*
			We wouldn't normally check here, but if anything goes wrong with the API, it's best
			to be able to handle it gracefully
		*/

		if (error_check())
		{
			$_SESSION["error"]["form"]["nas_device_edit"]	= "failed";
			header("Location: ../index.php?page=nasdevices/view.php&id=". $obj_nas_device->id ."");
			exit(0);
		}
		

		// clear error data
		error_clear();


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
