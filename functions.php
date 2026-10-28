<?php
/**
 * Rade Opačić theme functions.
 *
 * Classic PHP templates render markup that mirrors the Claude Design mockup
 * (design/Rade Opacic Shop.dc.html); WooCommerce supplies all product, cart
 * and order data.
 */

defined( 'ABSPATH' ) || exit;

define( 'RADE_VERSION', '1.0.0' );

/* ---------------------------------------------------------------------------
 * Setup & assets
 * ------------------------------------------------------------------------- */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce' );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Barlow + Barlow Condensed (latin-ext has Č Ć Đ Š Ž).
	wp_enqueue_style( 'rade-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600;700&display=swap&subset=latin-ext', array(), null );
	wp_enqueue_style( 'rade', rade_asset( 'css/rade.css' ), array(), rade_asset_ver( 'css/rade.css' ) );
	wp_enqueue_script( 'rade', rade_asset( 'js/rade.js' ), array(), rade_asset_ver( 'js/rade.js' ), array( 'in_footer' => true ) );
	wp_localize_script( 'rade', 'RADE', array(
		'addToCartUrl' => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'add_to_cart' ) : '',
	) );

	// Block styles are not used by these templates.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wc-blocks-style' );
}, 20 );

// WooCommerce's own stylesheets fight the design; the theme styles everything.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="icon" href="' . esc_url( rade_asset( 'images/favicon.png' ) ) . '">' . "\n";
}, 2 );

function rade_asset( $path ) {
	return get_theme_file_uri( 'assets/' . $path );
}

function rade_asset_ver( $path ) {
	$file = get_theme_file_path( 'assets/' . $path );
	return file_exists( $file ) ? (string) filemtime( $file ) : RADE_VERSION;
}

/* ---------------------------------------------------------------------------
 * Prices: the design shows "2.499 RSD" (dot thousands, no decimals, RSD after).
 * ------------------------------------------------------------------------- */

add_filter( 'wc_get_price_thousand_separator', fn() => '.' );
add_filter( 'wc_get_price_decimal_separator', fn() => ',' );
add_filter( 'wc_get_price_decimals', fn() => 0 );
add_filter( 'woocommerce_price_format', fn() => '%2$s&nbsp;%1$s' );
add_filter( 'woocommerce_currency_symbol', fn( $symbol, $currency ) => 'RSD' === $currency ? 'RSD' : $symbol, 10, 2 );

/** Plain-text price ("2.499 RSD") for places where wc_price() markup is unwanted. */
function rade_price( $amount ) {
	return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
}

/** "2.499 RSD", or "od 2.499 RSD" when sizes cost different amounts. */
function rade_price_label( $product ) {
	if ( ! $product->is_type( 'variable' ) ) {
		return rade_price( wc_get_price_to_display( $product ) );
	}
	$min = $product->get_variation_price( 'min', true );
	$max = $product->get_variation_price( 'max', true );
	return ( $min < $max ? 'od ' : '' ) . rade_price( $min );
}

/* ---------------------------------------------------------------------------
 * Small helpers
 * ------------------------------------------------------------------------- */

function rade_logo( $class = 'ro-logo__img' ) {
	return '<img class="' . esc_attr( $class ) . '" src="' . esc_url( rade_asset( 'images/logo_white.png' ) ) . '" alt="RO">';
}

function rade_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function rade_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/** Published products in shop order. */
function rade_products() {
	return function_exists( 'wc_get_products' ) ? wc_get_products( array(
		'status'  => 'publish',
		'limit'   => -1,
		'orderby' => 'menu_order',
		'order'   => 'ASC',
	) ) : array();
}

/** The featured product (the design sells one shirt): the first published product. */
function rade_main_product() {
	$products = rade_products();
	return $products ? $products[0] : null;
}

/** "Majica" nav target: the featured product, else the shop page. */
function rade_product_url() {
	$product = rade_main_product();
	if ( $product ) {
		return $product->get_permalink();
	}
	return function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
}

