![Bugfish](https://img.shields.io/badge/Bugfish-Software-orange)
![Status](https://img.shields.io/badge/Status-Finished-green)
![License](https://img.shields.io/badge/License-MIT-black)
![Version](https://img.shields.io/badge/Version-1.2.0-white)

Repository: https://github.com/bugfishtm/Dovecot-Certificate-Interface  
Documentation: https://bugfishtm.github.io/Dovecot-Certificate-Interface/  
The documentation is available in the "docs" folder inside this repositorie!

# Dovecot-Certificate-Interface [DCI]

This software is to enable dovecot per domain ssl certificates and manage / control them. Besides that it is designed to work automated together with ispconfig... Inside the webinterface are different informations in every areas which are there to help you using this software. I hope you will get an understanding about what this software was designed for and how you can get your use out of it. I wish you the best.

## Example Image
![plot](./_images/1.png)

## Changes on System Files

This software acts non-destructive. This means it does not change active configuration files, but only the file defined in settings.php with the constant: _CRON_DOVECOT_FILE_. The change you will do in the installation, which is to add a string to the dovecot.conf file at the end, can always be reverted and no other service oder changes will be made by the software which could break your system. If your dovecot process does not start at any time you use this software, this can only be related to the software in the case that the _CRON_DOVECOT_FILE_ is corrupted or bad written. (This does usually not happen by this software, because it does check every certificate for deep validation, but it can happen if you have other dovecot.conf files loaded and a domain may be duplicated in another one). It is not recommended to run this software together with plesk, virtualmin or every other hosting software which already has a working integration for dovecot per domain ssl connections. It is possible to use this panel standalone with no other hosting software in use, but you need to configure all dovecot settings by yourself. This script here will only create a file _CRON_DOVECOT_FILE_ which contains the config for the domains visible in the webinterface. It will not create or change other config files.

## User Management

This software includes user management capabilities. Users can be created, edited, and deleted, and different permissions for different areas can be assigned to them. Please note that if the administrator password is lost and no other user has access to the user management feature, the only way to regain access is to delete the websites _user database in the MySQL server or to change the password value of the administrative user inside that database tables "password" field to a new bcrypt generated string. You can generate this types of strings on my website if needed. Take a look in the apps section at www.bugfish.eu and look for a Module named similar to "PHP Password"...

## Logging

The background work of cronjobs, such as writing the dovecot config file, can be viewed and monitored in the "logs" section of the web interface. The status of the sync.php and ispconfig-fetch.php cronjob can be viewed there. The latest entries of the different types are always the ones of interest, as the previous ones are expired but can be viewed as archived.

## Debugging

Developers can use the "settings.php" file to set _MYSQL_LOGGING_ to "true." This will cause a new area to appear, displaying MySQL errors and allowing users to view them. This feature is not intended for use in productive environments. For information on the background work of the software, please view the "Logs" section.

## SSL Validation

Domains added to the software will be written to the Dovecot configuration file to enable per domain SSL certificates. Before being added to the configuration, the domains will undergo deep validation checks. The modulus of the certs will be checked to ensure they are equal between the cert and key. Only when the status is OK will the mail domain be added to Dovecot. If there is an error with the key or cert file, the domain will not be added...

## Use with ISPConfig

This script is able to automate the certificate config creation of dovecot for ssl mail domains. It will fetch the folder names of a pre given folder in the settings.php file (which can be left unchanged by default, because it contains the defaults ispconfig web root folders string) and after it has fetched the local domains out of the folders, it will try to get the certificates for them out of the domains subfolder "_domain_/ssl". If a custom ISP Config Certificate File is found, that will be used - until that is deleted in ispconfig. If there is no custom certificate for a website domain found, the script will try to find a letsencrypt cert. If both search operations for a valid certificate of a determined domain (by subfolder name) is valid, than the domain will not be saved into the datanbase. If you use the ispconfig-fetch cronjob file to make this script run with ispconfig automated, it is not possible anymore to set up domains or edit domains as user, as they would be cleaned up or overwritten by changes made by that cronjob. If you want to use this software in standalone mode do not activate the ispconfig-fetch cronjob!

## IP Blacklisting

After a specified number of failed login attempts (specified in the "settings.php" file), the IP address of the failed login user will be blocked. The IP will remain blocked until the daily.php cron is executed, or the IP is manually removed from the web interface. You can unblock IP Addresses in the "Blocklist" Area of the website.

## Example Image
![plot](./_images/main.png)

## Default Login for Webinterface
Username: admin  
Passwort: changeme

## Support and Assistance

If you encounter any issues or require assistance, please visit [bugfish.eu/forum](https://www.bugfish.eu/forum) for additional resources. You can also contact us at [request@bugfish.eu](mailto:request@bugfish.eu), and we will do our best to assist you.

This Android WebApp Example project offers a convenient way to deploy customized apps related to your website, enhancing your online presence and user experience.

## License Information

The license details for this Dovecot-Certificate-Interface project can be found in the "license.md" file within the project repository. Please review this file to understand the terms and conditions of use and distribution. It is essential to comply with the project's license to ensure legal and ethical usage of the provided resources.



