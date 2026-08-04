<?php
/**
 * Article Preview block template.
 * 
 * $attributes (array): The block attributes.
 * $content (string): The block default content.
 * $block (WP_Block): The block instance.
 */

// No output if url attribute is not set.
if ( empty( $attributes['url'] ) ) {
	return;
}

$og = \BlockParty\ArticlePreview\fetch_open_graph( $attributes['url'] );

if ( empty( $og ) ) {
	return;
}

$classes = "has-layout--{$attributes['layout']}";

$title = ! empty( $attributes['titleOverride'] )
	? $attributes['titleOverride'] 
	: ( ! empty( $og['title'] ) ? $og['title'] : '' );

$description = ! empty( $attributes['descriptionOverride'] ) 
	? $attributes['descriptionOverride'] 
	: ( ! empty( $og['description'] ) ? $og['description'] : '' );

$image_url = ! empty( $attributes['imageURLOverride'] ) 
	? $attributes['imageURLOverride'] 
	: ( ! empty( $og['image'] ) ? $og['image'] : '' );

$image_alt = ! empty( $attributes['imageAltOverride'] ) 
	? $attributes['imageAltOverride'] 
	: ( ! empty( $og['image:alt'] ) ? $og['image:alt'] : '' );

// Unique id for aria attributes.
$id = wp_unique_id('block-');
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => $classes ] ); ?>>
	<a href="<?php echo esc_url( $attributes['url'] ); ?>" target="blank" aria-labelledby="<?php echo $id; ?>-title">
		<div class="wrapper">
			<?php if ( $image_url ) : ?>
				<div class="image">
					<img src="<?php echo esc_attr( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
				</div>
			<?php endif; ?>
			<div class="text">
				<div class="title" id="<?php echo $id; ?>-title" ><?php echo esc_html( $title ); ?></div>
				<?php if ( $description ) : ?>
					<div class="description"><?php echo esc_html( $description ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</a>
</div>
