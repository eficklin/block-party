<?php
/**
 * Dynamic rendering for publication date block.
 * 
 * @package publication-date
 */

Namespace PublicationDate;

/**
 * Renders publication date block, a variation on the core post date block.
 * 
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post date for the current post wrapped inside "time" tags.
 */
function render_block( $attributes, $content, $block ) {
    $prepend        = get_post_meta( get_the_ID(), 'publication_date_prepend_text', true );
    $format         = get_post_meta( get_the_ID(), 'publication_date_format', true );
    $display_date   = get_the_date( $format );
    $attribute_date = get_the_date();
    $display_modified  = get_post_meta( get_the_ID(), 'publication_date_display_modified_date', true );
    if ( $display_modified ) {
        $modified_display   = get_the_modified_date( $format );
        $modified_attribute = get_the_modified_date();
    }

    ob_start();
    ?>
    <div class='block-publication-date'>
        <div class='publication-date-post-date'>
            <?php if ( $prepend ): ?>
                <span class='publication-date-preprend'><?php echo esc_html( $prepend ); ?></span>
            <?php endif; ?>
            <time datetime="<?php echo esc_attr( $attribute_date ); ?>">
                <?php echo esc_html( $display_date ); ?>
            </time>
        </div>
        <?php if ( $display_modified ): ?>
            <div class='publication-date-updated'>
                <span class='publication-date-prepend'><?php echo __( 'Updated on', 'publication-date' ) ?></span>
                <time datetime="<?php echo esc_attr( $modified_attribute ); ?>">
                    <?php echo esc_html( $modified_display ); ?>
                </time>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Register the block.
 */
function register_block() {
    register_block_type_from_metadata(
        __DIR__ . '/block.json',
        [
            'render_callback' => __NAMESPACE__ . '\render_block',
        ]
        );
}
add_action( 'init', __NAMESPACE__ . '\register_block' );

/**
 * Register postmeta to store the block's display options and make available in other contexts.
 */
function register_meta() {
    register_post_meta(
        'post',
        'publication_date_format',
        [
            'type'         => 'string',
            'description'  => __( 'Format string for the date display', 'publication-date' ),
            'default'      => get_option( 'date_format' ),
            'single'       => true,
            'show_in_rest' => true,
        ]
    );

    register_post_meta(
        'post',
        'publication_date_prepend_text',
        [
            'type'         => 'string',
            'description'  => __( 'Optional text prepended to the date display', 'publication-date' ),
            'default'      => '',
            'single'       => true,
            'show_in_rest' => true,
        ]
    );

    register_post_meta(
        'post',
        'publication_date_display_modified_date',
        [
            'type'         => 'boolean',
            'description'  => __( 'Whether to include the post\'s modified date or not', 'publicattion-date' ),
            'default'      => false,
            'single'       => true,
            'show_in_rest' => true,
        ]
    );
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_meta' );
