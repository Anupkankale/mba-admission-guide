<?php
/**
 * Single post.
 *
 * @package MBA_Admission_Guide
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => esc_html__( 'Admission Insights', 'mba-admission-guide' ),
		'subtitle' => has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : '',
	) );
	?>

	<section class="section">
		<div class="container container--narrow">
			<?php
			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation( array(
				'prev_text' => '<span class="pnav__label">' . esc_html__( 'Previous', 'mba-admission-guide' ) . '</span><span class="pnav__title">%title</span>',
				'next_text' => '<span class="pnav__label">' . esc_html__( 'Next', 'mba-admission-guide' ) . '</span><span class="pnav__title">%title</span>',
				'class'     => 'pnav',
			) );

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
