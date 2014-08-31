Wicker - The stupid wifi cracker
================================

Overview
--------
Wicker is a wireless tool that provides an easy to use GUI interface for the tools of penetration testing.

Configuration
-------------
In order for Wicker's features to work properly, you'll have to change some settings on your system.

1. Disable SELinux (or allow PHP to execute commands such as system, exec, passthru, etc.)
2. Add Webserver user to visudo with execute permissions for airodump-ng
    www-data        ALL=NOPASSWD:   /usr/local/sbin/airodump-ng
3. Comment out "Defaults requiretty" or add this line in visudo
    Defaults:www-data !requiretty
4. Make sure that the uploads, scans, and logs directories have permissions for the webserver user

Disclaimer
----------
Wicker is meant only for networks that you have *permission* for penetration testing on.

Users are fully responsible for any consequences resulting from the misuse of Wicker.
Wicker is provided for educational or information purposes only.
