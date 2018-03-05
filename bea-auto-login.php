<?php
/*
 Plugin Name: Be API - Autologin
 Plugin URI: https://github.com/BeAPI/bea-auto-login
 Description: Autolog the user if constants defined. DO NOT USE IN PRODUCTION.
 Author: Be API Technical team
 Author URI: http://www.beapi.fr

 ----

 Copyright 2018 Be API Technical team (technique@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
