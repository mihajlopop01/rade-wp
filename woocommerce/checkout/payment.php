<?php
/**
 * "Završi kupovinu" button. Returned as an AJAX fragment, so the root keeps
 * id="payment" / woocommerce-checkout-payment.
 *
 * The store only offers cash on delivery (shown as the box above, see
 * form-checkout.php); the gateway radio stays in the form (hidden) so
 * WooCommerce receives payment_method as usual.
 */
defined( 'ABSPATH' ) || exit;

$gateways = WC()->cart->needs_payment() ? WC()->payment_gateways()->get_available_payment_gateways() : array();
$first    = $gateways ? array_key_first( $gateways ) : '';
?>
<div id="payment" class="woocommerce-checkout-payment ro-payment">
	<?php if ( $gateways ) : ?>
		<ul class="wc_payment_methods payment_methods methods ro-payment__methods">
			<?php foreach ( $gateways as $id => $gateway ) : ?>
				<li class="wc_payment_method payment_method_<?php echo esc_attr( $id ); ?>">
					<input id="payment_method_<?php echo esc_attr( $id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $first ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order">
		<noscript><button type="submit" class="button" name="woocommerce_checkout_update_totals" value="1">Ažuriraj</button></noscript>
		<?php if ( wc_terms_and_conditions_checkbox_enabled() ) : ?>
			<input type="hidden" name="terms" value="on"><input type="hidden" name="terms-field" value="1">
		<?php endif; ?>
		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
		<button type="submit" class="ro-btn ro-btn--red ro-place-order" name="woocommerce_checkout_place_order" id="place_order" value="Završi kupovinu" data-value="Završi kupovinu">Završi kupovinu</button>
		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>