/** Main navigation, as in the mockup. */
function rade_nav_items() {
	return array(
		'home'    => array( 'Početna', home_url( '/' ) ),
		'product' => array( 'Majica', rade_product_url() ),
		'cart'    => array( 'Korpa', function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ) ),
	);
}

function rade_current_nav() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_product() || is_shop() || is_product_taxonomy() ) ) {
		return 'product';
	}
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return 'cart';
	}
	return '';
}

/** Footer info pages (created in run 2 — slugs fixed here). */
function rade_legal_links() {
	return array(
		'uslovi-koriscenja'     => 'Uslovi korišćenja',
		'politika-privatnosti'  => 'Politika privatnosti',
		'odustanak-od-kupovine' => 'Odustanak od kupovine',
		'zamena-velicina'       => 'Zamena veličina',
		'kontakt'               => 'Kontakt',
	);
}

/** Size guide rows: the design has no measurements yet ("—"); fill in when the owner sends them. */
function rade_size_guide_rows() {
	// Owner's measurements (2026-10-13): A = širina, B = dužina, cm.
	return array(
		array( 'S', '51', '72' ),
		array( 'M', '52', '74' ),
		array( 'L', '56', '77' ),
		array( 'XL', '60', '81' ),
		array( 'XXL', '63', '84' ),
	);
}

/** Store-wide delivery line shown on the product page. */
function rade_delivery_text() {
	return 'Post Express, 2–5 radnih dana';
}

/* ---------------------------------------------------------------------------
 * Product data for the custom size picker
 * ------------------------------------------------------------------------- */

/**
 * Normalised data for a product: sizes (first variation attribute), variations
 * (with price), gallery, and the visible non-variation attributes as detail rows
 * ("Sastav", "Proizvodnja").
 */
function rade_product_data( $product ) {
	$data = array(
		'id'         => $product->get_id(),
		'sizeKey'    => '',
		'sizes'      => array(),
		'variations' => array(),
		'gallery'    => array(),
		'details'    => array(),
	);

	$image_ids = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	foreach ( array_unique( $image_ids ) as $id ) {
		$data['gallery'][] = array(
			'id'    => (int) $id,
			'large' => wp_get_attachment_image_url( $id, 'large' ),
			'thumb' => wp_get_attachment_image_url( $id, 'woocommerce_thumbnail' ),
		);
	}

	foreach ( $product->get_attributes() as $attr ) {
		if ( $attr->get_variation() || ! $attr->get_visible() ) {
			continue;
		}
		$values = $attr->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $attr->get_name(), array( 'fields' => 'names' ) ) : $attr->get_options();
		$data['details'][] = array( wc_attribute_label( $attr->get_name(), $product ), implode( ', ', $values ) );
	}

	if ( ! $product->is_type( 'variable' ) ) {
		return $data;
	}

	// A taxonomy attribute is keyed by pa_<slug> with term slugs as values; a
	// custom one by its sanitized label with raw option text. Handle both.
	$product_attrs = $product->get_attributes();
	foreach ( $product->get_variation_attributes() as $raw_name => $values ) {
		$attr   = $product_attrs[ $raw_name ] ?? $product_attrs[ sanitize_title( $raw_name ) ] ?? null;
		$is_tax = $attr && $attr->is_taxonomy();
		$data['sizeKey'] = 'attribute_' . ( $is_tax ? $raw_name : sanitize_title( $raw_name ) );
		$options = array();
		foreach ( $values as $value ) {
			$options[ $value ] = $is_tax ? ( get_term_by( 'slug', $value, $raw_name )->name ?? $value ) : $value;
		}
		$order = array( 'XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL' );
		uksort( $options, function ( $a, $b ) use ( $options, $order ) {
			$ia = array_search( strtoupper( (string) $options[ $a ] ), $order, true );
			$ib = array_search( strtoupper( (string) $options[ $b ] ), $order, true );
			return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
		} );
		foreach ( $options as $value => $label ) {
			$data['sizes'][] = array( 'value' => (string) $value, 'label' => (string) $label );
		}
		break; // One picker: the design only has Veličina.
	}

	foreach ( $product->get_available_variations( 'objects' ) as $variation ) {
		$attrs = $variation->get_attributes();
		$price = wc_get_price_to_display( $variation );
		$data['variations'][] = array(
			'id'      => $variation->get_id(),
			'size'    => $attrs[ substr( $data['sizeKey'], 10 ) ] ?? '',
			'inStock' => $variation->is_in_stock(),
			'price'   => (float) $price,
			'priceS'  => rade_price( $price ),
		);
	}

	return $data;
}

