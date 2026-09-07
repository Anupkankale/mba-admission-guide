<?php
/**
 * Header for the Sikkim Manipal landing page.
 *
 * Loaded by get_header( 'landing' ) from
 * page-templates/template-sikkim-manipal.php.
 *
 * A landing page's header has one job: keep the visitor on the page and
 * moving toward the form. So this deliberately drops what the site-wide
 * header.php carries — the news ticker, the full site menu, the reading
 * progress bar — and keeps only the co-branded logo, in-page section
 * anchors, the counsellor phone number and one CTA.
 *
 * Links come from the "Landing Page Menu (Sikkim Manipal)" menu location
 * when one is assigned, and from the page's own section anchors otherwise.
 * See mbag_smu_nav().
 *
 * @package MBA_Admission_Guide
 */

$mbag_smu_logo = mbag_smu_img( 'logo-smu.webp' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="theme-color" content="#151A3A" />
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>
<body <?php body_class( 'smu-landing' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'mba-admission-guide' ); ?></a>

<header class="smu-head">
  <div class="smu-shell smu-head__in">
    <a class="smu-head__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
      <?php if ( '' !== $mbag_smu_logo ) : ?>
        <img src="<?php echo esc_url( $mbag_smu_logo ); ?>" width="1000" height="116"
          alt="<?php bloginfo( 'name' ); ?>" decoding="async" fetchpriority="high">
      <?php else : ?>
        <span class="smu-head__name"><?php bloginfo( 'name' ); ?></span>
      <?php endif; ?>
    </a>

    <button class="smu-head__toggle" id="smuNavToggle" type="button"
      aria-expanded="false" aria-controls="smuLandingNav">
      <span class="smu-head__bars" aria-hidden="true"></span>
      <span class="screen-reader-text"><?php esc_html_e( 'Toggle menu', 'mba-admission-guide' ); ?></span>
    </button>

    <nav class="smu-nav" id="smuLandingNav" aria-label="<?php esc_attr_e( 'Landing page sections', 'mba-admission-guide' ); ?>">
      <?php mbag_smu_nav(); ?>
    </nav>

    <div class="smu-head__cta">
      <a class="smu-head__phone" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>">
        <?php mbag_icon( 'phone' ); ?>
        <span><small><?php esc_html_e( 'Counsellor on call', 'mba-admission-guide' ); ?></small><?php echo esc_html( mbag_phone_display() ); ?></span>
      </a>
    </div>
  </div>
</header>

<main id="main" class="site-main">
