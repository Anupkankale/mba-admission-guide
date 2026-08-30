<?php
/**
 * Archives — category, tag, author, date, custom post type.
 *
 * @package MBA_Admission_Guide
 */

get_header();

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow'  => esc_html__( 'Archive', 'mba-admission-guide' ),
	'title'    => wp_strip_all_tags( get_the_archive_title() ),
	'subtitle' => wp_strip_all_tags( get_the_archive_description() ),
) );
?>

<section class="section tint">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="pgrid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
			<?php mbag_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/cta-band' ); ?>

<?php get_footer(); ?>
