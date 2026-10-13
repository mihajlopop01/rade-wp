<?php
/**
 * Footer (mockup screen 01) + the "Dodato u korpu" drawer. The info-page row
 * links to the legal pages created in run 2.
 */
$cart = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$main = rade_main_product();
?>
<footer class="ro-footer">
	<div class="ro-footer__brand"><?php echo rade_logo( 'ro-footer__logo' ); ?><div class="ro-footer__name">Rade Opačić Shop</div></div>
	<div class="ro-footer__col">
		<div class="ro-footer__label">Prodavnica</div>
		<a href="<?php echo esc_url( rade_product_url() ); ?>"><?php echo esc_html( $main ? $main->get_name() : 'Majica' ); ?></a>
		<a href="<?php echo esc_url( $cart ); ?>">Korpa</a>
	</div>
	<div class="ro-footer__col ro-footer__col--muted">
		<div class="ro-footer__label">Dostava</div>
		<div>Samo Srbija</div>
	</div>
	<div class="ro-footer__col ro-footer__col--muted">
		<div class="ro-footer__label">Plaćanje</div>
		<div>Plaćanje pouzećem</div>
	</div>
	<div class="ro-footer__mobile-links">
		<a href="<?php echo esc_url( rade_product_url() ); ?>">Majica</a>
		<a href="<?php echo esc_url( $cart ); ?>">Korpa</a>
	</div>
	<div class="ro-footer__bottom">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Rade Opačić Shop</span>
		<nav class="ro-footer__legal" aria-label="Informacije">
			<?php foreach ( rade_legal_links() as $slug => $label ) : ?>
				<a href="<?php echo esc_url( rade_page_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</footer>
<?php get_template_part( 'template-parts/drawer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
