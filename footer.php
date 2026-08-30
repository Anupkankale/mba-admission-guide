</main><!-- #main -->

<!-- ========== FOOTER ========== -->
<footer class="footer">
  <div class="container">
    <div class="fin">
      <?php mbag_brand( 'footer' ); ?>
      <nav class="fnav" aria-label="<?php esc_attr_e( 'Footer', 'mba-admission-guide' ); ?>">
        <?php
        if ( has_nav_menu( 'footer' ) ) {
          wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'flinks',
            'depth'          => 1,
          ) );
        } else {
          mbag_fallback_menu( 'flinks' );
        }
        ?>
      </nav>
    </div>
    <div class="fbot">
      &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
      &middot; <?php esc_html_e( 'Independent counselling &amp; comparison platform', 'mba-admission-guide' ); ?>
    </div>
  </div>
</footer>

<a class="wa" href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'mba-admission-guide' ); ?>"><?php mbag_icon( 'whatsapp' ); ?></a>

<div class="sticky">
  <a href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>" class="btn btn--line"><?php mbag_icon( 'phone' ); ?><span><?php esc_html_e( 'Call', 'mba-admission-guide' ); ?></span></a>
  <a href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener" class="btn btn--wa"><?php mbag_icon( 'whatsapp' ); ?><span><?php esc_html_e( 'WhatsApp', 'mba-admission-guide' ); ?></span></a>
  <?php mbag_brochure_button( 'btn btn--line', __( 'Brochure', 'mba-admission-guide' ) ); ?>
  <a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--hot"><?php mbag_icon( 'arrow-right' ); ?><span><?php esc_html_e( 'Apply Free', 'mba-admission-guide' ); ?></span></a>
</div>

<?php if ( is_front_page() ) : ?>
  <div class="toast" id="toast"><span class="dot"></span><p id="toastText"></p></div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
