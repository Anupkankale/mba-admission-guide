<?php
/**
 * Template Name: Full Width (No Hero)
 * Template Post Type: page
 *
 * Edge-to-edge page with no title banner — use it for pages you lay out
 * entirely in the block editor or a page builder.
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<section class="section section--flush">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'page' );
		endwhile;
		?>
	</div>
</section>

<?php get_footer(); ?>
