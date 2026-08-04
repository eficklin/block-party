<?php
/**
 * Plugin Name:       Block Party
 * Description:       My personal collection of starter code for custom blocks and other useful snippets gathered from many years of freelancing and agency work.
 * Version:           1.0.0
 * Requires at least: 6.9
 * Requires PHP:      8.3
 * Author:            Edward Ficklin
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ef-block-party
 */

namespace BlockParty;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function block_init() {
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}

	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', __NAMESPACE__ . '\block_init' );
