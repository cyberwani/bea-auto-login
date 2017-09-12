# BEA Auto Login
Autologin plugin for Dev, Qualif and Preprod environments

This plugin allows you to auto-login regardless of the password previously entered on your website.
This is IP centered for security reasons.

# Usage
You just have to define 3 constants :
```
define ( 'BEA_AUTOLOGIN_LOGIN', 'my-login' );
define ( 'BEA_AUTOLOGIN_PASS', 'my-pass' );
define ( 'BEA_AUTOLOGIN_IP', '192.168.0.1' );
```
# Changelog

## 1.1
* 12 Sept 2017
* fix missing $accepted_args on filter authenticate

## 1.0
initial
