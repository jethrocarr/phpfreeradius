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
		verify_nas_shortname

		Verify that the nas_shortname value supplied has not already been taken.

		Results
		-1	Failure - name is too long
		0	Failure - name in use
		1	Success - name is available
	*/

	function verify_nas_shortname()
	{
		log_debug("nas_devices", "Executing verify_nas_shortname()");


		// check shortname
		$sql_obj			= New sql_query;
		$sql_obj->string		= "SELECT id FROM `nas_devices` WHERE nas_shortname='". $this->data["nas_shortname"] ."' ";

		if ($this->id)
			$sql_obj->string	.= " AND id!='". $this->id ."'";

		$sql_obj->string		.= " LIMIT 1";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			log_write("debug", "nas_devices", "Shortname is already inuse!");

			return 0;
		}


		// check length
		if (count($this->data["nas_shortname"]) > 30)
		{
			log_write("debug", "nas_devices", "Shortname is too long");

			return -1;
		}
		
		return 1;

	} // end of verify_nas_shortname




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


			// not all hosts will have a shortname if they were added before version
			// 1.1.0, if they don't, then set the shortname to the hostname

			if (!$this->data["nas_shortname"])
			{
				$this->data["nas_shortname"] = $this->data["nas_hostname"];
			}


			// fetch any/all stationids for the selected NAS
			$sql_obj		= New sql_query;
			$sql_obj->string	= "SELECT station_id, nas_ldapgroup FROM nas_stationid WHERE id_nas='". $this->id ."'";
			$sql_obj->execute();

			if ($sql_obj->num_rows())
			{
				$sql_obj->fetch_array();

				$this->data["stationids"] = array();

				foreach ($sql_obj->data as $data_sql)
				{
					$nas_station = array();

					$nas_station["stationid"]			= $data_sql["station_id"];
					$nas_station["ldapgroup"]			= $data_sql["nas_ldapgroup"];

					$this->data["stationids"][]			= $nas_station;
				}
			}

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
						."nas_shortname='". $this->data["nas_shortname"] ."', "
						."nas_address='". $this->data["nas_address"] ."', "
						."nas_secret='". $this->data["nas_secret"] ."', "
						."nas_type='". $this->data["nas_type"] ."', "
						."nas_ldapgroup='". $this->data["nas_ldapgroup"] ."', "
						."nas_description='". $this->data["nas_description"] ."', "
						."nas_dns_record_ptr_altip='". $this->data["nas_dns_record_ptr_altip"] ."' "
						."WHERE id='". $this->id ."' LIMIT 1";
		$sql_obj->execute();



		/*
			Update NAS stationid records (if any)
		*/

		$sql_obj		= New sql_query;
		$sql_obj->string	= "DELETE FROM `nas_stationid` WHERE id_nas='". $this->id ."'";
		$sql_obj->execute();

		if (is_array($this->data["stationids"]))
		{
			foreach ($this->data["stationids"] as $nas_station)
			{
				$sql_obj->string	= "INSERT INTO `nas_stationid` (id_nas, station_id, nas_ldapgroup) VALUES ('". $this->id ."', '". $nas_station["stationid"]  ."', '". $nas_station["ldapgroup"] ."')";
				$sql_obj->execute();
			}
		}



		/*
			Update Configuration Version
		*/
		
		$sql_obj		= New sql_query;
		$sql_obj->string	= "UPDATE `config` SET value='". time() ."' WHERE name='SYNC_STATUS_CONFIG' LIMIT 1";
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
		action_update_namedmanager

		If NamedManager is enabled, this function will update the DNS entry for this NAS device.

		Returns
		0	Failure
		1	Success

	*/

	function action_update_namedmanager()
	{
		log_write("debug", "nas_device", "Executing action_update_namedmanager()");


		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"])
		{
			$data_tmp = array();


			/*
				Fetch IDs for records (if any)
			*/

			$sql_tmp_obj		= New sql_query;
			$sql_tmp_obj->string	= "SELECT nas_dns_record_a, nas_dns_record_ptr FROM `nas_devices` WHERE id='". $this->id ."' LIMIT 1";
			$sql_tmp_obj->execute();

			if ($sql_tmp_obj->num_rows())
			{
				$sql_tmp_obj->fetch_array();

				$data_tmp["id_record_a"]	= $sql_tmp_obj->data[0]["nas_dns_record_a"];
				$data_tmp["id_record_ptr"]	= $sql_tmp_obj->data[0]["nas_dns_record_ptr"];
			}
			else
			{
				$data_tmp["id_record_a"]	= 0;
				$data_tmp["id_record_ptr"]	= 0;
			}



			/*
				Determine IP
			*/

			if ($this->data["nas_dns_record_ptr_altip"])
			{
				$data_tmp["ipaddress"] = $this->data["nas_dns_record_ptr_altip"];
			}
			else
			{
				$data_tmp["ipaddress"] = $this->data["nas_address"];
			}

			

			/*
				Update A records
			*/

			if ($this->data["nas_dns_record_a"])
			{
				log_write("debug", "nas_device", "Creating/updating DNS record for ". $this->data["nas_hostname"] ."");


				$obj_named	= New namedmanager;

				if (!$obj_named->authenticate())
				{
					log_write("debug", "nas_device", "Failure to update NAS DNS records due to API authentication failure");
					return 0;
				}


				// pull domain from FQDN
				if (preg_match("/^([A-Za-z0-9-]*)\.(\S*)$/", $this->data["nas_hostname"], $matches))
				{
					$data_tmp["hostname"]	= $matches[1];
					$data_tmp["domain"]	= $matches[2];

					log_write("debug", "nas_device", "NAS device has hostname of ". $data_tmp["hostname"] ." and domain name of ". $data_tmp["domain"] ."");
				}
				else
				{
					log_write("error", "nas_device", "Unable to process DNS record, NAS name ". $this->data["nas_hostname"] ." does not appear to be a valid FQDN.");
					return 0;
				}


				// check if the domain exists - if it doesn't, we can't do much with the record
				$data_domains = $obj_named->fetch_domains();

				foreach ($data_domains as $data_domain)
				{
					if ($data_domain["domain_name"] == $data_tmp["domain"])
					{
						$data_tmp["valid"]	= 1;
						$data_tmp["id_domain"]	= $data_domain["id"];
					}
				}

				if (!$data_tmp["valid"])
				{
					log_write("error", "nas_device", "Unable to create a DNS record for ". $this->data["nas_hostname"] ." since domain ". $data_tmp["domain"] ." does not exist in NamedManager");
					return 0;
				}
				else
				{
					// update the record
					$data_tmp["id_record_a"] = $obj_named->update_record($data_tmp["id_domain"],
									$data_tmp["id_record_a"],
									$data_tmp["hostname"],
									"A",
									$data_tmp["ipaddress"],
									"",
									"");


					// update domain serial
					$obj_named->update_serial($data_tmp["id_domain"]);

					// update DB with ID
					$sql_obj		= New sql_query;
					$sql_obj->string	= "UPDATE `nas_devices` SET nas_dns_record_a='". $data_tmp["id_record_a"] ."' WHERE id='". $this->id ."' LIMIT 1";
					$sql_obj->execute();
				}
			}



			if ($this->data["nas_dns_record_ptr"])
			{
				// update PTR record
				log_write("debug", "nas_device", "Creating/updating DNS record for ". $this->data["nas_hostname"] ."");


				$obj_named	= New namedmanager;

				if (!$obj_named->authenticate())
				{
					log_write("debug", "nas_device", "Failure to update NAS DNS records due to API authentication failure");
					return 0;
				}



				// convert the IP into an arpa-style domain so we can verify it exists.
				$data_tmp["domain"]		= ipv4_convert_arpa($data_tmp["ipaddress"]);


				// check if the domain exists - if it doesn't, we can't do much with the record
				$data_tmp["valid"] = 0;

				$data_domains = $obj_named->fetch_domains();

				foreach ($data_domains as $data_domain)
				{
					if ($data_domain["domain_name"] == $data_tmp["domain"])
					{
						$data_tmp["valid"]	= 1;
						$data_tmp["id_domain"]	= $data_domain["id"];
					}
				}

				if (!$data_tmp["valid"])
				{
					log_write("error", "nas_device", "Unable to create a reverse/PTR DNS record for ". $data_tmp["ipaddress"] ." since domain ". $data_tmp["domain"] ." does not exist in NamedManager");
					return 0;
				}
				else
				{
					// get host porition of the IP
					$tmp			= explode(".", $data_tmp["ipaddress"]);
					$data_tmp["ipaddress"]	= $tmp[3];


					// update the record
					$data_tmp["id_record_ptr"] = $obj_named->update_record($data_tmp["id_domain"],
									$data_tmp["id_record_ptr"],
									$data_tmp["ipaddress"],
									"PTR",
									$this->data["nas_hostname"],
									"",
									"");


					// update domain serial
					$obj_named->update_serial($data_tmp["id_domain"]);

					// update DB with ID
					$sql_obj		= New sql_query;
					$sql_obj->string	= "UPDATE `nas_devices` SET nas_dns_record_ptr='". $data_tmp["id_record_ptr"] ."' WHERE id='". $this->id ."' LIMIT 1";
					$sql_obj->execute();
				}
			}
		}


		log_write("notification", "nas_device", "Updated DNS records for NAS device in NamedManager");

		return 1;

	} // end of action_update_namedmanager



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
			Delete NAS device Called-Station-IDs
		*/

		$sql_obj->string	= "DELETE FROM nas_stationid WHERE id_nas='". $this->id ."'";
		$sql_obj->execute();


		/*
			Un-associated any matched log entries
		*/

		$sql_obj->string	= "UPDATE logs SET id_nas='0' WHERE id_nas='". $this->id ."'";
		$sql_obj->execute();


		/*
			Update Configuration Version
		*/
		$sql_obj->string	= "UPDATE `config` SET value='". time() ."' WHERE name='SYNC_STATUS_CONFIG' LIMIT 1";
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






