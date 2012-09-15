=== WP Feedback & Survey Manager ===
Contributors: swashata
Donate link: http://www.intechgrity.com/about/buy-us-some-beer/
Tags: feedback, survey, form, web-form, database form, quiz, opinion
Requires at least: 3.1
Tested up to: 3.5
Stable tag: 1.0.1
License: GPLv3

Gather feedbacks and run surveys on your WordPress Blog. Stores the gathered data in database. Displays the form & trends with shortcodes.

== Description ==

The concept and working of the Plugin is very simple.

* You setup the form from the Settings Page. Set how many survey questions to show, how many feedbacks to ask for and of course any other personal opinion (or completely disable a feature).
* You use the Shortcodes for displaying on your Site/Blog.
* Finally use the Survey Reports Or View all Feedbacks pages to analyze the submissions.

Sounds easy enough? Even more... Publish the Trends of the survey by showing report based on latest 100 database records cached every hour.

**Also compatible with WordPress MultiSite** Each of the sites can run their own instances of survey and a different set of databases and options will be created.
Infact, this is the only way (now) to have more than one feedback form on your site.

**Caution:** Please do not network activate the plugin. Rather activate it individually for each of the sites where you'd like to have a feedback/survey form.

**Note:** I had to pass in the documentation to the loader class because it is a shortcut to add the documentation to all the slide-in *Help* sidebar. That is why, during instantiating the loader, I have used:
`$wp_feedback = new wp_feedback_loader(__FILE__, 'fbsr', '1.0.1', 'wp_fbsr', 'http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/', 'http://wordpress.org/support/plugin/wp-feedback-survey-manager');`
It does not callback the mentioned URL or send in any of your personal or sensitive WordPress information to my server.

= Documentation =

Check the Installation and FAQ page. For detailed documentation check [HERE at out blog](http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/)

= Feature List =
* Add Good Looking "tabbed" Feedback/Survey form on your blog easily. The form submission is done through AJAX with a nice effect. Falls back well on browser without JS.
* Both JavaScript as well as PHP validation on form submission.
* Nice design of the form using Google Web Fonts and jQuery UI.
* You can have upto 20 survey questions and feedback topics.
* Each survey question can have any number of options. The options can be single or multiple type.
* Storage of survey and feedbacks on database.
* You can mail different Feedbacks on different emails. Useful if you are working with a collaborative team to collect feedbacks.
* Detailed survey report on admin backend. AJAX-ed fetching of all surveys and displaying using Google Chart (check the screenshots).
* Inline HELP (from the WordPress Admin like sliding Help from every screen) makes it easy for you to understand the various aspects of this plugin.

And many more features... Just check the screenshots.

= Shortcodes =
This plugin comes with two shortcodes. One for displaying the FORM and other for displaying the Trends (The same Latest 100 Survey Reports you see on this screen)

* `[feedback]` : Just use this inside a Post/Page and the form will start appearing.
* `[feedback_trend]` : Use this to show the Trends based on latest 100 Survey Reports for all available questions. Just like the dashboard widget on this screen.

= Important Notes =

*Version 1.0.0 Released. This is the first public release*

= Credits =
The very basic & simplest form of the idea of this plugin came from my friend **Arnab Saha** during our Annual College Fest. As the development began, we pondered upon more ideas and finally we released it publicly.

The plugin uses a few free and/or open source products, which are:

* [Google WebFont](http://www.google.com/webfonts/) : To make the form look better.
* [jQuery UI](http://jqueryui.com/) : Renders the basic "Tab Like" appearance of the form.
* [Google Charts Tool](https://developers.google.com/chart/) : Renders the report charts on both backend as well as frontend.
* [jQuery Validation Engine](https://github.com/posabsolute/jQuery-Validation-Engine) : Wonderful form validation plugin from Position-absolute. Please note that we are using version 2.2 of this plugin which works while trying to validate a particular div and all form elements inside it.
* Icons : [Oxygen Icons](http://www.oxygen-icons.org/) and [WooFunctions Icon](http://www.woothemes.com/2009/09/woofunction-178-amazing-web-design-icons/)

Also special thanks to *Prateek Sarkar*, *Sayantan Mukherjee* for helping me with the beta testing of the plugin.

== Installation ==

= Automatic Install =

* Go to *WordPress Admin > Plugins > Add New*
* Search for WP Feedback & Survey Manager
* Install and activate

= Manual Installation =

* Download the latest stable release from here.

* Extract all files from the ZIP file, **making sure to keep the file/folder structure intact**, and then upload it to `/wp-content/plugins/`.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

= Plugin Activation =

Go to the admin area of your WordPress install and click on the "Plugins" menu. Click on "Activate" for the "WP Feedback & Survey Manager" plugin.

= Plugin Usage =

This is pretty much straight forward...

* **Setup**: Go to **Feedback > Settings** and setup the feedbacks and surveys. Remember, you can turn features off if you wish to.
* Use the shortcode `[feedback]` to display the feedback form on your blog
* Goto **Feedback > Survey Reports** or **Feedback > View all feedbacks** to view the reports
* Show the Trend of survey from latest 100 reports using `[feedback_trend]` shortcode

= Upgrading the Plugin =

This is first release. But on upcoming release, automatic upgradation should work.

== Frequently Asked Questions ==

Please visit the [documentation](http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/) page for an updated list of FAQs

== Screenshots ==

1. Survey Tab of the Form
2. Feedback Tab of the form
3. Personal Information Tab - showing JS validation
4. Admin - Dashboard
5. Admin - Survey Reports - Generates reports on the basis of all database entries (using ajax loader)
6. Admin - View a single feedback
7. Admin - View all feedbacks
8. Admin - Settings page - modify almost every aspects of the form
9. BONUS - Trends page shortcode - which shows latest 100 survey report (nicely)

== ChangeLog ==

= Version 1.0.1 =
* Generated the POT file for translation.
* Fixed some typo in the readme file.
* Added the WP support link to the admin section.
* Added error message on [feedback_trend] for no enabled surveys
* **Added**: /translations/fbsr-en_US.pot
* **Added**: /changelog
* **Updated**: /readme.txt
* **Updated**: /feedback_survey.php
* **Updated**: /classes/install-class.php
* **Updated**: /classes/form-class.php

= Version 1.0.0 =
* Public Release

== Upgrade Notice ==

= 1.0.1 =
Maintenance update
Added: POT file for translation

= 1.0.0 =
First public release of the plugin
