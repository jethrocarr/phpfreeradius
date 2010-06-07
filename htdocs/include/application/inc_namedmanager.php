<?php
/*
	include/application/inc_namedmanager.php

	Provides functions for talking to the NamedManager SOAP API for manipulating
	DNS rentries for the selected NAS device.
*/


class namedmanager
{
	var $client;

	var $api_url;
	var $api_key;


	/*
		constructor
	*/

	function namedmanager()
	{
		log_write("debug", "namedmanager", "Executing namedmanager() [contructor]");

		$this->api_url	= $GLOBALS["config"]["NAMEDMANAGER_API_URL"];
		$this->api_key	= $GLOBALS["config"]["NAMEDMANAGER_API_KEY"];
	}



	/*
		authenticate

		Connects to the NamedManager API and uses the API key to authenticate

		Returns
		0		Failure
		1		Success
	*/
	function authenticate()
	{
		log_write("debug", "namedmanager", "Executing authenticate()");


		/*
			Initiate connection & authenticate with NamedManager

		*/
		$this->client = new SoapClient($this->api_url ."/api/namedmanager.wsdl");
		$this->client->__setLocation($this->api_url ."/api/namedmanager.php");


		// login & get PHP session ID
		try
		{
			log_write("debug", "namedmanager", "Authenticating with API as DNS server...");

			if ($this->client->authenticate("ADMIN_API", $this->api_key))
			{
				log_write("debug", "namedmanager", "Authentication successful");

				return 1;
			}

		}
		catch (SoapFault $exception)
		{
			if ($exception->getMessage() == "ACCESS_DENIED")
			{
				log_write("error", "namedmanager", "Access denied whilst attempting to access NamedManager, verify that API KEY is correct.");

				return 0;
			}
			else
			{	
				log_write("error", "namedmanager", "Unknown failure whilst attempting to authenticate with the API - ". $exception->getMessage() ."");

				return 0;
			}
		}
	}


	/*
		update_serial

		Instruct NamedManager to update the serial of the domain
	
		Fields
		id_domain	ID of the domain name

		Returns
		0		Unexpected error occured
		#		Serial of the domain
	*/
	
	function update_serial ( $id_domain )
	{
		log_write("debug", "named_manager", "Executing update_serial($id_domain)");

		try
		{
			$serial = $this->client->update_serial($id_domain);

			if (!$serial)
			{
				log_write("debug", "namedmanager", "An error occured whilst communicating with the SOAP API update_serial function");
			}
			else
			{
				return $serial;
			}
		}
		catch (SoapFault $exception)
		{
			log_write("error", "namedmanager", "An unexpected error occured ". $exception->getMessage() ."");
		}

		return 0;

	} // end of update_serial



	/*
		update_record

		Update or create a record in NamedManager
	
		Fields
		id_domain	ID of the domain name
		id_record	(optional) ID of the record
		record_name
		record_type
		record_content
		record_ttl
		record_prio

		Returns
		0		Unexpected error occured
		#		Serial of the domain
	*/
	
	function update_record ($id_domain, $id_record, $record_name, $record_type, $record_content, $record_ttl, $record_prio)
	{
		log_write("debug", "named_manager", "Executing update_record($id_domain, $id_record, $record_name, $record_type, $record_content, $record_ttl, $record_prio)");

		try
		{
			$id_record = $this->client->update_record($id_domain, $id_record, $record_name, $record_type, $record_content, $record_ttl, $record_prio);

			if (!$id_record)
			{
				log_write("debug", "namedmanager", "An error occured whilst communicating with the SOAP API update_record function");
			}
			else
			{
				return $id_record;
			}
		}
		catch (SoapFault $exception)
		{
			log_write("error", "namedmanager", "An unexpected error occured ". $exception->getMessage() ."");
		}

		return 0;

	} // end of update_record



	/*
		fetch_domains

		Fetches the domain details including name and SOA serial. Provides enough information to
		enable the application to decide if we should query all the records for generating the
		configuration file

		Returns
		array		Array of all the domains
	*/

	function fetch_domains()
	{
		log_write("debug", "soap_api", "Executing fetch_domains()");

		try
		{
			$domains = $this->client->fetch_domains();
		}
		catch (SoapFault $exception)
		{
			if ($exception->getMessage() == "ACCESS_DENIED")
			{
				log_write("error", "namedmanager", "Access failure attempting to fetch domains");
				return 0;
			}
			else
			{	
				log_write("error", "namedmanager", "Unexpected error \"". $exception->getMessage() ."\" whilst attempting to fetch domains");
				return 0;
			}
		}

		return $domains;

	} // end of fetch_domains




	/*
		fetch_records

		Fetches all the domain records including SOA, NS, MX, A, PTR and more.


		Fields
		id_domain	ID of domain

		Returns
		array		Array of all the records
	*/

	function fetch_records( $id_domain )
	{
		log_write("debug", "soap_api", "Executing fetch_records( $id_domain )");

		try
		{
			$records = $this->client->fetch_records($id_domain);
		}
		catch (SoapFault $exception)
		{
			if ($exception->getMessage() == "ACCESS_DENIED")
			{
				log_write("error", "soap_api", "Access failure attempting to fetch domain records");
				return 0;
			}
			elseif ($exception->getMessage() == "NO_RECORDS")
			{
				log_write("warning", "soap_api", "There are no records for the requested domain.");
				return 0;
			}
			else
			{	
				log_write("error", "soap_api", "Unexpected error \"". $exception->getMessage() ."\" whilst attempting to fetch domain records");
				return 0;
			}
		}

		return $records;

	} // end of fetch_records





} // end of namedmanager


?>
