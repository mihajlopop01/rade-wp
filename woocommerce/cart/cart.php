<?php
/**
 * Cart (mockup screen 05): item rows + light "Pregled" summary.
 *
 * Keeps WooCommerce's form contract (woocommerce-cart-form, cart[key][qty],
 * update_cart, nonce, .product-remove > a) so wc cart.js handles AJAX
 * quantity updates and removal.
 */
defined( 'ABSPATH' ) || exit;

$cart     = WC()->cart;
$count    = $cart->get_cart_contents_count();
$shipping = $cart->needs_shipping() && $cart->show_shipping() ? rade_price( $cart->get_shipping_total() ) : '—';

do_action( 'woocommerce_before_cart' );
?>
<form class="woocommerce-cart-form ro-cart" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" data-count="<?php echo (int) $count; ?>">
	<h1 class="ro-cart__title">Korpa</h1>
	<div class="ro-cart__grid">
		<div class="ro-cart__items woocommerce-cart-form__contents">
			<?php
			foreach ( $cart->get_cart() as $key => $item ) :
				$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
				if ( ! $_product || ! $_product->exists() || $item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $item, $key ) ) {
					continue;
				}
				$link  = $_product->is_visible() ? $_product->get_permalink( $item ) : '#';
				$thumb = wp_get_attachment_image_url( $_product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
				$size  = rade_cart_item_size( $item );
				?>
				<div class="ro-line woocommerce-cart-form__cart-item cart_item">
					<a class="ro-line__img" href="<?php echo esc_url( $link ); ?>"><img src="<?php echo esc_url( $thumb ); ?>" alt=""></a>
					<div class="ro-line__info">
						<a class="ro-line__name" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( rade_cart_item_name( $item ) ); ?></a>
						<div class="ro-line__variant"><?php echo esc_html( ( $size ? 'Veličina ' . $size : '' ) ); ?><span class="ro-d"> · <?php echo esc_html( rade_price( $_product->get_price() ) ); ?></span></div>
						<div class="ro-line__remove product-remove ro-d"><a href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" class="remove" aria-label="Ukloni" data-product_id="<?php echo esc_attr( $item['product_id'] ); ?>">Ukloni</a></div>
					</div>
					<div class="ro-line__controls">
						<div class="ro-qty ro-qty--sm" data-ro-cart-qty>
							<button type="button" aria-label="Manje" data-step="-1">−</button>
							<input type="number" class="qty" name="cart[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="0" step="1" inputmode="numeric" aria-label="Količina" <?php echo $_product->is_sold_individually() ? 'readonly' : ''; ?>>
							<button type="button" aria-label="Više" data-step="1">+</button>
						</div>
						<div class="ro-line__remove product-remove ro-m"><a href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" class="remove" aria-label="Ukloni" data-product_id="<?php echo esc_attr( $item['product_id'] ); ?>">Ukloni</a></div>
					</div>
					<div class="ro-line__total"><?php echo esc_html( rade_price( $item['line_subtotal'] ) ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="ro-summary-light">
			<div class="ro-summary-light__title ro-d">Pregled</div>
			<div class="ro-summary-light__row"><span><span class="ro-d">Ukupno (bez dostave)</span><span class="ro-m">Međuzbir</span></span><span><?php echo esc_html( rade_price( $cart->get_subtotal() ) ); ?></span></div>
			<div class="ro-summary-light__row"><span>Dostava<span class="ro-d"> · Post Express</span></span><span><?php echo esc_html( $shipping ); ?></span></div>
			<div class="ro-summary-light__total"><span>Ukupno</span><span><?php echo esc_html( rade_price( $cart->get_total( 'edit' ) ) ); ?></span></div>
			<div class="ro-summary-light__note ro-d">Plaćanje pouzećem · Samo Srbija</div>
			<a class="ro-btn ro-btn--dark" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">Naruči</a>
		</div>
	</div>

	<button type="submit" name="update_cart" value="1" class="ro-hidden-submit" tabindex="-1" aria-hidden="true">Ažuriraj</button>
	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
</form>
<?php
do_action( 'woocommerce_after_cart' );
