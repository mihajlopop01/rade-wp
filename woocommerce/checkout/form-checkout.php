<?php
/**
 * Checkout (mockup screen 06).
 *
 * Left: "Poručivanje" with the billing fields (rade_checkout_field_spec()),
 * the cash-on-delivery box and "Završi kupovinu" (payment.php, moved here from
 * the order review in functions.php). Right: #order_review = "Vaša porudžbina"
 * card (review-order.php). On mobile the card is replaced by a one-line bar.
 *
 * @var WC_Checkout $checkout
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
$cart = WC()->cart;
?>
<div class="ro-checkout-bar ro-m"><span>Porudžbina (<?php echo (int) $cart->get_cart_contents_count(); ?>)</span><b><?php echo esc_html( rade_price( $cart->get_total( 'edit' ) ) ); ?></b></div>
<form name="checkout" method="post" class="checkout woocommerce-checkout ro-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="Poručivanje">

	<div class="ro-checkout__main" id="customer_details">
		<h1 class="ro-checkout__title">Poručivanje</h1>
		<div class="ro-label ro-d">Podaci za dostavu</div>
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<div class="woocommerce-billing-fields">
			<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
			<div class="woocommerce-billing-fields__field-wrapper ro-fields">
				<?php
				foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>
			<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<div class="ro-label ro-d">Način plaćanja</div>
		<div class="ro-cod">
			<span class="ro-cod__radio"><span></span></span>
			<div class="ro-cod__text"><b>Plaćanje pouzećem</b><span>Plaćate kuriru prilikom preuzimanja.</span></div>
		</div>

		<?php woocommerce_checkout_payment(); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	<div id="order_review" class="woocommerce-checkout-review-order ro-checkout__side">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>
<?php
do_action( 'woocommerce_after_checkout_form', $checkout );
