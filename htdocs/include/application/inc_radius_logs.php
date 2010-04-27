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

		// see if this log entry is related to any particular NAS
		$sql_obj		= New sql_query;
		$sql_obj->string	= "SELECT id, nas_hostname FROM nas_devices";
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
			}
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



} // end of class: radius_logs

?>
