# Introduction

**This project is considered deprecated and exists here for reference only**

phpfreeradius is an open source (AGPL) web-based application for managing a FreeRADIUS environment.

It's designed for a simple teleco configuration using LDAP as a backend for user authentication and supports restricting whom can login to a particular NAS based on their LDAP group (eg "admins", "dslcustomers", "dialupcustomers", etc).



# Key Features

* Provides an easy to use web based interface for managing FreeRADIUS NAS configuration.
* Designed to work with an LDAP directory for user authentication - partners with [LDAPAuthManager](https://projects.jethrocarr.com/p/oss-ldapauthmanager/) perfectly.
* Integrates with [NamedManager](https://projects.jethrocarr.com/p/oss-namedmanager/) to automatically create DNS names when a new RADIUS NAS device is added.
* Ability to limit NAS access to a particular LDAP group.
* Support for RADIUS attributes being delivered to the NAS based on the LDAP user or group (including custom vendor attributes).


