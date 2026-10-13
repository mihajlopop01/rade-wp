<?php
/**
 * Home (mockup screen 01): hero, marquee, featured product with quick add,
 * two image tiles.
 */
get_header();
$product = rade_main_product();
$link    = $product ? $product->get_permalink() : rade_product_url();
$marquee = array( 'Zvanični merč', 'Kickboxing', 'Proizvodnja Srbija', '100% pamuk', 'Rade Opačić' );
?>
<main class="ro-home">
	<section class="ro-hero">
		<picture>
			<source media="(max-width: 600px)" srcset="<?php echo esc_url( rade_asset( 'images/hero-mobile.jpg' ) ); ?>">
			<img class="ro-hero__img" src="<?php echo esc_url( rade_asset( 'images/banner.jpg' ) ); ?>" alt="Rade Opačić u zvaničnoj majici">
		</picture>
		<div class="ro-hero__copy">
			<div class="ro-eyebrow"><span class="ro-d">Zvanični merch</span><span class="ro-m">Zvanični merč · Kickboxing</span></div>
			<h1 class="ro-hero__title"><span class="ro-d">Rade<br>Opačić</span><span class="ro-m">Rade Opačić</span></h1>
			<p class="ro-hero__text"><span class="ro-d">Nova kolekcija uskoro u prodaji.</span><span class="ro-m">Rade Opačić Majica. 100% pamuk, proizvedeno u Srbiji.</span></p>
			<div class="ro-hero__ctas">
				<a class="ro-btn ro-btn--red ro-hero__buy" href="<?php echo esc_url( $link ); ?>">Kupi majicu</a>
				<a class="ro-hero__down" href="#proizvod" aria-label="Na proizvode">↓</a>
			</div>
		</div>
		<?php if ( $product ) : ?><div class="ro-hero__caption"><?php echo esc_html( $product->get_name() ); ?></div><?php endif; ?>
	</section>

	<div class="ro-marquee" aria-hidden="true"><div class="ro-marquee__track">
		<?php for ( $i = 0; $i < 4; $i++ ) : foreach ( $marquee as $word ) : ?><span><?php echo esc_html( $word ); ?></span><i></i><?php endforeach; endfor; ?>
	</div></div>

	<?php
	foreach ( rade_products() as $i => $p ) {
		get_template_part( 'template-parts/product-feature', null, array( 'product' => $p, 'index' => $i, 'id' => 0 === $i ? 'proizvod' : '' ) );
	}
	?>

	<div class="ro-tiles">
		<a class="ro-tile" href="<?php echo esc_url( $link ); ?>">
			<img src="<?php echo esc_url( rade_asset( 'images/banner-2.jpg' ) ); ?>" alt="Rade Opačić ulazi u ring">
			<span class="ro-tile__bar"><span class="ro-tile__title"><?php echo esc_html( $product ? $product->get_name() : 'Rade Opačić Majica' ); ?></span><span class="ro-tile__cta"><span class="ro-d">Pogledaj </span>→</span></span>
		</a>
		<a class="ro-tile ro-tile--b" href="<?php echo esc_url( $link ); ?>">
			<img src="<?php echo esc_url( rade_asset( 'images/banner-3.jpg' ) ); ?>" alt="Rade Opačić u meču">
			<span class="ro-tile__bar"><span class="ro-tile__title">Zvanični merč</span><span class="ro-tile__cta"><span class="ro-d">Kupi </span>→</span></span>
		</a>
	</div>
</main>
<?php
get_footer();
