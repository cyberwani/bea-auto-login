<?php
/*
Plugin Name: Bea Autologin
Version: 1.0.2
Description: Autolog the user if constants defined. DO NOT USE IN PRODUCTION.
Author: Be API Technical team
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Bea_Autologin {
	function __construct() {
		if ( ! $this->is_active() ) {
			return;
		}

		add_action( 'wp_authenticate', [ $this, 'authenticate' ], 10, 2 );
	}

	/**
	 * Check if the plugin is active.
	 *
	 * @return bool
	 */
	private function is_active() {
		if ( ! defined( 'BEA_AUTOLOGIN_LOGIN' ) || ! defined( 'BEA_AUTOLOGIN_PASS' ) || ! defined( 'BEA_AUTOLOGIN_IP' ) ) {
			return false;
		}

		return ! empty( BEA_AUTOLOGIN_LOGIN ) && ! empty( BEA_AUTOLOGIN_PASS ) && ! empty( BEA_AUTOLOGIN_IP );
	}

	/**
	 * On connection, whether this is a supported login autolog the user and set the password to a defined one.
	 *
	 *
	 * @param string $credentials : the user login
	 * @param string $password : the password typed by the user
	 *
	 */
	public function authenticate( $credentials, &$password ) {
		if ( BEA_AUTOLOGIN_LOGIN !== $credentials || BEA_AUTOLOGIN_IP !== $_SERVER['REMOTE_ADDR'] ) {
			return;
		}
		wp_set_password( BEA_AUTOLOGIN_PASS, get_user_by( 'login', BEA_AUTOLOGIN_LOGIN )->ID );

		$password = BEA_AUTOLOGIN_PASS;
	}
}

new Bea_Autologin();
