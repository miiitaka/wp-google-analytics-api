<?php
/*
Plugin Name: WordPress Google Analytics API
Plugin URI: https://github.com/miiitaka/wp-google-analytics-api
Description:
Version: 1.0.0
Author: Kazuya Takami
Author URI: http://programp.com/
License: GPLv2 or later
Text Domain: wp-google-analytics-api
Domain Path: /languages
*/

new Wp_Google_Analytics_Api();

/**
 * Basic Class
 *
 * @author  Kazuya Takami
 * @version 1.0.0
 * @since   1.0.0
 */
class Wp_Google_Analytics_Api {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain = 'wp-google-analytics-api';
	private $version     = '1.0.0';

	/**
	 * Constructor Define.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function __construct () {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}

	/**
	 * Add Menu to the Admin Screen.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function admin_menu () {
		add_menu_page(
			esc_html__( 'Google Analytics API', $this->text_domain ),
			esc_html__( 'Google Analytics API', $this->text_domain ),
			'manage_options',
			plugin_basename( __FILE__ ),
			array( $this, 'list_page_render' )
		);
	}

	/**
	 * Admin List Page Template Require.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	public function list_page_render () {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-google-analytics-api-admin-list.php' );
		new Wp_Google_Analytics_Api_Admin_List( $this->text_domain );
	}
}