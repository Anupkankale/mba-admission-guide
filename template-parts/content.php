<?php
/**
 * Single post content.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<div class="entry__meta">
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<?php
		$mbag_cats = get_the_category_list( ', ' );
		if ( $mbag_cats ) {
			echo '<span class="entry__cats">' . wp_kses_post( $mbag_cats ) . '</span>';
		}
		?>
	</div>

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
		<figure class="entry__media"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				/* translators: %s: hidden post title for screen readers. */
				esc_html__( 'Continue reading %s', 'mba-admission-guide' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		wp_link_pages( array(
			'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'mba-admission-guide' ) . ' ',
			'after'  => '</nav>',
		) );
		?>
	</div>

	<?php
	$mbag_tags = get_the_tag_list( '<div class="entry__tags">', '', '</div>' );
	if ( $mbag_tags ) {
		echo wp_kses_post( $mbag_tags );
	}
	?>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry__footer">
			<?php edit_post_link( esc_html__( 'Edit this post', 'mba-admission-guide' ), '<span class="entry__edit">', '</span>' ); ?>
		</footer>
	<?php endif; ?>
</article>
