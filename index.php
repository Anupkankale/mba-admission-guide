<?php
/**
 * Fallback template — used for the blog posts index and anything without a
 * more specific template. The landing page now lives in front-page.php.
 *
 * @package MBA_Admission_Guide
 */

get_header();

$mbag_blog_id = (int) get_option( 'page_for_posts' );
get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow'  => esc_html__( 'Latest Updates', 'mba-admission-guide' ),
	'title'    => $mbag_blog_id ? get_the_title( $mbag_blog_id ) : esc_html__( 'Admission Insights', 'mba-admission-guide' ),
	'subtitle' => esc_html__( 'Guides, comparisons and admission updates for Online and Distance MBA aspirants.', 'mba-admission-guide' ),
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
