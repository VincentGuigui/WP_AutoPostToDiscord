<?php
/**
 * WP Discord Post Plus HTTP
 *
 * @author      Nicola Mustone, Vincent Guigui
 * @license     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class of the requests handler for WP Discord Post Plus.
 */
class WP_Discord_Post_Plus_HTTP {
	/**
	 * The bot username.
	 *
	 * @var string
	 * @access private
	 */
	private $_username = '';

	/**
	 * The bot avatar URL.
	 *
	 * @var string
	 * @access private
	 */
	private $_avatar = '';

	/**
	 * The bot token.
	 *
	 * @var string
	 * @access private
	 */
	private $_token = '';

	/**
	 * The webhook URL.
	 *
	 * @var string
	 * @access private
	 */
	private $_webhook_url = '';

	/**
	 * The content of the request.
	 *
	 * @var string
	 * @access private
	 */
	private $_context = '';

	/**
	 * The ID of the context.
	 *
	 * @var string
	 * @access private
	 */
	private $_context_id = '';

	/**
	 * Sets the bot username.
	 *
	 * @param string $username The bot username.
	 */
	public function set_username( $username ) {
		$this->_username = sanitize_text_field( $username );
	}

	/**
	 * Sets the bot avatar.
	 *
	 * @param string $avatar The bot avatar URL.
	 */
	public function set_avatar( $avatar ) {
		$this->_avatar = esc_url_raw( $avatar );
	}

	/**
	 * Sets the bot token.
	 *
	 * @param string $token The bot token.
	 */
	public function set_token( $token ) {
		$this->_token = sanitize_key( $token );
	}

	/**
	 * WooCommerce Order ID
	 *
	 * @var integer
	 * @access private
	 */
	public $order_id = 0;

	public function must_be_sent() {
		return !empty($this->_webhook_url);
	}

	/**
	 * Sets the  webhook URL.
	 *
	 * @param string $url     Sets the webhook URL.
	 * @param string $context The context used for this specific instance.
	 */
	public function set_webhook_url() {
		$context   			  = $this->get_context();
		$id		   			  = $this->_context_id;
		$post_webhooks  	  = get_option( 'wp_discord_post_plus_post_webhook_url' );
		$woocommerce_webhooks = get_option( 'wp_discord_post_plus_settings_webhooks_input' );
		
		if ($context == 'post') {
			if (count($post_webhooks) === 0) {
				return false;
			}
			if (isset($_POST['wp_discord_metabox_override_channel']) && is_numeric($_POST['wp_discord_metabox_override_channel'])) {
				$categories = (array) $_POST['wp_discord_metabox_override_channel'];
			}
			else {
				$categories = get_the_category( $id );
				$category_ids = array();
				foreach ($categories as $category) {
					$category_ids[] = $category->term_id;
				}
				$categories = $category_ids;
			}

			if (count($categories) === 0) {
				return false;
			}
			
			foreach($post_webhooks as $webhooks) {
				if (in_array($webhooks['category'], $categories)) {
					$this->_webhook_url = esc_url_raw( $webhooks['webhook'] );
					return true;
				}

				if ($webhooks['category'] == -1 && !empty($webhooks['webhook'])) {
					$this->_webhook_url = esc_url_raw( $webhooks['webhook'] );
					return true;
				}
			}
		}
		
		if ($context == 'order') {
			if (count($woocommerce_webhooks) === 0) {
				return false;
			}

			$order = wc_get_order($id);

			foreach ($order->get_items() as $item_id => $item_product) {
				$product = $item_product->get_product();
				$category_ids = $product->get_category_ids();

				foreach($woocommerce_webhooks as $webhooks) {
					if (in_array($webhooks['category'], $category_ids)) {
						$this->_webhook_url = esc_url_raw( $webhooks['webhook'] );
						return true;
					}
	
					if ($webhooks['category'] == -1 && !empty($webhooks['webhook'])) {
						$this->_webhook_url = esc_url_raw( $webhooks['webhook'] );
						return true;
					}
				}
			}
		}
		
		if ($context == 'product') {
			//todo:: implement product
			$this->_webhook_url = null;
		}
		return false;
	}

	/**
	 * Sets the context of this request.
	 *
	 * @param string $context The context of this request.
	 */
	public function set_context( $context ) {
		if ( ! empty( $this->get_context() ) ) {
			$this->_context = sanitize_key( $context );
		} else {
			$this->_context = sanitize_key( $context );
		}
	}

	/**
	 * Sets the context ID of this request.
	 *
	 * @param string $id The ID of the context of this request.
	 */
	public function set_context_id( $id ) {
		$this->_context_id = $id;
	}

