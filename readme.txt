=== Plugin Name ===
Contributors: Nikoya
Tags: w3c validation, w3c, xhtml, html, html5, (x)html, check validation, validation, validity
Requires at least: 3.2.1 (certainly less too but not check)
Tested up to: 3.2.1
Stable tag: 0.4


== Description ==

Check the doctype validity using W3c validator (html , xhtml , ... ) when creating or updating  page / post / custom post type and show the result in backend
It show the result in back-end in sortable column, a link is add to the w3c for all file to help you to correct html error.

Very easy to see if a post / page is valid or not.

This plugin can check the W3C validity of your page / post or custom post type even if the site is not accessible from the Internet (if you work on local system for example)

== Installation ==

How to install the plugin and get it working.

1. Upload `(x)html-easy-validator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

you are done

now go the list of your post / page / custom post type in the WP back-end 


== Frequently Asked Questions ==
...



== Screenshots ==

1. This screenshot show how validation is display in the backend of wordpress and how is simple to see where validation problem are.
    As you can see, result are sortable
    Each result is a link to the w3c validator of your page /post ....
        you'll can use it to go the the full page of w3c validator and correct your code 

2. Settings give the capability to:
    - Check W3C validity even your website is in local network
    - Use direct input validation

== Changelog ==

= 0.4 =
* Second public release
    
    - ADD offline validation
    - ADD setings menu
    - ADD settings link from plugin pannel activation
    - ADD direct input for manual validation in setting menu
    
    - FIX admin pannel detection
    - Change color of warnings 

= 0.3 =
* First public release
    - Detect is post/page is private or protect
    - Fix custom post type detection bug
    

= 0.2 =
* Second private release
    - Replace Curl by get_headers
    - Fix custom post type validation bug


= 0.1 =
* First private release
    - Based on Curl only
    - Work on page / post and all custom post type not built-in
    - Show result in sortable columns
    - Full and Clean uninstall

    
