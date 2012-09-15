=== People Lists ===
Contributors: sgagan, enej, ctlt-dev, ubcdev
Tags: people lists, people, list, form, user profile, user avatar, thumbnail, upload photo, user, users, profile, biography, profile biography, user profile, description, profile description, rich text, wysiwyg, tinyMCE, photos, images,  members, directory, profiles, jQuery, sortable, tabbable, thickbox, overlay, media button, Your Profile
Requires at least: 3.3
Tested up to: 3.3
Stable Tag: trunk

Provides a shortcode [people-lists list=example-list] that can insert a People List on any page,post or even sidebar to list selected users.

== Description ==

People Lists provides a rich text editor on the profile page for easy modifications of specific user profile information that can be displayed on any page using the [people-lists list=example-list] shortcode. Admins will also be able to add custom fields to the Your Profile section on Wordpress and these fields can be displayed on any page using the People Lists template (which can be styled using HTML) that provides codes for every field that is desired to be displayed.  

**This plugin was developed alongside [User Avatar](http://wordpress.org/extend/plugins/user-avatar/ "User Avatar WordPress Plugin Homepage") and this plugin provides a thumbnail area in the Your Profile section, where users can upload & crop new images.  There is a specific code in People Lists that hooks this thumbnail into your lists template display, so make sure you grab it!!!**

Here's a quick breakdown of features that this plugin provides:

*  Rich-Text Editor (WYSIWYG) in Your Profile, making it easy for users of ANY WordPress role to add and format their Bibliographical Info (renamed to "General Information") very easily.
*  All users of *ANY role* on a blog will be displayed in the People Lists Settings page and users can be Dragged & Dropped into the a desired list of their choice.
*  A template providing default codes (e.g. %thumbnail%, %nickname%) and added fields codes (see below) allows for customization of different lists and for them to display different user profile information as desired.  HTML can be applied here, so lists can be styled to look pretty!
*  There is a Profile Settings area where sortable custom fields can be added to the Your Profile section on WordPress, and codes will be generated (e.g. %people-lists-mobile-number%) and displayed in your Template Info area to be used on lists of your choice.
*  Lists have a management area where they can be renamed, add new people or remove current people, re-sorted to be listed in the desired order and deleted. 
*  A People Lists media button has been added to Pages/Posts that allows users with editing privileges to insert a people lists shortcode with the click of a button!

I could go on and on, but I'll spread the love into the screenshot and FAQ sections!


== Installation ==

1. Download the plugin package `people-lists.zip`
1. Unzip the package and upload to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= I've activated the plugin, now where do I go?? =

Under Settings on the dashboard, click on People Lists to create a new list!  Also, under Users on the dashboard, click Your Profile to see the rich-text (WYSIWYG) editor and the same area where added fields will be displayed, as well as the [User Avatar](http://wordpress.org/extend/plugins/user-avatar/ "User Avatar WordPress Plugin Homepage") image display, which I have suggested you download because it has been built and uses the code %thumbnail% to pull the image that this plugin stores.

= I've added my list, but need to create a new field that WordPress doesn't provide, what should I do?? =

After creating a list, you will notice a tab named "Profile Settings" and after clicking on this, you simply have to enter the name of your new field and we will do the rest for you!  Don't worry about remembering the template code because we will provide it for you in the template section.  This new field is added to the Your Profile section (under 'Contact Info') and extends the default WordPress fields to accommodate any fields you require like a mobile number, job title, height or even the user's favourite pizza topping!

= I've got my list, fields and have gotten User Avatar, but what's this template thing about?? =

When you add a new list or visit the list page of a previously created list, you'll notice a link called "Template Info", which will toggle and display a tabbed area with default and added field codes.  Default codes are based off of the default WordPress fields for user profile information, while added field codes are the fields that you create in the "Profile Settings" page of this plugin.  To the right of the tabbed area is a default template for displaying user information,  which can be customized to add different template codes or styled by CSS to change the appearance of how they are displayed on a page or post.  The possibilities here are endless to help fit the needs of all users, so spend some time creating the perfect template for you!  

= I've set up the list and template, but how do I get a list to display in a page?? =

There are two ways to insert a list onto a page or post!  The easy method is to use the People Lists media button (the happy face :D) above the WYSIWYG on Pages and Posts.  That will popup an overlay where you simply select the list you want to insert and the shortcode for it with automatically be added to the editor.  The second method is to copy the shortcode from your list's management page and the only benefit to this method would be that you can insert this code in the sidebar if you had a very simple template and styled it to fit there.  Of course I would suggest clicking the happy face because I drew it myself and I think it's super awesome! :D 

= I have a super long available people's list, but how do I drag it to the selected people's list, which is off the page?? =

We even thought about this and have provided pin/unpin functionality, so the selected list goes where you go, making it easy for you to find the person you want on your list!  No problem.

= How to you work the filters to add fields additional user fields? ==

Instead of modifying the plugin and not benefit from any future updates. I created filters that can easily be used to add additional fields 
eather not included by the plugin or created by a different plugin.

here is some example code. 
`<?php 
/* test filters */
add_filter("people_list_custom_fields",'test_display_custom_fields');
function test_display_custom_fields($array){
	
	$array[] = "%this%"; //this is the string that is displayed in your template
	return $array;
}

add_filter("people_list_fields_display",'test_display_field',10,3);
function test_display_field($display_list,$user, $list)
{
	// $user // not being used but is the user object from the of a particular user. 
	// do doing something like $user->ID try something like var_dump($user) to see the whole object
	
	// $list refers to a particular list and is the variable used 
	// this way you can target a particular list 
	$display_list[] = "replace text"; // this is the text that is 
	return $display_list;
}
?>`


== Screenshots ==

1. These are the two areas where you'll notice changes after installing People Lists. (There's also a little happy face media button to add People Lists on Pages/Posts creation! :D)
2. Form for adding a new People Lists.
3. Form for management of a created People List.
4. Template Info displaying default codes (left) and default template (right).
5. Profile Settings form for adding new fields (e.g. Job Title, Mobile Contact, etc).
6. Template Info displaying added fields codes and their usage in the template.
7. Changes to Your Profile and where the new fields are displayed for users.
8. Rich-text (WYSIWYG) area making is much easier for users to insert any General Information.
9. Media button overlay to insert People List shortcode directly into page/post by clicking.

== Changelog ==

= 1.3.10 =
* bug fix: the editor supports content being entered in it better. 
* feature: you are able to remove the editor and by adding `define("PEOPLE_LIST_VISUAL_EDITOR", false);` to wp-config.php

= 1.3.9 = 
* bug fix: The editor converted was escaping the html string

= 1.3.8 = 
* bug fix: Compatibility with WordPress 3.3 it uses the new wp_editor API

= 1.3.7 = 
* bug fix: removes a PHP warning
* bug fix: better css styling of a list

= 1.3.6 = 
* bug fix: try again!

= 1.3.5 = 
* bug fix: removed unused clf_base_tiny_filter_remove_fullscreen and replaced it with people_lists_tiny_filter_remove_fullscreen
* Please don't download 1.3.4 because it doesn't work compatblity with 3.2 restored

= 1.3.4 =
* bug fix, compatibility with 3.2
* added the ability to add before and after strings
= 1.3.3 =
* bug fix, readded some of the functionality previously removed. - spotted by flayakaya

= 1.3.2 =
* added the ability for users to add media to their bio pages. If they has such ability (thanks jZed for making it happen)

= 1.3.1 =
* added the ability to size the image that are displayed by %thumbnail%
* added filter to the plugin which lets you extend it to your hearts content.
* translation ready

= 1.3 =
* Updated so it works with WP 3.1 and also made it more a bit more future proof

= 1.2 =
* Added all applicable default codes from Your Profile (website, username, aim, yim, jabber)

= 1.0 =
* First Public Release

== Upgrade Notice ==

= Upgrade to version 1.2 =
* August 27th, 2010

= No Upgrades yet =
* August 1st, 2010

