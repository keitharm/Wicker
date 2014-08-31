Wicker - The stupid wifi cracker.

==Configuration==
In order for Wicker's features to work properly, you'll have to change some settings on your system.
1. Disable SELinux (or allow PHP to execute commands such as system, exec, passthru, etc.)
2. Add Webserver user to visudo
    www-data        ALL=NOPASSWD:   /usr/local/sbin/airodump-ng
3. Comment out "Defaults requiretty"
    Defaults:www-data !requiretty
4. Make sure that the uploads, scans, and logs directories have permissions for the webserver user
