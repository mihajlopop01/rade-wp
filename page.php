<?php
/**
 * Pages. Cart and checkout render WooCommerce's classic templates (overridden in
 * theme/woocommerce/) instead of the page's block content; "vodic-za-velicine"
 * shows the size table; other pages (legal/info) get a simple text layout,
 * which the design leaves unspecified.
 */

if ( function_exists( 'is_cart' ) && is_cart() ) {
	get_header();
	echo '<main class="ro-cart-page">';
	echo do_shortcode( '[woocommerce_cart]' );
	echo '</main>';
	get_footer();
	return;
}

if ( function_exists( 'is_checkout' ) && is_checkout() ) {
	$thanks = is_wc_endpoint_url( 'order-received' );
	get_header();
	echo '<main class="' . ( $thanks ? 'ro-thanks-page' : 'ro-checkout-page' ) . '">';
	echo do_shortcode( '[woocommerce_checkout]' );
	echo '</main>';
	get_footer();
	return;
}

get_header();
?>
<main class="ro-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1 class="ro-page__title"><?php the_title(); ?></h1>
		<?php if ( is_page( 'vodic-za-velicine' ) ) : ?>
			<div class="ro-guide ro-guide--inline"><?php get_template_part( 'template-parts/size-guide', null, array( 'inline' => true ) ); ?></div>
		<?php endif; ?>
		<div class="ro-page__content"><?php the_content(); ?></div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