/* ---------------------------------------------------------------------------
 * Cart behaviour
 * ------------------------------------------------------------------------- */

// "Kupi odmah" posts the normal add-to-cart form plus rade_buy_now=1.
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	if ( ! empty( $_REQUEST['rade_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return wc_get_checkout_url();
	}
	return $url;
} );

// Live cart count + subtotal for the header badges and the drawer.
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$count = rade_cart_count();
	$fragments['.ro-cart-count']   = '<span class="ro-cart-count">' . $count . '</span>';
	$fragments['.ro-drawer-count'] = '<span class="ro-drawer-count">' . $count . '</span>';
	$fragments['.ro-drawer-total'] = '<span class="ro-drawer-total">' . esc_html( rade_price( WC()->cart->get_subtotal() ) ) . '</span>';
	return $fragments;
} );

/** The size label of a cart item ("M"), or ''. */
function rade_cart_item_size( $cart_item ) {
	foreach ( (array) ( $cart_item['variation'] ?? array() ) as $key => $value ) {
		$taxonomy = str_replace( 'attribute_', '', $key );
		return taxonomy_exists( $taxonomy ) ? ( get_term_by( 'slug', $value, $taxonomy )->name ?? $value ) : $value;
	}
	return '';
}

/** Product name without the " - M" variation suffix. */
function rade_cart_item_name( $cart_item ) {
	$product = wc_get_product( $cart_item['product_id'] );
	return $product ? $product->get_name() : '';
}

// Flat-rate shipping is shown before an address is entered.
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );
add_filter( 'pre_option_woocommerce_shipping_cost_requires_address', fn() => 'no' );

/* ---------------------------------------------------------------------------
 * Checkout fields — the mockup's fields, order and wording.
 * ------------------------------------------------------------------------- */

function rade_checkout_field_spec() {
	return array(
		'email'      => array( 'Email', 'ime@primer.rs', 10, 'form-row-wide' ),
		'first_name' => array( 'Ime', '', 20, 'form-row-first' ),
		'last_name'  => array( 'Prezime', '', 30, 'form-row-last' ),
		'country'    => array( 'Država', '', 40, 'form-row-wide' ),
		'address_1'  => array( 'Adresa', 'Ulica i broj', 50, 'form-row-wide' ),
		'city'       => array( 'Grad', '', 60, 'form-row-first' ),
		'postcode'   => array( 'Poštanski broj', '11000', 70, 'form-row-last' ),
		'phone'      => array( 'Telefon', '06x xxx xxxx', 80, 'form-row-wide' ),
	);
}

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$billing = array();
	foreach ( rade_checkout_field_spec() as $key => [ $label, $placeholder, $priority, $class ] ) {
		$field                = $fields['billing'][ 'billing_' . $key ] ?? array();
		$field['label']       = $label;
		$field['placeholder'] = $placeholder;
		$field['priority']    = $priority;
		$field['required']    = true;
		$field['class']       = array( $class );
		$billing[ 'billing_' . $key ] = $field;
	}
	$billing['billing_postcode']['custom_attributes'] = array( 'inputmode' => 'numeric', 'maxlength' => '5' );
	$fields['billing'] = $billing;
	unset( $fields['order'] );
	return $fields;
}, 20 );

