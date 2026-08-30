<?php
/**
 * Default template for static pages (About, Privacy Policy, Terms, etc.).
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array(
		'subtitle' => has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : '',
	) );
	?>

	<section class="section">
		<div class="container container--narrow">
			<?php
			get_template_part( 'template-parts/content', 'page' );

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</section>
	<?php
endwhile;
?>

<?php get_template_part( 'template-parts/cta-band' ); ?>

<?php get_footer(); ?>
