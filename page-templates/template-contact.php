<?php
/**
 * Template Name: Contact (No Form)
 * Template Post Type: page
 *
 * Contact page with no lead form on it at all. The page's job is to hand the
 * visitor straight to a human — WhatsApp first, phone second — rather than
 * ask them to fill in a third form they have already skipped twice.
 *
 * Every number comes from Appearance > Customize > Contact Details, so this
 * template never hard-codes one and can never drift from the floating
 * WhatsApp bubble, the mobile bar or the CTA rail.
 *
 * @package MBA_Admission_Guide
 */

get_header();

$mbag_hours    = mbag_hours();
$mbag_response = mbag_response_time();
?>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => esc_html__( 'Contact', 'mba-admission-guide' ),
		'subtitle' => esc_html__( 'Message or call a counsellor directly — no form, no waiting for a callback queue.', 'mba-admission-guide' ),
	) );
	?>

<section class="section">
  <div class="container">
    <div class="ctacard rv">
      <h2 class="h2"><?php esc_html_e( 'Talk to a counsellor now', 'mba-admission-guide' ); ?></h2>
      <p class="lead"><?php esc_html_e( 'WhatsApp is the fastest way to reach us. Tap below and the chat opens with your message already started.', 'mba-admission-guide' ); ?></p>

      <div class="ctacard__actions">
        <a class="btn btn--wa btn--lg" href="<?php echo esc_url( mbag_whatsapp_link() ); ?>" target="_blank" rel="noopener">
          <?php mbag_icon( 'whatsapp' ); ?> <?php esc_html_e( 'Contact Now on WhatsApp', 'mba-admission-guide' ); ?>
        </a>
        <a class="btn btn--dark btn--lg" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>">
          <?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?>
        </a>
      </div>

      <?php if ( '' !== $mbag_hours || '' !== $mbag_response ) : ?>
      <ul class="ctacard__meta">
        <?php if ( '' !== $mbag_hours ) : ?>
          <li><span class="tick"><?php mbag_icon( 'clock' ); ?></span> <?php echo esc_html( $mbag_hours ); ?></li>
        <?php endif; ?>
        <?php if ( '' !== $mbag_response ) : ?>
          <li><span class="tick"><?php mbag_icon( 'chat' ); ?></span> <?php echo esc_html( $mbag_response ); ?></li>
        <?php endif; ?>
        <li><span class="tick"><?php mbag_icon( 'secure' ); ?></span> <?php esc_html_e( 'Free guidance. We are an independent platform, not a university.', 'mba-admission-guide' ); ?></li>
      </ul>
      <?php endif; ?>
    </div>

    <?php if ( '' !== trim( get_the_content() ) ) : ?>
      <div class="mujprose rv"><?php the_content(); ?></div>
    <?php endif; ?>
  </div>
</section>

	<?php
endwhile;
?>

<?php get_footer(); ?>