// address-i18n.js re-applies locale labels/placeholders/priorities on load; keep ours.
add_filter( 'woocommerce_get_country_locale_default', function ( $locale ) {
	foreach ( rade_checkout_field_spec() as $key => [ $label, $placeholder, $priority, $class ] ) {
		if ( isset( $locale[ $key ] ) ) {
			$locale[ $key ]['label']       = $label;
			$locale[ $key ]['placeholder'] = $placeholder;
			$locale[ $key ]['priority']    = $priority;
			$locale[ $key ]['class']       = array( $class, 'address-field' );
		}
	}
	return $locale;
} );

add_filter( 'woocommerce_get_country_locale', function ( $locales ) {
	foreach ( $locales as $country => $fields ) {
		foreach ( array_keys( rade_checkout_field_spec() ) as $key ) {
			unset( $locales[ $country ][ $key ]['label'], $locales[ $country ][ $key ]['placeholder'], $locales[ $country ][ $key ]['priority'], $locales[ $country ][ $key ]['class'] );
		}
	}
	return $locales;
} );

// 5-digit postcode, phone with at least 8 digits.
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	$postcode = trim( (string) ( $data['billing_postcode'] ?? '' ) );
	if ( '' !== $postcode && ! preg_match( '/^\d{5}$/', $postcode ) ) {
		$errors->add( 'billing_postcode_validation', 'Poštanski broj ima 5 cifara.', array( 'id' => 'billing_postcode' ) );
	}
	$phone = (string) ( $data['billing_phone'] ?? '' );
	if ( '' !== trim( $phone ) && strlen( preg_replace( '/\D/', '', $phone ) ) < 8 ) {
		$errors->add( 'billing_phone_validation', 'Unesite ispravan broj telefona.', array( 'id' => 'billing_phone' ) );
	}
}, 10, 2 );

// Not in the design: coupon/login toggles, order-details table on thank-you.
// The "Završi kupovinu" block sits in the left column (form-checkout.php), not under the summary.
add_action( 'wp', function () {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
} );

add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

// The site language may be English: translate the WooCommerce messages customers can see.
add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	$map = array(
		'%s is a required field.'             => '%s je obavezno polje.',
		'%s is not a valid email address.'    => '%s nije ispravna email adresa.',
		'%s is not a valid phone number.'     => '%s nije ispravan broj telefona.',
		'%s is not a valid postcode / ZIP.'   => '%s nije ispravan poštanski broj.',
		'Please enter a valid email address.' => 'Unesite ispravan email.',
		'Cart updated.'                       => 'Korpa je ažurirana.',
		'%s removed.'                         => '%s je uklonjen iz korpe.',
		'Undo?'                               => 'Vrati?',
		'View cart'                           => 'Pogledaj korpu',
		'Please choose product options by visiting <a href="%1$s" title="%2$s">%3$s</a>.' => 'Izaberite veličinu na <a href="%1$s" title="%2$s">%3$s</a>.',
		// Order emails.
		'Order summary'                       => 'Pregled porudžbine',
		'Order #%s'                           => 'Porudžbina #%s',
		'Order #%1$s (%2$s)'                  => 'Porudžbina #%1$s (%2$s)',
		'[Order #%s]'                         => '[Porudžbina #%s]',
		'[Order #%1$s] (%2$s)'                => '[Porudžbina #%1$s] (%2$s)',
		'Product'                             => 'Proizvod',
		'Quantity'                            => 'Količina',
		'Price'                               => 'Cena',
		'Subtotal:'                           => 'Međuzbir:',
		'Discount:'                           => 'Popust:',
		'Shipping:'                           => 'Dostava:',
		'Total:'                              => 'Ukupno:',
		'Payment method:'                     => 'Način plaćanja:',
		'via %s'                              => '%s',
		'Billing address'                     => 'Adresa za naplatu',
		'Shipping address'                    => 'Adresa za dostavu',
		'Customer details'                    => 'Podaci o kupcu',
		'Customer note'                       => 'Napomena kupca',
		'Note:'                               => 'Napomena:',
		'Free!'                               => 'Besplatno!',
		// WooCommerce's own order-email body, in case its default template is used instead of ours.
		'Hi %s,'                              => 'Zdravo %s,',
		'Hi,'                                 => 'Zdravo,',
		'Thank you for your order'            => 'Hvala na porudžbini!',
		'Just to let you know &mdash; we’ve received your order, and it is now being processed.' => 'Primili smo tvoju porudžbinu i uskoro kreće na pakovanje.',
		'Here’s a reminder of what you’ve ordered:' => 'Evo pregleda šta si poručio/la:',
		'Thanks again! If you need any help with your order, please contact us at %s.' => 'Hvala još jednom! Ako ti treba pomoć oko porudžbine, javi nam se na %s.',
	);
	return $map[ $text ] ?? $translation;
}, 10, 2 );

