# Software Installation Guide

## General Information

Before deploying the software, review the configuration/settings file for your instance. Ensure that the `settings.php` file is correctly configured. While this software has been successfully used in many production environments, there are no absolute guarantees. 

This software has been tested on Ubuntu and Debian systems, though it may work on other systems that have not been tested. If using a system other than Debian or Ubuntu, you might need to adjust deeper settings within the `settings.php` file. This software is optimized for Debian/Ubuntu with Apache and PHP 8. Below are the requirements and installation steps to follow:




## Requirements

To ensure smooth operation of the web software alongside Dovecot for managing mail SSL certificates, the following requirements must be met:

### Server Dependencies

- **Root permissions** for cronjobs on the system.
- **Dovecot**: Ensure that Dovecot is installed and properly configured on your server.
- **OpenSSL**: Required for handling SSL/TLS certificates.
- **Postfix** (or an equivalent MTA): For handling outgoing mail.
- **Web Server**: Apache, Nginx, or any compatible server for hosting the web software.
- **MySQL database**: Connection and user credentials (configured in `settings.php`).

### Apache Requirements

- **Apache2** web server with PHP 7/8 support.
- **Apache2 modules**: `rewrite`.

### PHP Requirements

- **PHP Version**: 8.X
- **PHP modules**: `mysqli`, `curl`, `intl`, `mbstring`, `gd`.

### System Requirements

- **Operating System**: Linux-based OS (e.g., Ubuntu, CentOS, Debian) is recommended.
- **RAM**: Minimum of 1GB.
- **Disk Space**: At least 10GB of free space for logs, emails, and software.


## Installation Procedure

1. **Upload Files**: Upload the files from the "source" directory of this repository to your web space.
2. **Configure Settings**: Check the `settings.sample.php` file and make the necessary changes. Set your MySQL login credentials in the `settings.php` file. 
3. **Rename Configuration File**: Rename `settings.sample.php` to `settings.php`.
4. **Automatic SQL Tables**: The software will automatically create any required SQL tables when the website is first opened. An initial user will be created, and login credentials will be provided at the end of this guide.
5. **Setup Cronjob**: Configure the cronjob as described below. Without it, Dovecot configuration will not be written.
6. **Edit Dovecot Configuration**: It is crucial to modify the `dovecot.conf` file as described in the documentation below.

## Initial Setup

After uploading the project files to the server (outside of the source directory), modify the `settings.sample.php` file as needed and rename it to `settings.php`. This step is mandatory to ensure the software functions correctly, as valid MySQL user data is required.

### Configuration Settings

Below is a list of settings you can configure in the `settings.php` file:

| Constant                        | Description                                                                                             |
|---------------------------------|---------------------------------------------------------------------------------------------------------|
| `_TITLE_`                       | Set the website title, which will be shown in your browser tab.                                         |
| `_IMPRESSUM_`                   | Link to your impressum page, accessible from the footer.                                                |
| `_SQL_HOST_`                    | SQL Database Host.                                                                                      |
| `_SQL_USER_`                    | SQL Database User.                                                                                      |
| `_SQL_PASS_`                    | SQL Database Password.                                                                                  |
| `_SQL_DB_`                      | SQL Database name.                                                                                     |
| `_IP_BLACKLIST_DAILY_OP_LIMIT_` | IP blacklist limit for blocking IPs (default is 1000). Reset daily if the cronjob `daily.php` is executed. |
| `_CSRF_VALID_LIMIT_TIME_`       | Validity period of a CSRF key for form validation (default is 1000 seconds).                           |
| `_MYSQL_LOGGING_`               | Set to "true" to enable MySQL logging and the debug area in the web interface. Set to "false" to disable. |
| `_COOKIES_`                     | Cookie prefix. No need to change unless you are familiar with the implications.                        |
| `_CRON_DOVECOT_FILE_`           | Path to the Dovecot configuration file for SSL certificate/domain settings. Include this in `dovecot.conf`. |
| `_CRON_ISP_FOLDER_SEARCH_`      | Path for fetching subfolder names from ISPConfig (only needed if using ISPConfig).                      |

## Cronjob Setup

To ensure the software operates correctly, configure the following cronjobs:

### Mandatory Cronjobs

| Command                            | Interval | Description                                                                                           |
|------------------------------------|----------|-------------------------------------------------------------------------------------------------------|
| `_webroot_/_cronjob/daily.php`     | Daily    | Resets blacklisted IPs (optional but recommended).                                                    |
| `_webroot_/_cronjob/sync.php`      | X        | Executes all domain and Dovecot related operations. Recommended interval: hourly. This is mandatory. |

### Optional ISP Config Domain Fetch Cronjob

| Command                            | Interval | Description                                                                                           |
|------------------------------------|----------|-------------------------------------------------------------------------------------------------------|
| `_webroot_/_cronjob/ispconfig_fetch.php` | X        | Fetches SSL certificates and domains from ISPConfig webroot folders. Only needed if using ISPConfig. |

## Edit Dovecot Configuration

**Important:** Modify the `dovecot.conf` file to make the script work. Add the following line to the end of the file:

```
!include_try dci.certs.conf
```

## Initial Login

After successfully deploying the software, log in with the following credentials:

- **Username**: admin
- **Password**: changeme

**Important:** Change the initial password after the first successful login.


