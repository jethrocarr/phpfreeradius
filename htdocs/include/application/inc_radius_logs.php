<?php
/*
	inc_radius_logs.php

	Provides high-level functions for querying and managing nas devices in the MySQL
	database.

*/




/*
	CLASS RADIUS_LOGS

	Functions for quering and updating logs.
*/
class radius_logs
{
	var $id_server;		// radius server to assign log entries to
	var $id_nas;		// NAS to assign log entries to



	/*
		log_push

		Creates a new log entry based on the supplied information.

		Fields
		$this->id_server	(optional) ID of the radius server the log originated from
		$this->id_nas		(optional) ID of the NAS to use, if not specified, the log push process will run a check

		Results
		0	Failure
		1	Success
	*/
	function log_push($timestamp, $log_type, $log_contents)
	{
		log_debug("radius_logs", "Executing log_push($timestamp, $log_type, $log_contents)");


		// do retention clean check
		if ($GLOBALS["config"]["LOG_RETENTION_PERIOD"])
		{
			// check when we last ran a retention clean
			if ($GLOBALS["config"]["LOG_RETENTION_CHECKTIME"] < (time() - 86400))
			{
				$this->log_retention_clean();
			}
		}


		// see if this log entry is related to any particular NAS
		$sql_obj		= New sql_query;
		$sql_obj->string	= "SELECT id, nas_hostname, nas_shortname FROM nas_devices";
		$sql_obj->execute();

		if ($sql_obj->num_rows())
		{
			$sql_obj->fetch_array();

			foreach ($sql_obj->data as $data)
			{
				if (preg_match("/". $data["nas_hostname"] ."/i", $log_contents))
				{
					$this->id_nas	= $data["id"];
				}
				elseif (preg_match("/". $data["nas_shortname"] ."/i", $log_contents))
				{
					// replace the shortname with the hostname for user clarity
					$log_contents	= str_replace($data["nas_shortname"], $data["nas_hostname"], $log_contents);

					$this->id_nas	= $data["id"];
				}
			}
		}

		// flag known annoying messages as debug for easy filtering
		if ($log_contents == "rlm_ldap: parsing radiusReplyItem failed: \n")
		{
			$log_type = "Debug";
		}

		if ($log_contents == "rlm_ldap: parsing radiusCheckItem failed: \n")
		{
			$log_type = "Debug";
		}



		// write log
		$sql_obj		= New sql_query;
		$sql_obj->string	= "INSERT INTO logs (id_server, id_nas, timestamp, log_type, log_contents) VALUES ('". $this->id_server ."', '". $this->id_nas ."', '$timestamp', '$log_type', '$log_contents')";
		$sql_obj->execute();


		// update last sync on radius server option
		if ($this->id_server)
		{
			$obj_server		= New radius_server;
			$obj_server->id		= $this->id_server;
			$obj_server->action_update_log_version($timestamp);
		}


		return 1;

	} // end of log_push


	/*
		log_retention_clean

		Cleans the log table of outdated records.

		This process needs to take place at least every day to ensure speedy performance and is triggered from either
		a log API call or an audit log entry (since there is no guarantee that either logging method is going to be enabled,
		we have to trigger on any.)

		Returns
		0	No log clean requires
		1	Performed log clean.
	*/

	function log_retention_clean()
	{
		log_write("debug", "radius_logs", "Executing log_retention_clean()");
		log_write("debug", "radius_logs", "A retention clean is required - last one was more than 24 hours ago.");

		// calc date to clean up to
		$clean_time	= time() - ($GLOBALS["config"]["LOG_RETENTION_PERIOD"] * 86400);
		$clean_date	= time_format_humandate($clean_time);


		// clean
		$obj_sql_clean		= New sql_query;
		$obj_sql_clean->string	= "DELETE FROM logs WHERE timestamp <= '$clean_time'";
		$obj_sql_clean->execute();

		$clean_removed = $obj_sql_clean->fetch_affected_rows();

		unset($obj_sql_clean);


		// update rentention time check
		$obj_sql_clean		= New sql_query;
		$obj_sql_clean->string	= "UPDATE `config` SET value='". time() ."' WHERE name='LOG_RETENTION_CHECKTIME' LIMIT 1";
		$obj_sql_clean->execute();

		unset($obj_sql_clean);


		// add audit entry - we have to set the LOG_RETENTION_CHECKTIME variable here to avoid
		// looping the program, as the SQL change above won't be applied until the current transaction
		// is commited.

		$GLOBALS["config"]["LOG_RETENTION_CHECKTIME"] = time();
		$this->log_push(time(), "audit", "Automated log retention clean completed, removed $clean_removed records order than $clean_date");


		// complete
		log_write("debug", "radius_logs", "Completed retention log clean, removed $clean_removed log records older than $clean_date");

		return 1;
	}



} // end of class: radius_logs

?>