// Dates in WooCommerce emails/orders: numeric Serbian format instead of "October 13, 2026".
add_filter( 'woocommerce_date_format', function () {
	return 'd.m.Y.';
} );

add_filter( 'gettext_with_context_woocommerce', function ( $translation, $text, $context ) {
	return ( 'Billing %s' === $text && 'checkout-validation' === $context ) ? '%s' : $translation;
}, 10, 3 );

add_filter( 'ngettext_woocommerce', function ( $translation, $single, $plural, $number ) {
	if ( '%s has been added to your cart.' === $single ) {
		return 1 === (int) $number ? '%s je dodat u korpu.' : '%s su dodati u korpu.';
	}
	return $translation;
}, 10, 4 );

add_filter( 'woocommerce_countries', function ( $countries ) {
	$countries['RS'] = 'Srbija';
	return $countries;
} );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

add_filter( 'woocommerce_order_button_text', fn() => 'Završi kupovinu' );

/* -------------------------------------------------------------------------
 * Order numbers: checkout orders get 411029, 411030, … (no prefix) stored as
 * meta, so they don't skip like internal IDs do. The next number is one above
 * the highest existing one, so deleted test orders free their numbers again.
 * Orders created outside checkout (wp-admin, REST) keep their plain ID.
 * ------------------------------------------------------------------------- */

define( 'RADE_ORDER_NUMBER_START', 411029 );

function rade_highest_order_number() {
	global $wpdb;
	$table = \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()
		? $wpdb->prefix . 'wc_orders_meta' : $wpdb->postmeta;
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(CAST(meta_value AS UNSIGNED)) FROM {$table} WHERE meta_key = %s", 'rade_order_number' ) );
}

add_action( 'woocommerce_checkout_order_created', function ( $order ) {
	if ( $order->get_meta( 'rade_order_number' ) ) {
		return;
	}
	$order->update_meta_data( 'rade_order_number', (string) max( RADE_ORDER_NUMBER_START, rade_highest_order_number() + 1 ) );
	$order->save_meta_data();
} );

add_filter( 'woocommerce_order_number', function ( $order_number, $order ) {
	return $order->get_meta( 'rade_order_number' ) ?: $order_number;
}, 10, 2 );

// Google Apps Script answers every POST with a 302 once the row is written, and
// WooCommerce counts a 302 as delivered. Never make WordPress follow it (no
// 'redirection' in woocommerce_webhook_http_args): it repeats the request as a GET
// that still carries the JSON body, Google answers 400, and after 6 such
// "failures" WooCommerce disables the webhook while the rows still get written.

// The invoicing system reads "Način plaćanja" from the sheet and needs exactly
// this text for pouzećem, whatever the gateway's title says.
add_filter( 'woocommerce_webhook_payload', function ( $payload, $resource ) {
	if ( 'order' === $resource && isset( $payload['payment_method'] ) && 'cod' === $payload['payment_method'] ) {
		$payload['payment_method_title'] = 'Plaćanje pouzećem';
	}
	return $payload;
}, 10, 2 );

