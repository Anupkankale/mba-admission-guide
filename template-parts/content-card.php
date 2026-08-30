<?php
/**
 * Post card used in the blog, archive and search listings.
 *
 * @package MBA_Admission_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'pcard' ); ?>>
	<a class="pcard__thumb" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'alt' => '' ) );
		} else {
			$mbag_title = wp_strip_all_tags( get_the_title() );
			$mbag_first = function_exists( 'mb_substr' ) ? mb_substr( $mbag_title, 0, 1 ) : substr( $mbag_title, 0, 1 );
			echo '<span class="pcard__ph">' . esc_html( $mbag_first ) . '</span>';
		}
		?>
	</a>
	<div class="pcard__body">
		<div class="entry__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
		<h2 class="pcard__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="pcard__ex"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
		<a class="pcard__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'mba-admission-guide' ); ?> <?php mbag_icon( 'arrow-right' ); ?>
			<span class="screen-reader-text"><?php the_title(); ?></span>
		</a>
	</div>
</article>
