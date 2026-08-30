<?php
/**
 * Page content.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
		<figure class="entry__media"><?php the_post_thumbnail( 'large' ); ?></figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'mba-admission-guide' ) . ' ',
			'after'  => '</nav>',
		) );
		?>
	</div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry__footer">
			<?php
			edit_post_link(
				sprintf(
					/* translators: %s: post title. */
					esc_html__( 'Edit %s', 'mba-admission-guide' ),
					'<span class="screen-reader-text">' . get_the_title() . '</span>'
				),
				'<span class="entry__edit">',
				'</span>'
			);
			?>
		</footer>
	<?php endif; ?>
</article>
