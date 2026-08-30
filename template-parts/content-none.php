<?php
/**
 * Shown when a query returns no results.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="empty">
	<h2><?php esc_html_e( 'Nothing found here', 'mba-admission-guide' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No results matched your search. Try a different keyword — for example a university or a specialization name.', 'mba-admission-guide' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There is no content to show yet. In the meantime, talk to a counsellor about your Online MBA options.', 'mba-admission-guide' ); ?></p>
		<p class="empty__cta"><a class="btn btn--hot" href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>"><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></a></p>
	<?php endif; ?>
</div>
