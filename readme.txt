=== Auto Post to Discord ===
Contributors: Vincent Guigui
Tags: discord, post, forum, publish, server, chat, gaming, streaming, community, blog
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 1.0.5
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Auto Post to Discord integrates with WordPress to send your new post to discord channels.

== Description ==

Auto Post to Discord integrates with WordPress and WooCommerce (if installed) to send your new post and orders to discord channels. You can configure multiple channels separately for your blog posts or WooCommerce orders. 

### Features ### 

 * Send new post to Discord
 * Send new post as thread to Discord forum


### Send New Post to Discord ### 
After you configure the Webhook URLs, all of your published posts of the selected category will be sent to Discord automatically. 
You can select a separate channel for each category. You will also be able to select a default channel for posts that don't match any category. If your post has multiple categories, the post will only be sent to the first one.
If you want to post as a thread, you can declare a thread name (eg: "%title%)".

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the /wp-content/plugins/auto-post-to-discord directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. Go to the settings panel and ‘Auto Post to Discord’ to setup webhook URLs

== Frequently Asked Questions ==

= How do I use it? =

Go to the settings panel to configure the settings. Once configured, the plugin works in the background. 

= Posts not being posted to all channels? =

If your post has multiple categories, the post will only go to the channel for the first category.  

= Is there a pro-version? =

The plugin is free. There's no lock-ins or paid promotions needed. However, if you need a feature, feel free to contact the developer for modifications. 

== Screenshots ==

1. Post Channel Setup

== Changelog ==

= 1.0.0 =
* Initial Release

= 1.0.1 =
* Select Avatar URL form the media gallery
* Allow to override the channel in the post metabox
*  Mention everyone bug fixed
* Allow to override the mention everyone in the post metabox

= 1.0.2 =
* Fixed issue with disable embed content
* Renamed All (the default category) to Default to avoid confusion 
* Minor Fixes

= 1.0.3 =
* Auto Post to Discord branding
* Add Thread setting

= 1.0.4 =
* Mapping from Wordpress Category name to Discord tags ID
* Post are sent even if created by a cron/plugin
