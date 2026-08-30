<?php
/**
 * Template Name: Thank You (Lead Confirmation)
 * Template Post Type: page
 *
 * Conversion confirmation page. Point your form's redirect here after a
 * successful submit (Appearance > Customize > Lead Forms > Thank You page).
 *
 * The page is automatically noindex'd — see mbag_noindex_robots() in
 * functions.php — so it never shows up in search results and never inflates
 * your conversion count from organic traffic.
 *
 * @package MBA_Admission_Guide
 */

get_header();

$mbag_phone_display = mbag_phone_display();
$mbag_phone_link    = mbag_phone_link();
$mbag_whatsapp      = mbag_whatsapp_link( __( 'Hi, I just submitted the admission form. Please share the Online MBA details.', 'mba-admission-guide' ) );
?>

<section class="page-hero page-hero--tall ty">
	<span class="orb orb--a" aria-hidden="true"></span>
	<span class="orb orb--b" aria-hidden="true"></span>
	<div class="container center">
		<div class="ty__check"><?php mbag_icon( 'check' ); ?></div>
		<span class="pill"><span class="pulse-dot"></span> <?php esc_html_e( 'Request received', 'mba-admission-guide' ); ?></span>
		<h1 class="page-hero__title">
			<?php
			while ( have_posts() ) {
				the_post();
				the_title();
			}
			rewind_posts();
			?>
		</h1>
		<p class="page-hero__sub"><?php esc_html_e( 'Your details are with our counselling team. Expect a call within one working day — usually much sooner.', 'mba-admission-guide' ); ?></p>

		<div class="hero__cta hero__cta--center">
			<a class="btn btn--hot btn--lg shine" href="tel:<?php echo esc_attr( $mbag_phone_link ); ?>"><?php mbag_icon( 'phone' ); ?> <?php echo esc_html( $mbag_phone_display ); ?></a>
			<a class="btn btn--wa btn--lg" href="<?php echo esc_url( $mbag_whatsapp ); ?>" target="_blank" rel="noopener"><?php mbag_icon( 'whatsapp' ); ?> <?php esc_html_e( 'Message on WhatsApp', 'mba-admission-guide' ); ?></a>
		</div>
		<p class="ty__hint"><?php esc_html_e( 'Save the number so you recognise our call. Calls come between 10 AM and 7 PM, Mon–Sat.', 'mba-admission-guide' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="head">
			<span class="eyebrow"><?php esc_html_e( 'What happens next', 'mba-admission-guide' ); ?></span>
			<h2 class="h2"><?php esc_html_e( 'Three short steps from here', 'mba-admission-guide' ); ?></h2>
		</div>
		<div class="steps">
			<div class="step rv">
				<b>1</b>
				<h3><?php esc_html_e( 'Counsellor call', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'We understand your background, work experience and career goal before suggesting anything.', 'mba-admission-guide' ); ?></p>
			</div>
			<div class="step rv">
				<b>2</b>
				<h3><?php esc_html_e( 'Shortlist &amp; compare', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'You get a side-by-side view of fees, approvals, specializations and exam pattern for the best-fit universities.', 'mba-admission-guide' ); ?></p>
			</div>
			<div class="step rv">
				<b>3</b>
				<h3><?php esc_html_e( 'Application support', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'Document checklist, eligibility check and help through the university application, end to end.', 'mba-admission-guide' ); ?></p>
			</div>
			<div class="step rv">
				<b>4</b>
				<h3><?php esc_html_e( 'Keep the details handy', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'Keep your graduation marksheet, ID proof and a passport photo scanned — it speeds the process up.', 'mba-admission-guide' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php
// Editable body content from the page editor, shown only when it is filled in.
while ( have_posts() ) :
	the_post();

	if ( trim( get_the_content() ) !== '' ) :
		?>
		<section class="section tint">
			<div class="container container--narrow">
				<?php get_template_part( 'template-parts/content', 'page' ); ?>
			</div>
		</section>
		<?php
	endif;
endwhile;
?>

<section class="section tint ty__next">
	<div class="container">
		<div class="head">
			<span class="eyebrow"><?php esc_html_e( 'While you wait', 'mba-admission-guide' ); ?></span>
			<h2 class="h2"><?php esc_html_e( 'Do this in the next five minutes', 'mba-admission-guide' ); ?></h2>
		</div>
		<div class="fgrid">
			<a class="feat" href="<?php echo esc_url( mbag_anchor( 'compare' ) ); ?>">
				<span class="fico"><?php mbag_icon( 'chart' ); ?></span>
				<h3><?php esc_html_e( 'Compare universities', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'Fees, approvals and duration for every program, in one table.', 'mba-admission-guide' ); ?></p>
			</a>
			<a class="feat" href="<?php echo esc_url( mbag_anchor( 'specializations' ) ); ?>">
				<span class="fico"><?php mbag_icon( 'target' ); ?></span>
				<h3><?php esc_html_e( 'Pick a specialization', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'Marketing, Finance, HR, Analytics and more — see what suits your career path.', 'mba-admission-guide' ); ?></p>
			</a>
			<a class="feat" href="<?php echo esc_url( mbag_anchor( 'faqs' ) ); ?>">
				<span class="fico"><?php mbag_icon( 'chat' ); ?></span>
				<h3><?php esc_html_e( 'Read the FAQs', 'mba-admission-guide' ); ?></h3>
				<p><?php esc_html_e( 'Validity, recognition, exams and eligibility — answered plainly.', 'mba-admission-guide' ); ?></p>
			</a>
		</div>
	</div>
</section>

<?php
/**
 * Fires on the Thank You page, inside the content area.
 *
 * Attach your GA4 / Google Ads / Meta Pixel conversion snippet here from a
 * code-snippets plugin or a child theme, instead of pasting scripts into the
 * page editor:
 *
 *     add_action( 'mbag_thank_you', function () { ?><script>…</script><?php } );
 */
do_action( 'mbag_thank_you' );

get_footer();
