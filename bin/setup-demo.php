<?php
/**
 * Demo Content Setup Script (WP-CLI)
 *
 * Run from Studio site root:
 *   studio wp eval-file wp-content/themes/infiniti/bin/setup-demo.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_log = function ( $msg ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::log( $msg );
	} else {
		echo $msg . "\n";
	}
};

require_once get_template_directory() . '/includes/demo-content.php';

$result = infiniti_install_demo_content( $_log );

if ( class_exists( 'WP_CLI' ) ) {
	if ( $result ) {
		WP_CLI::success( 'Demo content setup complete!' );
	} else {
		WP_CLI::error( 'Demo content setup failed.' );
	}
}
