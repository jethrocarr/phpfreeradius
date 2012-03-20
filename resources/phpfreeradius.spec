Summary: A web-based management system for FreeRadius servers, consisting of a PHP web interface and some PHP CLI components to hook into FreeRadius.
Name: phpfreeradius
Version: 1.3.0
Release: 1%{dist}
License: AGPLv3
URL: http://www.amberdms.com/phpfreeradius
Group: Applications/Internet
Source0: phpfreeradius-%{version}.tar.bz2

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
BuildArch: noarch
BuildRequires: gettext

%description
phpfreeradius is a web-based interface for managing and viewing FreeRadius configuration and logs.


%package interface
Summary: phpfreeradius web-based interface and API components
Group: Applications/Internet

Requires: httpd, mod_ssl
Requires: php >= 5.3.0, mysql-server, php-mysql, php-ldap, php-soap
Requires: perl, perl-DBD-MySQL
Prereq: httpd, php, mysql-server, php-mysql

%description interface
Provides the phpfreeradius interface and associated template configuration files


%package integration
Summary: FreeRadius integration components for phpfreeradius
Group: Applications/Internet

Requires: php-cli >= 5.3.0, php-soap, php-process
Requires: perl, perl-DBD-MySQL

%description integration
Provides applications for linking FreeRadius to phpfreeradius via the administrative API.


%prep
%setup -q -n phpfreeradius-%{version}

%build


%install
rm -rf $RPM_BUILD_ROOT
mkdir -p -m0755 $RPM_BUILD_ROOT%{_sysconfdir}/phpfreeradius/
mkdir -p -m0755 $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/

# install application files and resources
cp -pr * $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/


# install configuration file
install -m0700 htdocs/include/sample-config.php $RPM_BUILD_ROOT%{_sysconfdir}/phpfreeradius/config.php
ln -s %{_sysconfdir}/phpfreeradius/config.php $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/htdocs/include/config-settings.php

# install linking config file
install -m755 htdocs/include/config.php $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/htdocs/include/config.php


# install configuration file
install -m0700 scripts/include/sample-config.php $RPM_BUILD_ROOT%{_sysconfdir}/phpfreeradius/config-integration.php
ln -s %{_sysconfdir}/phpfreeradius/config-integration.php $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/scripts/include/config-settings.php

# install linking config file
install -m755 scripts/include/config.php $RPM_BUILD_ROOT%{_datadir}/phpfreeradius/scripts/include/config.php



# install the apache configuration file
mkdir -p $RPM_BUILD_ROOT%{_sysconfdir}/httpd/conf.d
install -m 644 resources/phpfreeradius-httpdconfig.conf $RPM_BUILD_ROOT%{_sysconfdir}/httpd/conf.d/phpfreeradius.conf

# install the bootscript
mkdir -p $RPM_BUILD_ROOT/etc/init.d/
install -m 755 resources/phpfreeradiuslogging.rcsysinit $RPM_BUILD_ROOT/etc/init.d/phpfreeradiuslogging

# install the cronfile
mkdir -p $RPM_BUILD_ROOT%{_sysconfdir}/cron.d/
install -m 644 resources/phpfreeradius-integration.cron $RPM_BUILD_ROOT%{_sysconfdir}/cron.d/phpfreeradius-integration


%post interface

# Reload apache
echo "Reloading httpd..."
/etc/init.d/httpd reload

# update/install the MySQL DB
if [ $1 == 1 ];
then
	# install - requires manual user MySQL setup
	echo "Run cd %{_datadir}/phpfreeradius/resources/; ./autoinstall.pl to install the SQL database."
else
	# upgrade - we can do it all automatically! :-)
	echo "Automatically upgrading the MySQL database..."
	%{_datadir}/phpfreeradius/resources/schema_update.pl --schema=%{_datadir}/phpfreeradius/sql/ -v
fi



%post integration

if [ $1 == 0 ];
then
	# upgrading existing rpm
	echo "Restarting logging process..."
	/etc/init.d/phpfreeradiuslogging restart
fi


%postun interface

# check if this is being removed for good, or just so that an
# upgrade can install.
if [ $1 == 0 ];
then
	# user needs to remove DB
	echo "phpfreeradius has been removed, but the MySQL database and user will need to be removed manually."
fi


%preun integration

# stop running process
/etc/init.d/phpfreeradiuslogging stop



%clean
rm -rf $RPM_BUILD_ROOT

%files interface
%defattr(-,root,root)
%config %dir %{_sysconfdir}/phpfreeradius
%attr(770,root,apache) %config(noreplace) %{_sysconfdir}/phpfreeradius/config.php
%attr(660,root,apache) %config(noreplace) %{_sysconfdir}/httpd/conf.d/phpfreeradius.conf
%{_datadir}/phpfreeradius/htdocs
%{_datadir}/phpfreeradius/resources
%{_datadir}/phpfreeradius/sql

%files integration
%defattr(-,root,root)
%config %dir %{_sysconfdir}/phpfreeradius
%config %dir %{_sysconfdir}/cron.d/phpfreeradius-integration
%attr(770,root,root) %config(noreplace) %{_sysconfdir}/phpfreeradius/config-integration.php
%{_datadir}/phpfreeradius/scripts
/etc/init.d/phpfreeradiuslogging


%changelog
* Tue Mar 20 2012 Jethro Carr <jethro.carr@amberdms.com> 1.3.0
- Updated to 1.3.0 release
* Tue Jun 28 2011 Jethro Carr <jethro.carr@amberdms.com> 1.2.2
- Updated to 1.2.2 release
* Wed Apr 13 2011 Jethro Carr <jethro.carr@amberdms.com> 1.2.1
- Updated to 1.2.1 release (bug fixes only)
* Tue Apr  5 2011 Jethro Carr <jethro.carr@amberdms.com> 1.2.0
- Updated to 1.2.0 release
* Sun Mar 27 2011 Jethro Carr <jethro.carr@amberdms.com> 1.1.0_beta_2
- Updated to 1.1.0_beta_2 release
* Tue Aug 03 2010 Jethro Carr <jethro.carr@amberdms.com> 1.1.0_beta_1
- Updated to 1.1.0_beta_1 release
* Tue Jun 08 2010 Jethro Carr <jethro.carr@amberdms.com> 1.0.2
- Updated to 1.0.2 release
* Mon May 17 2010 Jethro Carr <jethro.carr@amberdms.com> 1.0.1
- Updated to 1.0.1 release
* Tue Apr 27 2010 Jethro Carr <jethro.carr@amberdms.com> 1.0.0
- Inital Stable Release
* Tue Apr 20 2010 Jethro Carr <jethro.carr@amberdms.com> 1.0.0_alpha_1
- Inital Application Release

