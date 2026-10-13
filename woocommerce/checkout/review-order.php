<?php
/**
 * "Vaša porudžbina" card. Returned as an AJAX fragment by update_order_review,
 * so the root keeps the woocommerce-checkout-review-order-table class.
 */
defined( 'ABSPATH' ) || exit;

$cart     = WC()->cart;
$shipping = $cart->needs_shipping() ? rade_price( $cart->get_shipping_total() ) : '—';
?>
<div class="woocommerce-checkout-review-order-table ro-summary">
	<div class="ro-summary__title">Vaša porudžbina</div>
	<?php
	foreach ( $cart->get_cart() as $key => $item ) :
		$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
		if ( ! $_product || ! $_product->exists() || $item['quantity'] <= 0 ) {
			continue;
		}
		$thumb = wp_get_attachment_image_url( $_product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
		$size  = rade_cart_item_size( $item );
		?>
		<div class="ro-summary__item">
			<span class="ro-summary__img"><img src="<?php echo esc_url( $thumb ); ?>" alt=""><span class="ro-badge"><?php echo (int) $item['quantity']; ?></span></span>
			<div class="ro-summary__meta"><b><?php echo esc_html( rade_cart_item_name( $item ) ); ?></b><?php if ( $size ) : ?><span>Veličina <?php echo esc_html( $size ); ?></span><?php endif; ?></div>
			<span class="ro-summary__price"><?php echo esc_html( rade_price( $item['line_subtotal'] ) ); ?></span>
		</div>
	<?php endforeach; ?>

	<div class="ro-summary__rows">
		<div class="ro-summary__row"><span>Ukupno (bez dostave)</span><span><?php echo esc_html( rade_price( $cart->get_subtotal() ) ); ?></span></div>
		<div class="ro-summary__row"><span>Dostava · Post Express</span><span><?php echo esc_html( $shipping ); ?></span></div>
		<div class="ro-summary__total"><span>Ukupno</span><span><?php echo esc_html( rade_price( $cart->get_total( 'edit' ) ) ); ?></span></div>
	</div>

	<?php
	// Keep the chosen shipping method in the form (checkout.js and validation read it).
	if ( $cart->needs_shipping() ) {
		$chosen = WC()->session->get( 'chosen_shipping_methods', array() );
		foreach ( WC()->shipping()->get_packages() as $i => $package ) {
			$method = $chosen[ $i ] ?? ( $package['rates'] ? array_key_first( $package['rates'] ) : '' );
			if ( $method ) {
				printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" value="%2$s" class="shipping_method">', (int) $i, esc_attr( $method ) );
			}
		}
	}
	?>
</div>
