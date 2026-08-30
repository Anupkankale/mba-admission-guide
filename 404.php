<?php
/**
 * 404 — page not found.
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<section class="page-hero page-hero--tall">
	<span class="orb orb--a" aria-hidden="true"></span>
	<span class="orb orb--b" aria-hidden="true"></span>
	<div class="container center">
		<span class="pill"><?php esc_html_e( 'Error 404', 'mba-admission-guide' ); ?></span>
		<h1 class="page-hero__title"><?php esc_html_e( 'This page moved or never existed', 'mba-admission-guide' ); ?></h1>
		<p class="page-hero__sub"><?php esc_html_e( 'The link is broken or the page was removed. Search below, or head back and continue comparing Online MBA programs.', 'mba-admission-guide' ); ?></p>
		<div class="e404__search"><?php get_search_form(); ?></div>
		<div class="hero__cta hero__cta--center">
			<a class="btn btn--hot btn--lg shine" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php mbag_icon( 'home' ); ?> <?php esc_html_e( 'Back to Home', 'mba-admission-guide' ); ?></a>
			<a class="btn btn--glass btn--lg" href="<?php echo esc_url( mbag_anchor( 'compare' ) ); ?>"><?php esc_html_e( 'Compare Universities', 'mba-admission-guide' ); ?></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
