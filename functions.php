<?php
/**
 * MBA Admission Guide theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MBAG_VERSION', '1.20.0' );

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
		'primary' => __( 'Primary Menu', 'mba-admission-guide' ),
		'footer'  => __( 'Footer Menu', 'mba-admission-guide' ),
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
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap',
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
		'popup'      => array(
			'delay'  => mbag_popup_active() ? (int) mbag_popup_settings()['delay'] : 0,
			'scroll' => mbag_popup_active() ? (int) mbag_popup_settings()['scroll'] : 0,
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'mbag_scripts' );

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
		'label'   => __( 'WhatsApp Number (with country code, no +)', 'mba-admission-guide' ),
		'section' => 'mbag_contact',
		'type'    => 'text',
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
 * FONT AWESOME ICONS
 *
 * Loaded from cdnjs as three split files (core + solid + brands)
 * instead of all.min.css: the theme uses no "regular" or v4-shim
 * glyphs, and skipping them saves roughly 40% of the payload.
 *
 * Templates never hard-code an <i> tag. They call mbag_icon(), so
 * swapping icon sets later is a change in one function, not in
 * fifty templates.
 * ---------------------------------------------------------------
 */

define( 'MBAG_FA_VERSION', '6.7.2' );

/**
 * Handles used by plugins that ship their own Font Awesome.
 *
 * If any of these is already queued we stand down, because two copies of
 * Font Awesome is 200KB of duplicate CSS and a specificity fight.
 *
 * @return string[]
 */
function mbag_fa_conflict_handles() {
	return array(
		'font-awesome',
		'fontawesome',
		'font-awesome-5',
		'elementor-icons-fa-solid',
		'elementor-icons-fa-brands',
		'wpforms-font-awesome',
	);
}

/**
 * Should the theme load Font Awesome itself?
 *
 * Disable from a child theme or snippet with:
 *     add_filter( 'mbag_load_font_awesome', '__return_false' );
 *
 * @return bool
 */
function mbag_load_font_awesome() {
	foreach ( mbag_fa_conflict_handles() as $handle ) {
		if ( wp_style_is( $handle, 'enqueued' ) || wp_style_is( $handle, 'done' ) ) {
			return apply_filters( 'mbag_load_font_awesome', false );
		}
	}

	return apply_filters( 'mbag_load_font_awesome', true );
}

/**
 * Enqueue Font Awesome after plugins have had their turn, so the
 * conflict check above sees their handles.
 */
function mbag_enqueue_font_awesome() {
	if ( ! mbag_load_font_awesome() ) {
		return;
	}

	$base = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . MBAG_FA_VERSION . '/css/';

	wp_enqueue_style( 'mbag-fa-core', $base . 'fontawesome.min.css', array(), MBAG_FA_VERSION );
	wp_enqueue_style( 'mbag-fa-solid', $base . 'solid.min.css', array( 'mbag-fa-core' ), MBAG_FA_VERSION );
	wp_enqueue_style( 'mbag-fa-brands', $base . 'brands.min.css', array( 'mbag-fa-core' ), MBAG_FA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'mbag_enqueue_font_awesome', 20 );

/**
 * Warm up the connections the stylesheets above depend on.
 *
 * @param array  $urls           URLs to print.
 * @param string $relation_type  The hint type.
 * @return array
 */
function mbag_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );

		if ( mbag_load_font_awesome() ) {
			$urls[] = array( 'href' => 'https://cdnjs.cloudflare.com', 'crossorigin' => 'anonymous' );
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'mbag_resource_hints', 10, 2 );

/**
 * The theme's icon vocabulary: one name -> one Font Awesome class.
 *
 * Names are intent-based ('phone', 'secure') rather than shape-based, so a
 * later icon swap does not leave templates lying about what they show.
 *
 * @return array<string,string>
 */
function mbag_icon_map() {
	return array(
		// Contact and conversion.
		'phone'        => 'fa-solid fa-phone-volume',
		'whatsapp'     => 'fa-brands fa-whatsapp',
		'chat'         => 'fa-solid fa-comments',
		'secure'       => 'fa-solid fa-lock',
		'check'        => 'fa-solid fa-check',
		'arrow-right'  => 'fa-solid fa-arrow-right-long',
		'arrow-left'   => 'fa-solid fa-arrow-left-long',
		'star'         => 'fa-solid fa-star',
		'search'       => 'fa-solid fa-magnifying-glass',
		'clock'        => 'fa-solid fa-hourglass-half',
		'home'         => 'fa-solid fa-house',
		'download'     => 'fa-solid fa-file-arrow-down',

		// Programme benefits.
		'anywhere'     => 'fa-solid fa-house-laptop',
		'flexible'     => 'fa-solid fa-clock',
		'target'       => 'fa-solid fa-bullseye',
		'chart'        => 'fa-solid fa-chart-column',
		'laptop'       => 'fa-solid fa-laptop',
		'rocket'       => 'fa-solid fa-rocket',
		'documents'    => 'fa-solid fa-file-lines',
		'graduate'     => 'fa-solid fa-user-graduate',
		'university'   => 'fa-solid fa-building-columns',
	);
}

/**
 * Build the markup for one icon.
 *
 * Icons here are decorative — every one sits next to a text label — so they
 * are hidden from screen readers. If you ever use an icon *as* the label,
 * pass $label so assistive tech has something to announce.
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

	$classes = trim( $map[ $name ] . ' mbag-i ' . $class );

	if ( '' !== $label ) {
		return sprintf(
			'<i class="%1$s" role="img" aria-label="%2$s"></i>',
			esc_attr( $classes ),
			esc_attr( $label )
		);
	}

	return sprintf( '<i class="%s" aria-hidden="true"></i>', esc_attr( $classes ) );
}

/**
 * Echo an icon. See mbag_get_icon().
 *
 * @param string $name  Key from mbag_icon_map().
 * @param string $class Extra CSS classes.
 * @param string $label Accessible label. Empty = decorative.
 */
function mbag_icon( $name, $class = '', $label = '' ) {
	echo wp_kses(
		mbag_get_icon( $name, $class, $label ),
		array( 'i' => array( 'class' => array(), 'role' => array(), 'aria-label' => array(), 'aria-hidden' => array() ) )
	);
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
		'manipal-jaipur'  => 'Manipal University Jaipur',
		'sikkim-manipal'  => 'Sikkim Manipal University',
		'vit'             => 'VIT University',
		'nmims'           => 'NMIMS Online',
		'gla'             => 'GLA University Online',
		'dayananda-sagar' => 'Dayananda Sagar University Online',
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
		'delay'    => (int) get_theme_mod( 'mbag_popup_delay', 15 ),
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
		'default'           => 15,
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