/*
	ui_nas_device

	NAS device configuration form logic which has been functionised to prevent code
	duplication within the application

*/

class ui_nas_device
{
	var $obj_form;		// form object


	/*
		ui_form

		Generate the form to be used for NAS device configuration
	*/

	function ui_form()
	{
		log_write("debug", "ui_radius_attributes", "Executing ui_form()");


		/*
			Define form structure
		*/
		$this->obj_form			= New form_input;

		// note: we do not define the form attributes here, we leave the actual render page for that.



		/*
			General NAS Configuration
		*/

		$structure = NULL;
		$structure["fieldname"] 		= "nas_hostname";
		$structure["type"]			= "input";
		$structure["options"]["req"]		= "yes";
		$structure["options"]["label"]		= " ". lang_trans("help_nas_hostname");
		$this->obj_form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] 		= "nas_shortname";
		$structure["type"]			= "input";
		$structure["options"]["req"]		= "yes";
		$structure["options"]["max_length"]	= 30;
		//$structure["options"]["width"]	= 250;
		$structure["options"]["help"]		= lang_trans("help_nas_shortname_inline");
		$structure["options"]["label"]		= " ". lang_trans("help_nas_shortname");
		$this->obj_form->add_input($structure);


		$structure = NULL;
		$structure["fieldname"] 	= "nas_address_type";
		$structure["type"]		= "radio";
		$structure["values"]		= array("ipv4_single", "ipv4_range", "hostname");
		$structure["options"]["req"]	= "yes";
		$structure["defaultvalue"]	= "ipv4_single";
		$this->obj_form->add_input($structure);
							

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_ipv4_range";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_ipv4_range");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_host";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_host");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_address_ipv4";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["options"]["label"]	= " ". lang_trans("help_nas_address_ipv4");
		$this->obj_form->add_input($structure);


		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_ipv4_range", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_host", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_address_ipv4", "show");

		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_na", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_a", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_ptr", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_single", "nas_dns_record_ptr_altip", "show");

		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_ipv4_range", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_host", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_address_ipv4", "hide");

		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_na", "show");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_a", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_ptr", "hide");
		$this->obj_form->add_action("nas_address_type", "ipv4_range", "nas_dns_record_ptr_altip", "hide");

		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_ipv4_range", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_host", "show");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_address_ipv4", "hide");
	
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_na", "show");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_a", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_ptr", "hide");
		$this->obj_form->add_action("nas_address_type", "hostname", "nas_dns_record_ptr_altip", "hide");


		$structure = form_helper_prepare_dropdownfromdb("nas_type", "SELECT id, nas_type as label FROM nas_types ORDER BY nas_type");
		$structure["options"]["req"]	= "yes";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_description";
		$structure["type"]		= "textarea";
		$this->obj_form->add_input($structure);



		/*
			NamedManager DNS Integration
		*/
		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"] == "enabled")
		{
			$structure = NULL;
			$structure["fieldname"]			= "nas_dns_record_na";
			$structure["type"]			= "message";
			$structure["defaultvalue"]		= "<i>". lang_trans("help_nas_dns_record_na") ."</i>";

// TODO: why does this CSS break the javascript show/hide stuff?
//			$structure["options"]["css_row_class"]	= "table_highlight_info";

			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_a";
			$structure["type"]		= "checkbox";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_a");
			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_ptr";
			$structure["type"]		= "checkbox";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_ptr");
			$this->obj_form->add_input($structure);

			$structure = NULL;
			$structure["fieldname"]		= "nas_dns_record_ptr_altip";
			$structure["type"]		= "input";
			$structure["options"]["label"]	= " ". lang_trans("help_nas_dns_record_ptr_altip");
			$this->obj_form->add_input($structure);
		}



		/*
			NAS Authentication
		*/

		$structure = NULL;
		$structure["fieldname"] 	= "nas_secret";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$structure["defaultvalue"]	= $GLOBALS["config"]["DEFAULT_NAS_PASSWORD"];
		$this->obj_form->add_input($structure);
	


		/*
			Called Station ID Identification

			This section of the interface allows the user to configure the LDAP group
			assignment on a Called-Station-ID basis, which allows one NAS with multiple
			different authentication sections to validate users against LDAP.
		*/

		// lookup LDAP users/groups
		$obj_ldap = New ldap_auth_lookup;
		$obj_ldap->list_groups();


		// descriptive field
		$structure = NULL;
		$structure["fieldname"]			= "nas_station_description";
		$structure["type"]			= "message";
		$structure["defaultvalue"]		= "<p>". lang_trans("nas_station_description") ."</p>";
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]			= "nas_station_description2";
		$structure["type"]			= "message";
		$structure["defaultvalue"]		= "<p>". lang_trans("nas_station_description2") ."</p>";
		$this->obj_form->add_input($structure);



		// called station ID authentication
		if ($_SESSION["error"]["num_stationids"])
		{
			$num_stationid  = $_SESSION["error"]["num_stationids"];
		}
		elseif ($this->obj_nas_device->id)
		{
			$sql_obj		= New sql_query;
			$sql_obj->string	= "SELECT id FROM nas_stationid WHERE id_nas='". $this->obj_nas_device->id ."'";
			$sql_obj->execute();

			$num_stationid = $sql_obj->num_rows();
		}

		if ($num_stationid < 1)
		{
			$num_stationid = 1;
		}

		$structure = NULL;
		$structure["fieldname"]		= "num_stationids";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $num_stationid;
		$this->obj_form->add_input($structure);


		// The first record is a special meta-entry which maps to the default LDAP group value
		$structure = NULL;
		$structure["fieldname"]		= "nas_station_0_stationid";
		$structure["type"]		= "text";
		$structure["defaultvalue"]	= "Default LDAP Group for Authentication";
		$this->obj_form->add_input($structure);

		$structure = NULL;

		foreach ($obj_ldap->data as $group_name)
		{
			$structure["values"][]	= $group_name;
		}

		$structure["fieldname"] 		= "nas_station_0_ldapgroup";
		$structure["type"]			= "dropdown";
		$structure["options"]["req"]		= "yes";
		$this->obj_form->add_input($structure);


		// structure form row
		$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_0"][]		= "nas_station_0_stationid";
		$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_0"][]		= "nas_station_0_ldapgroup";


		// header
		$structure = NULL;
		$structure["fieldname"]		= "nas_station_header_stationid";
		$structure["type"]		= "text";
		$structure["defaultvalue"]	= lang_trans("header_stationid");
		$this->obj_form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"]		= "nas_station_header_ldapgroup";
		$structure["type"]		= "text";
		$structure["defaultvalue"]	= lang_trans("header_ldap_group");
		$this->obj_form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"]		= "nas_station_header_controls";
		$structure["type"]		= "text";
		$structure["defaultvalue"]	= "";
		$this->obj_form->add_input($structure);

		$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_header"][]		= "nas_station_header_stationid";
		$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_header"][]		= "nas_station_header_ldapgroup";
		$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_header"][]		= "nas_station_header_controls";


		// user-added entries
		for ($i=1; $i <= $num_stationid; $i++)
		{
			// Called Station ID
			$structure = NULL;
			$structure["fieldname"]			= "nas_station_". $i ."_stationid";
			$structure["type"]			= "input";
			$structure["options"]["width"]		= "300";
			$this->obj_form->add_input($structure);

			// LDAP group
			$structure = NULL;

			foreach ($obj_ldap->data as $group_name)
			{
				$structure["values"][]	= $group_name;
			}

			$structure["fieldname"] 		= "nas_station_". $i ."_ldapgroup";
			$structure["type"]			= "dropdown";
			$this->obj_form->add_input($structure);

			// controls link
			$structure = NULL;
			$structure["fieldname"]		= "nas_station_". $i ."_controls";
			$structure["type"]		= "message";
			$structure["defaultvalue"]	= "<input name=\"nas_station_". $i ."_delete_undo\" type=\"hidden\" value=\"false\"><strong class=\"delete_undo\"><a href=\"\" class=\"button_small\">delete</a></strong></input>";
			$this->obj_form->add_input($structure);


			// structure form row
			$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_$i"][]		= "nas_station_". $i ."_stationid";
			$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_$i"][]		= "nas_station_". $i ."_ldapgroup";
			$this->obj_form->subforms_grouped["nas_auth_users"]["nas_station_$i"][]		= "nas_station_". $i ."_controls";
		}


		$structure = NULL;
		$structure["fieldname"]		= "nas_station_addbutton";
		$structure["type"]		= "message";
		$structure["defaultvalue"]	= "<strong class=\"add_stationid\"><a href=\"\" class=\"button_small\">Add Called-Station-ID Access Mapping</a></strong>";
		$this->obj_form->add_input($structure);
		


		// hidden section
		$structure = NULL;
		$structure["fieldname"] 	= "id_nas";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $this->obj_nas_device->id;
		$this->obj_form->add_input($structure);
			
		// submit section
		$structure = NULL;
		$structure["fieldname"] 	= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "Save Changes";
		$this->obj_form->add_input($structure);
		
		
		// define subforms
		$this->obj_form->subforms["nas_details"]	= array("nas_hostname", "nas_shortname", "nas_address_type", "nas_address_ipv4", "nas_address_host", "nas_address_ipv4_range", "nas_type", "nas_description");

		if ($GLOBALS["config"]["NAMEDMANAGER_FEATURE"] == "enabled")
		{
			$this->obj_form->subforms["nas_dns"]	= array("nas_dns_record_na", "nas_dns_record_a", "nas_dns_record_ptr", "nas_dns_record_ptr_altip");
		}

		$this->obj_form->subforms["nas_auth"]				= array("nas_secret");
		$this->obj_form->subforms["nas_auth_users"][]			= "nas_station_description";
		$this->obj_form->subforms["nas_auth_users"][]			= "nas_station_0";
		$this->obj_form->subforms["nas_auth_users"][]			= "nas_station_description2";
		$this->obj_form->subforms["nas_auth_users"][]			= "nas_station_header";

		for ($i=1; $i <= $num_stationid; $i++)
		{
			$this->obj_form->subforms["nas_auth_users"][]		= "nas_station_". $i;
		}

		$this->obj_form->subforms["nas_auth_users"][]			= "nas_station_addbutton";


		$this->obj_form->subforms["hidden"]		= array("id_nas", "num_stationids");
		$this->obj_form->subforms["submit"]		= array("submit");

	} // end of ui_form()

} // end of class: ui_nas_device


?>
