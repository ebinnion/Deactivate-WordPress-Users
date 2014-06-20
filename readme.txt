=== Plugin Name ===
Contributors: ebinnion, cranewest
Donate link: http://manofhustle.com/
Tags: deactivate, users, disable, user, authentication
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows admins to deactivate a user as opposed to deleting a user. Works with web and XML-RPC based authentication.

== Description ==

Allows admins to deactivate a user as opposed to deleting a user. Works with web and XML-RPC based authentication.

Once installed and activated, a checkbox appears on the user profile settings page. When checked, the user will not be able to login with the account. If they attempt to login, `This user has been deactivated. Please contact the administrator.` will be displayed in the login error box.


== Installation ==
Installation is standard, via FTP or via the plugin downloader in WordPress admin.


== Changelog ==

= 1.1 =
* Restricts display of checkbox
* Users can no longer deactivate selves
* Must be user 1 to deactivate an admin

= 1.0 =
* First working version of the plugin
