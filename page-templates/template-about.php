<?php
/**
 * Template Name: About (Narrow + CTA)
 * Template Post Type: page
 *
 * Narrow layout for the About page. It borrows the legal template's reading
 * width — an About page is prose, and full-width prose on a desktop runs to
 * unreadable line lengths — but keeps the closing CTA band, which the legal
 * template deliberately drops. Someone who has just read who we are is a
 * reasonable person to ask for an enquiry; someone reading the refund policy
 * is not.
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => esc_html__( 'About us', 'mba-admission-guide' ),
		'subtitle' => esc_html__( 'Independent admission guidance for working professionals — free to you, at every step.', 'mba-admission-guide' ),
	) );
	?>

	<section class="section">
		<div class="container container--narrow">
			<?php get_template_part( 'template-parts/content', 'page' ); ?>
		</div>
	</section>
	<?php
endwhile;

get_template_part( 'template-parts/cta-band' );
?>

<?php get_footer(); ?>
