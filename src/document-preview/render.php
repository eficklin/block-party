<?php
/**
 * Document Preview block template.
 * 
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 */

  if ( ! empty( $attributes['mediaId'] ) ) {
    $url      = wp_get_attachment_url( $attributes['mediaId'] );
    $metadata = wp_get_attachment_metadata( $attributes['mediaId'] );
    $type     = get_post_mime_type( $attributes['mediaId'] );
    $title    = get_the_title( $attributes['mediaId'] );
  }

  if ( empty( $url ) ) {
    return;
  }
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
  <?php if ( ! empty( $attributes['documentTitle'] ) ) : ?>
	  <h2 class="document-preview__title"><?php echo wp_kses_post( $attributes['documentTitle'] ); ?></h2>
  <?php endif; ?>
  <?php if ( ! empty( $attributes['description'] ) ) : ?>
	  <p class="document-preview__description"><?php echo wp_kses_post( $attributes['description'] ); ?></p>
  <?php endif; ?>
  <?php if ( $type === 'application/pdf' ) : ?>
    <object class="document-preview__embed--pdf" type="application/pdf" data="<?php echo esc_attr( $url ); ?>"></object>
  <?php else : ?>
    <iframe class="document-preview__embed--mso" src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo esc_url( $url ); ?>"></iframe>
  <?php endif; ?>
  <?php if ( ! empty( $title ) && ! empty( $metadata ) ) : ?>
    <div class="document-preview__meta">
      <?php echo __( 'File', 'document-preview' ); ?>: <?php echo $title; ?> (<?php echo size_format( $metadata['filesize'], 1 ); ?>)
    </div>
  <?php endif; ?>
  <div class="document-preview__download">
    <a class="wp-element-button" href="<?php echo esc_url( $url ); ?>" download><?php echo __( 'Download', 'document-preview' ); ?></a>
  </div>
</div>
