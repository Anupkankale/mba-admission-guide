<?php
/**
 * Inner-page hero: breadcrumb + title + optional subtitle.
 *
 * @package MBA_Admission_Guide
 *
 * Args (via get_template_part $args):
 *   title    string Heading text. Defaults to the queried object title.
 *   subtitle string Optional line under the heading.
 *   eyebrow  string Optional small label above the heading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args     = wp_parse_args( $args ?? array(), array(
	'title'    => '',
	'subtitle' => '',
	'eyebrow'  => '',
) );
$mbag_ttl = $args['title'] ? $args['title'] : wp_strip_all_tags( get_the_title() );
?>
<section class="page-hero">
	<span class="orb orb--a" aria-hidden="true"></span>
	<span class="orb orb--b" aria-hidden="true"></span>
	<div class="container">
		<?php mbag_breadcrumbs(); ?>
		<?php if ( $args['eyebrow'] ) : ?>
			<span class="pill"><span class="pulse-dot"></span> <?php echo esc_html( $args['eyebrow'] ); ?></span>
		<?php endif; ?>
		<h1 class="page-hero__title"><?php echo esc_html( $mbag_ttl ); ?></h1>
		<?php if ( $args['subtitle'] ) : ?>
			<p class="page-hero__sub"><?php echo esc_html( $args['subtitle'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
