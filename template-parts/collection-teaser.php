<?php
/**
 * Home: teaser for the coming collection (duks + donji deo trenerke).
 * No prices or links yet; remove the get_template_part() call in front-page.php
 * when the collection goes on sale.
 */
$images = array(
	array( 'kolekcija-duks-ledja', 'Duks Team Opačić, leđa', 'Duks, leđa', 'ro-teaser__item--main', '100vw' ),
	array( 'kolekcija-duks-prednja', 'Duks Team Opačić, prednja strana', 'Duks, prednja strana', '', '50vw' ),
	array( 'kolekcija-trenerka', 'Donji deo trenerke', 'Donji deo trenerke', '', '50vw' ),
);
?>
<section class="ro-teaser" id="nova-kolekcija">
	<div class="ro-teaser__copy">
		<div class="ro-eyebrow">Uskoro</div>
		<img class="ro-teaser__art" src="<?php echo esc_url( rade_asset( 'images/kolekcija-motiv-2400.webp' ) ); ?>" srcset="<?php echo esc_url( rade_asset( 'images/kolekcija-motiv-1200.webp' ) ); ?> 1200w, <?php echo esc_url( rade_asset( 'images/kolekcija-motiv-2400.webp' ) ); ?> 2400w" sizes="(max-width: 900px) 100vw, 50vw" width="2400" height="1197" loading="lazy" alt="Team Opačić. Dok dišeš, imaš šansu. Dok se boriš, imaš nadu. Nema predaje.">
		<h2 class="ro-teaser__title">Nova kolekcija</h2>
		<p class="ro-teaser__text">Komplet: duks i donji deo trenerke. Uskoro u prodaji.</p>
	</div>
	<?php foreach ( $images as [ $file, $alt, $caption, $class, $mobile ] ) : ?>
		<figure class="ro-teaser__item <?php echo esc_attr( $class ); ?>">
			<img src="<?php echo esc_url( rade_asset( "images/{$file}-1600.webp" ) ); ?>" srcset="<?php echo esc_url( rade_asset( "images/{$file}-800.webp" ) ); ?> 800w, <?php echo esc_url( rade_asset( "images/{$file}-1600.webp" ) ); ?> 1600w" sizes="(max-width: 900px) <?php echo esc_attr( $mobile ); ?>, 50vw" loading="lazy" alt="<?php echo esc_attr( $alt ); ?>">
			<figcaption><?php echo esc_html( $caption ); ?></figcaption>
		</figure>
	<?php endforeach; ?>
</section>
