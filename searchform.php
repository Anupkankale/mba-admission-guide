<?php
/**
 * Search form.
 *
 * @package MBA_Admission_Guide
 */

$mbag_sid = wp_unique_id( 'mbag-search-' );
?>
<form role="search" method="get" class="sform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $mbag_sid ); ?>"><?php esc_html_e( 'Search for:', 'mba-admission-guide' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $mbag_sid ); ?>" class="sform__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search universities, specializations…', 'mba-admission-guide' ); ?>" />
	<button type="submit" class="btn btn--dark btn--sm"><?php mbag_icon( 'search' ); ?> <?php esc_html_e( 'Search', 'mba-admission-guide' ); ?></button>
</form>
