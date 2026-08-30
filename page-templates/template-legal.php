<?php
/**
 * Template Name: Legal / Policy (Narrow)
 * Template Post Type: page
 *
 * Narrow, quiet layout for Privacy Policy, Terms, Refund Policy and
 * Disclaimer pages — no conversion band, no distractions.
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => esc_html__( 'Legal', 'mba-admission-guide' ),
		'subtitle' => sprintf(
			/* translators: %s: last modified date. */
			esc_html__( 'Last updated on %s', 'mba-admission-guide' ),
			esc_html( get_the_modified_date() )
		),
	) );
	?>

	<section class="section">
		<div class="container container--narrow">
			<?php get_template_part( 'template-parts/content', 'page' ); ?>
		</div>
	</section>
	<?php
endwhile;
?>

<?php get_footer(); ?>
