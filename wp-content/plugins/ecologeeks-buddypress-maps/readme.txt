=== BuddyPress Maps ===
Contributors: G.Breant
Donate link: http://dev.benoitgreant.be
Tags: BuddyPress,maps,Google Maps,geolocation,geo,profile
Requires at least: WP 2.9.1, BuddyPress 1.2
Tested up to: WP 3.0-alpha, BuddyPress 1.2.2
Stable tag: 0.30

== Description ==

BuddyPress Maps is a component that allows to find and display location markers on a Google Map.  
It includes several plugins to work with BuddyPress and its API has been coded to allow others plugins to use the component.

You can enable or disable those plugins :
* Profile maps (for saving the user's location)
* Members map (a map with all the user's location)
* Friends map
* Groups maps
* Custom markers

There is an option for the groups maps to allow displaying member location markers, custom group markers (if you need to give your group a specific location) that the group admin can add; or both.

You can also use the plugin's API to add maps to your own plugins.

<h4>Try the demo</h4>
You can registrer on <a href="http://dev.benoitgreant.be/">the demo website</a> to check how it works.
* <a href="http://dev.benoitgreant.be/members/admin/profile">Demo for profile</a>
* <a href="http://dev.benoitgreant.be/wordpress-mu/maps/members">Demo for members map</a>
* <a href="http://dev.benoitgreant.be/wordpress-mu/groups/buddypress-map-for-groups-test/map">Demo for group map</a>

== Installation ==

= BuddyPress Maps = 

* Copy the directory buddypress-maps to wp-content/plugins.
* Activate the plugin site-wide
* Go to the options page (Maps) under the BuddyPress menu and change the options if needed. 
* If you want a custom theme; copy the directory /maps from buddypress-maps/theme to your current theme and edit it.


== Frequently Asked Questions ==

== Screenshots ==
1. Group Map
2. Profile edition (no title/description for the marker)
3. Single marker edition (with title/description)
4. BuddyPress Maps options for Profile/Groups
5. BuddyPress Maps options
6. Profile view
7. BuddyPress Maps in the BuddyPress Classifieds component (several markers)

== Changelog ==
= 0.30 =
* Added groups markers (using tabs).  Each group of markers can be customisable.
* Added cleanup functions when removing a user
* Added custom markers plugin
* Various bug fixes
* Added user information for each marker
= 0.28 =
* Localization fixes
= 0.27 =
* Now uses JSON included with Wordpress rather than custom one for javascript
= 0.26 =
* Fixed infoWindow bug
* Removed "Delete the location group & datas from XProfile" option as it can be confusing
* Added marker icons in the members loop when user has a profile marker
= 0.25 =
* Fixed bugs
* Now admin can batch locate users
= 0.2=
* Lots of stuff rewritten
* Members map
* Friends map
* Groups maps now allow group admin to save custom markers.
= 0.1.6=
* New option to guess user's location from his IP when the map is empty
* Added Localization
= 0.15-alpha =
* Updated plugin information
* Added Screeshots
* Various fixes
= 0.12-beta =
Fixed Bugs
= 0.1-beta =
* First version
