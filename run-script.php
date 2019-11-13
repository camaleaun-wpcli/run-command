<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Run scripts.
 *
 * @when before_wp_load
 */
$wpcli_run_command = dirname( __FILE__ ) . '/src/Run_Command.php';
if ( file_exists( $wpcli_run_command ) ) {
	require_once $wpcli_run_command;
}
WP_CLI::add_command( 'run', 'Camaleaun\Run_Command' );