	/**
	 * Returns the bot username.
	 *
	 * @return string
	 */
	public function get_username() {
		return $this->_username;
	}

	/**
	 * Returns the bot avatar URL.
	 *
	 * @return string
	 */
	public function get_avatar() {
		return $this->_avatar;
	}

	/**
	 * Returns the bot token.
	 *
	 * @return string
	 */
	public function get_token() {
		return $this->_token;
	}

	/**
	 * Returns the webhook URL.
	 *
	 * @return string
	 */
	public function get_webhook_url() {
		return $this->_webhook_url;
	}

	/**
	 * Returns the context of the request.
	 *
	 * @return string
	 */
	public function get_context() {
		return $this->_context;
	}

	/**
	 * Sets up the main properties to process the request.
	 *
	 * @param string $context The context of the request for this instance.
	 * @param string $id The context id of the request for this instance.
	 */
	public function __construct( $context, $id) {
		$this->set_context( $context );
		$this->set_context_id( $id );
		$this->set_username( get_option( 'wp_discord_post_plus_bot_username' ) );
		$this->set_avatar( get_option( 'wp_discord_post_plus_avatar_url' ) );
		$this->set_token( get_option( 'wp_discord_post_plus_bot_token' ) );
		$this->set_webhook_url();
	}

	/**
	 * Processes a request and sends it to Discord.
	 *
	 * @param  string $content The message sent along wih the embed.
	 * @param  array  $embed   The embed content.
	 * @param  int    $id      The post ID.
	 * @return object;
	 */
	public function process($post, $content = '', $embed = array(), $id = 0, $thread_name = null, $tags = null) {

		if (empty($this->_webhook_url)) {
			if ( wp_discord_post_plus_is_logging_enabled() ) {
				error_log( 'WP Discord Post Plus - Request aborted. Webhook URL empty.' );
			}
			return false;
		}

		$response = $this->_send_request( $id, $content, $embed, $thread_name, $tags );

		if ( ! is_wp_error( $response ) && ( $response["response"]["code"] == 200 || $response["response"]["code"] == 204)) {
			if ( wp_discord_post_plus_is_logging_enabled() ) {
				error_log( 'WP Discord Post Plus - Request sent.' );
				update_post_meta( $id, 'wp_discord_response', "OK:" . print_r($response["body"], true) . print_r($response["response"], true) );
			}
		} else {
			if ( wp_discord_post_plus_is_logging_enabled() ) {
				if (is_wp_error( $response )) {
					update_post_meta( $id, 'wp_discord_response', "KO:" . $response->get_error_message() );
					error_log( sprintf( 'WP Discord Post Plus - Request not sent. %s', $response->get_error_message() ) );
				}
				else {
					update_post_meta( $id, 'wp_discord_response', "KO:" . print_r($response["response"], true));
					error_log( sprintf( 'WP Discord Post Plus - Request not sent. %s', print_r($response["response"], true) ) );
				}
			}
		}

		return $response;
	}

	/**
	 * Handles the HTTP request and returns a response.
	 *
	 * @param  string $id			Id of the post
	 * @param  string $content		The content of the request
	 * @param  array  $embed		The embed content
	 * @param  array  $threadname	The thread name
	 * @param  array  $tags			List of tags
	 * @return object
	 * @access private
	 */
	private function _send_request( $id, $content, $embed, $thread_name, $tags ) {
		$args = array(
			'content'    => html_entity_decode( esc_html( $content ) ),
			'username'   => esc_html( $this->get_username() ),
			'avatar_url' => esc_url( $this->get_avatar() )
		);

		if ( ! empty($thread_name))
			$args['thread_name'] = strlen($thread_name) > 90 ? substr($thread_name,0,90)."..." : $thread_name;;
		if ( ! empty($tags)) {
			$args['applied_tags'] = $tags;

		}

		if ( ! empty( $embed ) ) {
			$args['embeds'] = WP_Discord_Post_plus_Formatting::get_embed( $embed );
		}

		$args = apply_filters( 'wp_discord_post_plus_request_body_args', $args );

		$request = apply_filters(
			'wp_discord_post_plus_request_args',
			array(
				'headers' => array(
					'Authorization' => 'Bot ' . esc_html( $this->get_token() ),
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $args ),
			)
		);

		if ( wp_discord_post_plus_is_logging_enabled() ) {
			error_log( print_r( $request, true ) );
			update_post_meta( $id, 'wp_discord_request', print_r( $request, true ) );

		}

		do_action( 'wp_discord_post_plus_before_request', $request, $this->get_webhook_url() );

		$response = wp_remote_post( esc_url( $this->get_webhook_url() ), $request );

		do_action( 'wp_discord_post_plus_after_request', $response );

		return $response;
	}

}
