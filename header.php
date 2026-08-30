<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="theme-color" content="#0B1235" />
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'mba-admission-guide' ); ?></a>

<div class="progress" id="progress"></div>

<!-- ========== TICKER ========== -->
<div class="ticker" aria-hidden="true">
  <div class="ticker__track" id="ticker"></div>
</div>

<!-- ========== HEADER ========== -->
<header class="header">
  <div class="container header__in">
    <?php mbag_brand( 'header' ); ?>

    <button class="navtoggle" id="navToggle" type="button" aria-expanded="false" aria-controls="primaryNav">
      <span class="navtoggle__bars" aria-hidden="true"></span>
      <span class="screen-reader-text"><?php esc_html_e( 'Toggle menu', 'mba-admission-guide' ); ?></span>
    </button>

    <nav class="nav" id="primaryNav" aria-label="<?php esc_attr_e( 'Primary', 'mba-admission-guide' ); ?>">
      <?php
      if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'nav__list',
          'depth'          => 2,
        ) );
      } else {
        mbag_fallback_menu( 'nav__list' );
      }
      ?>
      <div class="nav__cta">
        <a href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>" class="btn btn--line btn--sm"><?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?></a>
        <a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--hot btn--sm"><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></a>
      </div>
    </nav>

    <div class="header__right">
      <a href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>" class="hphone">
        <span class="pulse-dot" aria-hidden="true"></span>
        <span><small><?php esc_html_e( 'Counsellor on call', 'mba-admission-guide' ); ?></small><?php echo esc_html( mbag_phone_display() ); ?></span>
      </a>
      <a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--hot btn--sm shine"><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></a>
    </div>
  </div>
</header>

<main id="main" class="site-main">
