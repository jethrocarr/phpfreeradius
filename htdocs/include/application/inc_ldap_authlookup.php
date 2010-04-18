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

		Fetches an array of all the available groups from the LDAP database and saves into $this->data
	*/

	function list_groups()
	{
		log_write("debug", "ldap_auth_lookup", "Executing list_groups()");
	
		// clear data
		$this->data = array();

		// get a list of all the groups
		$this->obj_ldap->srvcfg["base_dn"] = "ou=Group,". $GLOBALS["config"]["ldap_dn"];

		if ($this->obj_ldap->search("cn=*", array("cn")))
		{
			foreach ($this->obj_ldap->data as $data_group)
			{
				if ($data_group["cn"][0])
				{
					$this->data[] = $data_group["cn"][0];
				}
			}

		}

	} // end of list_groups
	

} // end of class: ldap_auth_lookup