// A disabled webhook stops filling the sheet without any sign, so tell the owner.
add_action( 'woocommerce_webhook_disabled_due_delivery_failures', function ( $webhook_id ) {
	wp_mail(
		'popovic.mihajlo1@gmail.com',
		'Webhook za Google Sheet je ISKLJUČEN: ' . home_url(),
		'WooCommerce je isključio webhook ' . $webhook_id . ' na ' . home_url() . ' posle više neuspešnih slanja. Nove porudžbine ne idu u "Sve Porudžbine" dok se ne uključi ponovo (WooCommerce → Podešavanja → Napredno → Webhooks).'
	);
} );

/* -------------------------------------------------------------------------
 * Order emails — Serbian copy in the design's colours.
 * Customer body: woocommerce/emails/customer-processing-order.php (+ plain/),
 * admin body: woocommerce/emails/admin-new-order.php (+ plain/).
 * ------------------------------------------------------------------------- */

// Subject filters run after WooCommerce's placeholder replacement, so build the number here.
add_filter( 'woocommerce_email_subject_customer_processing_order', function ( $subject, $order ) {
	$number = $order ? $order->get_order_number() : '';
	return 'Vaša porudžbina u Rade Opačić shopu je primljena! (#' . $number . ')';
}, 10, 2 );

add_filter( 'woocommerce_email_heading_customer_processing_order', function () {
	return 'Hvala na porudžbini!';
} );

add_filter( 'woocommerce_email_subject_new_order', function ( $subject, $order ) {
	$number = $order ? $order->get_order_number() : '';
	return 'Nova porudžbina #' . $number . ' — Rade Opačić';
}, 10, 2 );

add_filter( 'woocommerce_email_heading_new_order', function () {
	return 'Nova porudžbina';
} );

add_filter( 'woocommerce_email_footer_text', function () {
	return 'Rade Opačić · radeopacic.com';
} );

add_filter( 'woocommerce_email_additional_content_customer_processing_order', function () {
	return 'Ako imate pitanja u vezi sa porudžbinom, pišite nam na support@themerchpack.com.';
} );

add_filter( 'woocommerce_email_additional_content_new_order', '__return_empty_string' );

// Design tokens: red accent, light panel, near-black text.
foreach ( array(
	'woocommerce_email_base_color'            => '#d22630',
	'woocommerce_email_background_color'      => '#f3f1ec',
	'woocommerce_email_body_background_color' => '#ffffff',
	'woocommerce_email_text_color'            => '#0c0c0b',
	'woocommerce_email_footer_text_color'     => '#8d8a84',
) as $rade_option => $rade_value ) {
	add_filter( "pre_option_{$rade_option}", fn() => $rade_value );
}

/* -------------------------------------------------------------------------
 * Outgoing mail over SMTP (Titan Mail, opacic@themerchpack.com).
 * Hostinger's PHP mail() is unreliable, so wp_mail() logs in to Titan.
 * Address and password are NOT in the theme (this repo is on GitHub) —
 * they live in wp-config.php on the server as RADE_SMTP_USER / RADE_SMTP_PASS.
 * Without them, nothing changes.
 * ------------------------------------------------------------------------- */

add_action( 'phpmailer_init', function ( $phpmailer ) {
	if ( ! defined( 'RADE_SMTP_USER' ) || ! defined( 'RADE_SMTP_PASS' ) ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = 'smtp.titan.email';
	$phpmailer->Port       = 587;
	$phpmailer->SMTPSecure = 'tls';
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = RADE_SMTP_USER;
	$phpmailer->Password   = RADE_SMTP_PASS;
	$phpmailer->setFrom( RADE_SMTP_USER, 'Rade Opačić', false );
} );

if ( defined( 'RADE_SMTP_USER' ) ) {
	add_filter( 'woocommerce_email_from_address', fn() => RADE_SMTP_USER );
}
add_filter( 'woocommerce_email_from_name', fn() => 'Rade Opačić' );
