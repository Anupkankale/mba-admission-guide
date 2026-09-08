<?php
/**
 * Footer for the Sikkim Manipal landing page.
 *
 * Loaded by get_footer( 'landing' ) from
 * page-templates/template-sikkim-manipal.php.
 *
 * The site-wide footer.php is wrong here for one concrete reason: its links
 * resolve to front-page anchors (#universities, #compare, #faqs), so on this
 * template every one of them navigates the visitor away to the home page.
 * On a landing page that is a straight conversion leak. This footer keeps the
 * links in-page and gives the two ways to reach a human directly.
 *
 * The floating WhatsApp button and the mobile sticky bar are repeated from
 * footer.php on purpose — they are markup, not includes, and dropping them
 * would remove them from this template only.
 *
 * @package MBA_Admission_Guide
 */

$mbag_smu_f_logo = mbag_smu_img( 'logo-smu.webp' );
$mbag_smu_f_hours = mbag_hours();
$mbag_smu_f_reply = mbag_response_time();
?>
</main><!-- #main -->

<footer class="smu-foot">
  <div class="smu-shell">
    <div class="smu-foot__top">

      <div class="smu-foot__brand">
        <?php if ( '' !== $mbag_smu_f_logo ) : ?>
          <span class="smu-foot__plate">
            <img src="<?php echo esc_url( $mbag_smu_f_logo ); ?>" width="1000" height="116"
              alt="<?php bloginfo( 'name' ); ?>" decoding="async" loading="lazy">
          </span>
        <?php else : ?>
          <strong class="smu-foot__name"><?php bloginfo( 'name' ); ?></strong>
        <?php endif; ?>

        <p><?php esc_html_e( 'Independent admission guidance for working professionals. We help you compare programmes honestly, check your eligibility and apply — at no cost to you.', 'mba-admission-guide' ); ?></p>
      </div>

      <div class="smu-foot__col">
        <h2><?php esc_html_e( 'On this page', 'mba-admission-guide' ); ?></h2>
        <?php mbag_smu_nav( 'smu-foot__links' ); ?>
      </div>

      <div class="smu-foot__col">
        <h2><?php esc_html_e( 'Talk to a counsellor', 'mba-admission-guide' ); ?></h2>

        <a class="smu-foot__contact" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>">
          <?php mbag_icon( 'phone' ); ?>
          <span><?php echo esc_html( mbag_phone_display() ); ?></span>
        </a>

        <a class="smu-foot__contact" href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener">
          <?php mbag_icon( 'whatsapp' ); ?>
          <span><?php esc_html_e( 'Chat on WhatsApp', 'mba-admission-guide' ); ?></span>
        </a>

        <?php if ( '' !== $mbag_smu_f_hours || '' !== $mbag_smu_f_reply ) : ?>
          <ul class="smu-foot__meta">
            <?php if ( '' !== $mbag_smu_f_hours ) : ?>
              <li><?php mbag_icon( 'clock' ); ?> <?php echo esc_html( $mbag_smu_f_hours ); ?></li>
            <?php endif; ?>
            <?php if ( '' !== $mbag_smu_f_reply ) : ?>
              <li><?php mbag_icon( 'chat' ); ?> <?php echo esc_html( $mbag_smu_f_reply ); ?></li>
            <?php endif; ?>
          </ul>
        <?php endif; ?>
      </div>

    </div>

    <p class="smu-foot__disc">
      <?php
      printf(
        /* translators: %s: name of the university the landing page is about. */
        esc_html__( 'Disclaimer: We act as a marketing service partner only. %s holds full rights to request change or removal of any non-relevant content. Images used are for illustrative purposes and do not directly represent the respective colleges or universities.', 'mba-admission-guide' ),
        '<strong>' . esc_html__( 'Sikkim Manipal University', 'mba-admission-guide' ) . '</strong>'
      );
      ?>
    </p>

    <div class="smu-foot__bot">
      <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
      <span><?php esc_html_e( 'Independent counselling platform — not affiliated with the university', 'mba-admission-guide' ); ?></span>
    </div>
  </div>
</footer>

<a class="wa" href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener"
  aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'mba-admission-guide' ); ?>"><?php mbag_icon( 'whatsapp' ); ?></a>

<div class="sticky">
  <a href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>" class="btn btn--line"><?php mbag_icon( 'phone' ); ?><span><?php esc_html_e( 'Call', 'mba-admission-guide' ); ?></span></a>
  <a href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener" class="btn btn--wa"><?php mbag_icon( 'whatsapp' ); ?><span><?php esc_html_e( 'WhatsApp', 'mba-admission-guide' ); ?></span></a>
  <?php mbag_brochure_button( 'btn btn--line', __( 'Brochure', 'mba-admission-guide' ) ); ?>
  <a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--hot"><?php mbag_icon( 'arrow-right' ); ?><span><?php esc_html_e( 'Apply Free', 'mba-admission-guide' ); ?></span></a>
</div>

<?php wp_footer(); ?>
</body>
</html>
