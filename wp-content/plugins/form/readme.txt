=== Form Builder ===
Contributors: zingiri
Donate link: http://www.zingiri.com/donations
Tags: form, forms
Requires at least: 2.1.7
Tested up to: 3.4
Stable tag: 1.1.7

Create amazing web forms with ease. No scripts, no HTML, no coding. Just drag-and-drop the form elements to create your professional looking form.

== Description ==

Create amazing web forms with ease. No scripts, no HTML, no coding. Just drag-and-drop the form elements to create your professional looking form.

The free version allows creation of one form, the Pro version of the plugin offers unlimited forms.

Note: Form Builder uses web services stored on Zingiri's servers, read more in the plugin's FAQ about what that means.

== Installation ==

1. Upload the `form` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Include the code [form ID] in any page to display the form with the selected ID.

Please visit [Zingiri](http://www.zingiri.com/plugins-and-addons/form-builder/#installation "Zingiri") for more information and support.

== Frequently Asked Questions ==

= This plugin uses web services, what exactly does that mean? =
Web services are simple way of delivering software solutions. Basically it means that the software & data is hosted on our secure servers and that you can access it from anywhere in the world. 
No need to worry about backing up your data, managing systems, we do it for you.

= What about data privacy? =
Support Tickets Center uses web services stored on Zingiri's servers. In doing so, personal data is collected and stored on our servers. 
This data includes amongst others your admin email address as this is used, together with the API key as a unique identifier for your account on Zingiri's servers. 
We have a very strict [privacy policy](http://www.zingiri.com/privacy-policy/ "privacy policy") as well as [terms & conditions](http://www.zingiri.com/terms/ "terms & conditions") governing data stored on our servers.

Please visit the [Zingiri Support Forums](http://forums.zingiri.com/forumdisplay.php?fid=59 "Zingiri Support Forum") for more information and support.

== Screenshots ==

Screenshots will be coming soon [here](http://www.zingiri.com/plugins-and-addons/form-builder/ "screenshots").

== Changelog ==

= 1.1.7 =
* Updated allowed extensions to 'jpg','bmp','png','zip','pdf','gif','doc','xls','wav','jpeg','docx','ppt','pptx','mp3'
* Fixed issue with wrong endpoint in forms
* Checked compatibility with WP 3.4

= 1.1.6 =
* Fixed issue with password showing mandatory although fields are filled
* Fixed rendering of UTF-8 characters in field labels 
* Fixed issue with checkbox always defaulting to 'on'
* Fixed issue with date defaulting to 1970
* Updated css to force removal of bullets in form display
* Removed form edit link in front end
* Default column names to upper case
* Post fix column names with id for uniqueness
* Added verification on duplicate column names
* Fixed issue with US vs Europe date formats for date field
* Added time picker widget to Time element type
* Removed europe_date element type and added option to select date format (US or Europe) to date element type

= 1.1.5 =
* Updated readme.txt and settings page regarding the use of web services and data privacy policy

= 1.1.4 =
* Replaced protoype/scriptaculous scripts with jQuery scripts
* Fixed issue with date elements
* Added uninstall hook
* Remember API key after deactivation
* Removed rules from control panel (for now)

= 1.1.3 =
* Fixed issue with http class not parsing post variables properly
* Removed loading of news feed

= 1.1.2 =
* Only load admin javascript and styles on Bookings pages

= 1.1.1 =
* Fixed issue with url encoding

= 1.1.0 =
* Added new submit button with option to email the form's content
* Fixed issue with textarea with editor
* Checked compatibility with Wordpress 3.3.1

= 1.0.4 =
* Added jQuery UI library
* Added styling for date picker

= 1.0.3 =
* Fixed issue with html area and captcha elements

= 1.0.2 =
* Fixed issues with single and multiple file upload elements
* Fixed issues with single and multiple image upload elements

= 1.0.1 =
* Updated readme file

= 1.0.0 =
* First release