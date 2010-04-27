<?php
/*
	include/application/inc_ldap_auth_lookup.php

	Functions for quering the LDAP database containing groups and users, used to fetch
	information such as avaliable groups that can be used by the NAS devices.
*/



/*
	CLASS: LDAP_AUTH_LOOKUP

	Functions to query user and group data
*/

class ldap_auth_lookup
{
	var $obj_ldap;		// LDAP object

	var $id;		// ID of the user account to handle
	var $data;


	/*
		Constructor
	*/
	function ldap_auth_lookup()
	{
		/*
			Init LDAP database connection
		*/
		$this->obj_ldap = New ldap_query;

		// connect to LDAP server
		if (!$this->obj_ldap->connect())
		{
			log_write("error", "user_auth", "An error occurred in the authentication backend, please contact your system administrator");
			return -1;
		}
	}




	/*
		list_groups

		Fetches an array of all the available groups from the LDAP database and saves into $this->data. Note that this function
		EXCLUDES any user-only groups and only shows created groups.
	*/

	function list_groups()
	{
		log_write("debug", "ldap_auth_lookup", "Executing list_groups()");
	
		// clear data
		$this->data = array();


		// fetch the GID number for all users, so we can exclude user-only groups
		$this->obj_ldap->srvcfg["base_dn"] = "ou=People,". $GLOBALS["config"]["ldap_dn"];

		if ($this->obj_ldap->search("uid=*", array("gidnumber")))
		{
			// generate user list array
			$user_gid_array	= array();

			foreach ($this->obj_ldap->data as $data_ldap)
			{
				$user_gid_array[]	=  $data_ldap["gidnumber"][0];
			}
		}


		// get a list of all the groups
		$this->obj_ldap->srvcfg["base_dn"] = "ou=Group,". $GLOBALS["config"]["ldap_dn"];

		if ($this->obj_ldap->search("cn=*", array("cn", "gidnumber")))
		{
			foreach ($this->obj_ldap->data as $data_group)
			{
				if ($data_group["cn"][0])
				{
					if (!in_array($data_group["gidnumber"][0], $user_gid_array))
					{
						$this->data[] = $data_group["cn"][0];
					}
				}
			}
		}


	} // end of list_groups
	

} // end of class: ldap_auth_lookup

