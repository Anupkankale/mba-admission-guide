<?php
/**
 * MBA Admission Guide theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MBAG_VERSION', '1.34.0' );

/**
 * Theme setup.
 */
function mbag_setup() {
	load_theme_textdomain( 'mba-admission-guide', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 48,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Let the editor preview match the front end.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );

	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'mbag-card', 720, 460, true );

	register_nav_menus( array(
		'primary'     => __( 'Primary Menu', 'mba-admission-guide' ),
		'footer'      => __( 'Footer Menu', 'mba-admission-guide' ),
		// Used only by header-landing.php. Left unassigned, the landing
		// header falls back to that page's own section anchors.
		'smu_landing' => __( 'Landing Page Menu (Sikkim Manipal)', 'mba-admission-guide' ),
	) );
}
add_action( 'after_setup_theme', 'mbag_setup' );

/**
 * Content width used by oEmbeds and wide images.
 */
function mbag_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mbag_content_width', 820 );
}
add_action( 'after_setup_theme', 'mbag_content_width', 0 );

/**
 * Enqueue styles and scripts.
 */
function mbag_scripts() {
	// Google Fonts.
	wp_enqueue_style(
		'mbag-fonts',
		// Manrope only. Weight 800 is included because the headings use it —
		// without it the browser synthesises a fake bold, which looks smeared.
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	// Theme stylesheet.
	wp_enqueue_style( 'mbag-style', get_stylesheet_uri(), array(), MBAG_VERSION );

	// Theme script (footer).
	wp_enqueue_script( 'mbag-main', get_template_directory_uri() . '/js/main.js', array(), MBAG_VERSION, true );

	// Threaded comments on posts/pages that allow them.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Pass theme options (phone, WhatsApp, thank-you redirect) to JS.
	wp_localize_script( 'mbag-main', 'mbagSettings', array(
		'phone'      => mbag_phone_display(),
		'phoneLink'  => mbag_phone_link(),
		'whatsapp'   => mbag_whatsapp_number(),
		'thankYou'   => mbag_thank_you_url(),
		'isFront'    => is_front_page(),
		'uniLogos'   => mbag_university_logos(),
		'uniLogoPlaceholder' => mbag_university_logo_placeholder(),
		// One source for the star glyph, so the hero rating and the
		// testimonial cards cannot end up using different marks.
		'starIcon'   => mbag_get_icon( 'star' ),
		'popup'      => array(
			'delay'  => mbag_popup_active() ? (int) mbag_popup_settings()['delay'] : 0,
			'scroll' => mbag_popup_active() ? (int) mbag_popup_settings()['scroll'] : 0,
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'mbag_scripts' );

/**
 * Warm up the Google Fonts connections.
 *
 * Two origins are involved and both need a hint: fonts.googleapis.com serves
 * the CSS, and the @font-face rules inside it then point at fonts.gstatic.com.
 * Only gstatic was hinted before, so the stylesheet — the render-blocking half
 * — still paid a full DNS + TLS handshake before it could start downloading.
 *
 * @param array  $urls          URLs to print.
 * @param string $relation_type The hint type.
 * @return array
 */
function mbag_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'mbag_resource_hints', 10, 2 );

/**
 * ---------------------------------------------------------------
 * TRIM WHAT CORE ADDS BUT THIS THEME NEVER USES
 *
 * WordPress prints these on every page whether or not anything on
 * the page needs them. None of this theme's templates do.
 * ---------------------------------------------------------------
 */
function mbag_trim_core_assets() {
	// The emoji polyfill: an inline detection script in <head> plus
	// wp-emoji-release.min.js. It exists to render emoji on operating
	// systems that shipped without them, which no browser in this site's
	// analytics still needs. Real emoji characters keep working — they
	// are just drawn by the OS font instead of swapped for Twemoji PNGs.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// wp-embed.js only powers embedding *other* WordPress posts as cards.
	// Nothing here does that, and it does not affect YouTube/Vimeo oEmbeds.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'mbag_trim_core_assets' );

/**
 * Stop the emoji plugin from re-adding itself to TinyMCE.
 *
 * @param array $plugins TinyMCE plugins.
 * @return array
 */
function mbag_disable_emojis_tinymce( $plugins ) {
	return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}
add_filter( 'tiny_mce_plugins', 'mbag_disable_emojis_tinymce' );

/**
 * Drop the s.w.org DNS-prefetch the emoji script leaves behind.
 *
 * @param array  $urls          URLs to print.
 * @param string $relation_type The hint type.
 * @return array
 */
function mbag_remove_emoji_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$urls = array_filter(
			$urls,
			function ( $url ) {
				return false === strpos( is_array( $url ) ? ( $url['href'] ?? '' ) : $url, 's.w.org' );
			}
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'mbag_remove_emoji_dns_prefetch', 10, 2 );

/**
 * ---------------------------------------------------------------
 * SCRIPT LOADING
 *
 * Lighthouse showed ten first-party scripts blocking first paint,
 * most of them from plugins (Contact Form 7, analytics trackers).
 * A blocking <script> stops HTML parsing until it has downloaded
 * and run; `defer` lets parsing continue and runs the script after
 * the document is parsed, in the same order.
 * ---------------------------------------------------------------
 */

/**
 * Drop jQuery Migrate.
 *
 * It is a compatibility shim for code written against jQuery 1.x, and it costs
 * a 1,070ms blocking request in the audit. Nothing in this theme uses jQuery.
 *
 * Two steps, because removing it from jQuery's dependency list is not enough
 * on a site with plugins: any plugin that names 'jquery-migrate' in its own
 * deps, or enqueues it directly, pulls it back in. Emptying the src is what
 * actually stops it — core treats a handle with no src as an alias, so it
 * prints no <script> tag while every dependency on it still resolves.
 *
 * If a plugin here genuinely needs it, delete this block rather than working
 * around it. The symptom is a console error naming jQuery.migrate.
 *
 * @param WP_Scripts $scripts Script registry.
 */
function mbag_remove_jquery_migrate( $scripts ) {
	if ( is_admin() ) {
		return;
	}

	if ( ! empty( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			array( 'jquery-migrate' )
		);
	}

	if ( ! empty( $scripts->registered['jquery-migrate'] ) ) {
		$scripts->registered['jquery-migrate']->src = '';
	}
}
add_action( 'wp_default_scripts', 'mbag_remove_jquery_migrate', 99 );

/**
 * Map a local asset URL back to a filesystem path.
 *
 * Returns '' for anything not served from this install's wp-content or
 * wp-includes, so a CDN or third-party URL is never touched.
 *
 * @param string $url Asset URL.
 * @return string Absolute path, or '' if the URL is not local.
 */
function mbag_local_asset_path( $url ) {
	$url = strtok( $url, '?' );

	foreach ( array(
		array( content_url(), WP_CONTENT_DIR ),
		array( includes_url(), ABSPATH . WPINC ),
	) as $pair ) {
		list( $base_url, $base_dir ) = $pair;

		// Compare protocol-relative, so http/https never causes a miss.
		$needle   = preg_replace( '#^https?:#', '', $base_url );
		$haystack = preg_replace( '#^https?:#', '', $url );

		if ( 0 === strpos( $haystack, $needle ) ) {
			return $base_dir . substr( $haystack, strlen( $needle ) );
		}
	}

	return '';
}

/**
 * Let core inline small stylesheets.
 *
 * Every <link rel="stylesheet"> is render-blocking, and on this host a 0.6 KiB
 * file still cost 1,070ms — the cost is the round trip, not the bytes.
 *
 * Core already does this in wp_maybe_inline_styles(), but only for styles that
 * declare a 'path'. Core's own stylesheets set it; plugin stylesheets almost
 * never do, which is why they stay render-blocking. So all this does is supply
 * the missing 'path' for local stylesheets and let core decide the rest:
 *
 *   - core keeps a 40KB total budget (filter: styles_inline_size_limit) and
 *     inlines smallest-first, so it cannot bloat the document;
 *   - core runs _wp_normalize_relative_css_links(), which rewrites relative
 *     url() references instead of breaking them;
 *   - the theme's own style.css is far larger than the budget and stays a
 *     separate cacheable file, which is what we want for repeat visits.
 *
 * Doing it this way rather than filtering style_loader_tag ourselves means
 * core owns the budget, the URL rewriting and the sourceURL comments.
 */
function mbag_inline_small_styles() {
	$styles = wp_styles();

	foreach ( $styles->queue as $handle ) {
		if ( ! isset( $styles->registered[ $handle ] ) ) {
			continue;
		}

		// Never override a path a plugin or core already set.
		if ( $styles->get_data( $handle, 'path' ) ) {
			continue;
		}

		$src = $styles->registered[ $handle ]->src;

		if ( ! $src || ! is_string( $src ) ) {
			continue;
		}

		$path = mbag_local_asset_path( $src );

		if ( '' !== $path && is_readable( $path ) ) {
			wp_style_add_data( $handle, 'path', $path );
		}
	}
}
// Priority 1 on wp_head, before core's wp_maybe_inline_styles() at the same hook.
add_action( 'wp_enqueue_scripts', 'mbag_inline_small_styles', PHP_INT_MAX );

/**
 * Handles that must keep running during parse.
 *
 * jQuery stays blocking because plugins commonly echo a raw
 * <script>jQuery(...)</script> straight into the footer. Those are invisible
 * to WP_Scripts, so core's own eligibility check cannot know about them.
 *
 * @return string[]
 */
function mbag_blocking_scripts() {
	return apply_filters( 'mbag_blocking_scripts', array( 'jquery', 'jquery-core', 'jquery-migrate' ) );
}

/**
 * Ask core to defer front-end scripts.
 *
 * Uses the script strategy API added in WP 6.3 rather than rewriting the
 * <script> tag ourselves. That matters, because core refuses to delay a
 * script when it would break:
 *
 *   - a handle with an inline 'after' script (its inline code runs during
 *     parse and would hit a global that does not exist yet);
 *   - a handle whose dependents are not themselves deferred.
 *
 * Those are the same rules a hand-rolled filter has to reimplement, and core
 * already applies them across the whole dependency tree. Anything ineligible
 * silently stays blocking, which is the safe outcome.
 */
function mbag_defer_scripts() {
	$scripts  = wp_scripts();
	$blocking = mbag_blocking_scripts();

	foreach ( $scripts->queue as $handle ) {
		mbag_defer_script_tree( $handle, $blocking, $scripts );
	}
}
add_action( 'wp_enqueue_scripts', 'mbag_defer_scripts', PHP_INT_MAX );

/**
 * Mark a handle and its dependencies as 'defer'.
 *
 * Dependencies are walked because $wp_scripts->queue holds only the handles
 * that were enqueued directly — wp-hooks and wp-i18n arrive as dependencies
 * of a plugin script and would otherwise never be marked.
 *
 * @param string     $handle   Script handle.
 * @param string[]   $blocking Handles to leave alone.
 * @param WP_Scripts $scripts  Script registry.
 * @param string[]   $seen     Handles already visited, guarding circular deps.
 */
function mbag_defer_script_tree( $handle, $blocking, $scripts, &$seen = array() ) {
	if ( isset( $seen[ $handle ] ) || in_array( $handle, $blocking, true ) ) {
		return;
	}

	$seen[ $handle ] = true;

	if ( ! isset( $scripts->registered[ $handle ] ) ) {
		return;
	}

	foreach ( $scripts->registered[ $handle ]->deps as $dep ) {
		mbag_defer_script_tree( $dep, $blocking, $scripts, $seen );
	}

	// Core validates this and falls back to blocking when deferring is unsafe.
	wp_script_add_data( $handle, 'strategy', 'defer' );
}

/**
 * Basic Customizer options for contact details.
 */
function mbag_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'mbag_contact', array(
		'title'    => __( 'Contact Details', 'mba-admission-guide' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'mbag_phone', array(
		'default'           => mbag_contact_defaults()['phone'],
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'mbag_phone', array(
		'label'   => __( 'Displayed Phone Number', 'mba-admission-guide' ),
		'section' => 'mbag_contact',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'mbag_phone_link', array(
		'default'           => mbag_contact_defaults()['phone_link'],
		'sanitize_callback' => 'mbag_sanitize_phone',
	) );
	$wp_customize->add_control( 'mbag_phone_link', array(
		'label'   => __( 'Phone Number for tel: link (no spaces)', 'mba-admission-guide' ),
		'section' => 'mbag_contact',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'mbag_whatsapp', array(
		'default'           => mbag_contact_defaults()['whatsapp'],
		'sanitize_callback' => 'mbag_sanitize_digits',
	) );
	$wp_customize->add_control( 'mbag_whatsapp', array(
		'label'       => __( 'WhatsApp Number (with country code, no +)', 'mba-admission-guide' ),
		'description' => __( 'Used by the floating bubble, the mobile bar, the CTA rail and the Contact page. Changing it here changes all of them.', 'mba-admission-guide' ),
		'section'     => 'mbag_contact',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'mbag_hours', array(
		'default'           => mbag_contact_defaults()['hours'],
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mbag_hours', array(
		'label'       => __( 'Counselling hours', 'mba-admission-guide' ),
		'description' => __( 'Shown on the Contact page. Confirm this matches when someone actually answers.', 'mba-admission-guide' ),
		'section'     => 'mbag_contact',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'mbag_response', array(
		'default'           => mbag_contact_defaults()['response'],
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mbag_response', array(
		'label'       => __( 'Response time', 'mba-admission-guide' ),
		'description' => __( 'Shown on the Contact page, under the hours.', 'mba-admission-guide' ),
		'section'     => 'mbag_contact',
		'type'        => 'text',
	) );
}
add_action( 'customize_register', 'mbag_customize_register' );

/**
 * Fallback menu for the primary nav (if no menu is assigned).
 */
function mbag_fallback_menu( $class = 'flinks' ) {
	$links = array(
		'universities'    => __( 'Universities', 'mba-admission-guide' ),
		'compare'         => __( 'Compare', 'mba-admission-guide' ),
		'specializations' => __( 'Specializations', 'mba-admission-guide' ),
		'faqs'            => __( 'FAQs', 'mba-admission-guide' ),
	);

	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $anchor => $label ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( mbag_anchor( $anchor ) ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * ---------------------------------------------------------------
 * CONTACT FORM 7 INTEGRATION
 *
 * The theme ships with three hard-coded lead forms (hero, middle,
 * bottom). Each one is replaced automatically by a Contact Form 7
 * form as soon as you assign a CF7 form ID to that slot in
 * Appearance > Customize > Lead Forms.
 *
 * If CF7 is not installed, or a slot is left blank, the original
 * static markup is shown instead - so the page never breaks.
 * ---------------------------------------------------------------
 */

/**
 * The form slots available in the templates.
 */
function mbag_form_slots() {
	return array(
		'hero'   => __( 'Hero form (top of page)', 'mba-admission-guide' ),
		'talk'   => __( 'Talk to a Counsellor form (middle of page)', 'mba-admission-guide' ),
		'footer' => __( 'Final CTA form (bottom of page)', 'mba-admission-guide' ),
		'popup'  => __( 'Popup form (timed / scroll / button)', 'mba-admission-guide' ),
	);
}

/**
 * The form ID every slot falls back to.
 *
 * @return string
 */
function mbag_default_form_id() {
	return (string) apply_filters( 'mbag_default_form_id', '570b038' );
}

/**
 * The CF7 form ID to render in a slot.
 *
 * Resolution order:
 *   1. the ID set for this slot in the Customizer
 *   2. the ID set for the popup slot — so configuring ONE form drives the
 *      whole site instead of asking for the same ID four times
 *   3. the theme's default form ID
 *
 * This is what keeps the same form on the hero, the middle section, the
 * final CTA and the popup. Set a slot explicitly only when you deliberately
 * want a different form there (a shorter one in the footer, say).
 *
 * @param string $slot Slot key.
 * @return string
 */
function mbag_form_slot_id( $slot ) {
	$id = trim( (string) get_theme_mod( 'mbag_cf7_' . $slot, '' ) );

	if ( '' !== $id ) {
		return $id;
	}

	if ( 'popup' !== $slot ) {
		$shared = trim( (string) get_theme_mod( 'mbag_cf7_popup', '' ) );

		if ( '' !== $shared ) {
			return $shared;
		}
	}

	return mbag_default_form_id();
}

/**
 * Back-compat wrapper.
 *
 * @param string $slot Slot key.
 * @return string
 */
function mbag_form_slot_default( $slot ) {
	return mbag_default_form_id();
}

/**
 * Is Contact Form 7 active?
 */
function mbag_cf7_active() {
	return defined( 'WPCF7_VERSION' ) || class_exists( 'WPCF7' );
}

/**
 * Does a CF7 form with this ID actually exist?
 *
 * Without this check a mistyped or stale ID renders CF7's own
 * "Error: Contact form not found." text straight onto the live page —
 * inside a popup, that is the entire popup. Falling back to the theme's
 * static form (or, for the popup, to nothing at all) fails quietly instead.
 *
 * @param string $id Hash ID from the CF7 shortcode, or a legacy numeric ID.
 * @return bool
 */
function mbag_cf7_form_exists( $id ) {
	$id = trim( (string) $id );

	if ( '' === $id || ! mbag_cf7_active() ) {
		return false;
	}

	// Modern hash IDs: 7+ hex characters.
	if ( function_exists( 'wpcf7_get_contact_form_by_hash' ) && preg_match( '/^[0-9a-f]{7,}$/', $id ) ) {
		return (bool) wpcf7_get_contact_form_by_hash( $id );
	}

	// Legacy numeric post IDs.
	if ( ctype_digit( $id ) && function_exists( 'wpcf7_contact_form' ) ) {
		return (bool) wpcf7_contact_form( (int) $id );
	}

	return false;
}

/**
 * Render the Contact Form 7 form assigned to a slot.
 *
 * @param string $slot One of hero|talk|footer.
 * @return bool True if a CF7 form was rendered. False means the template
 *              should fall back to the theme's built-in static markup.
 */
function mbag_lead_form( $slot ) {
	if ( ! mbag_cf7_active() ) {
		return false;
	}

	$id = mbag_form_slot_id( $slot );
	if ( '' === $id || ! mbag_cf7_form_exists( $id ) ) {
		return false;
	}

	$shortcode = sprintf(
		'[contact-form-7 id="%s" html_class="form mbag-form"]',
		esc_attr( $id )
	);

	echo '<div class="mbag-cf7 mbag-cf7--' . esc_attr( $slot ) . '">';
	echo do_shortcode( $shortcode );
	echo '</div>';

	return true;
}

/**
 * Customizer: assign a CF7 form to each slot.
 */
function mbag_customize_forms( $wp_customize ) {
	$wp_customize->add_section( 'mbag_forms', array(
		'title'       => __( 'Lead Forms', 'mba-admission-guide' ),
		'priority'    => 31,
		'description' => __( 'Paste one Contact Form 7 form ID into the Popup slot and every slot uses it — hero, middle, final CTA and popup. Fill another slot only if you want a different form there. Find the ID in Contact > Forms, inside id="..." in the shortcode column.', 'mba-admission-guide' ),
	) );

	foreach ( mbag_form_slots() as $slot => $label ) {
		$wp_customize->add_setting( 'mbag_cf7_' . $slot, array(
			'default'           => mbag_form_slot_default( $slot ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'mbag_cf7_' . $slot, array(
			'label'   => $label,
			'section' => 'mbag_forms',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'mbag_customize_forms' );

/**
 * Nudge the admin to install CF7 if no slot is wired up yet.
 */
function mbag_cf7_admin_notice() {
	if ( ! current_user_can( 'install_plugins' ) || mbag_cf7_active() ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'MBA Admission Guide:', 'mba-admission-guide' ),
		esc_html__( 'the lead forms on your page are display-only until you connect a form plugin. Install Contact Form 7, then assign your forms under Appearance &rsaquo; Customize &rsaquo; Lead Forms.', 'mba-admission-guide' ),
		esc_url( admin_url( 'plugin-install.php?s=contact+form+7&tab=search&type=term' ) ),
		esc_html__( 'Install Contact Form 7', 'mba-admission-guide' )
	);
}
add_action( 'admin_notices', 'mbag_cf7_admin_notice' );

/**
 * ---------------------------------------------------------------
 * TEMPLATE HELPERS
 *
 * Small, escaped-at-output helpers shared by every template file.
 * ---------------------------------------------------------------
 */

/**
 * Default contact details.
 *
 * Defined in one place so the Customizer defaults and the template helpers
 * below can never drift apart. Changing the number here changes it for a
 * fresh install; an existing site keeps whatever is saved in the Customizer.
 *
 * @return array<string,string>
 */
function mbag_contact_defaults() {
	return array(
		'phone'      => '+91 63629 46008',
		'phone_link' => '+916362946008',
		'whatsapp'   => '916362946008',
		'hours'      => __( 'Monday to Saturday, 9:00 am to 8:00 pm', 'mba-admission-guide' ),
		'response'   => __( 'We usually reply within one working day.', 'mba-admission-guide' ),
	);
}

/**
 * Sanitize a tel: value — digits, plus sign and nothing else.
 *
 * @param string $value Raw value.
 * @return string
 */
function mbag_sanitize_phone( $value ) {
	return preg_replace( '/[^0-9+]/', '', (string) $value );
}

/**
 * Sanitize a digits-only value (WhatsApp numbers).
 *
 * @param string $value Raw value.
 * @return string
 */
function mbag_sanitize_digits( $value ) {
	return preg_replace( '/[^0-9]/', '', (string) $value );
}

/**
 * The phone number as displayed to visitors.
 *
 * @return string
 */
function mbag_phone_display() {
	return (string) get_theme_mod( 'mbag_phone', mbag_contact_defaults()['phone'] );
}

/**
 * The phone number used inside tel: links.
 *
 * @return string
 */
function mbag_phone_link() {
	return mbag_sanitize_phone( get_theme_mod( 'mbag_phone_link', mbag_contact_defaults()['phone_link'] ) );
}

/**
 * The WhatsApp number (digits only, country code included).
 *
 * @return string
 */
function mbag_whatsapp_number() {
	return mbag_sanitize_digits( get_theme_mod( 'mbag_whatsapp', mbag_contact_defaults()['whatsapp'] ) );
}

/**
 * Counselling hours, as shown on the Contact page.
 *
 * @return string
 */
function mbag_hours() {
	return trim( (string) get_theme_mod( 'mbag_hours', mbag_contact_defaults()['hours'] ) );
}

/**
 * Expected response time, as shown on the Contact page.
 *
 * @return string
 */
function mbag_response_time() {
	return trim( (string) get_theme_mod( 'mbag_response', mbag_contact_defaults()['response'] ) );
}

/**
 * A ready-to-use wa.me link with a prefilled message.
 *
 * @param string $message Optional prefilled text.
 * @return string
 */
function mbag_whatsapp_link( $message = '' ) {
	$url = 'https://wa.me/' . mbag_whatsapp_number();

	if ( '' === $message ) {
		$message = __( 'Hi, I want Online MBA admission guidance', 'mba-admission-guide' );
	}

	return add_query_arg( 'text', rawurlencode( $message ), $url );
}

/**
 * Link to a section of the landing page from anywhere on the site.
 *
 * On the front page this stays a plain in-page anchor (so smooth scrolling
 * still works). On every other page it becomes an absolute URL back to the
 * landing page, which is what makes the shared header, footer and CTA bands
 * safe to reuse on the Thank You page, blog posts and legal pages.
 *
 * @param string $anchor Section id, with or without the leading '#'.
 * @return string
 */
function mbag_anchor( $anchor ) {
	$anchor = ltrim( (string) $anchor, '#' );

	if ( is_front_page() ) {
		return '#' . $anchor;
	}

	return home_url( '/#' . $anchor );
}

/**
 * The Thank You page URL, if one has been selected in the Customizer.
 *
 * @return string Empty string when no page is set.
 */
function mbag_thank_you_url() {
	$id = (int) get_theme_mod( 'mbag_thankyou_page', 0 );

	if ( $id > 0 && 'publish' === get_post_status( $id ) ) {
		return get_permalink( $id );
	}

	return '';
}

/**
 * Customizer: pick the Thank You page.
 */
function mbag_customize_thankyou( $wp_customize ) {
	$wp_customize->add_setting( 'mbag_thankyou_page', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'mbag_thankyou_page', array(
		'label'       => __( 'Thank You page (after form submit)', 'mba-admission-guide' ),
		'description' => __( 'Create a page using the "Thank You (Lead Confirmation)" template, then select it here. Contact Form 7 submissions redirect to it automatically.', 'mba-admission-guide' ),
		'section'     => 'mbag_forms',
		'type'        => 'dropdown-pages',
	) );
}
add_action( 'customize_register', 'mbag_customize_thankyou' );

/**
 * Keep the Thank You page and search results out of search engines.
 *
 * A thank-you page that ranks means strangers landing on it and firing your
 * conversion tag, which quietly ruins the numbers you make decisions on.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function mbag_noindex_robots( $robots ) {
	if ( is_search() || is_404() || is_page_template( 'page-templates/template-thank-you.php' ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'mbag_noindex_robots' );

/**
 * Numbered pagination for the blog, archives and search.
 */
function mbag_pagination() {
	the_posts_pagination( array(
		'mid_size'           => 1,
		'prev_text'          => mbag_get_icon( 'arrow-left' ) . ' ' . esc_html__( 'Newer', 'mba-admission-guide' ),
		'next_text'          => esc_html__( 'Older', 'mba-admission-guide' ) . ' ' . mbag_get_icon( 'arrow-right' ),
		'screen_reader_text' => esc_html__( 'Posts navigation', 'mba-admission-guide' ),
		'class'              => 'mbag-pagination',
	) );
}

/**
 * Lightweight breadcrumb trail for inner pages.
 *
 * Deliberately simple: Home > (parents) > current. If an SEO plugin already
 * outputs breadcrumbs of its own, hide these with CSS or unhook the caller.
 */
function mbag_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$crumbs = array(
		'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'mba-admission-guide' ) . '</a>',
	);

	if ( is_singular() ) {
		$post_id = get_queried_object_id();

		foreach ( array_reverse( get_post_ancestors( $post_id ) ) as $ancestor ) {
			$crumbs[] = '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
		}

		if ( is_single() ) {
			$blog_id = (int) get_option( 'page_for_posts' );
			if ( $blog_id ) {
				$crumbs[] = '<a href="' . esc_url( get_permalink( $blog_id ) ) . '">' . esc_html( get_the_title( $blog_id ) ) . '</a>';
			}
		}

		$crumbs[] = '<span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_title( $post_id ) ) ) . '</span>';
	} elseif ( is_archive() ) {
		$crumbs[] = '<span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	} elseif ( is_search() ) {
		$crumbs[] = '<span aria-current="page">' . esc_html__( 'Search results', 'mba-admission-guide' ) . '</span>';
	} elseif ( is_404() ) {
		$crumbs[] = '<span aria-current="page">' . esc_html__( 'Not found', 'mba-admission-guide' ) . '</span>';
	}

	echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'mba-admission-guide' ) . '">';
	echo wp_kses_post( implode( '<span class="crumbs__sep" aria-hidden="true">/</span>', $crumbs ) );
	echo '</nav>';
}

/**
 * Is an SEO plugin already handling meta tags?
 *
 * @return bool
 */
function mbag_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Output a meta description when no SEO plugin is doing it.
 *
 * Pages and posts use their own excerpt/content, so every new page you create
 * gets a sensible description without touching a template.
 */
function mbag_meta_description() {
	if ( mbag_seo_plugin_active() ) {
		return;
	}

	$description = '';

	if ( is_front_page() ) {
		// front-page.php ignores the assigned page's content, so its description
		// comes from the tagline / theme default instead of that page's text.
		$description = get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = wp_strip_all_tags( term_description() );
	}

	if ( '' === trim( (string) $description ) ) {
		$description = get_bloginfo( 'description' );
	}

	if ( '' === trim( (string) $description ) ) {
		$description = __( 'Compare Online MBA and Distance MBA programs, universities, specializations, eligibility and fees. Free admission guidance for working professionals and graduates.', 'mba-admission-guide' );
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr( wp_trim_words( $description, 30, '' ) )
	);
}
add_action( 'wp_head', 'mbag_meta_description', 1 );

/**
 * Body classes that let the CSS treat the landing page and inner pages
 * differently without any per-template overrides.
 *
 * @param array $classes Existing classes.
 * @return array
 */
function mbag_body_classes( $classes ) {
	$classes[] = is_front_page() ? 'is-landing' : 'is-inner';

	if ( is_page_template( 'page-templates/template-thank-you.php' ) ) {
		$classes[] = 'is-thankyou';
	}

	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'mbag_body_classes' );

/**
 * Excerpt length and ending, used by the post cards.
 */
function mbag_excerpt_length() {
	return 26;
}
add_filter( 'excerpt_length', 'mbag_excerpt_length' );

function mbag_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'mbag_excerpt_more' );

/**
 * Pingback header on singular views.
 */
function mbag_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s" />' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'mbag_pingback_header' );

/**
 * Remind the admin to set up the Thank You page once CF7 is wired up.
 */
function mbag_thankyou_admin_notice() {
	if ( ! current_user_can( 'edit_theme_options' ) || ! mbag_cf7_active() || mbag_thank_you_url() ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}

	printf(
		'<div class="notice notice-info is-dismissible"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'MBA Admission Guide:', 'mba-admission-guide' ),
		esc_html__( 'no Thank You page is set yet. Create a page with the "Thank You (Lead Confirmation)" template, then select it under Appearance › Customize › Lead Forms so every form submit redirects there.', 'mba-admission-guide' ),
		esc_url( admin_url( 'post-new.php?post_type=page' ) ),
		esc_html__( 'Create the page', 'mba-admission-guide' )
	);
}
add_action( 'admin_notices', 'mbag_thankyou_admin_notice' );

/**
 * ---------------------------------------------------------------
 * INLINE SVG ICONS
 *
 * The theme used to pull Font Awesome from cdnjs as three CSS
 * files. Lighthouse measured that at ~2,550ms of render-blocking
 * time: three round trips to a third-party origin, plus the two
 * webfonts (fa-solid-900 + fa-brands-400, ~260KB) those files then
 * request — all to draw 21 icons.
 *
 * Those 21 icons are ~8KB of path data, so they now ship inline in
 * the page. That is zero requests, zero third-party DNS/TLS, and
 * nothing render-blocking. The paths below are lifted verbatim from
 * Font Awesome Free 6.7.2 (CC BY 4.0), so the icons are the same
 * shapes the design was drawn with.
 *
 * Templates never hard-code an <svg> tag. They call mbag_icon(), so
 * swapping icon sets later is a change in one function, not in
 * fifty templates.
 * ---------------------------------------------------------------
 */

/**
 * The theme's icon vocabulary: one name -> [ viewBox, path ].
 *
 * Names are intent-based ('phone', 'secure') rather than shape-based, so a
 * later icon swap does not leave templates lying about what they show.
 *
 * @return array<string,array{0:string,1:string}>
 */
function mbag_icon_map() {
	return array(

		// Contact and conversion.
		'phone'        => array( '0 0 512 512', 'M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z' ),
		'whatsapp'     => array( '0 0 448 512', 'M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z' ),
		'chat'         => array( '0 0 640 512', 'M208 352c114.9 0 208-78.8 208-176S322.9 0 208 0S0 78.8 0 176c0 38.6 14.7 74.3 39.6 103.4c-3.5 9.4-8.7 17.7-14.2 24.7c-4.8 6.2-9.7 11-13.3 14.3c-1.8 1.6-3.3 2.9-4.3 3.7c-.5 .4-.9 .7-1.1 .8l-.2 .2s0 0 0 0s0 0 0 0C1 327.2-1.4 334.4 .8 340.9S9.1 352 16 352c21.8 0 43.8-5.6 62.1-12.5c9.2-3.5 17.8-7.4 25.2-11.4C134.1 343.3 169.8 352 208 352zM448 176c0 112.3-99.1 196.9-216.5 207C255.8 457.4 336.4 512 432 512c38.2 0 73.9-8.7 104.7-23.9c7.5 4 16 7.9 25.2 11.4c18.3 6.9 40.3 12.5 62.1 12.5c6.9 0 13.1-4.5 15.2-11.1c2.1-6.6-.2-13.8-5.8-17.9c0 0 0 0 0 0s0 0 0 0l-.2-.2c-.2-.2-.6-.4-1.1-.8c-1-.8-2.5-2-4.3-3.7c-3.6-3.3-8.5-8.1-13.3-14.3c-5.5-7-10.7-15.4-14.2-24.7c24.9-29 39.6-64.7 39.6-103.4c0-92.8-84.9-168.9-192.6-175.5c.4 5.1 .6 10.3 .6 15.5z' ),
		'secure'       => array( '0 0 448 512', 'M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z' ),
		'check'        => array( '0 0 448 512', 'M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z' ),
		'arrow-right'  => array( '0 0 512 512', 'M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z' ),
		'arrow-left'   => array( '0 0 512 512', 'M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 73.4-73.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-128 128z' ),
		'star'         => array( '0 0 576 512', 'M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z' ),
		'search'       => array( '0 0 512 512', 'M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z' ),
		'clock'        => array( '0 0 384 512', 'M32 0C14.3 0 0 14.3 0 32S14.3 64 32 64l0 11c0 42.4 16.9 83.1 46.9 113.1L146.7 256 78.9 323.9C48.9 353.9 32 394.6 32 437l0 11c-17.7 0-32 14.3-32 32s14.3 32 32 32l32 0 256 0 32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-11c0-42.4-16.9-83.1-46.9-113.1L237.3 256l67.9-67.9c30-30 46.9-70.7 46.9-113.1l0-11c17.7 0 32-14.3 32-32s-14.3-32-32-32L320 0 64 0 32 0zM96 75l0-11 192 0 0 11c0 19-5.6 37.4-16 53L112 128c-10.3-15.6-16-34-16-53zm16 309c3.5-5.3 7.6-10.3 12.1-14.9L192 301.3l67.9 67.9c4.6 4.6 8.6 9.6 12.1 14.9L112 384z' ),
		'home'         => array( '0 0 576 512', 'M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z' ),
		'download'     => array( '0 0 384 512', 'M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM216 232l0 102.1 31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31L168 232c0-13.3 10.7-24 24-24s24 10.7 24 24z' ),

		// Programme benefits.
		'anywhere'     => array( '0 0 640 512', 'M218.3 8.5c12.3-11.3 31.2-11.3 43.4 0l208 192c6.7 6.2 10.3 14.8 10.3 23.5l-144 0c-19.1 0-36.3 8.4-48 21.7l0-37.7c0-8.8-7.2-16-16-16l-64 0c-8.8 0-16 7.2-16 16l0 64c0 8.8 7.2 16 16 16l64 0 0 128-160 0c-26.5 0-48-21.5-48-48l0-112-32 0c-13.2 0-25-8.1-29.8-20.3s-1.6-26.2 8.1-35.2l208-192zM352 304l0 144 192 0 0-144-192 0zm-48-16c0-17.7 14.3-32 32-32l224 0c17.7 0 32 14.3 32 32l0 160 32 0c8.8 0 16 7.2 16 16c0 26.5-21.5 48-48 48l-48 0-192 0-48 0c-26.5 0-48-21.5-48-48c0-8.8 7.2-16 16-16l32 0 0-160z' ),
		'flexible'     => array( '0 0 512 512', 'M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z' ),
		'target'       => array( '0 0 512 512', 'M448 256A192 192 0 1 0 64 256a192 192 0 1 0 384 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 80a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm0-224a144 144 0 1 1 0 288 144 144 0 1 1 0-288zM224 256a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z' ),
		'chart'        => array( '0 0 512 512', 'M32 32c17.7 0 32 14.3 32 32l0 336c0 8.8 7.2 16 16 16l400 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L80 480c-44.2 0-80-35.8-80-80L0 64C0 46.3 14.3 32 32 32zM160 224c17.7 0 32 14.3 32 32l0 64c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32zm128-64l0 160c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-160c0-17.7 14.3-32 32-32s32 14.3 32 32zm64 32c17.7 0 32 14.3 32 32l0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96c0-17.7 14.3-32 32-32zM480 96l0 224c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-224c0-17.7 14.3-32 32-32s32 14.3 32 32z' ),
		'laptop'       => array( '0 0 640 512', 'M128 32C92.7 32 64 60.7 64 96l0 256 64 0 0-256 384 0 0 256 64 0 0-256c0-35.3-28.7-64-64-64L128 32zM19.2 384C8.6 384 0 392.6 0 403.2C0 445.6 34.4 480 76.8 480l486.4 0c42.4 0 76.8-34.4 76.8-76.8c0-10.6-8.6-19.2-19.2-19.2L19.2 384z' ),
		'rocket'       => array( '0 0 512 512', 'M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-88.5c13-21.9 36.5-35.3 61.9-35.3l82.3 0c2.4-4 4.8-7.7 7.2-11.3C289.1-4.1 411.1-8.1 483.9 5.3c11.6 2.1 20.6 11.2 22.8 22.8c13.4 72.9 9.3 194.8-111.4 276.7c-3.5 2.4-7.3 4.8-11.3 7.2l0 82.3c0 25.4-13.4 49-35.3 61.9l-88.5 52.5c-7.4 4.4-16.6 4.5-24.1 .2s-12.1-12.2-12.1-20.9l0-107.2c-14.1 4.9-26.4 8.9-35.7 11.9c-11.2 3.6-23.4 .5-31.8-7.8zM384 168a40 40 0 1 0 0-80 40 40 0 1 0 0 80z' ),
		'documents'    => array( '0 0 384 512', 'M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM112 256l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z' ),
		'graduate'     => array( '0 0 448 512', 'M219.3 .5c3.1-.6 6.3-.6 9.4 0l200 40C439.9 42.7 448 52.6 448 64s-8.1 21.3-19.3 23.5L352 102.9l0 57.1c0 70.7-57.3 128-128 128s-128-57.3-128-128l0-57.1L48 93.3l0 65.1 15.7 78.4c.9 4.7-.3 9.6-3.3 13.3s-7.6 5.9-12.4 5.9l-32 0c-4.8 0-9.3-2.1-12.4-5.9s-4.3-8.6-3.3-13.3L16 158.4l0-71.8C6.5 83.3 0 74.3 0 64C0 52.6 8.1 42.7 19.3 40.5l200-40zM111.9 327.7c10.5-3.4 21.8 .4 29.4 8.5l71 75.5c6.3 6.7 17 6.7 23.3 0l71-75.5c7.6-8.1 18.9-11.9 29.4-8.5C401 348.6 448 409.4 448 481.3c0 17-13.8 30.7-30.7 30.7L30.7 512C13.8 512 0 498.2 0 481.3c0-71.9 47-132.7 111.9-153.6z' ),
		'university'   => array( '0 0 512 512', 'M243.4 2.6l-224 96c-14 6-21.8 21-18.7 35.8S16.8 160 32 160l0 8c0 13.3 10.7 24 24 24l400 0c13.3 0 24-10.7 24-24l0-8c15.2 0 28.3-10.7 31.3-25.6s-4.8-29.9-18.7-35.8l-224-96c-8-3.4-17.2-3.4-25.2 0zM128 224l-64 0 0 196.3c-.6 .3-1.2 .7-1.8 1.1l-48 32c-11.7 7.8-17 22.4-12.9 35.9S17.9 512 32 512l448 0c14.1 0 26.5-9.2 30.6-22.7s-1.1-28.1-12.9-35.9l-48-32c-.6-.4-1.2-.7-1.8-1.1L448 224l-64 0 0 192-40 0 0-192-64 0 0 192-48 0 0-192-64 0 0 192-40 0 0-192zM256 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64z' ),
	);
}

/**
 * Build the markup for one icon.
 *
 * Icons here are decorative — every one sits next to a text label — so they
 * are hidden from screen readers. If you ever use an icon *as* the label,
 * pass $label so assistive tech has something to announce.
 *
 * The returned string is not run through wp_kses. Everything structural
 * (viewBox, path) comes from the hardcoded map above, and the two variable
 * parts are escaped here — so kses would add no safety, and it would lowercase
 * `viewBox` on the way past.
 *
 * @param string $name  Key from mbag_icon_map().
 * @param string $class Extra CSS classes.
 * @param string $label Accessible label. Empty = decorative.
 * @return string
 */
function mbag_get_icon( $name, $class = '', $label = '' ) {
	$map = mbag_icon_map();

	if ( ! isset( $map[ $name ] ) ) {
		return '';
	}

	list( $view, $path ) = $map[ $name ];

	$classes = trim( 'mbag-i ' . $class );

	// currentColor keeps the CSS in control of icon colour, exactly as the
	// font-based icons were. focusable="false" stops IE/Edge legacy from
	// putting decorative icons in the tab order.
	$attrs = sprintf(
		'class="%s" viewBox="%s" xmlns="http://www.w3.org/2000/svg" fill="currentColor" focusable="false"',
		esc_attr( $classes ),
		esc_attr( $view )
	);

	if ( '' !== $label ) {
		$attrs .= sprintf( ' role="img" aria-label="%s"', esc_attr( $label ) );
	} else {
		$attrs .= ' aria-hidden="true"';
	}

	return sprintf( '<svg %s><path d="%s"/></svg>', $attrs, esc_attr( $path ) );
}

/**
 * Echo an icon. See mbag_get_icon().
 *
 * @param string $name  Key from mbag_icon_map().
 * @param string $class Extra CSS classes.
 * @param string $label Accessible label. Empty = decorative.
 */
function mbag_icon( $name, $class = '', $label = '' ) {
	echo mbag_get_icon( $name, $class, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from a hardcoded map; variable parts escaped in mbag_get_icon().
}

/**
 * ---------------------------------------------------------------
 * EDITOR COLOR PALETTE
 *
 * Same tokens as style.css, so colours picked in the block editor
 * are the brand colours and stay correct if the palette is retuned.
 * ---------------------------------------------------------------
 */
function mbag_editor_palette() {
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => __( 'Night', 'mba-admission-guide' ),   'slug' => 'night',   'color' => '#0B1235' ),
		array( 'name' => __( 'Royal', 'mba-admission-guide' ),   'slug' => 'royal',   'color' => '#2A3B96' ),
		array( 'name' => __( 'Violet', 'mba-admission-guide' ),  'slug' => 'violet',  'color' => '#4B2E83' ),
		array( 'name' => __( 'Gold', 'mba-admission-guide' ),    'slug' => 'gold',    'color' => '#FFB020' ),
		array( 'name' => __( 'Sand', 'mba-admission-guide' ),    'slug' => 'sand',    'color' => '#FFF3DF' ),
		array( 'name' => __( 'Ink', 'mba-admission-guide' ),     'slug' => 'ink',     'color' => '#141B34' ),
		array( 'name' => __( 'Muted', 'mba-admission-guide' ),   'slug' => 'muted',   'color' => '#5C6A8A' ),
		array( 'name' => __( 'Surface', 'mba-admission-guide' ), 'slug' => 'surface', 'color' => '#F5F7FC' ),
		array( 'name' => __( 'White', 'mba-admission-guide' ),   'slug' => 'white',   'color' => '#FFFFFF' ),
	) );

	add_theme_support( 'editor-font-sizes', array(
		array( 'name' => __( 'Small', 'mba-admission-guide' ),  'slug' => 'small',  'size' => 14 ),
		array( 'name' => __( 'Normal', 'mba-admission-guide' ), 'slug' => 'normal', 'size' => 17 ),
		array( 'name' => __( 'Large', 'mba-admission-guide' ),  'slug' => 'large',  'size' => 21 ),
		array( 'name' => __( 'Heading', 'mba-admission-guide' ), 'slug' => 'heading', 'size' => 32 ),
	) );
}
add_action( 'after_setup_theme', 'mbag_editor_palette', 11 );

/**
 * ---------------------------------------------------------------
 * UNIVERSITY LOGOS
 *
 * The university cards and the logo marquee show a real logo image
 * when one is available, and fall back to the lettermark tile that
 * shipped with the theme when it is not. Nothing breaks half-set-up:
 * add logos one at a time and each card upgrades on its own.
 *
 * Drop files into either location, named after the slug below:
 *
 *   wp-content/uploads/university-logos/amity.png   <- preferred
 *   wp-content/themes/<theme>/images/universities/amity.png
 *
 * The uploads folder is checked first and is the one to use on a
 * live site: files inside the theme are deleted when the theme is
 * re-uploaded or updated.
 * ---------------------------------------------------------------
 */

/**
 * Slug => display name, used for the Customizer labels only.
 *
 * The card copy (tag line, bullets) still lives in the U array at the top of
 * js/main.js. Keep the names here in step with that array.
 *
 * @return array<string,string>
 */
function mbag_universities() {
	return apply_filters( 'mbag_universities', array(
		'amity'           => 'Amity University Online',
		'vit'             => 'VIT University',
		'sikkim-manipal'  => 'Sikkim Manipal University',
		'manipal-jaipur'  => 'Manipal University Jaipur',
		'nmims'           => 'NMIMS Online',
		'gla'             => 'GLA University Online',
		'dayananda-sagar' => 'Dayananda Sagar University Online',
		'jain'            => 'Jain Online University',
	) );
}

/**
 * Slugs for the universities listed in js/main.js.
 *
 * Keep these in sync with the `slug` field of the U array in main.js —
 * that is the key the front end looks the logo up by.
 *
 * @return string[]
 */
function mbag_university_slugs() {
	return apply_filters( 'mbag_university_slugs', array_keys( mbag_universities() ) );
}

/**
 * Where logo files are looked for, in priority order.
 *
 * @return array[] Each entry: array( 'dir' => absolute path, 'url' => base URL ).
 */
function mbag_university_logo_locations() {
	$uploads = wp_upload_dir();

	$locations = array();

	if ( empty( $uploads['error'] ) ) {
		$locations[] = array(
			'dir' => trailingslashit( $uploads['basedir'] ) . 'university-logos',
			'url' => trailingslashit( $uploads['baseurl'] ) . 'university-logos',
		);
	}

	$locations[] = array(
		'dir' => get_theme_file_path( 'images/universities' ),
		'url' => get_theme_file_uri( 'images/universities' ),
	);

	return apply_filters( 'mbag_university_logo_locations', $locations );
}

/**
 * The placeholder shown for a university with no logo file yet.
 *
 * Replace it per-university by dropping <slug>.<ext> into
 * wp-content/uploads/university-logos/ — the real file wins automatically.
 *
 * @return string Empty string when no placeholder file exists.
 */
function mbag_university_logo_placeholder() {
	foreach ( mbag_university_logo_locations() as $location ) {
		foreach ( array( 'svg', 'avif', 'webp', 'png', 'jpg', 'jpeg' ) as $ext ) {
			$path = trailingslashit( $location['dir'] ) . 'placeholder.' . $ext;

			if ( file_exists( $path ) ) {
				return add_query_arg(
					'v',
					(string) filemtime( $path ),
					trailingslashit( $location['url'] ) . 'placeholder.' . $ext
				);
			}
		}
	}

	return '';
}

/**
 * Resolve slug => logo URL for every logo actually present on disk.
 *
 * Each directory is read once with a single glob() rather than probing
 * slug x extension combinations, so adding universities or file types
 * does not multiply the filesystem calls.
 *
 * @return array<string,string>
 */
function mbag_university_logos() {
	$slugs = array_flip( mbag_university_slugs() );
	$logos = array();

	// 1. Anything picked in Appearance > Customize > University Logos wins.
	//    This is the no-FTP, no-redeploy way to swap a single logo.
	foreach ( array_keys( $slugs ) as $slug ) {
		$id = (int) get_theme_mod( 'mbag_uni_logo_' . $slug, 0 );

		if ( $id > 0 ) {
			$src = wp_get_attachment_image_src( $id, 'medium' );

			if ( $src ) {
				$logos[ $slug ] = $src[0];
			}
		}
	}

	foreach ( mbag_university_logo_locations() as $location ) {
		if ( ! is_dir( $location['dir'] ) ) {
			continue;
		}

		$files = glob( trailingslashit( $location['dir'] ) . '*.{svg,avif,webp,png,jpg,jpeg}', GLOB_BRACE );

		if ( ! $files ) {
			continue;
		}

		foreach ( $files as $file ) {
			$slug = pathinfo( $file, PATHINFO_FILENAME );

			// Only known universities, and never overwrite a higher-priority hit.
			// ('placeholder' is handled separately and is not a university.)
			if ( ! isset( $slugs[ $slug ] ) || isset( $logos[ $slug ] ) ) {
				continue;
			}

			// filemtime doubles as a cache-buster when a logo is replaced.
			$logos[ $slug ] = add_query_arg(
				'v',
				(string) filemtime( $file ),
				trailingslashit( $location['url'] ) . wp_basename( $file )
			);
		}
	}

	return $logos;
}

/**
 * ---------------------------------------------------------------
 * LEAD POPUP
 *
 * A single modal holding the popup-slot Contact Form 7 form. It
 * opens on three triggers, all configurable in the Customizer:
 *
 *   1. after N seconds        (default 15)
 *   2. at N% scroll depth     (default 50)
 *   3. any button or link marked data-mbag-popup
 *
 * The automatic triggers fire once per browser session and never on
 * the Thank You page — someone who has already converted should not
 * be asked again. Button clicks always open it.
 * ---------------------------------------------------------------
 */

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function mbag_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Clamp a Customizer integer to a sane range.
 *
 * @param mixed                $value   Raw value.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return int
 */
function mbag_sanitize_range( $value, $setting ) {
	$value = absint( $value );
	$input = $setting->manager->get_control( $setting->id );

	$min = isset( $input->input_attrs['min'] ) ? (int) $input->input_attrs['min'] : 0;
	$max = isset( $input->input_attrs['max'] ) ? (int) $input->input_attrs['max'] : 100;

	return max( $min, min( $max, $value ) );
}

/**
 * Popup configuration, resolved once and shared by PHP and JS.
 *
 * @return array
 */
function mbag_popup_settings() {
	return array(
		'enabled'  => (bool) get_theme_mod( 'mbag_popup_enable', true ),
		'delay'    => (int) get_theme_mod( 'mbag_popup_delay', 10 ),
		'scroll'   => (int) get_theme_mod( 'mbag_popup_scroll', 50 ),
		'title'    => (string) get_theme_mod( 'mbag_popup_title', __( 'Get Free MBA Counselling', 'mba-admission-guide' ) ),
		'subtitle' => (string) get_theme_mod( 'mbag_popup_subtitle', __( 'Share a few details and a counsellor will call you with the best-fit Online MBA options.', 'mba-admission-guide' ) ),
	);
}

/**
 * Should the popup render on this request?
 *
 * @return bool
 */
function mbag_popup_active() {
	$settings = mbag_popup_settings();

	if ( ! $settings['enabled'] || ! mbag_cf7_active() ) {
		return false;
	}

	// Nothing to show if the slot has no form assigned, or the form is gone.
	if ( ! mbag_cf7_form_exists( mbag_form_slot_id( 'popup' ) ) ) {
		return false;
	}

	return (bool) apply_filters( 'mbag_popup_active', true );
}

/**
 * Print the popup markup in the footer.
 *
 * The dialog ships in the page hidden rather than being injected on the
 * trigger: the CF7 form is then already in the DOM, so its scripts, nonce
 * and the theme's phone-field handling are all wired up before it opens.
 */
function mbag_render_popup() {
	if ( ! mbag_popup_active() ) {
		return;
	}

	$settings = mbag_popup_settings();

	// Render the form first — if CF7 returns nothing, print no dialog at all.
	ob_start();
	$rendered = mbag_lead_form( 'popup' );
	$form     = ob_get_clean();

	if ( ! $rendered || '' === trim( $form ) ) {
		return;
	}
	?>
	<div class="mbag-modal" id="mbagPopup" hidden>
		<div class="mbag-modal__overlay" data-mbag-close></div>
		<div class="mbag-modal__dialog card" role="dialog" aria-modal="true" aria-labelledby="mbagPopupTitle">
			<button type="button" class="mbag-modal__close" data-mbag-close>
				<span aria-hidden="true">&times;</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Close', 'mba-admission-guide' ); ?></span>
			</button>
			<div class="card__head">
				<h2 id="mbagPopupTitle"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php if ( '' !== $settings['subtitle'] ) : ?>
					<p><?php echo esc_html( $settings['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="card__body">
				<?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput -- CF7 shortcode output, already escaped by the plugin. ?>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'mbag_render_popup' );

/**
 * Customizer: popup behaviour. Sits inside the existing Lead Forms section.
 */
function mbag_customize_popup( $wp_customize ) {
	$wp_customize->add_setting( 'mbag_popup_enable', array(
		'default'           => true,
		'sanitize_callback' => 'mbag_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'mbag_popup_enable', array(
		'label'       => __( 'Enable the lead popup', 'mba-admission-guide' ),
		'description' => __( 'Shows the Popup form above. Opens once per visit after the delay or scroll depth below, and on any button marked data-mbag-popup.', 'mba-admission-guide' ),
		'section'     => 'mbag_forms',
		'type'        => 'checkbox',
	) );

	$wp_customize->add_setting( 'mbag_popup_delay', array(
		'default'           => 10,
		'sanitize_callback' => 'mbag_sanitize_range',
	) );
	$wp_customize->add_control( 'mbag_popup_delay', array(
		'label'       => __( 'Open after (seconds)', 'mba-admission-guide' ),
		'description' => __( '0 turns the timer off.', 'mba-admission-guide' ),
		'section'     => 'mbag_forms',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 0, 'max' => 300, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mbag_popup_scroll', array(
		'default'           => 50,
		'sanitize_callback' => 'mbag_sanitize_range',
	) );
	$wp_customize->add_control( 'mbag_popup_scroll', array(
		'label'       => __( 'Open at scroll depth (%)', 'mba-admission-guide' ),
		'description' => __( '0 turns the scroll trigger off. Whichever trigger fires first wins.', 'mba-admission-guide' ),
		'section'     => 'mbag_forms',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
	) );

	$wp_customize->add_setting( 'mbag_popup_title', array(
		'default'           => __( 'Get Free MBA Counselling', 'mba-admission-guide' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mbag_popup_title', array(
		'label'   => __( 'Popup heading', 'mba-admission-guide' ),
		'section' => 'mbag_forms',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'mbag_popup_subtitle', array(
		'default'           => __( 'Share a few details and a counsellor will call you with the best-fit Online MBA options.', 'mba-admission-guide' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mbag_popup_subtitle', array(
		'label'   => __( 'Popup sub-heading', 'mba-admission-guide' ),
		'section' => 'mbag_forms',
		'type'    => 'text',
	) );
}
add_action( 'customize_register', 'mbag_customize_popup' );

/**
 * Keep the lead popup off the Contact page.
 *
 * That page exists to hand the visitor to a person with no form in the way.
 * The popup otherwise renders in the footer of every page and opens itself on
 * a timer and at 50% scroll, so without this a form modal would appear ten
 * seconds after arriving on the one page built not to have one.
 *
 * Side effect worth knowing: mbag_brochure_button() also checks
 * mbag_popup_active(), so on this page the sticky bar and rail brochure
 * buttons degrade to a link to the homepage form instead of opening the
 * modal. That is the intended behaviour here, not a regression.
 *
 * @param bool $active Whether the popup should run.
 * @return bool
 */
function mbag_no_popup_on_contact( $active ) {
	if ( is_page_template( 'page-templates/template-contact.php' ) ) {
		return false;
	}

	return $active;
}
add_filter( 'mbag_popup_active', 'mbag_no_popup_on_contact' );

/**
 * Drop Contact Form 7's assets on the Contact page.
 *
 * CF7 enqueues its stylesheet and two scripts on every page whether or not a
 * form is present. On this template there is no form and no popup, so those
 * are three requests — one of them render-blocking — for markup that never
 * renders.
 *
 * Scoped to this one template on purpose. A site-wide "dequeue when no form
 * is detected" rule has to guess, and it guesses wrong on any page holding a
 * form in a widget, a shortcode or a builder block. Here we know.
 */
function mbag_dequeue_cf7_on_contact() {
	if ( ! is_page_template( 'page-templates/template-contact.php' ) ) {
		return;
	}

	foreach ( array( 'contact-form-7', 'swv' ) as $handle ) {
		wp_dequeue_script( $handle );
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'mbag_dequeue_cf7_on_contact', 100 );

/**
 * ---------------------------------------------------------------
 * RIGHT CTA RAIL
 *
 * Two vertical buttons pinned to the right edge:
 *
 *   Enquire Now       -> tel: link, the number from Contact Details
 *   Download Brochure -> opens the lead popup, because the brochure is
 *                        the thing being traded for the contact details
 *
 * The brochure button carries its own popup heading, so the modal
 * matches what was clicked instead of asking for "counselling" after
 * the visitor asked for a brochure.
 * ---------------------------------------------------------------
 */

/**
 * The "Download Brochure" control, used by both the left rail and the
 * mobile bottom bar.
 *
 * Rendered as a <button> when the popup is available, because it performs an
 * action rather than navigating. With no popup (Contact Form 7 inactive, or
 * no form assigned to the slot) it degrades to a link to the on-page form
 * instead of becoming a dead control.
 *
 * @param string $class Classes for the element.
 * @param string $label Visible label.
 * @param string $icon  Icon key from mbag_icon_map().
 */
function mbag_brochure_button( $class, $label, $icon = 'download' ) {
	$icon_html = mbag_get_icon( $icon );

	if ( mbag_popup_active() ) {
		printf(
			'<button type="button" class="%1$s" data-mbag-popup data-mbag-popup-title="%2$s" data-mbag-popup-sub="%3$s" data-mbag-source="%4$s">%5$s<span>%6$s</span></button>',
			esc_attr( $class ),
			esc_attr__( 'Download the Brochure', 'mba-admission-guide' ),
			esc_attr__( 'Enter your details and we will send the programme brochure and fee structure.', 'mba-admission-guide' ),
			esc_attr__( 'Brochure download', 'mba-admission-guide' ),
			$icon_html, // Built by mbag_get_icon(), already escaped.
			esc_html( $label )
		);
		return;
	}

	printf(
		'<a class="%1$s" href="%2$s">%3$s<span>%4$s</span></a>',
		esc_attr( $class ),
		esc_url( mbag_anchor( 'apply' ) ),
		$icon_html,
		esc_html( $label )
	);
}

/**
 * Print the right CTA rail.
 */
function mbag_render_cta_rail() {
	if ( ! get_theme_mod( 'mbag_rail_enable', true ) ) {
		return;
	}

	?>
	<div class="rail">
		<a class="rail__btn rail__btn--call" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>">
			<?php mbag_icon( 'phone' ); ?>
			<span><?php esc_html_e( 'Enquire Now', 'mba-admission-guide' ); ?></span>
		</a>

		<?php mbag_brochure_button( 'rail__btn rail__btn--brochure', __( 'Download Brochure', 'mba-admission-guide' ) ); ?>
	</div>
	<?php
}
add_action( 'wp_footer', 'mbag_render_cta_rail', 5 );

/**
 * Customizer: switch the rail off without editing a template.
 */
function mbag_customize_rail( $wp_customize ) {
	$wp_customize->add_setting( 'mbag_rail_enable', array(
		'default'           => true,
		'sanitize_callback' => 'mbag_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'mbag_rail_enable', array(
		'label'       => __( 'Show the CTA rail', 'mba-admission-guide' ),
		'description' => __( 'The vertical "Enquire Now" and "Download Brochure" buttons pinned to the right edge. Hidden on phones, where the bottom bar carries the same CTAs.', 'mba-admission-guide' ),
		'section'     => 'mbag_contact',
		'type'        => 'checkbox',
	) );
}
add_action( 'customize_register', 'mbag_customize_rail' );

/**
 * ---------------------------------------------------------------
 * SITE LOGO
 *
 * Three levels, in order:
 *
 *   1. a logo set in Customize > Site Identity  (wins — WordPress
 *      generates its own responsive sizes for it)
 *   2. images/logo.webp shipped with the theme
 *   3. the "M" lettermark, so the brand is never blank
 *
 * Header and footer both call mbag_brand(), so the fallback chain
 * lives in one place instead of being repeated per template.
 * ---------------------------------------------------------------
 */

/**
 * Find a bundled logo file, whatever extension it was supplied in.
 *
 * @param string $variant '' for the standard logo, 'light' for the
 *                        light-on-dark version.
 * @return array{url:string,width:int,height:int}|null
 */
function mbag_logo_file( $variant = '' ) {
	$base = 'images/logo' . ( '' !== $variant ? '-' . $variant : '' );

	// SVG first: it is resolution-independent, so it wins when present.
	foreach ( array( 'svg', 'webp', 'png', 'jpg', 'jpeg' ) as $ext ) {
		$path = get_theme_file_path( $base . '.' . $ext );

		if ( ! file_exists( $path ) ) {
			continue;
		}

		$size = 'svg' === $ext ? false : @getimagesize( $path );

		return array(
			// filemtime doubles as a cache-buster when the logo is replaced.
			'url'    => add_query_arg( 'v', (string) filemtime( $path ), get_theme_file_uri( $base . '.' . $ext ) ),
			'width'  => $size ? (int) $size[0] : 244,
			'height' => $size ? (int) $size[1] : 112,
		);
	}

	return null;
}

/**
 * The bundled logo for a given context, falling back to the standard file.
 *
 * @param string $variant '' or 'light'.
 * @return array{url:string,width:int,height:int}|null
 */
function mbag_bundled_logo( $variant = '' ) {
	if ( '' !== $variant ) {
		$logo = mbag_logo_file( $variant );

		if ( $logo ) {
			return $logo;
		}
	}

	return mbag_logo_file( '' );
}

/**
 * Print the brand block (logo or lettermark) used in the header and footer.
 *
 * @param string $context 'header' or 'footer'. Only affects the CSS hook.
 */
function mbag_brand( $context = 'header' ) {
	// 1. A Customizer logo wins, and brings its own srcset.
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	/* Both the header and the footer are dark navy bars, and the supplied
	   logo has a dark navy wordmark that all but disappears on them. The
	   'light' variant carries the same mark with a white wordmark. */
	$logo = mbag_bundled_logo( 'light' );

	// Trimmed: a stray space in the Site Title otherwise lands in the alt text.
	$name = trim( (string) get_bloginfo( 'name' ) );

	// 2. The bundled file.
	if ( $logo ) {
		printf(
			'<a href="%1$s" class="brand brand--img brand--%2$s" rel="home"><img src="%3$s" width="%4$d" height="%5$d" alt="%6$s" class="brand__logo" %7$s /></a>',
			esc_url( home_url( '/' ) ),
			esc_attr( $context ),
			esc_url( $logo['url'] ),
			(int) $logo['width'],
			(int) $logo['height'],
			// The logo links home, which the link context already conveys; the
			// alt only needs to name the brand.
			esc_attr( '' !== $name ? $name : __( 'Site logo', 'mba-admission-guide' ) ),
			// The header logo is above the fold; the footer one is not.
			'header' === $context ? 'fetchpriority="high"' : 'loading="lazy" decoding="async"'
		);
		return;
	}

	// 3. Lettermark.
	printf(
		'<a href="%1$s" class="brand brand--%2$s" rel="home"><span class="brand__mark" aria-hidden="true">M</span><span><b>%3$s</b><small>%4$s</small></span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $context ),
		esc_html( $name ),
		esc_html__( 'Online MBA 2026', 'mba-admission-guide' )
	);
}

/**
 * Customizer: pick a logo for any university straight from the Media Library.
 *
 * This is the path that needs no FTP and no redeploy — the setting overrides
 * both the uploads folder and the file bundled with the theme.
 */
function mbag_customize_university_logos( $wp_customize ) {
	$wp_customize->add_section( 'mbag_uni_logos', array(
		'title'       => __( 'University Logos', 'mba-admission-guide' ),
		'priority'    => 32,
		'description' => __( 'Replace any university logo without touching files. Leave one empty to use the logo shipped with the theme, or the placeholder if there is none. Wide logos (about 2:1) fit the card best.', 'mba-admission-guide' ),
	) );

	foreach ( mbag_universities() as $slug => $name ) {
		$setting = 'mbag_uni_logo_' . $slug;

		$wp_customize->add_setting( $setting, array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control(
			new WP_Customize_Media_Control( $wp_customize, $setting, array(
				'label'     => $name,
				'section'   => 'mbag_uni_logos',
				'mime_type' => 'image',
			) )
		);
	}
}
add_action( 'customize_register', 'mbag_customize_university_logos' );

/**
 * ---------------------------------------------------------------
 * MANIPAL UNIVERSITY JAIPUR LANDING PAGE
 *
 * Data for page-templates/template-manipal-jaipur.php.
 *
 * Every field here is EMPTY BY DEFAULT and every section that reads
 * one hides itself when it has nothing. That is deliberate: fees,
 * approvals and placement figures are claims this site would be
 * making in its own name, on a page paid traffic lands on. An empty
 * section is a gap; a wrong fee is a complaint. Fill them in from
 * the university's own material, not from a competitor's page.
 * ---------------------------------------------------------------
 */

/**
 * One Manipal landing field.
 *
 * @param string $key Field key without the mbag_muj_ prefix.
 * @return string Trimmed value, '' when unset.
 */
function mbag_muj_field( $key ) {
	return trim( (string) get_theme_mod( 'mbag_muj_' . $key, '' ) );
}

/**
 * A comma-separated field, as a clean array.
 *
 * @param string $key Field key without the mbag_muj_ prefix.
 * @return string[]
 */
function mbag_muj_list( $key ) {
	$raw = mbag_muj_field( $key );

	if ( '' === $raw ) {
		return array();
	}

	return array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
}

/**
 * The fields shown in the Customizer, in order.
 *
 * @return array<string,array{label:string,help:string,list:bool}>
 */
function mbag_muj_fields() {
	return array(
		'duration'        => array(
			'label' => __( 'Course duration', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 24 months (4 semesters)', 'mba-admission-guide' ),
			'list'  => false,
		),
		'mode'            => array(
			'label' => __( 'Study mode', 'mba-admission-guide' ),
			'help'  => __( 'e.g. Online / Distance', 'mba-admission-guide' ),
			'list'  => false,
		),
		'eligibility'     => array(
			'label' => __( 'Eligibility', 'mba-admission-guide' ),
			'help'  => __( 'One line. e.g. Bachelor\'s degree from a recognised university.', 'mba-admission-guide' ),
			'list'  => false,
		),
		'fee'             => array(
			'label' => __( 'Indicative total fee', 'mba-admission-guide' ),
			'help'  => __( 'Leave empty unless you have confirmed it. The fee block stays hidden while this is blank.', 'mba-admission-guide' ),
			'list'  => false,
		),
		'emi'             => array(
			'label' => __( 'EMI / payment note', 'mba-admission-guide' ),
			'help'  => __( 'e.g. EMI options available. Shown under the fee.', 'mba-admission-guide' ),
			'list'  => false,
		),
		'approvals'       => array(
			'label' => __( 'Approvals & accreditation', 'mba-admission-guide' ),
			'help'  => __( 'Comma separated, e.g. UGC-entitled, NAAC A+, AICTE. Only add what you can evidence.', 'mba-admission-guide' ),
			'list'  => true,
		),
		'specializations' => array(
			'label' => __( 'Specializations', 'mba-admission-guide' ),
			'help'  => __( 'Comma separated. Falls back to the theme-wide specialization list when empty.', 'mba-admission-guide' ),
			'list'  => true,
		),
	);
}

/**
 * Customizer panel for the Manipal landing page.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function mbag_customize_muj( $wp_customize ) {
	$wp_customize->add_section( 'mbag_muj', array(
		'title'       => __( 'Manipal Jaipur Landing', 'mba-admission-guide' ),
		'priority'    => 38,
		'description' => __( 'Fills the Manipal University Jaipur landing page. Anything left blank is left off the page rather than guessed at.', 'mba-admission-guide' ),
	) );

	foreach ( mbag_muj_fields() as $key => $field ) {
		$wp_customize->add_setting( 'mbag_muj_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( 'mbag_muj_' . $key, array(
			'label'       => $field['label'],
			'description' => $field['help'],
			'section'     => 'mbag_muj',
			'type'        => 'text',
		) );
	}
}
add_action( 'customize_register', 'mbag_customize_muj' );

/**
 * Specializations for the Manipal page.
 *
 * Uses the page's own list when set, otherwise the theme-wide list that the
 * front page already shows, so the section is never empty for want of copy.
 *
 * @return string[]
 */
function mbag_muj_specializations() {
	$own = mbag_muj_list( 'specializations' );

	if ( $own ) {
		return $own;
	}

	return array(
		__( 'Marketing Management', 'mba-admission-guide' ),
		__( 'Finance', 'mba-admission-guide' ),
		__( 'Human Resource Management', 'mba-admission-guide' ),
		__( 'Business Analytics', 'mba-admission-guide' ),
		__( 'Data Science', 'mba-admission-guide' ),
		__( 'Operations Management', 'mba-admission-guide' ),
		__( 'Information Technology', 'mba-admission-guide' ),
	);
}

/**
 * Admin nudge: the template is in use but no data has been entered yet.
 */
function mbag_muj_admin_notice() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'page' !== $screen->id ) {
		return;
	}

	global $post;

	if ( ! $post || 'page-templates/template-manipal-jaipur.php' !== get_page_template_slug( $post ) ) {
		return;
	}

	$filled = 0;

	foreach ( array_keys( mbag_muj_fields() ) as $key ) {
		if ( '' !== mbag_muj_field( $key ) ) {
			++$filled;
		}
	}

	if ( $filled ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'This page uses the Manipal Jaipur template, but none of its details are filled in yet — the fee, eligibility and approvals sections are hidden until you add them.', 'mba-admission-guide' ),
		esc_url( admin_url( 'customize.php?autofocus[section]=mbag_muj' ) ),
		esc_html__( 'Open Customizer → Manipal Jaipur Landing', 'mba-admission-guide' )
	);
}
add_action( 'admin_notices', 'mbag_muj_admin_notice' );

/**
 * ---------------------------------------------------------------
 * SIKKIM MANIPAL LANDING
 *
 * Data for page-templates/template-sikkim-manipal.php.
 *
 * Same rule as the Manipal Jaipur block above: every field is EMPTY
 * BY DEFAULT and every block that reads one hides itself when it has
 * nothing. The design this page is ported from shipped hard-coded
 * fees, placement counts and accreditation badges. Those are claims
 * this site would be making in its own name on a page paid traffic
 * lands on, so none of them are baked into the template — fill them
 * in from the university's own material.
 * ---------------------------------------------------------------
 */

/**
 * One Sikkim Manipal landing field.
 *
 * @param string $key Field key without the mbag_smu_ prefix.
 * @return string Trimmed value, '' when unset.
 */
function mbag_smu_field( $key ) {
	return trim( (string) get_theme_mod( 'mbag_smu_' . $key, '' ) );
}

/**
 * A comma-separated field, as a clean array.
 *
 * @param string $key Field key without the mbag_smu_ prefix.
 * @return string[]
 */
function mbag_smu_list( $key ) {
	$raw = mbag_smu_field( $key );

	if ( '' === $raw ) {
		return array();
	}

	return array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
}

/**
 * Split a stat into an animatable number and its suffix.
 *
 * "35K+" becomes 35 / "K+", "100%" becomes 100 / "%". The counter in
 * sikkim-manipal.js animates the number and the suffix is printed as-is,
 * which is what lets the Customizer hold a single human-readable string
 * instead of asking for the two halves separately.
 *
 * @param string $value Raw field value.
 * @return array{num:string,suffix:string,plain:string} Empty num = print plain.
 */
function mbag_smu_stat_parts( $value ) {
	$value = trim( $value );

	if ( '' === $value ) {
		return array(
			'num'    => '',
			'suffix' => '',
			'plain'  => '',
		);
	}

	if ( preg_match( '/^([0-9][0-9,]*)\s*(.*)$/', $value, $matches ) ) {
		return array(
			'num'    => str_replace( ',', '', $matches[1] ),
			'suffix' => trim( $matches[2] ),
			'plain'  => $value,
		);
	}

	// No leading number — print the string and skip the animation.
	return array(
		'num'    => '',
		'suffix' => '',
		'plain'  => $value,
	);
}

/**
 * The fields shown in the Customizer, in order.
 *
 * @return array<string,array{label:string,help:string}>
 */
function mbag_smu_fields() {
	return array(
		'duration'         => array(
			'label' => __( 'Course duration', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 2 Years (4 semesters)', 'mba-admission-guide' ),
		),
		'mode'             => array(
			'label' => __( 'Study mode', 'mba-admission-guide' ),
			'help'  => __( 'e.g. Online / Distance', 'mba-admission-guide' ),
		),
		'eligibility'      => array(
			'label' => __( 'Eligibility', 'mba-admission-guide' ),
			'help'  => __( 'One line. e.g. Bachelor\'s degree from a recognised university.', 'mba-admission-guide' ),
		),
		'fee_total'        => array(
			'label' => __( 'Total programme fee', 'mba-admission-guide' ),
			'help'  => __( 'e.g. INR 1,20,000. The whole fee block stays hidden while all three fee fields are blank.', 'mba-admission-guide' ),
		),
		'fee_semester'     => array(
			'label' => __( 'Fee per semester', 'mba-admission-guide' ),
			'help'  => __( 'e.g. INR 30,000', 'mba-admission-guide' ),
		),
		'fee_emi'          => array(
			'label' => __( 'EMI starting from', 'mba-admission-guide' ),
			'help'  => __( 'e.g. INR 5,000/mo', 'mba-admission-guide' ),
		),
		'approvals'        => array(
			'label' => __( 'Approvals & accreditation', 'mba-admission-guide' ),
			'help'  => __( 'Comma separated, e.g. NAAC A+, UGC entitled, AICTE. Drives the strip under the hero. Only add what you can evidence.', 'mba-admission-guide' ),
		),
		'specializations'  => array(
			'label' => __( 'Specializations', 'mba-admission-guide' ),
			'help'  => __( 'Comma separated. Falls back to the theme-wide specialization list when empty.', 'mba-admission-guide' ),
		),
		'stat_years'       => array(
			'label' => __( 'Stat — years of education', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 30+. Shown in the Advantages column.', 'mba-admission-guide' ),
		),
		'stat_faculty'     => array(
			'label' => __( 'Stat — faculty & staff', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 530+', 'mba-admission-guide' ),
		),
		'stat_learners'    => array(
			'label' => __( 'Stat — learners offered placement assistance', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 35K+. Shown in the Global Impact band.', 'mba-admission-guide' ),
		),
		'stat_opportunities' => array(
			'label' => __( 'Stat — opportunities created', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 25K+', 'mba-admission-guide' ),
		),
		'stat_partners'    => array(
			'label' => __( 'Stat — hiring partners', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 500+', 'mba-admission-guide' ),
		),
		'stat_placement'   => array(
			'label' => __( 'Stat — placement assistance', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 100%', 'mba-admission-guide' ),
		),
		'legacy'           => array(
			'label' => __( 'Legacy badge', 'mba-admission-guide' ),
			'help'  => __( 'e.g. 70+ Years. Sits on the degree image. Hidden when blank.', 'mba-admission-guide' ),
		),
		'form_id'          => array(
			'label' => __( 'Landing page Contact Form 7 ID', 'mba-admission-guide' ),
			'help'  => __( 'Used for the hero form and the popup on this page only, e.g. 31b7fbb. Leave blank to use the site-wide forms from Customize > Lead Forms.', 'mba-admission-guide' ),
		),
		'phone'            => array(
			'label' => __( 'Landing page phone number', 'mba-admission-guide' ),
			'help'  => __( 'Overrides the site-wide number on this page only — header, footer, closing CTA, sticky bar and WhatsApp. Leave blank to use Contact Details. e.g. +91 96067 02758', 'mba-admission-guide' ),
		),
		'whatsapp'         => array(
			'label' => __( 'Landing page WhatsApp number', 'mba-admission-guide' ),
			'help'  => __( 'Only if WhatsApp differs from the phone number above. Digits with country code, e.g. 919606702758.', 'mba-admission-guide' ),
		),
		'hiring_partners'  => array(
			'label' => __( 'Hiring partner names', 'mba-admission-guide' ),
			'help'  => __( 'Comma separated. The recruiter grid is hidden entirely while this is blank, rather than showing empty LOGO tiles.', 'mba-admission-guide' ),
		),
	);
}

/**
 * Customizer section for the Sikkim Manipal landing page.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function mbag_customize_smu( $wp_customize ) {
	$wp_customize->add_section( 'mbag_smu', array(
		'title'       => __( 'Sikkim Manipal Landing', 'mba-admission-guide' ),
		'priority'    => 39,
		'description' => __( 'Fills the Sikkim Manipal University landing page. Anything left blank is left off the page rather than guessed at.', 'mba-admission-guide' ),
	) );

	foreach ( mbag_smu_fields() as $key => $field ) {
		$wp_customize->add_setting( 'mbag_smu_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( 'mbag_smu_' . $key, array(
			'label'       => $field['label'],
			'description' => $field['help'],
			'section'     => 'mbag_smu',
			'type'        => 'text',
		) );
	}
}
add_action( 'customize_register', 'mbag_customize_smu' );

/**
 * Specializations for the Sikkim Manipal page, with a line of copy each.
 *
 * The names are Customizer-driven; the descriptions come from the map below
 * when the name is one we have copy for, and fall back to a line that points
 * at a counsellor rather than inventing a curriculum claim.
 *
 * @return array[] Each entry: array( 'name' => string, 'desc' => string ).
 */
function mbag_smu_specializations() {
	$names = mbag_smu_list( 'specializations' );

	if ( ! $names ) {
		$names = array(
			__( 'Finance', 'mba-admission-guide' ),
			__( 'Human Resource Management', 'mba-admission-guide' ),
			__( 'Systems', 'mba-admission-guide' ),
			__( 'Operations & Supply Chain', 'mba-admission-guide' ),
			__( 'Marketing', 'mba-admission-guide' ),
			__( 'Healthcare', 'mba-admission-guide' ),
		);
	}

	$copy = array(
		'finance'                  => __( 'Corporate finance, reporting, investments and risk — for FP&A, treasury, banking and controllership tracks.', 'mba-admission-guide' ),
		'human resource management' => __( 'Talent acquisition, performance systems, compensation and employment law — for HRBP and people-ops roles.', 'mba-admission-guide' ),
		'hrm'                      => __( 'Talent acquisition, performance systems, compensation and employment law — for HRBP and people-ops roles.', 'mba-admission-guide' ),
		'systems'                  => __( 'Information systems, data-driven decisions and technology management — for product, IT and business-analyst roles.', 'mba-admission-guide' ),
		'operations & supply chain' => __( 'Process design, logistics, procurement and quality — for manufacturing, e-commerce and supply-chain roles.', 'mba-admission-guide' ),
		'operations management'    => __( 'Process design, logistics, procurement and quality — for manufacturing, e-commerce and supply-chain roles.', 'mba-admission-guide' ),
		'marketing'                => __( 'Brand strategy, consumer behaviour, digital channels and sales management — for growth, brand and category roles.', 'mba-admission-guide' ),
		'marketing management'     => __( 'Brand strategy, consumer behaviour, digital channels and sales management — for growth, brand and category roles.', 'mba-admission-guide' ),
		'healthcare'               => __( 'Health services, hospital operations and healthcare policy — for administration roles across providers and insurers.', 'mba-admission-guide' ),
		'business analytics'       => __( 'Data modelling, visualisation and decision science applied to business problems.', 'mba-admission-guide' ),
		'data science'             => __( 'Statistics, machine learning and analytics tooling aimed at management roles.', 'mba-admission-guide' ),
		'information technology'   => __( 'IT strategy, systems and delivery management for technology-led organisations.', 'mba-admission-guide' ),
	);

	$out = array();

	foreach ( $names as $name ) {
		$key = strtolower( $name );

		$out[] = array(
			'name' => $name,
			'desc' => isset( $copy[ $key ] )
				? $copy[ $key ]
				: __( 'Ask a counsellor how this specialization maps to the roles you are aiming at next.', 'mba-admission-guide' ),
		);
	}

	return $out;
}

/**
 * Is the current request the Sikkim Manipal landing page?
 *
 * @return bool
 */
function mbag_smu_is_template() {
	return is_page_template( 'page-templates/template-sikkim-manipal.php' );
}

/**
 * Load this page's stylesheet, font and script — and only on this page.
 *
 * Runs at priority 20 so mbag_scripts() has already registered mbag-style;
 * the dependency then guarantees the override lands after the theme CSS
 * rather than racing it. Both defer marking and small-CSS inlining hook at
 * PHP_INT_MAX, so these get the same treatment as the theme's own assets.
 */
function mbag_smu_assets() {
	if ( ! mbag_smu_is_template() ) {
		return;
	}

	// The ported design is set in Poppins; the theme itself ships Manrope.
	wp_enqueue_style(
		'mbag-smu-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	/* Versioned by file mtime rather than MBAG_VERSION. These two files are
	   edited far more often than the theme version is bumped, and a changed
	   file behind an unchanged ?ver= is served stale from browser caches. */
	$css = get_theme_file_path( 'css/sikkim-manipal.css' );
	$js  = get_theme_file_path( 'js/sikkim-manipal.js' );

	wp_enqueue_style(
		'mbag-smu',
		get_template_directory_uri() . '/css/sikkim-manipal.css',
		array( 'mbag-style' ),
		file_exists( $css ) ? (string) filemtime( $css ) : MBAG_VERSION
	);

	wp_enqueue_script(
		'mbag-smu',
		get_template_directory_uri() . '/js/sikkim-manipal.js',
		array(),
		file_exists( $js ) ? (string) filemtime( $js ) : MBAG_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'mbag_smu_assets', 20 );

/**
 * Admin nudge: the template is in use but no data has been entered yet.
 */
function mbag_smu_admin_notice() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'page' !== $screen->id ) {
		return;
	}

	global $post;

	if ( ! $post || 'page-templates/template-sikkim-manipal.php' !== get_page_template_slug( $post ) ) {
		return;
	}

	$filled = 0;

	foreach ( array_keys( mbag_smu_fields() ) as $key ) {
		if ( '' !== mbag_smu_field( $key ) ) {
			++$filled;
		}
	}

	if ( $filled ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'This page uses the Sikkim Manipal template, but none of its details are filled in yet — the accreditation strip, fee block, statistics and recruiter grid stay hidden until you add them.', 'mba-admission-guide' ),
		esc_url( admin_url( 'customize.php?autofocus[section]=mbag_smu' ) ),
		esc_html__( 'Open Customizer → Sikkim Manipal Landing', 'mba-admission-guide' )
	);
}
add_action( 'admin_notices', 'mbag_smu_admin_notice' );

/**
 * ---------------------------------------------------------------
 * COURSE CAROUSEL
 *
 * Drives the "Explore Our Top Online Degree Courses" carousel.
 *
 * Edited here rather than in the Customizer for the same reason the
 * university cards are edited in the `U` array in js/main.js: one
 * course needs ten fields, and ten Customizer text boxes per course
 * is worse to maintain than one array. See UNIVERSITIES.md.
 *
 * 'slug' must match a key from mbag_universities() — it is what the
 * logo lookup and the lead popup's university field key off. Any
 * field left as '' is simply not printed on the card, so a course
 * with no confirmed fee shows the rest of its row and omits that line.
 *
 * Filterable, so a child theme or a plugin can replace the list
 * without touching this file.
 *
 * @return array[]
 */
function mbag_courses() {
	return apply_filters( 'mbag_courses', array(
		array(
			'title'       => __( 'Master of Business Administration', 'mba-admission-guide' ),
			'university'  => 'Manipal University Jaipur',
			'slug'        => 'manipal-jaipur',
			'badge'       => __( 'Most Popular', 'mba-admission-guide' ),
			'tag'         => __( 'Super/Dual Specialization', 'mba-admission-guide' ),
			'rating'      => '4.9',
			'duration'    => __( '24 months', 'mba-admission-guide' ),
			'fee'         => 'INR 1,80,000',
			'eligibility' => __( 'Min 50% in graduation', 'mba-admission-guide' ),
			'scholarship' => __( 'Up to 20% Scholarship', 'mba-admission-guide' ),
			'image'       => 'course-mba-muj.webp',
		),
		array(
			'title'       => __( 'Master of Business Administration', 'mba-admission-guide' ),
			'university'  => 'Sikkim Manipal University',
			'slug'        => 'sikkim-manipal',
			'badge'       => __( 'Trending', 'mba-admission-guide' ),
			'tag'         => __( 'Dual Specialization', 'mba-admission-guide' ),
			'rating'      => '4.5',
			'duration'    => __( '24 months', 'mba-admission-guide' ),
			'fee'         => 'INR 1,20,000',
			'eligibility' => __( 'Min 50% in graduation', 'mba-admission-guide' ),
			'scholarship' => __( 'Up to 30% Scholarship', 'mba-admission-guide' ),
			'image'       => 'course-mba-smu.webp',
		),
		array(
			'title'       => __( 'Master of Computer Applications', 'mba-admission-guide' ),
			'university'  => 'Manipal University Jaipur',
			'slug'        => 'manipal-jaipur',
			'badge'       => __( 'Most Popular', 'mba-admission-guide' ),
			'tag'         => __( 'In-Demand Specializations', 'mba-admission-guide' ),
			'rating'      => '4.8',
			'duration'    => __( '24 months', 'mba-admission-guide' ),
			'fee'         => 'INR 1,58,000',
			'eligibility' => __( 'Min 50% in graduation', 'mba-admission-guide' ),
			'scholarship' => __( 'Up to 20% Scholarship', 'mba-admission-guide' ),
			'image'       => 'course-mca-muj.webp',
		),
		array(
			'title'       => __( 'Master of Computer Applications', 'mba-admission-guide' ),
			'university'  => 'Sikkim Manipal University',
			'slug'        => 'sikkim-manipal',
			'badge'       => __( 'Trending', 'mba-admission-guide' ),
			'tag'         => __( 'Flexible Schedule', 'mba-admission-guide' ),
			'rating'      => '4.5',
			'duration'    => __( '24 months', 'mba-admission-guide' ),
			'fee'         => 'INR 1,44,000',
			'eligibility' => __( 'Min 50% in graduation', 'mba-admission-guide' ),
			'scholarship' => __( 'Up to 30% Scholarship', 'mba-admission-guide' ),
			'image'       => 'course-mca-smu.webp',
		),
		array(
			'title'       => __( 'Master of Arts', 'mba-admission-guide' ),
			'university'  => 'Sikkim Manipal University',
			'slug'        => 'sikkim-manipal',
			'badge'       => __( 'Trending', 'mba-admission-guide' ),
			'tag'         => __( 'Multiple Specializations', 'mba-admission-guide' ),
			'rating'      => '4.4',
			'duration'    => __( '24 months', 'mba-admission-guide' ),
			'fee'         => 'INR 72,000',
			'eligibility' => __( 'Min 50% in graduation', 'mba-admission-guide' ),
			'scholarship' => __( 'Up to 30% Scholarship', 'mba-admission-guide' ),
			'image'       => 'course-ma-smu.webp',
		),
	) );
}


/**
 * URL for a file in images/smu/, or '' when it is not there.
 *
 * filemtime doubles as a cache-buster, the same trick mbag_university_logos()
 * uses — replace the file and the new one is served immediately.
 *
 * Returning '' for a missing file is what lets every caller wrap its <img>
 * in a truthiness check, so a deleted asset leaves a tidy gap instead of a
 * broken image icon.
 *
 * @param string $file File name inside images/smu/.
 * @return string
 */
function mbag_smu_img( $file ) {
	$file = ltrim( (string) $file, '/' );

	if ( '' === $file ) {
		return '';
	}

	$path = get_theme_file_path( 'images/smu/' . $file );

	if ( ! file_exists( $path ) ) {
		return '';
	}

	return add_query_arg(
		'v',
		(string) filemtime( $path ),
		get_theme_file_uri( 'images/smu/' . $file )
	);
}

/**
 * The landing header's navigation.
 *
 * Uses the menu assigned to the "Landing Page Menu" location when there is
 * one, so the links stay editable from Appearance > Menus. With none
 * assigned it falls back to this page's own section anchors, which is what
 * a landing page actually wants — same pattern as mbag_fallback_menu().
 *
 * @param string $class UL class.
 */
function mbag_smu_nav( $class = 'smu-nav__list' ) {
	if ( has_nav_menu( 'smu_landing' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'smu_landing',
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => 1,
		) );

		return;
	}

	$links = array(
		'courses'        => __( 'Courses', 'mba-admission-guide' ),
		'programme'      => __( 'Programme', 'mba-admission-guide' ),
		'advantages'     => __( 'Why SMU', 'mba-admission-guide' ),
		'placements'     => __( 'Placements', 'mba-admission-guide' ),
		'fees'           => __( 'Fees', 'mba-admission-guide' ),
		'admission'      => __( 'Admission', 'mba-admission-guide' ),
	);

	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $anchor => $label ) {
		printf(
			'<li><a href="#%1$s">%2$s</a></li>',
			esc_attr( $anchor ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Point every contact touchpoint on the landing page at its own number.
 *
 * The template never prints a number directly — the header, footer, closing
 * CTA, mobile sticky bar, floating WhatsApp button and the numbers handed to
 * main.js all resolve through mbag_phone_display(), mbag_phone_link() and
 * mbag_whatsapp_number(). Those read theme mods, so filtering the mods here
 * redirects all of them at once, and nothing else on the site moves.
 *
 * Hooked to 'wp' because mbag_smu_is_template() needs the main query, and
 * because that still runs before wp_enqueue_scripts — which is where
 * mbag_scripts() localizes the same numbers into mbagSettings for the JS.
 * Hooking any later would leave the scripts pointing at the site number.
 */
function mbag_smu_contact_overrides() {
	if ( ! mbag_smu_is_template() ) {
		return;
	}

	/* Defaults live in code, not only in the database. A theme mod set through
	   the Customizer exists on one install; this page's number has to be right
	   the moment the theme is deployed anywhere, so the constant below is the
	   fallback and the Customizer field overrides it. */
	$phone = mbag_smu_field( 'phone' );

	if ( '' === $phone ) {
		$phone = mbag_smu_default_phone();
	}

	if ( '' !== $phone ) {
		add_filter( 'theme_mod_mbag_phone', static function () use ( $phone ) {
			return $phone;
		} );

		add_filter( 'theme_mod_mbag_phone_link', static function () use ( $phone ) {
			return mbag_sanitize_phone( $phone );
		} );
	}

	// Every lead form on this page — the hero card and the popup — comes from
	// this page's own CF7 form when one is set. mbag_form_slot_id() reads
	// these mods, so filtering them redirects each slot without touching the
	// site-wide assignments in Customize > Lead Forms.
	/* Same reason as the phone number below: a theme mod set in the Customizer
	   exists in one database, so a fresh deploy would fall back to the
	   site-wide forms. The default lives in code; the Customizer field wins. */
	$form = mbag_smu_field( 'form_id' );

	if ( '' === $form ) {
		$form = mbag_smu_default_form_id();
	}

	if ( '' !== $form ) {
		foreach ( array_keys( mbag_form_slots() ) as $slot ) {
			add_filter( 'theme_mod_mbag_cf7_' . $slot, static function () use ( $form ) {
				return $form;
			} );
		}
	}

	// WhatsApp follows the phone number unless it is set separately.
	$whatsapp = mbag_smu_field( 'whatsapp' );
	$whatsapp = ( '' !== $whatsapp ) ? $whatsapp : $phone;

	if ( '' !== $whatsapp ) {
		add_filter( 'theme_mod_mbag_whatsapp', static function () use ( $whatsapp ) {
			return mbag_sanitize_digits( $whatsapp );
		} );
	}
}
add_action( 'wp', 'mbag_smu_contact_overrides' );

/**
 * The landing page's own phone number.
 *
 * Kept here rather than left to a Customizer value alone so the number is
 * correct on a fresh deploy, before anyone opens the Customizer. The
 * "Landing page phone number" field still wins when it is filled in.
 *
 * WhatsApp derives from this unless a separate WhatsApp number is set.
 *
 * @return string
 */
function mbag_smu_default_phone() {
	return (string) apply_filters( 'mbag_smu_default_phone', '+91 96067 02758' );
}

/**
 * Let a static front page keep its own page template.
 *
 * WordPress checks front-page.php before the template assigned in Page
 * Attributes, so a page promoted to "Your homepage" silently loses its
 * template and renders the generic front page instead. That is fine while
 * the homepage is the default one; it is wrong the moment the homepage is
 * meant to be a landing page.
 *
 * Returning '' here makes template-loader.php fall through its remaining
 * conditions to is_page(), which honours the assigned template. front-page.php
 * still wins whenever the front page has no template of its own, so nothing
 * changes for a site that has not opted in.
 *
 * @param string $template Path front-page.php resolved to.
 * @return string
 */
function mbag_front_page_template( $template ) {
	if ( get_page_template_slug( get_queried_object_id() ) ) {
		return '';
	}

	return $template;
}
add_filter( 'frontpage_template', 'mbag_front_page_template' );

/**
 * The Contact Form 7 form used on the landing page.
 *
 * Kept in code so the page is wired to the right form the moment the theme is
 * deployed, before anyone opens the Customizer. The "Landing page Contact
 * Form 7 ID" field still overrides it.
 *
 * If the form is missing on an install, mbag_lead_form() falls back to the
 * phone and WhatsApp CTA and the popup does not render, rather than printing
 * a shortcode for a form that is not there.
 *
 * @return string
 */
function mbag_smu_default_form_id() {
	return (string) apply_filters( 'mbag_smu_default_form_id', 'a28fde8' );
}
