<?php
/**
 * Back-end functionality to support the Article Preview block.
 */

namespace BlockParty\ArticlePreview;

const HASH_ALGO        = 'sha256'; // Hashing algorithm to use to generate cache keys.
const CACHE_EXPIRATION = DAY_IN_SECONDS; // Maximum cache lifetime;
const UA_STRING        = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0';
const ABILITY_CATEGORY = 'block-party';

/**
 * Helper for fetching the open graph data from supplied URL.
 * 
 * @param string $url          The url to fetch OG tags from.
 * @param bool   $bypass_cache Bypass the cache? Default is false.
 * @return array
 */
function fetch_open_graph( $url, $bypass_cache = false ) {
	// Check for cached values first.
	$cache_key    = 'article_preview_' . hash( HASH_ALGO, $url );
	$cached_fetch = get_transient( $cache_key );

	if ( ! empty( $cached_fetch ) && $bypass_cache === false ) {
		return $cached_fetch;
	}

	// Nothing in the cache, start fetching.
	$open_graph_tags = [];

	try {
		$client = new \Embed\Http\CurlClient();
		$client->setSettings([
			'user_agent' => UA_STRING,
		]);
		$embed  = new \Embed\Embed( new \Embed\Http\Crawler( $client ) );
		$info   = $embed->get( $url );

		error_log( print_r( $info, true) );
		
		$open_graph_tags['title']       = $info->title;
		$open_graph_tags['description'] = $info->description;
		$open_graph_tags['image']       = (string) $info->image;

		$alt   = '';
		$metas = $info->getMetas();
		if ( ! empty( $metas->str( 'og:image:alt' ) ) ) {
			$alt = $metas->str( 'og:image:alt' );
		} else if ( ! empty( $metas->str( 'twitter:image:alt' ) ) ) {
			$alt = $metas->str( 'twitter:image:alt' );
		}
		$open_graph_tags['alt'] = $alt;

		// Cache results.
		set_transient( $cache_key, $open_graph_tags, CACHE_EXPIRATION );
	} catch (\Exception $e) {
		error_log($e->getMessage());
	}

	return $open_graph_tags;
}

/**
 * Register REST API endpoint for fetching Open Graph data.
 */
function register_rest_routes() {
	register_rest_route(
		'article-preview/v1',
		'/open-graph',
		array(
			'methods'             => 'GET',
			'callback'            => __NAMESPACE__ . '\open_graph_endpoint',
			'permission_callback' => '__return_true',
			'args'                => array(
				'url' => array(
					'required'          => true,
					'type'              => 'string',
					'validate_callback' => function( $param, $request, $key ) {
						return filter_var( $param, FILTER_VALIDATE_URL ) !== false;
					},
					'sanitize_callback' => 'esc_url_raw',
				),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_routes' );

/**
 * REST API endpoint callback for fetching Open Graph data.
 *
 * @param \WP_REST_Request $request The REST API request object.
 * @return \WP_REST_Response|\WP_Error The response or error.
 */
function open_graph_endpoint( $request ) {
	$url = $request->get_param( 'url' );

	if ( empty( $url ) ) {
		return new \WP_Error(
			'missing_url',
			'URL parameter is required.',
			array( 'status' => 400 )
		);
	}

	$open_graph_data = fetch_open_graph( $url );

	if ( empty( $open_graph_data ) ) {
		return new \WP_Error(
			'no_open_graph_data',
			'No Open Graph data found for the provided URL.',
			array( 'status' => 404 )
		);
	}

	return rest_ensure_response( $open_graph_data );
}

/**
 * Register an ability for the open graph fetching function.
 */
function register_ability() {
	wp_register_ability(
		\BlockParty\ABILITY_CATEGORY_SLUG . '/fetch-open-graph',
		[
			'label'               => __( 'Fetch Open Graph Tags', 'block-party' ),
			'description'         => __( 'Fetches open graph tags for given url.', 'block-party' ),
			'category'            => \BlockParty\ABILITY_CATEGORY_SLUG,
			'input_schema'        => [
				'type'   => 'string',
				'format' => 'uri',
			],
			'output_schema'       => [
				'type' => 'object',
			],
			'execute_callback'    => __NAMESPACE__ . '\fetch_open_graph',
			'permission_callback' => '__return_true',
			'meta'                => [
				'show_in_rest' => true,
			],
		]
	);
}
add_action( 'wp_abilities_api_init', __NAMESPACE__ . '\register_ability' );
