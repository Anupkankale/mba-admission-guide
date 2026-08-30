<?php
/**
 * Comments area.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do not load for password-protected posts until the password is entered.
if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php
			$mbag_count = get_comments_number();
			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s comment', '%s comments', $mbag_count, 'mba-admission-guide' ) ),
				esc_html( number_format_i18n( $mbag_count ) )
			);
			?>
		</h2>

		<ol class="comments__list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size' => 44,
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => esc_html__( 'Previous', 'mba-admission-guide' ),
			'next_text' => esc_html__( 'Next', 'mba-admission-guide' ),
		) );

		if ( ! comments_open() ) :
			?>
			<p class="comments__closed"><?php esc_html_e( 'Comments are closed.', 'mba-admission-guide' ); ?></p>
			<?php
		endif;
	endif;

	comment_form( array(
		'class_submit'  => 'btn btn--hot',
		'title_reply'   => esc_html__( 'Leave a question', 'mba-admission-guide' ),
		'comment_notes_before' => '<p class="tiny">' . esc_html__( 'Your email address is not published. Required fields are marked *', 'mba-admission-guide' ) . '</p>',
	) );
	?>
</section>
