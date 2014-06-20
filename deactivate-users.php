<?php
/**
 * Plugin Name: Deactivate WordPress Users
 * Plugin URI: http://crane-west.com
 * Description: Allows admins to deactivate a user as opposed to deleting a user. Works with web and XML-RPC based authentication.
 * Version: 1.0
 * Author: Eric Binnion, Crane|West
 * Author URI: http://crane-west.com
 * License: GPLv2 or later
 */

class Deactivate_Users {

	/**
	 * Member data for ensuring singleton pattern
	 */
	private static $instance = null;

	/**
	 * Adds all of the filters and hooks
	 */
	function __construct() {

		// Enforces a single instance of this class.
		if ( isset( self::$instance ) ) {
			wp_die( esc_html__( 'The Deactivate_Users class has already been loaded.', 'deactivate-users' ) );
		}

		self::$instance = $this;

		add_action( 'init', array( $this, 'init' ), 9999 );
		add_filter( 'cmb_meta_boxes', array( $this, 'add_user_meta' ), 12 );
		add_filter( 'authenticate', array( $this, 'validate_active_user' ), 50, 3 );
	}

	/**
	 * Include the cmb_Meta_Box class for adding meta boxes if not already included
	 */
	function init() {
		if ( ! class_exists( 'cmb_Meta_Box' ) ) {
			require_once( 'lib/metabox/init.php' );
		}
	}

	/**
	 * Used to check that user is valid and active.
	 *
	 * @param WP_User user object
	 * @param string $username
	 * @param string $password
	 *
	 * @return WP_User if user is valid and active
	 * @return WP_Error on failure
	 */
	function validate_active_user( $user, $username, $password ) {

		// This is the case for the user failing authentication in another process.
		// In this case, let's just return the WP_Error object;
		if ( is_wp_error( $user ) ) {
			return $user;
		} else {
			if( ! isset( $user ) ) {

				// This is the case for the user not being authenticated, in which case, let's validate
				// the login cookie to get the user_id
				$user_id = wp_validate_auth_cookie();

				if ( $user_id ) {
					$user = new WP_User( $user_id );
				} else {
					return new WP_Error( 'expired_session', __( 'Please log in again.' ) );
				}
			}

			$deactivated = get_user_meta( $user->ID, '_deactivate_users_deactivate', true );

			// This is the case for the user being deactivated.
			if( 'on' == $deactivated ) {

				// Clear any auth cookie if set.
				wp_clear_auth_cookie();
				return new WP_Error( 'deactivated_user', __( 'This user has been deactivated. Please contact the administrator.' ) );
			} else {
				return $user;
			}
		}
	}

	/**
	 * Uses the Custom Metaboxes and Fields project to create a checkbox
	 * that is used to deactivate users.
	 * Docs here: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki
	 *
	 * @param array An array of meta boxes
	 *
	 *@return array An array of meta boxes
	 */
	function add_user_meta( $meta_boxes ) {
		$user = wp_get_current_user();

		/*
		 * Only add the checkbox if current user is an administrator, current profile is not current user's profile,
		 * and the current profile is not an admin or the current user is user 1.
		 */
		if( current_user_can( 'edit_users' ) && isset( $_GET['user_id'] ) && ( ! user_can( $_GET['user_id'], 'edit_users' ) || 1 == $user->ID ) ) {
			$prefix = '_deactivate_users_';

			$meta_boxes[] = array(
				'id'         => '_deactivate_user_meta',
				'title'      => 'Deactivate User?',
				'pages'      => array( 'user' ), // post type
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, // Show field names on the left
				'fields'     => array(
					array(
					    'name' => 'Deactivate user?',
					    'desc' => 'Check this box to deactivate the user.',
					    'id'   => $prefix . 'deactivate',
					    'type' => 'checkbox'
					),
				),
			);
		}

		return $meta_boxes;
	}
}

new Deactivate_Users();
