<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 * 
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 */

$prepend           = $attributes['prependText'];
$format            = $attributes['dateFormat'] ?: get_option( 'date_format' );
$display_date      = get_the_date( $format );
$attribute_date    = get_the_date();
$display_modified  = $attributes['displayModified'];
if ( $display_modified ) {
    $modified_display   = get_the_modified_date( $format );
    $modified_attribute = get_the_modified_date();
}
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
    <div class='publication-date-post-date'>
        <?php if ( $prepend ): ?>
            <span class='publication-date-prepend'><?php echo esc_html( $prepend ); ?></span>
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
