<?php
/**
 * Search results.
 *
 * @package MBA_Admission_Guide
 */

get_header();

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow' => esc_html__( 'Search', 'mba-admission-guide' ),
	'title'   => sprintf(
		/* translators: %s: search query. */
		esc_html__( 'Results for “%s”', 'mba-admission-guide' ),
		get_search_query()
	),
	'subtitle' => sprintf(
		/* translators: %s: number of results. */
		esc_html( _n( '%s result found.', '%s results found.', (int) $GLOBALS['wp_query']->found_posts, 'mba-admission-guide' ) ),
		number_format_i18n( (int) $GLOBALS['wp_query']->found_posts )
	),
) );
?>

<section class="section tint">
	<div class="container">
		<div class="search-bar"><?php get_search_form(); ?></div>

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

<?php get_footer(); ?>
