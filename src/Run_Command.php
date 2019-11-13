<?php

/**
 * Run scripts
 */

namespace Camaleaun;

use WP_CLI;
use WP_CLI\Utils;
use Mustangostang\Spyc;

/**
 * Run scripts.
 *
 * ## OPTIONS
 *
 * <script>
 * : The name of script.
 *
 * ## EXAMPLES
 *
 *     $ wp run install
 *     Success: Finish script run.
 *
 * @when before_wp_load
 */
class Run_Command {

	/**
	 * Run scripts.
	 *
	 * ## OPTIONS
	 *
	 * <script>
	 * : The name of script.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp run install
	 *     Success: Finish script run.
	 *
	 * @when before_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $script ) = $args;
		$extra_config = WP_CLI::get_runner()->extra_config;
		$scripts      = isset( $extra_config['scripts'] ) ? $extra_config['scripts'] : null;

		if ( ! isset( $scripts ) || ! isset( $scripts[ $script ] ) ) {
			WP_CLI::error( "Script '$script' not defined." );
		}

		foreach ( $scripts[ $script ] as $command ) {
			$command = preg_replace( '/^wp\s+/', '', $command );
			WP_CLI::log( $command );
			WP_CLI::runcommand( $command, array( 'return' => 'all' ) );
		}

		// WP_CLI::log( 'Test' );
		// var_dump( $extra_config );
	}
}
