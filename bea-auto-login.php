<?php
/*
Plugin Name: Be API - Autologin
Version: 2.0.1
Description: Autolog the user if constants defined. DO NOT USE IN PRODUCTION.
Author: Be API Technical team
Network: true
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Bea_Autologin {
	function __construct() {
		add_action( 'wp_authenticate', [ $this, 'authenticate' ], 10, 2 );
		add_action( 'wp', [ $this, 'autologin_url' ] );
	}

	/**
	 * On connection, whether this is a supported login autolog the user and set the password to a defined one.
	 *
	 *
	 * @param string $credentials : the user login
	 * @param string $password    : the password typed by the user
	 *
	 * @return bool
	 *
	 */
	public function authenticate( $credentials, &$password ) {
		if ( ! defined( 'BEA_AUTOLOGIN_LOGIN' ) || ! defined( 'BEA_AUTOLOGIN_PASS' ) || ! defined( 'BEA_AUTOLOGIN_IP' ) ) {
			return false;
		}

		if ( empty( BEA_AUTOLOGIN_LOGIN ) || empty( BEA_AUTOLOGIN_PASS ) || empty( BEA_AUTOLOGIN_IP ) ) {
			return false;
		}

		if ( BEA_AUTOLOGIN_LOGIN !== $credentials || BEA_AUTOLOGIN_IP !== $_SERVER['REMOTE_ADDR'] ) {
			return false;
		}
		wp_set_password( BEA_AUTOLOGIN_PASS, get_user_by( 'login', BEA_AUTOLOGIN_LOGIN )->ID );

		$password = BEA_AUTOLOGIN_PASS;
	}

	/**
	 * On access to specific page, autologin user defined or login to first admin user
	 *
	 * @return bool
	 *
	 */
	public function autologin_url() {
		if ( is_user_logged_in() ) {
			return false;
		}

		if ( ! defined( 'BEA_AUTOLOGIN_SLUG' ) || ! defined( 'BEA_AUTOLOGIN_IP' ) ) {
			return false;
		}

		if ( empty( BEA_AUTOLOGIN_SLUG ) || empty( BEA_AUTOLOGIN_IP ) ) {
			return false;
		}

		if ( BEA_AUTOLOGIN_IP !== $_SERVER['REMOTE_ADDR'] ) {
			return false;
		}

		global $wp;
		if ( BEA_AUTOLOGIN_SLUG != $wp->request ) {
			return false;
		}

		$admin = $this->get_admin();
		if ( empty( $admin ) ) {
			return false;
		}

		// Login
		wp_set_current_user( $admin->ID, $admin->user_login );
		wp_set_auth_cookie( $admin->ID );
		do_action( 'wp_login', $admin->user_login );

		wp_safe_redirect( admin_url() );
		exit;
	}

	/**
	 * If constant login defined, get value or get first admin user
	 *
	 * @return WP_User|bool
	 *
	 */
	public function get_admin() {
		if ( defined( 'BEA_AUTOLOGIN_LOGIN' ) && ! empty( BEA_AUTOLOGIN_LOGIN ) ) {
			$admin = get_user_by( 'login', BEA_AUTOLOGIN_LOGIN );
		} else {
			if ( is_multisite() ) {
				$admins      = get_super_admins();
				$admin_login = reset( $admins );
				$admin       = get_user_by( 'login', $admin_login );
			} else {
				$admins = get_users( [
					'role'    => 'administrator',
					'number'  => 1,
					'orderby' => 'ID',
					'order'   => 'ASC',
				] );

				$admin = reset( $admins );
			}
		}

		return $admin;
	}
}

new Bea_Autologin();
