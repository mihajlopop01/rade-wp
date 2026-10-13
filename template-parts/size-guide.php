<?php
/**
 * "Vodič za veličine" (mockup screen 03): centred modal on desktop, bottom
 * sheet on mobile. With $args['inline'] it renders as a plain table (page).
 */
$inline  = ! empty( $args['inline'] );
$product = $args['product'] ?? null;
$table   = function () {
	?>
	<div class="ro-guide__table">
		<div class="ro-guide__row ro-guide__row--head"><span>Veličina</span><span>Širina<span class="ro-d"> (cm)</span></span><span>Dužina<span class="ro-d"> (cm)</span></span></div>
		<?php foreach ( rade_size_guide_rows() as [ $s, $w, $l ] ) : ?>
			<div class="ro-guide__row"><span><?php echo esc_html( $s ); ?></span><span><?php echo esc_html( $w ); ?></span><span><?php echo esc_html( $l ); ?></span></div>
		<?php endforeach; ?>
	</div>
	<p class="ro-guide__note">Mere su u centimetrima, merene na ravno položenoj majici.</p>
	<?php
};

if ( $inline ) {
	$table();
	return;
}
?>
<div class="ro-overlay ro-guide" hidden data-ro-guide>
	<div class="ro-guide__panel" role="dialog" aria-modal="true" aria-labelledby="ro-guide-title">
		<span class="ro-sheet-handle"></span>
		<div class="ro-guide__head">
			<div>
				<?php if ( $product ) : ?><div class="ro-eyebrow ro-d"><?php echo esc_html( $product->get_name() ); ?></div><?php endif; ?>
				<h2 class="ro-guide__title" id="ro-guide-title">Vodič za veličine</h2>
			</div>
			<button type="button" class="ro-close" aria-label="Zatvori" data-ro-close>×</button>
		</div>
		<?php $table(); ?>
	</div>
</div>
