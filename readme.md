# Auto Post as Discord Post or Forum

Automatically send new posts from a WP category to Discord as individual post or as a thread (for forum).

This is a modified version of WP Discord Post plugin by WP Tasker and Nicola Mustone that is available on the marketplace.

**Principle:** Any new post in a specific category will be posted to Discord

## Installation
1. Upload the plugin files to the /wp-content/plugins/auto-post-to-discord directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the ‘Plugins’ screen in WordPress
3. Go to the settings panel then ‘Auto Post to Discord’ to setup webhook URLs for the categories


## Manual publishing
When you create a post, you need to enable the "Send to Discord" checkbox in the Meta box of the post.
Ensure, there is a webhook defined for one of the category of the post.

## Auto publishing
If you have an automated process to create posts (eg: wpematico, cron, importer...), it should work as long as the posts have the right category.

## Wordpress Categories to Discord Tags (forum)
In ‘Auto Post to Discord’ settings, you can give a list of categoris (by name) and their respective Discord tags ID.
The textbox allows you to enter one mapping per line using the following format:
``
category_name:tag_id
``

eg: 
```
Job Offer:123456789
Blog:98765321
News:52754376
```
If you need a tag to be applied whatever the categories, use ``Forced:tag_id``

**Note**: To get the Discord Tags IDs, go to Discord and right-click on any existing tag and copy to clipboard.