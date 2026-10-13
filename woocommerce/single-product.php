<?php
/**
 * Product page (mockup screen 02): thumbs + main image, info column with size
 * chips, qty, "Dodaj u korpu" / "Kupi odmah", detail rows; banner below.
 * On mobile the add button moves to a sticky bottom bar.
 *
 * The form posts WooCommerce's normal add-to-cart fields; rade.js intercepts
 * "Dodaj u korpu" for AJAX + the drawer.
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}
	$data    = rade_product_data( $product );
	$gallery = $data['gallery'];
	$main    = $gallery[0] ?? array( 'id' => 0, 'large' => wc_placeholder_img_src( 'large' ), 'thumb' => wc_placeholder_img_src() );
	$label   = rade_price_label( $product );
	?>
	<main class="ro-product">
		<?php wc_print_notices(); ?>
		<div class="ro-product__grid">
			<div class="ro-thumbs">
				<?php foreach ( $gallery as $i => $img ) : ?>
					<button type="button" class="ro-thumb<?php echo 0 === $i ? ' is-active' : ''; ?>" data-ro-thumb data-id="<?php echo (int) $img['id']; ?>" data-large="<?php echo esc_url( $img['large'] ); ?>" aria-label="Slika <?php echo (int) $i + 1; ?>"><img src="<?php echo esc_url( $img['thumb'] ); ?>" alt=""></button>
				<?php endforeach; ?>
			</div>
			<div class="ro-product__media"><img src="<?php echo esc_url( $main['large'] ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" data-ro-main-img></div>

			<form class="ro-product__info cart" method="post" enctype="multipart/form-data" action="<?php echo esc_url( $product->get_permalink() ); ?>"
				data-ro-buy data-name="<?php echo esc_attr( $product->get_name() ); ?>" data-price="<?php echo esc_attr( $label ); ?>" data-img="<?php echo esc_url( $main['thumb'] ); ?>" data-variations="<?php echo esc_attr( wp_json_encode( $data['variations'] ) ); ?>">
				<div class="ro-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Početna</a><span>/</span><span>Majica</span></div>
				<h1 class="ro-product__title"><?php echo esc_html( $product->get_name() ); ?></h1>
				<div class="ro-product__price" data-ro-price><?php echo esc_html( $label ); ?></div>

				<?php if ( $data['sizes'] ) : ?>
					<div class="ro-product__sizes">
						<div class="ro-product__sizes-head"><span class="ro-label">Veličina<span data-ro-size-suffix></span></span><button type="button" class="ro-guide-link" data-ro-guide-open>Vodič za veličine</button></div>
						<div class="ro-sizegrid" data-ro-sizes>
							<?php foreach ( $data['sizes'] as $s ) : ?>
								<button type="button" class="ro-size" data-ro-size data-value="<?php echo esc_attr( $s['value'] ); ?>" data-label="<?php echo esc_attr( $s['label'] ); ?>" aria-pressed="false"><?php echo esc_html( $s['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
						<div class="ro-error" hidden data-ro-error>Izaberite veličinu pre dodavanja u korpu.</div>
					</div>
				<?php endif; ?>

				<div class="ro-product__buy">
					<span class="ro-label ro-m">Količina</span>
					<div class="ro-qty">
						<button type="button" aria-label="Manje" data-ro-qty="-1">−</button>
						<span data-ro-qty-value>1</span>
						<button type="button" aria-label="Više" data-ro-qty="1">+</button>
					</div>
					<button type="submit" class="ro-btn ro-btn--red ro-product__add ro-d" data-ro-add>Dodaj u korpu</button>
				</div>
				<button type="submit" class="ro-btn ro-btn--light" name="rade_buy_now" value="1" data-ro-add>Kupi odmah</button>

				<div class="ro-rows">
					<div class="ro-row"><span>Dostava</span><span><?php echo esc_html( rade_delivery_text() ); ?></span></div>
					<?php foreach ( $data['details'] as [ $name, $value ] ) : ?>
						<div class="ro-row"><span><?php echo esc_html( $name ); ?></span><span><?php echo esc_html( $value ); ?></span></div>
					<?php endforeach; ?>
				</div>

				<div class="ro-stickybar">
					<div class="ro-stickybar__meta"><span class="ro-stickybar__size" data-ro-size-short>Izaberite veličinu</span><span class="ro-stickybar__price" data-ro-price><?php echo esc_html( $label ); ?></span></div>
					<button type="submit" class="ro-btn ro-btn--red" data-ro-add>Dodaj u korpu</button>
				</div>

				<input type="hidden" name="add-to-cart" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="product_id" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="quantity" value="1" data-ro-qty-input>
				<?php if ( $data['sizeKey'] ) : ?>
					<input type="hidden" name="variation_id" value="" data-ro-variation>
					<input type="hidden" name="<?php echo esc_attr( $data['sizeKey'] ); ?>" value="" data-ro-size-input>
				<?php endif; ?>
			</form>
		</div>
		<a class="ro-product__banner" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( rade_asset( 'images/banner-2.jpg' ) ); ?>" alt="Rade Opačić" loading="lazy">
			<span>Rade Opačić</span>
		</a>
	</main>
	<?php
	get_template_part( 'template-parts/size-guide', null, array( 'product' => $product ) );
endwhile;

get_footer();
