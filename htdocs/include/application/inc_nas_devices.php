<?php
/*
	inc_nas_devices.php

	Provides high-level functions for querying and managing nas devices in the MySQL
	database.
*/




/*
	CLASS NAS_DEVICE

	Provides functions for querying and managing NAS devices in the
	database.
*/
class nas_device
{
	var $id;		// ID of the NAS device to manipulate (if any)
	var $data;



	/*
		verify_id

		Checks that the provided ID is a valid NAS device

		Results
		0	Failure to find the ID
		1	Success - NAS exists
	*/

	function verify_id()
	{
		log_debug("nas_devices", "Executing verify_id()");

		if ($this->id)
		{
			$sql_obj		= New sql_query;
			$sql_obj->string	= "SELECT id FROM `nas_devices` WHERE id='". $this->id ."' LIMIT 1";
			$sql_obj->execute();

			if ($sql_obj->num_rows())
			{
				return 1;
			}
		}

		return 0;

	} // end of verify_id



	/*
		verify_nas_address

		Checks that the nas_address value supplied has not already been taken.

		Results
		0	Failure - address in use
		1	Success - address is available
	*/

	function verify_nas_address()
	{
		log_debug("nas_devices", "Executing verify_nas_address()");

		$sql_obj			= New sql_query;
		$sql_obj->string		= "SELECT id FROM `nas_devices` WHERE nas_address='". $this->data["nas_address"] ."' ";

		if ($this->id)
			$sql_obj->string	.= " AND id!='". $this->id ."'";

		$sql_obj->string		.= " LIMIT 1";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			return 0;
		}
		
		return 1;

	} // end of verify_nas_address



	/*
		verify_nas_hostname

		Verify that the nas_hostname value supplied has not already been taken.

		Results
		0	Failure - name in use
		1	Success - name is available
	*/

	function verify_nas_hostname()
	{
		log_debug("nas_devices", "Executing verify_nas_hostname()");

		$sql_obj			= New sql_query;
		$sql_obj->string		= "SELECT id FROM `nas_devices` WHERE nas_hostname='". $this->data["nas_hostname"] ."' ";

		if ($this->id)
			$sql_obj->string	.= " AND id!='". $this->id ."'";

		$sql_obj->string		.= " LIMIT 1";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			return 0;
		}
		
		return 1;

	} // end of verify_nas_hostname




	/*
		verify_nas_ldapgroup

		Check the LDAP database for the supplied group to verify that it actually exists.

		Results
		0	Failure - name in use
		1	Success - name is available
	*/

	function verify_nas_ldapgroup()
	{
		log_debug("nas_devices", "Executing verify_nas_ldapgroup()");

		$obj_ldap = New ldap_auth_lookup;
		$obj_ldap->list_groups();

		if (!in_array($this->data["nas_ldapgroup"], $obj_ldap->data))
		{
			return 0;
		}
		
		return 1;

	} // end of verify_nas_ldapgroup




	/*
		load_data

		Load the NAS's information into the $this->data array.

		Returns
		0	failure
		1	success
	*/
	function load_data()
	{
		log_debug("nas_devices", "Executing load_data()");

		$sql_obj		= New sql_query;
		$sql_obj->string	= "SELECT * FROM nas_devices WHERE id='". $this->id ."' LIMIT 1";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			$sql_obj->fetch_array();

			$this->data = $sql_obj->data[0];

			return 1;
		}

		// failure
		return 0;

	} // end of load_data




	/*
		action_create

		Create a new NAS based on the data in $this->data

		Results
		0	Failure
		#	Success - return ID
	*/
	function action_create()
	{
		log_debug("nas_devices", "Executing action_create()");

		// create a new NAS
		$sql_obj		= New sql_query;
		$sql_obj->string	= "INSERT INTO `nas_devices` (nas_hostname, nas_address) VALUES ('". $this->data["nas_hostname"]. "', '". $this->data["nas_address"] ."')";
		$sql_obj->execute();

		$this->id = $sql_obj->fetch_insert_id();

		return $this->id;

	} // end of action_create




	/*
		action_update

		Update a NAS's details based on the data in $this->data. If no ID is provided,
		it will first call the action_create function.

		Returns
		0	failure
		#	success - returns the ID
	*/
	function action_update()
	{
		log_debug("nas_devices", "Executing action_update()");


		/*
			Start Transaction
		*/
		$sql_obj = New sql_query;
		$sql_obj->trans_begin();


		/*
			If no ID supplied, create a new NAS first
		*/
		if (!$this->id)
		{
			$mode = "create";

			if (!$this->action_create())
			{
				return 0;
			}
		}
		else
		{
			$mode = "update";
		}



		/*
			Update NAS Details
		*/

		$sql_obj->string	= "UPDATE `nas_devices` SET "
						."nas_hostname='". $this->data["nas_hostname"] ."', "
						."nas_address='". $this->data["nas_address"] ."', "
						."nas_secret='". $this->data["nas_secret"] ."', "
						."nas_type='". $this->data["nas_type"] ."', "
						."nas_ldapgroup='". $this->data["nas_ldapgroup"] ."', "
						."nas_description='". $this->data["nas_description"] ."' "
						."WHERE id='". $this->id ."' LIMIT 1";
		$sql_obj->execute();

		


		/*
			Commit
		*/

		if (error_check())
		{
			$sql_obj->trans_rollback();

			log_write("error", "nas_devices", "An error occured when updating NAS device.");

			return 0;
		}
		else
		{
			$sql_obj->trans_commit();

			if ($mode == "update")
			{
				log_write("notification", "nas_devices", "NAS device has been successfully updated.");
			}
			else
			{
				log_write("notification", "nas_devices", "NAS device successfully created.");
			}
			
			return $this->id;
		}

	} // end of action_update



	/*
		action_delete

		Deletes a NAS device.

		Results
		0	failure
		1	success
	*/
	function action_delete()
	{
		log_debug("nas_device", "Executing action_delete()");

		/*
			Start Transaction
		*/

		$sql_obj = New sql_query;
		$sql_obj->trans_begin();


		/*
			Delete NAS device
		*/
			
		$sql_obj->string	= "DELETE FROM nas_devices WHERE id='". $this->id ."' LIMIT 1";
		$sql_obj->execute();


		/*
			Un-associated any matched log entries
		*/

		$sql_obj->string	= "UPDATE logs SET id_nas='0' WHERE id_nas='". $this->id ."'";
		$sql_obj->execute();


		/*
			Commit
		*/
		
		if (error_check())
		{
			$sql_obj->trans_rollback();

			log_write("error", "nas_device", "An error occured whilst trying to delete the selected NAS.");

			return 0;
		}
		else
		{
			$sql_obj->trans_commit();

			log_write("notification", "nas_device", "NAS has been successfully deleted.");

			return 1;
		}
	}


} // end of class:nas_device



?>
