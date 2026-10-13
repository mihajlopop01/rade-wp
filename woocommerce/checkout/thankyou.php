<?php
/**
 * Thank-you screen (mockup screen 07): order summary left, ring-walk photo right.
 *
 * @var WC_Order|false $order
 */
defined( 'ABSPATH' ) || exit;

$failed = $order && $order->has_status( 'failed' );
?>
<section class="ro-thanks">
	<div class="ro-thanks__copy">
		<?php if ( $order && ! $failed ) :
			$name = trim( $order->get_billing_first_name() );
			?>
			<div class="ro-eyebrow">Porudžbina #<?php echo esc_html( $order->get_order_number() ); ?></div>
			<h1 class="ro-thanks__title"><span class="ro-d">Hvala na poverenju</span><span class="ro-m">Hvala</span><?php echo $name ? ', ' . esc_html( $name ) : ''; ?>!</h1>
			<p class="ro-thanks__text">Porudžbina je primljena. Plaćanje pouzećem prilikom preuzimanja.<span class="ro-d"> Potvrda: <?php echo esc_html( $order->get_billing_email() ); ?></span></p>
			<div class="ro-thanks__items">
				<?php foreach ( $order->get_items() as $item ) :
					$size = '';
					foreach ( $item->get_meta_data() as $meta ) {
						if ( 0 !== strpos( $meta->key, '_' ) ) {
							$size = taxonomy_exists( $meta->key ) ? ( get_term_by( 'slug', $meta->value, $meta->key )->name ?? $meta->value ) : $meta->value;
						}
					}
					$name_only = preg_replace( '/ - [^-]*$/', '', $item->get_name() );
					?>
					<div class="ro-thanks__item"><span><?php echo esc_html( $name_only . ( $size ? ' · ' . $size : '' ) . ' × ' . $item->get_quantity() ); ?></span><span><?php echo esc_html( rade_price( $item->get_subtotal() ) ); ?></span></div>
				<?php endforeach; ?>
				<div class="ro-thanks__item ro-thanks__item--ship"><span>Dostava · Post Express</span><span><?php echo esc_html( rade_price( $order->get_shipping_total() ) ); ?></span></div>
				<div class="ro-thanks__total"><span>Ukupno</span><span><?php echo esc_html( rade_price( $order->get_total() ) ); ?></span></div>
			</div>
		<?php elseif ( $failed ) : ?>
			<h1 class="ro-thanks__title">Porudžbina nije uspela</h1>
			<p class="ro-thanks__text">Nažalost, porudžbina nije mogla da bude obrađena. Pokušajte ponovo.</p>
		<?php else : ?>
			<h1 class="ro-thanks__title">Još nema porudžbine</h1>
		<?php endif; ?>
		<a class="ro-btn ro-btn--light ro-thanks__home" href="<?php echo esc_url( home_url( '/' ) ); ?>">Nazad na početnu</a>
	</div>
	<div class="ro-thanks__media"><img src="<?php echo esc_url( rade_asset( 'images/banner-2.jpg' ) ); ?>" alt="Rade Opačić"></div>
</section>
<?php
if ( $order ) {
	// Lets tracking/analytics integrations run; the default order-details table is unhooked in functions.php.
	do_action( 'woocommerce_thankyou', $order->get_id() );
}
