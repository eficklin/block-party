<?php
/**
 * Back-end functionality to support eh Article Preview block.
 */

namespace BlockParty\ArticlePreview;

// Helper for fetching the actual OG data from supplied URL.
// Will need caching of tags at least. Would we want to get fancy and fetch the image and store in media lib?
function fetch_open_graph( $url ) {
	$open_graph_tags = [];

	try {
		$client = new \Embed\Http\CurlClient();
		$client->setSettings([
			'user_agent' => 'Mozilla/5.0 (compatible; SimpleScraper)'
		]);
		$embed  = new \Embed\Embed( new \Embed\Http\Crawler( $client ) );
		$info   = $embed->get( $url );
		
		$open_graph_tags['title']       = $info->title;
		$open_graph_tags['description'] = $info->description;
		$open_graph_tags['image']       = (string) $info->image;

		$alt = '';
		$metas = $info->getMetas();
		if ( ! empty( $metas->str( 'og:image:alt' ) ) ) {
			$alt = $metas->str( 'og:image:alt' );
		} else if ( ! empty( $metas->str( 'twitter:image:alt' ) ) ) {
			$alt = $metas->str( 'twitter:image:alt' );
		}
		$open_graph_tags['alt'] = $alt;
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