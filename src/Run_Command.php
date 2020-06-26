<?php

/**
 * Run scripts
 */

namespace Camaleaun;

use WP_CLI;
use WP_CLI\Utils;

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
	 *     Success: Finish 'install' script.
	 *
	 * @when before_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $script ) = $args;

		$configurator = WP_CLI::get_configurator();

		list( $config, $extra_config ) = $configurator->to_array();

		$scripts = Utils\get_flag_value( $extra_config, 'scripts' );

		$commands = Utils\get_flag_value( $scripts, $script );

		if ( ! $commands ) {
			WP_CLI::error( "Script '$script' not defined." );
		}

		foreach ( $commands as $command ) {
			if ( ! preg_match( '/^wp\s+/', $command ) ) {
				continue;
			}
			$command = preg_replace( '/^wp\s+/', '', $command );

			$output = WP_CLI::runcommand(
				$command,
				array(
					'return'     => 'all',
					'launch'     => true,
					'exit_error' => false,
				)
			);

			$quiet = Utils\get_flag_value( $config, 'quiet' );
			$quiet = $quiet || preg_match( '/\s+--quiet[\s=$]?/', $command );

			$command = preg_replace( '/\s+--quiet=?[^\s$]*/', '', $command );

			if ( ! $quiet ) {
				WP_CLI::log( WP_CLI::colorize( "Running: %gwp {$command}%n" ) );
			}
			$output = $output->stderr ? $output->stderr : $output->stdout;
			if ( ! empty( $output ) ) {
				self::output( $output, $quiet );
			}
		}
	}

	private static function output( $output, $quiet = false ) {
		if ( empty( $output ) ) {
			return;
		}
		$type    = '';
		$message = $output;
		preg_match( '/^(Success|Warning|Error)?:?\s*([^$]+)/', $output, $match );
		if ( 3 === count( $match ) ) {
			list( $type, $message ) = array_slice( $match, 1 );
		}

		$type = strtolower( $type );
		if ( empty( $type ) ) {
			WP_CLI::line( $message );
		} elseif ( ! $quiet ) {
			if ( $type !== 'error' ) {
				call_user_func( "WP_CLI::$type", $message );
			} else {
				WP_CLI::log( WP_CLI::colorize( '%RError:%n ' . $message ) );
			}
		}
	}
}
