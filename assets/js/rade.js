/**
 * Rade Opačić shop interactions (mirrors the mockup's states): mobile menu,
 * home quick add, product size/qty/gallery, size-guide sheet, AJAX
 * add-to-cart + "Dodato u korpu" drawer, cart +/-.
 */
( function () {
	'use strict';

	const $ = ( sel, root = document ) => root.querySelector( sel );
	const $$ = ( sel, root = document ) => Array.from( root.querySelectorAll( sel ) );

	/* ---------- Overlays (size guide, drawer) ---------- */
	let lastFocus = null;
	function openOverlay( el ) {
		if ( ! el ) return;
		lastFocus = document.activeElement;
		el.hidden = false;
		document.body.classList.add( 'ro-locked' );
		const close = $( '[data-ro-close]', el );
		if ( close ) close.focus();
	}
	function closeOverlay( el ) {
		el.hidden = true;
		document.body.classList.remove( 'ro-locked' );
		if ( lastFocus ) lastFocus.focus();
	}
	$$( '.ro-overlay' ).forEach( ( el ) => {
		el.addEventListener( 'click', ( e ) => {
			if ( e.target === el || e.target.closest( '[data-ro-close]' ) ) closeOverlay( el );
		} );
	} );
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key !== 'Escape' ) return;
		$$( '.ro-overlay' ).filter( ( el ) => ! el.hidden ).forEach( closeOverlay );
		closeMenu();
	} );

	/* ---------- Mobile menu ---------- */
	const menu = $( '[data-ro-menu]' );
	function closeMenu() {
		if ( ! menu ) return;
		menu.hidden = true;
		$$( '[data-ro-menu-toggle]' ).forEach( ( b ) => b.setAttribute( 'aria-expanded', 'false' ) );
	}
	$$( '[data-ro-menu-toggle]' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', ( e ) => {
			e.stopPropagation();
			if ( ! menu ) return;
			menu.hidden = ! menu.hidden;
			btn.setAttribute( 'aria-expanded', String( ! menu.hidden ) );
		} );
	} );
	document.addEventListener( 'click', ( e ) => {
		if ( menu && ! menu.hidden && ! menu.contains( e.target ) ) closeMenu();
	} );

	/* ---------- Shared: AJAX add + drawer ---------- */
	function formatPrice( n ) {
		return Math.round( n ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, '.' ) + ' RSD';
	}

	function applyFragments( fragments ) {
		Object.keys( fragments ).forEach( ( sel ) => {
			let nodes;
			try {
				nodes = $$( sel );
			} catch ( err ) {
				return;
			}
			nodes.forEach( ( node ) => {
				const tpl = document.createElement( 'template' );
				tpl.innerHTML = fragments[ sel ].trim();
				if ( tpl.content.firstElementChild ) node.replaceWith( tpl.content.firstElementChild );
			} );
		} );
	}

	/** Resolves true when added; false when the caller should fall back to a normal request. */
	function ajaxAdd( productId, qty, button ) {
		if ( ! window.RADE || ! RADE.addToCartUrl || ! window.fetch ) return Promise.resolve( false );
		return fetch( RADE.addToCartUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams( { product_id: productId, quantity: qty } ),
		} )
			.then( ( r ) => r.json() )
			.then( ( data ) => {
				if ( ! data || data.error ) return false;
				applyFragments( data.fragments || {} );
				if ( window.jQuery ) {
					window.jQuery( document.body ).trigger( 'added_to_cart', [ data.fragments, data.cart_hash, window.jQuery( button ) ] );
				}
				return true;
			} )
			.catch( () => false );
	}

	function showDrawer( { img, name, variant, total } ) {
		const drawer = $( '[data-ro-drawer]' );
		if ( ! drawer ) return;
		$( '[data-ro-drawer-img]', drawer ).src = img;
		$( '[data-ro-drawer-name]', drawer ).textContent = name;
		$( '[data-ro-drawer-variant]', drawer ).textContent = variant;
		$( '[data-ro-drawer-price]', drawer ).textContent = total;
		openOverlay( drawer );
	}

	/* ---------- Home feature: size chips + quick add ---------- */
	$$( '[data-ro-card]' ).forEach( ( card ) => {
		const variations = JSON.parse( card.dataset.variations || '[]' );
		const sizeBtns = $$( '[data-ro-card-size]', card );
		const sizesBox = $( '[data-ro-card-sizes]', card );
		const error = $( '[data-ro-card-error]', card );
		const addBtn = $( '[data-ro-card-add]', card );
		const priceEl = $( '[data-ro-card-price]', card );
		const state = { size: '', sizeLabel: '' };
		const find = () => variations.find( ( v ) => v.size === state.size );

		function render() {
			sizeBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.size;
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
				b.disabled = ! variations.some( ( v ) => v.size === b.dataset.value && v.inStock );
			} );
			const v = find();
			if ( priceEl ) priceEl.textContent = v ? v.priceS : card.dataset.price;
		}

		sizeBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.size = b.dataset.value;
				state.sizeLabel = b.dataset.label;
				if ( error ) error.hidden = true;
				if ( sizesBox ) sizesBox.classList.remove( 'is-error' );
				render();
			} )
		);

		if ( addBtn ) {
			addBtn.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const v = find();
				if ( ! v || ! v.inStock ) {
					if ( error ) error.hidden = false;
					if ( sizesBox ) sizesBox.classList.add( 'is-error' );
					return;
				}
				addBtn.classList.add( 'is-busy' );
				ajaxAdd( v.id, 1, addBtn ).then( ( ok ) => {
					addBtn.classList.remove( 'is-busy' );
					if ( ! ok ) {
						window.location.href = addBtn.href;
						return;
					}
					showDrawer( {
						img: card.dataset.img,
						name: card.dataset.name,
						variant: 'Veličina ' + state.sizeLabel + ' · Količina 1',
						total: v.priceS,
					} );
					state.size = '';
					state.sizeLabel = '';
					render();
				} );
			} );
		}

		render();
	} );

	/* ---------- Product page ---------- */
	const form = $( '[data-ro-buy]' );
	if ( form ) initProduct( form );

	function initProduct( form ) {
		const variations = JSON.parse( form.dataset.variations || '[]' );
		const mainImg = $( '[data-ro-main-img]' );
		const thumbs = $$( '[data-ro-thumb]' );
		const sizeBtns = $$( '[data-ro-size]', form );
		const sizeInput = $( '[data-ro-size-input]', form );
		const variationInput = $( '[data-ro-variation]', form );
		const qtyInput = $( '[data-ro-qty-input]', form );
		const error = $( '[data-ro-error]', form );
		const state = { size: '', sizeLabel: '', qty: 1 };

		const findVariation = () => variations.find( ( v ) => v.size === state.size );

		function render() {
			sizeBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.size;
				b.disabled = ! variations.some( ( v ) => v.size === b.dataset.value && v.inStock );
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
			} );
			$$( '[data-ro-size-suffix]', form ).forEach( ( el ) => ( el.textContent = state.sizeLabel ? ': ' + state.sizeLabel : '' ) );
			$$( '[data-ro-size-short]', form ).forEach( ( el ) => ( el.textContent = state.sizeLabel ? 'Veličina ' + state.sizeLabel : 'Izaberite veličinu' ) );
			$( '[data-ro-qty-value]', form ).textContent = state.qty;
			qtyInput.value = state.qty;
			if ( sizeInput ) sizeInput.value = state.size;
			const v = findVariation();
			if ( variationInput ) variationInput.value = v && v.inStock ? v.id : '';
			$$( '[data-ro-price]', form ).forEach( ( el ) => ( el.textContent = v && state.size ? v.priceS : form.dataset.price ) );
		}

		sizeBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.size = b.dataset.value;
				state.sizeLabel = b.dataset.label;
				if ( error ) error.hidden = true;
				$( '[data-ro-sizes]', form ).classList.remove( 'is-error' );
				render();
			} )
		);
		thumbs.forEach( ( t ) =>
			t.addEventListener( 'click', () => {
				mainImg.src = t.dataset.large;
				thumbs.forEach( ( x ) => x.classList.toggle( 'is-active', x === t ) );
			} )
		);
		$$( '[data-ro-qty]', form ).forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.qty = Math.max( 1, state.qty + Number( b.dataset.roQty ) );
				render();
			} )
		);

		const guide = $( '[data-ro-guide]' );
		$$( '[data-ro-guide-open]' ).forEach( ( b ) => b.addEventListener( 'click', () => openOverlay( guide ) ) );

		function needsSize() {
			if ( ! variationInput || variationInput.value ) return false;
			if ( error ) error.hidden = false;
			const sizes = $( '[data-ro-sizes]', form );
			if ( sizes ) {
				sizes.classList.add( 'is-error' );
				sizes.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
			return true;
		}

		form.addEventListener( 'submit', ( e ) => {
			const submitter = e.submitter;
			if ( needsSize() ) {
				e.preventDefault();
				return;
			}
			// "Kupi odmah" posts normally (server redirects to checkout).
			if ( submitter && submitter.name === 'rade_buy_now' ) return;
			if ( ! window.RADE || ! RADE.addToCartUrl || ! window.fetch ) return;
			e.preventDefault();
			const v = findVariation();
			const qty = state.qty;
			$$( '[data-ro-add]', form ).forEach( ( b ) => ( b.disabled = true ) );
			ajaxAdd( v ? v.id : form.querySelector( '[name="add-to-cart"]' ).value, qty, submitter ).then( ( ok ) => {
				$$( '[data-ro-add]', form ).forEach( ( b ) => ( b.disabled = false ) );
				if ( ! ok ) {
					// Let WooCommerce show its own error (e.g. out of stock) via a normal post.
					HTMLFormElement.prototype.submit.call( form );
					return;
				}
				showDrawer( {
					img: form.dataset.img,
					name: form.dataset.name,
					variant: ( state.sizeLabel ? 'Veličina ' + state.sizeLabel + ' · ' : '' ) + 'Količina ' + qty,
					total: formatPrice( ( v ? v.price : 0 ) * qty ),
				} );
				state.qty = 1;
				render();
			} );
		} );

		render();
	}

	/* ---------- Cart page: +/- trigger WooCommerce's AJAX cart update ---------- */
	document.addEventListener( 'click', ( e ) => {
		const btn = e.target.closest( '[data-ro-cart-qty] button[data-step]' );
		if ( ! btn ) return;
		const input = $( 'input.qty', btn.parentElement );
		const next = Math.max( 1, ( parseInt( input.value, 10 ) || 1 ) + Number( btn.dataset.step ) );
		if ( String( next ) === input.value ) return;
		input.value = next;
		const update = $( '.woocommerce-cart-form [name="update_cart"]' );
		if ( update ) {
			update.disabled = false;
			update.click();
		}
	} );

	// Keep the header badge in sync after WooCommerce's AJAX cart update / removal.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_wc_div', () => {
			const cartForm = $( '.woocommerce-cart-form' );
			const count = cartForm ? cartForm.dataset.count : '0';
			$$( '.ro-cart-count' ).forEach( ( el ) => ( el.textContent = count ) );
		} );
	}
} )();
