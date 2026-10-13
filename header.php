<?php
/**
 * Header: red delivery bar + nav (mockup, all screens). On the home page the
 * desktop header sits transparently over the hero image.
 */
$current = rade_current_nav();
$count   = rade_cart_count();
$cart    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="ro-bar">Dostava samo za Srbiju. Uskoro dostava za ceo Balkan.</div>
<header class="ro-header<?php echo is_front_page() ? ' ro-header--over' : ''; ?>">
	<button type="button" class="ro-burger" aria-label="Meni" aria-expanded="false" data-ro-menu-toggle><span></span><span></span><span></span></button>
	<a class="ro-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo rade_logo(); ?><span class="ro-logo__text">Rade Opačić</span></a>
	<nav class="ro-nav" aria-label="Glavni meni">
		<?php foreach ( rade_nav_items() as $key => [ $label, $url ] ) : ?>
			<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<a class="ro-cartbtn" href="<?php echo esc_url( $cart ); ?>" aria-label="Korpa"><span class="ro-cartbtn__label">Korpa</span><span class="ro-cart-count"><?php echo (int) $count; ?></span></a>
</header>
<div class="ro-menu" hidden data-ro-menu>
	<?php foreach ( rade_nav_items() as $key => [ $label, $url ] ) : ?>
		<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><?php echo 'cart' === $key ? ' (<span class="ro-cart-count">' . (int) $count . '</span>)' : ''; ?></a>
	<?php endforeach; ?>
</div>
