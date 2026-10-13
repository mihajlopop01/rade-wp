<?php
/**
 * "Dodato u korpu" (mockup screen 04): right drawer on desktop, bottom sheet
 * on mobile. rade.js fills in the item that was just added.
 */
if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
	return;
}
?>
<div class="ro-overlay ro-drawer" hidden data-ro-drawer>
	<div class="ro-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="ro-drawer-title">
		<span class="ro-sheet-handle"></span>
		<div class="ro-drawer__head">
			<div class="ro-drawer__title" id="ro-drawer-title">Dodato u korpu</div>
			<button type="button" class="ro-close" aria-label="Zatvori" data-ro-close>×</button>
		</div>
		<div class="ro-drawer__item">
			<img src="" alt="" data-ro-drawer-img>
			<div class="ro-drawer__meta">
				<div class="ro-drawer__name" data-ro-drawer-name></div>
				<div class="ro-drawer__variant" data-ro-drawer-variant></div>
				<div class="ro-drawer__price" data-ro-drawer-price></div>
			</div>
		</div>
		<div class="ro-drawer__spacer"></div>
		<div class="ro-drawer__foot">
			<div class="ro-drawer__sum"><span>Međuzbir (<span class="ro-drawer-count"><?php echo (int) rade_cart_count(); ?></span>)</span><span class="ro-drawer-total"><?php echo esc_html( rade_price( WC()->cart->get_subtotal() ) ); ?></span></div>
			<a class="ro-btn ro-btn--red" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">Naruči</a>
			<a class="ro-btn ro-btn--ghost" href="<?php echo esc_url( wc_get_cart_url() ); ?>">Pogledaj korpu</a>
		</div>
	</div>
</div>
