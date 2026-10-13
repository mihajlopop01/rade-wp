<?php
/**
 * Featured product block (mockup screen 01, "01 / Majica"): image (hover shows
 * the second gallery image), name, price, details, size chips + quick add.
 *
 * @var array $args { product: WC_Product, index: int, id: string }
 */
$product = $args['product'];
$data    = rade_product_data( $product );
$gallery = $data['gallery'];
$main    = $gallery[0]['large'] ?? wc_placeholder_img_src( 'large' );
$hover   = $gallery[1]['large'] ?? '';
$link    = $product->get_permalink();
?>
<section class="ro-feature" <?php echo $args['id'] ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?> data-ro-card data-name="<?php echo esc_attr( $product->get_name() ); ?>" data-price="<?php echo esc_attr( rade_price_label( $product ) ); ?>" data-img="<?php echo esc_url( $gallery[0]['thumb'] ?? $main ); ?>" data-variations="<?php echo esc_attr( wp_json_encode( $data['variations'] ) ); ?>">
	<a class="ro-feature__media" href="<?php echo esc_url( $link ); ?>">
		<img src="<?php echo esc_url( $main ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php if ( $hover ) : ?><img class="ro-feature__hover" src="<?php echo esc_url( $hover ); ?>" alt="" loading="lazy"><?php endif; ?>
	</a>
	<div class="ro-feature__info">
		<div class="ro-eyebrow"><?php echo esc_html( sprintf( '%02d', $args['index'] + 1 ) ); ?> / Majica</div>
		<div class="ro-feature__head">
			<h2 class="ro-feature__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h2>
			<div class="ro-feature__price" data-ro-card-price><?php echo esc_html( rade_price_label( $product ) ); ?></div>
		</div>
		<?php if ( $data['details'] ) : ?>
			<div class="ro-feature__details">
				<?php foreach ( $data['details'] as [ $label, $value ] ) : ?><div><?php echo esc_html( $label . ': ' . $value ); ?></div><?php endforeach; ?>
				<div>Dostava: 2-5 radnih dana</div>
			</div>
		<?php endif; ?>
		<?php if ( $data['sizes'] ) : ?>
			<div class="ro-quick">
				<div class="ro-label ro-d">Veličina</div>
				<div class="ro-quick__sizes" data-ro-card-sizes>
					<?php foreach ( $data['sizes'] as $s ) : ?>
						<button type="button" class="ro-size" data-ro-card-size data-value="<?php echo esc_attr( $s['value'] ); ?>" data-label="<?php echo esc_attr( $s['label'] ); ?>" aria-pressed="false"><?php echo esc_html( $s['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="ro-error" hidden data-ro-card-error>Izaberite veličinu.</div>
				<a class="ro-btn ro-btn--red ro-quick__add" href="<?php echo esc_url( $link ); ?>" data-ro-card-add>Dodaj u korpu</a>
			</div>
		<?php else : ?>
			<a class="ro-link-cta" href="<?php echo esc_url( $link ); ?>">Izaberi veličinu →</a>
		<?php endif; ?>
	</div>
</section>
