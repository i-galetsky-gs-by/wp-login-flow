<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Login_Flow_User_Activation extends WP_Login_Flow_User {


	function __construct() {

	}

	function check( $user_id ) {

		$status = get_user_meta( $user_id, 'activation_status', false );
		// Meta key does not exist, probably existing user
		if ( is_array($status) && empty( $status ) ) {
			if( get_option( 'wplf_require_existing_activation' ) ) return false;
			return true;
		}

		// User has activated, activation_status = 1
		if( is_array( $status ) && $status[0] ) return true;

		// User has not activated, activation_status = 0
		return false;
	}

	/**
	 * @param      $user_id
	 * @param int  $activated
	 */
	public static function set( $user_id, $activated = 1 ) {
		if( $activated ) {
			update_user_meta( $user_id, 'activation_date', time() );
		} else {
			update_user_meta( $user_id, 'activation_signup', time() );
		}

		update_user_meta( $user_id, 'activation_status', $activated );

	}

	public function get_url( $key, $user_login ){

		if( get_option( 'wplf_rewrite_activate' ) && get_option( 'wplf_rewrite_activate_slug' ) ){
			$url = trailingslashit( get_option( 'wplf_rewrite_activate_slug' ) . '/' . rawurlencode( $user_login ) . '/' . $key );
		} else {
			$url = "wp-login.php?action=rp&login=" . rawurlencode( $user_login ) . "&key=" . $key;
		}

		return network_site_url( $url, 'login' );

	}

	function send_admin_email( $user ){

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$message = sprintf( __( 'New user activation on your site %s:' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "\r\n";

		@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Activation' ), $blogname ), $message );

	}
}