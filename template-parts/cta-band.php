<?php
/**
 * Reusable conversion band for inner pages.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="final ctaband">
	<span class="orb orb--c" aria-hidden="true"></span>
	<div class="container">
		<span class="eyebrow"><?php esc_html_e( 'Free Admission Guidance', 'mba-admission-guide' ); ?></span>
		<h2 class="h2"><?php esc_html_e( 'Still deciding which Online MBA fits you?', 'mba-admission-guide' ); ?></h2>
		<p class="lead"><?php esc_html_e( 'Talk to a counsellor about universities, specializations, eligibility and fees — no cost, no obligation.', 'mba-admission-guide' ); ?></p>
		<div class="actions">
			<a class="btn btn--hot btn--lg shine" href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>"><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></a>
			<a class="btn btn--glass btn--lg" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>"><?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?></a>
		</div>
	</div>
</section>
