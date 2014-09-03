Wicker - The stupid wifi cracker
================================

Overview
--------
Wicker is a web interface that runs on top of the aircrack-ng suite and pyrit.
It allows you to run "cracking sessions" for WEP/WPA on a remote server that's
beefy enough to not take 999999 years while you can capture handshakes and IVs
on your wimpy (and way more portable) laptop.

Configuration
-------------
In order for Wicker's features to work properly, you'll have to change some 
settings on your system.

1. Disable SELinux (or allow PHP to execute commands such as system, exec, 
    passthru, etc.)
2. Add Webserver user to visudo with execute permissions for airodump-ng and kill
```
    www-data        ALL=NOPASSWD:   /usr/local/sbin/airodump-ng, /bin/kill
```
3. Comment out "Defaults requiretty" or add this line in visudo
```
    Defaults:www-data !requiretty
```
4. Make sure that the uploads, scans, and logs directories have permissions 
    for the webserver user

Disclaimer
----------
Any actions and or activities related to Wicker and your usage of it is solely
your responsibility. The misuse of Wicker can result in criminal charges 
brought against the persons in question. The authors of Wicker will not be
held liable for your usage of Wicker.

All data that Wicker operates on either through itself or through the use
of other tools must be data gathered with permission from the owner of
said data.

Wicker is intended for educational and research purposes only.

We (The authors of Wicker) reserve the right to modify this disclaimer 
at any time.

