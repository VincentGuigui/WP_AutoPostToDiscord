<?php
/**
 * WP Discord Post Plus Metaboxes
 *
 * @author      M Yakub Mizan
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class to show metaboxes on order, post and proudct page.
 */
class WP_Discord_Post_Plus_Metabox {
	/**
	 * Adds the hook to handle posts.
	 */
	public function __construct() {
        add_action('add_meta_boxes', array($this, 'custom_meta_boxes'), 10, 1);
        add_action('publish_post', array($this, 'publish_post'), 20, 2);
    }
    
    /**
     * Custom metabox callback
     */
    public function custom_meta_boxes()
    {
        $screens = ['post',];
        foreach ($screens as $screen) {
            add_meta_box(
                'wp_discord_plus_metabox',          
                'WP Discord Plus',  
                array($this, 'custom_meta_boxes_html'),  
                $screen, 
                'side'
            );
        }
    }

    public function custom_meta_boxes_html($post)
    {
        $mention_checked = '';

        if ( get_option( 'wp_discord_post_plus_mention_everyone' ) === 'yes' ) {
            $mention_checked = 'checked="checked"';
        }

        $channels = get_option('wp_discord_post_plus_post_webhook_url');
        
    ?>
        <div>
            <br />
            <input id='wp_discord_metabox_send_flag' name='wp_discord_metabox_send_flag' type="checkbox" value="yes">
            <label for="wp_discord_metabox_send_flag">Send to Discord</label>
        </div>
    
        <div>
            <br />
            <input id='wp_discord_metabox_mention_flag' name='wp_discord_metabox_mention_flag' type="checkbox" value="yes" <?php echo $mention_checked; ?>>
            <label for="wp_discord_metabox_mention_flag"> Mention @everyone </label>
        </div>
        <hr />
        <p> If you want to override the channel for this particular post, select it from the list. </p> 
        <?php if (!empty($channels)): ?>
        <div>
            <label for="wp_discord_metabox_mention_flag"> Override Channel </label>
            <br />
            <select name='wp_discord_metabox_override_channel'> 
                <option value=''> Select A Channel </option>
               <?php 
                foreach($channels as $channel) {
                    echo "<option value='" . $channel['category'] . "'> " . $channel['chatroom'] . "</option>";
                } ?>
            </select>
        </div>
        <?php
        endif; 
    }

    public function publish_post($post_id, $post)
    {
        if (get_post_status($post_id) == 'auto-draft') {
            return;
        }

        $discord_flag = false;
        // in case of interactive publishing 
        if (isset($_POST['post_status']) && $_POST['post_status'] === "publish") {
            if (isset($_POST['wp_discord_metabox_send_flag'])) {
                $discord_flag = true;
            }
        } else { 
            // non interactive publishing (eg: cron), discord metas may be empty so lets try to send it
            // there will be a category check anyway
            $discord_flag = true;
        }
        
        if ( $discord_flag ) {
            do_action('send_post_to_discord', $post_id, $post);
        }
    }
    
}

return new WP_Discord_Post_Plus_Metabox();
