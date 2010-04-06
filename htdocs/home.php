<?php
/*
	Summary/Welcome page for phpfreeradius
*/

if (!user_online())
{
	// Because this is the default page to be directed to, if the user is not
	// logged in, they should go straight to the login page.
	//
	// All other pages will display an error and prompt the user to login.
	//
	include_once("user/login.php");
}
else
{
	class page_output
	{
		function check_permissions()
		{
			// only allow radiusadmins to have access
			if (user_permissions_get("radiusadmins"))
			{
				return 1;
			}
			else
			{
				log_write("error", "page_output", "You do not have permissions to access this interface, request your administrator to assign you to the radiusadmins group");
				return 0;
			}
		}

		function check_requirements()
		{
			// nothing todo
			return 1;
		}
			
		function execute()
		{
			// nothing todo
			return 1;
		}

		function render_html()
		{
			print "<h3>OVERVIEW</h3>";
			//print "<p>Welcome to <a target=\"new\" href=\"http://www.amberdms.com/ldapauthmanager\">LDAPAuthManager</a>, an open-source, PHP web-based LDAP authentication management interface designed to make it easy to manage users running on centralised authentication environments.</p>";
			print "<p>Welcome to phpfreeradius, a PHP web-based Free Radius management interface designed to make it easy to manage Free Radius servers across multiple systems via an easy to use web-based interface.</p>";
	
			
			// buttons
			print "<br><p>";
			print "<a class=\"button\" href=\"index.php?page=logs/logs.php\">View Free Radius Logs</a> ";
			print "<a class=\"button\" href=\"index.php?page=nasdevices/nasdevices.php\">Manage NAS Devices</a> ";
			print "<a class=\"button\" href=\"index.php?page=huntgroups/huntgroups.php\">NAS &lt;-&gt; Group Assignment</a> ";

			print "</p>";

		}
	}
}

?>
