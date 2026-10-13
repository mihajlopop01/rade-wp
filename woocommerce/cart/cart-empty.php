<?php
/**
 * Empty cart (mockup screen 05, empty state).
 */
defined( 'ABSPATH' ) || exit;

woocommerce_output_all_notices();
?>
<div class="ro-cart ro-cart--empty">
	<h1 class="ro-cart__title">Korpa</h1>
	<div class="ro-empty wc-empty-cart-message">
		<p>Korpa je prazna.</p>
		<a class="ro-btn ro-btn--red" href="<?php echo esc_url( rade_product_url() ); ?>">Pogledaj majicu</a>
	</div>
</div>
