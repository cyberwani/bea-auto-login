# BeAPI - Autologin
Autologin plugin for Dev, Qualif and Preprod environments

This plugin autolog to an admin account based on defined slug url

This plugin allows you to auto-login regardless of the password previously entered on your website.
This is IP centered for security reasons.

# Usage
You just have to define these constants :

For security :

```
define ( 'BEA_AUTOLOGIN_IP', '192.168.0.1' );
```

For Autologin URL :
```
define ( 'BEA_AUTOLOGIN_SLUG', 'beapi' );
```

For Login form :
```
define ( 'BEA_AUTOLOGIN_LOGIN', 'my-login' );
define ( 'BEA_AUTOLOGIN_PASS', 'my-pass' );
```
# Changelog

## 2.0
* Allow auto-login in admin when access to specific url

## 1.0.4
* fix wrong SERVER_ADDR for REMOTE_ADDR

## 1.0.3
* fix wrong checking constant BEA_AUTOLOGIN_IP
* fix missing $accepted_args on filter authenticate
* fix wrong REMOTE_ADDR for SERVER_ADDR

## 1.0
initial
