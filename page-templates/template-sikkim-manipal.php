<?php
/**
 * Template Name: Sikkim Manipal Landing
 * Template Post Type: page
 *
 * Dedicated landing page for the Sikkim Manipal University Online MBA.
 *
 * Ported from a standalone HTML design, with three deliberate changes:
 *
 *  1. Every figure the original hard-coded — fee, per-semester fee, EMI,
 *     learner and recruiter counts, accreditation badges, the legacy
 *     number — now reads from Appearance > Customize > Sikkim Manipal
 *     Landing, and each block hides itself when its field is empty.
 *     Nothing here asserts a number the site owner has not entered.
 *  2. The original's own forms, lead modal, WhatsApp bubble, chatbot and
 *     mobile bar are gone. Lead capture goes through the Contact Form 7
 *     slot assigned in Customize > Lead Forms, and the floating WhatsApp
 *     button, sticky bar and timed popup already come from footer.php and
 *     mbag_render_popup() on every page of the site.
 *  3. Markup is namespaced under .smu with smu- prefixed classes, because
 *     the design reuses class names the theme already owns.
 *
 * The page body from the editor, if any, renders as an extra prose section
 * between the programme block and the advantages.
 *
 * @package MBA_Admission_Guide
 */

get_header( 'landing' );

$mbag_smu_name        = __( 'Sikkim Manipal University', 'mba-admission-guide' );
$mbag_smu_duration    = mbag_smu_field( 'duration' );
$mbag_smu_mode        = mbag_smu_field( 'mode' );
$mbag_smu_eligibility = mbag_smu_field( 'eligibility' );
$mbag_smu_fee_total   = mbag_smu_field( 'fee_total' );
$mbag_smu_fee_sem     = mbag_smu_field( 'fee_semester' );
$mbag_smu_fee_emi     = mbag_smu_field( 'fee_emi' );
$mbag_smu_legacy      = mbag_smu_field( 'legacy' );
$mbag_smu_approvals   = mbag_smu_list( 'approvals' );
$mbag_smu_partners    = mbag_smu_list( 'hiring_partners' );
$mbag_smu_specs       = mbag_smu_specializations();

// The fee band only earns its space once at least one of the three exists.
$mbag_smu_has_fee = ( '' !== $mbag_smu_fee_total || '' !== $mbag_smu_fee_sem || '' !== $mbag_smu_fee_emi );

/**
 * Render one animated stat.
 *
 * The number animates and the suffix is printed as-is, which is what lets
 * the Customizer hold a single human-readable string like "35K+".
 *
 * @param string $value Raw Customizer value.
 * @param string $label Caption under the number.
 * @param string $class Wrapper class — smu-imp or smu-stat.
 */
$mbag_smu_stat = static function ( $value, $label, $class ) {
	$parts = mbag_smu_stat_parts( $value );

	if ( '' === $parts['plain'] ) {
		return;
	}
	?>
	<div class="<?php echo esc_attr( $class ); ?>">
		<b>
		<?php if ( '' !== $parts['num'] ) : ?>
			<span class="smu-num" data-smu-target="<?php echo esc_attr( $parts['num'] ); ?>">0</span><?php
			if ( '' !== $parts['suffix'] ) :
				?><sup><?php echo esc_html( $parts['suffix'] ); ?></sup><?php
			endif;
		else :
			echo esc_html( $parts['plain'] );
		endif;
		?>
		</b>
		<span><?php echo esc_html( $label ); ?></span>
	</div>
	<?php
};

/**
 * Drop the stats whose Customizer field is empty.
 *
 * Kept as a list of pairs rather than a value-keyed map: two stats can
 * legitimately hold the same figure ("100%" twice), and an array keyed by
 * value would silently discard one of them.
 *
 * @param array[] $rows Each row: array( field key, label ).
 * @return array[] Each row: array( value, label ).
 */
$mbag_smu_pick = static function ( array $rows ) {
	$out = array();

	foreach ( $rows as $row ) {
		$value = mbag_smu_field( $row[0] );

		if ( '' !== $value ) {
			$out[] = array( $value, $row[1] );
		}
	}

	return $out;
};

$mbag_smu_adv_stats = $mbag_smu_pick( array(
	array( 'stat_years', __( 'years of quality education', 'mba-admission-guide' ) ),
	array( 'stat_faculty', __( 'faculty & staff', 'mba-admission-guide' ) ),
) );

$mbag_smu_impact_left = $mbag_smu_pick( array(
	array( 'stat_learners', __( 'Learners offered placement assistance', 'mba-admission-guide' ) ),
	array( 'stat_opportunities', __( 'Opportunities created', 'mba-admission-guide' ) ),
) );

$mbag_smu_impact_right = $mbag_smu_pick( array(
	array( 'stat_partners', __( 'Hiring partners', 'mba-admission-guide' ) ),
	array( 'stat_placement', __( 'Placement assistance', 'mba-admission-guide' ) ),
) );

$mbag_smu_has_impact = ( $mbag_smu_impact_left || $mbag_smu_impact_right );

/**
 * Next band background class.
 *
 * Alternation is decided here rather than in CSS because the accreditation
 * strip, the impact band and the fee band each hide themselves when their
 * Customizer fields are empty. :nth-of-type() would count the sections
 * written in this file; this counts the ones that actually reach the page,
 * so the stripe never doubles up where a section dropped out.
 *
 * @return string Class attribute fragment, leading space included.
 */
$mbag_smu_band = 0;

$mbag_smu_bg = static function () use ( &$mbag_smu_band ) {
	++$mbag_smu_band;

	return ( 0 === $mbag_smu_band % 2 ) ? ' smu-band smu-band--alt' : ' smu-band';
};

// Read the featured image before the loop below consumes the query.
$mbag_smu_has_cert = has_post_thumbnail();

?>

<div class="smu">

<!-- ========== HERO ========== -->
<section class="smu-hero" id="top">
  <div class="smu-shell smu-hero-grid">
    <div>
      <span class="smu-eyebrow"><?php esc_html_e( 'Online Programme', 'mba-admission-guide' ); ?></span>

      <h1><?php echo esc_html( $mbag_smu_name ); ?> <span class="smu-h-orange"><?php esc_html_e( 'Takes The Online Stage!', 'mba-admission-guide' ); ?></span></h1>

      <p class="smu-sell"><?php esc_html_e( 'An online MBA with dual specialisation, built for people who cannot stop working to study. Understand the programme, eligibility and fees — and get independent guidance before you apply.', 'mba-admission-guide' ); ?></p>

      <div class="smu-hero-btns">
        <button type="button" class="smu-btn smu-btn-orange" data-mbag-popup
          data-mbag-popup-title="<?php echo esc_attr( sprintf( /* translators: %s: university name. */ __( 'Enquire — %s', 'mba-admission-guide' ), $mbag_smu_name ) ); ?>"
          data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will take you through eligibility, fees and the admission process.', 'mba-admission-guide' ); ?>"
          data-mbag-source="<?php esc_attr_e( 'SMU hero — Enquire Now', 'mba-admission-guide' ); ?>"
          data-mbag-university="<?php echo esc_attr( $mbag_smu_name ); ?>"><?php esc_html_e( 'Enquire Now', 'mba-admission-guide' ); ?></button>
        <?php mbag_brochure_button( 'smu-btn smu-btn-line-navy', __( 'Download Brochure', 'mba-admission-guide' ) ); ?>
      </div>

      <?php
      // Structural facts only — nothing here asserts an unverified figure.
      $mbag_smu_ticks = array_values( array_filter( array(
        $mbag_smu_duration,
        $mbag_smu_mode,
        __( 'Dual specialisation, one degree', 'mba-admission-guide' ),
        __( 'Free counselling before you apply', 'mba-admission-guide' ),
      ) ) );
      ?>
      <ul class="smu-hero-ticks">
        <?php foreach ( $mbag_smu_ticks as $mbag_smu_tick ) : ?>
          <li><?php echo esc_html( $mbag_smu_tick ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- LEAD FORM -->
    <div class="smu-leadcard" id="apply">
      <h2><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></h2>
      <p class="smu-sub"><?php esc_html_e( 'Share a few details and a counsellor will call you back about this programme.', 'mba-admission-guide' ); ?></p>
      <div class="smu-tick-row">
        <span><?php esc_html_e( '100% Free', 'mba-admission-guide' ); ?></span>
        <span><?php esc_html_e( 'No Spam', 'mba-admission-guide' ); ?></span>
        <span><?php esc_html_e( 'Quick Response', 'mba-admission-guide' ); ?></span>
      </div>

      <?php
      /* Reuses the hero lead form assigned in Customize > Lead Forms. When
         no Contact Form 7 form is assigned, point the visitor at the contact
         route that definitely exists rather than printing an empty card. */
      if ( ! mbag_lead_form( 'hero' ) ) :
      ?>
        <p><?php esc_html_e( 'Call or WhatsApp us and a counsellor will take you through eligibility, fees and the admission process.', 'mba-admission-guide' ); ?></p>
        <p style="margin-top:14px"><a class="smu-btn smu-btn-orange" style="width:100%" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>"><?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?></a></p>
      <?php endif; ?>

      <p class="smu-secure"><?php mbag_icon( 'secure' ); ?> <?php esc_html_e( 'Your details stay private. No spam.', 'mba-admission-guide' ); ?></p>
    </div>
  </div>
</section>

<?php
$mbag_smu_courses = mbag_courses();

if ( $mbag_smu_courses ) :
?>
<!-- ========== COURSE CAROUSEL ========== -->
<section class="smu-section smu-courses<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="courses">
  <div class="smu-shell">
    <div class="smu-courses-head">
      <div>
        <span class="smu-eyebrow"><?php esc_html_e( 'Programmes', 'mba-admission-guide' ); ?></span>
        <h2><?php esc_html_e( 'Explore Our Top', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Online Degree Courses', 'mba-admission-guide' ); ?></span></h2>
        <div class="smu-gold-bar"></div>
      </div>
      <div class="smu-course-nav">
        <button type="button" class="smu-course-arrow" data-smu-prev aria-label="<?php esc_attr_e( 'Previous courses', 'mba-admission-guide' ); ?>" aria-controls="smuCourseTrack">&lsaquo;</button>
        <button type="button" class="smu-course-arrow" data-smu-next aria-label="<?php esc_attr_e( 'Next courses', 'mba-admission-guide' ); ?>" aria-controls="smuCourseTrack">&rsaquo;</button>
        <?php
        /* Hidden until the marquee actually starts, so it is never a dead
           control for a visitor whose carousel does not auto-scroll. */
        ?>
        <button type="button" class="smu-course-arrow smu-course-toggle" data-smu-toggle hidden
          aria-pressed="false" aria-controls="smuCourseTrack"
          aria-label="<?php esc_attr_e( 'Pause the course carousel', 'mba-admission-guide' ); ?>"
          data-smu-pause-label="<?php esc_attr_e( 'Pause the course carousel', 'mba-admission-guide' ); ?>"
          data-smu-play-label="<?php esc_attr_e( 'Resume the course carousel', 'mba-admission-guide' ); ?>"><span aria-hidden="true"></span></button>
      </div>
    </div>

    <div class="smu-course-track" id="smuCourseTrack" data-smu-track tabindex="0" role="region"
      aria-label="<?php esc_attr_e( 'Online degree courses', 'mba-admission-guide' ); ?>">
      <?php foreach ( $mbag_smu_courses as $mbag_smu_course ) : ?>
        <?php
        // Not shown on the card any more, but still tags the lead with the
        // university the card belongs to.
        $mbag_smu_c_uni = isset( $mbag_smu_course['university'] ) ? $mbag_smu_course['university'] : '';
        $mbag_smu_c_hot  = ( isset( $mbag_smu_course['badge'] ) && __( 'Most Popular', 'mba-admission-guide' ) === $mbag_smu_course['badge'] );
        ?>
        <?php $mbag_smu_c_img = mbag_smu_img( isset( $mbag_smu_course['image'] ) ? $mbag_smu_course['image'] : '' ); ?>
        <article class="smu-course-card">
          <?php if ( '' !== $mbag_smu_c_img ) : ?>
            <div class="smu-course-photo">
              <img src="<?php echo esc_url( $mbag_smu_c_img ); ?>" alt="" width="378" height="320" decoding="async">
            </div>
          <?php endif; ?>
          <div class="smu-course-badges">
            <?php if ( ! empty( $mbag_smu_course['badge'] ) ) : ?>
              <span class="smu-course-badge<?php echo $mbag_smu_c_hot ? ' smu-course-badge--hot' : ''; ?>"><?php echo esc_html( $mbag_smu_course['badge'] ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $mbag_smu_course['tag'] ) ) : ?>
              <span class="smu-course-tag"><?php echo esc_html( $mbag_smu_course['tag'] ); ?></span>
            <?php endif; ?>
          </div>

          <h3><?php echo esc_html( $mbag_smu_course['title'] ); ?></h3>

          <div class="smu-course-meta">
            <?php if ( ! empty( $mbag_smu_course['rating'] ) ) : ?>
              <span class="smu-course-rating"><?php mbag_icon( 'star' ); ?> <?php echo esc_html( $mbag_smu_course['rating'] ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $mbag_smu_course['duration'] ) ) : ?>
              <span class="smu-course-dur"><?php mbag_icon( 'clock' ); ?> <?php echo esc_html( $mbag_smu_course['duration'] ); ?></span>
            <?php endif; ?>
          </div>

          <?php if ( ! empty( $mbag_smu_course['fee'] ) ) : ?>
            <p class="smu-course-fee"><?php echo esc_html( $mbag_smu_course['fee'] ); ?></p>
          <?php endif; ?>

          <?php if ( ! empty( $mbag_smu_course['eligibility'] ) ) : ?>
            <p class="smu-course-elig"><?php echo esc_html( $mbag_smu_course['eligibility'] ); ?></p>
          <?php endif; ?>

          <button type="button" class="smu-btn smu-btn-orange smu-course-cta" data-mbag-popup
            data-mbag-popup-title="<?php echo esc_attr( sprintf( /* translators: 1: course title, 2: university name. */ __( '%1$s — %2$s', 'mba-admission-guide' ), $mbag_smu_course['title'], $mbag_smu_c_uni ) ); ?>"
            data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will take you through eligibility, fees and the admission process.', 'mba-admission-guide' ); ?>"
            data-mbag-source="<?php echo esc_attr( sprintf( /* translators: 1: course title, 2: university name. */ __( 'Course card — %1$s, %2$s', 'mba-admission-guide' ), $mbag_smu_course['title'], $mbag_smu_c_uni ) ); ?>"
            data-mbag-university="<?php echo esc_attr( $mbag_smu_c_uni ); ?>"><?php esc_html_e( 'Apply Now', 'mba-admission-guide' ); ?></button>
        </article>
      <?php endforeach; ?>
    </div>

    <p class="smu-course-note"><?php esc_html_e( 'Fees, ratings and scholarships are indicative and change between intakes. Confirm the current figures with a counsellor before you apply.', 'mba-admission-guide' ); ?></p>
  </div>
</section>
<?php endif; ?>

<?php if ( $mbag_smu_approvals ) : ?>
<!-- ========== ACCREDITATIONS ========== -->
<div class="smu-creds">
  <div class="smu-shell smu-creds-in">
    <?php foreach ( $mbag_smu_approvals as $mbag_smu_approval ) : ?>
      <div class="smu-cred"><b><?php echo esc_html( $mbag_smu_approval ); ?></b></div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ========== 1 · PROGRAMME ========== -->
<section class="smu-section smu-prog<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="programme">
  <div class="smu-shell smu-prog-grid">
    <div>
      <h2><?php esc_html_e( 'Online MBA from', 'mba-admission-guide' ); ?><br><span class="smu-h-orange"><span class="smu-h-underline"><?php echo esc_html( $mbag_smu_name ); ?></span></span></h2>
      <div class="smu-gold-bar"></div>

      <p class="smu-lede"><?php
        printf(
          /* translators: %s: university name. */
          esc_html__( 'The online Master of Business Administration with dual specialisation from %s is designed to equip working professionals with the business and management skills a corporate role asks for. Learners build expertise across two domains by choosing any two specialisations from the list below.', 'mba-admission-guide' ),
          esc_html( $mbag_smu_name )
        );
      ?></p>

      <?php if ( $mbag_smu_specs ) : ?>
      <p class="smu-spec-h"><?php esc_html_e( 'Choose your specialization', 'mba-admission-guide' ); ?></p>
      <div class="smu-chips" data-smu-chips>
        <?php foreach ( $mbag_smu_specs as $mbag_smu_i => $mbag_smu_spec ) : ?>
          <button class="smu-chip<?php echo 0 === $mbag_smu_i ? ' is-on' : ''; ?>" type="button"
            aria-pressed="<?php echo 0 === $mbag_smu_i ? 'true' : 'false'; ?>"
            data-smu-title="<?php echo esc_attr( $mbag_smu_spec['name'] ); ?>"
            data-smu-desc="<?php echo esc_attr( $mbag_smu_spec['desc'] ); ?>"><?php echo esc_html( $mbag_smu_spec['name'] ); ?></button>
        <?php endforeach; ?>
      </div>
      <div class="smu-spec-box" data-smu-specbox aria-live="polite">
        <b><?php echo esc_html( $mbag_smu_specs[0]['name'] ); ?></b>
        <p><?php echo esc_html( $mbag_smu_specs[0]['desc'] ); ?></p>
      </div>
      <?php endif; ?>

      <button type="button" class="smu-btn smu-btn-orange" data-mbag-popup
        data-mbag-popup-title="<?php esc_attr_e( 'Explore the Online MBA', 'mba-admission-guide' ); ?>"
        data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will walk you through the programme structure.', 'mba-admission-guide' ); ?>"
        data-mbag-source="<?php esc_attr_e( 'SMU programme — Explore', 'mba-admission-guide' ); ?>"
        data-mbag-university="<?php echo esc_attr( $mbag_smu_name ); ?>"><?php esc_html_e( 'Explore Programme', 'mba-admission-guide' ); ?> <span class="smu-arr" aria-hidden="true">&rarr;</span></button>
    </div>
  </div>
</section>

<?php
// Anything typed into the page editor renders here, or nothing at all.
while ( have_posts() ) :
	the_post();

	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="smu-section<?php echo esc_attr( $mbag_smu_bg() ); ?>">
		  <div class="smu-shell">
		    <div class="smu-prose"><?php the_content(); ?></div>
		  </div>
		</section>
		<?php
	endif;
endwhile;
?>

<!-- ========== 2 · ADVANTAGES ========== -->
<section class="smu-section smu-adv<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="advantages">
  <div class="smu-shell smu-adv-grid">
    <div>
      <span class="smu-eyebrow"><?php esc_html_e( 'Advantages', 'mba-admission-guide' ); ?></span>
      <h2><?php esc_html_e( 'Online MBA', 'mba-admission-guide' ); ?><br><span class="smu-h-orange"><?php esc_html_e( 'Advantages', 'mba-admission-guide' ); ?></span></h2>
      <div class="smu-gold-bar"></div>
      <p class="smu-lede"><?php esc_html_e( 'Everything you need to grow — academically, professionally, and personally.', 'mba-admission-guide' ); ?></p>

      <?php if ( $mbag_smu_adv_stats ) : ?>
      <div class="smu-stats">
        <?php
        foreach ( $mbag_smu_adv_stats as $mbag_smu_row ) {
          $mbag_smu_stat( $mbag_smu_row[0], $mbag_smu_row[1], 'smu-stat' );
        }
        ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="smu-adv-cards">
      <div class="smu-adv-card">
        <div class="smu-circle"><?php mbag_icon( 'chart' ); ?></div>
        <h3><?php esc_html_e( 'Higher Earning Potential', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Open up managerial roles across a wider range of organisations and industries than a bachelor\'s degree alone reaches.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-adv-card">
        <div class="smu-circle"><?php mbag_icon( 'rocket' ); ?></div>
        <h3><?php esc_html_e( 'Master Leadership &amp; Managerial Skills', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Build expertise in leadership, strategic thinking and problem-solving through industry-relevant business concepts.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-adv-card">
        <div class="smu-circle"><?php mbag_icon( 'target' ); ?></div>
        <h3><?php esc_html_e( 'Better Career Prospects', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Advance with in-demand skills that help you grow inside your current organisation or move to a bigger one.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-adv-card">
        <div class="smu-circle"><?php mbag_icon( 'graduate' ); ?></div>
        <h3><?php esc_html_e( 'Robust Alumni Network', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Build connections with professionals from diverse backgrounds and gain industry insight through the university\'s alumni network.', 'mba-admission-guide' ); ?></p>
      </div>
    </div>
  </div>
</section>

<?php if ( $mbag_smu_has_impact ) : ?>
<!-- ========== 3 · GLOBAL IMPACT ========== -->
<section class="smu-section smu-impact"><?php $mbag_smu_bg(); ?>
  <div class="smu-shell">
    <div class="smu-center">
      <h2><?php esc_html_e( 'Academic Excellence,', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Global Impact.', 'mba-admission-guide' ); ?></span></h2>
      <div class="smu-gold-bar"></div>
    </div>

    <div class="smu-impact-grid">
      <div class="smu-imp-col">
        <?php
        foreach ( $mbag_smu_impact_left as $mbag_smu_row ) {
          $mbag_smu_stat( $mbag_smu_row[0], $mbag_smu_row[1], 'smu-imp' );
        }
        ?>
      </div>

      <div class="smu-map-wrap">
        <?php
        $mbag_smu_map = mbag_smu_img( 'map.webp' );

        if ( '' !== $mbag_smu_map ) :
        ?>
          <img src="<?php echo esc_url( $mbag_smu_map ); ?>" width="592" height="276" decoding="async" loading="lazy"
            alt="<?php esc_attr_e( 'World map showing learner and recruiter locations', 'mba-admission-guide' ); ?>">
        <?php endif; ?>
      </div>

      <div class="smu-imp-col">
        <?php
        foreach ( $mbag_smu_impact_right as $mbag_smu_row ) {
          $mbag_smu_stat( $mbag_smu_row[0], $mbag_smu_row[1], 'smu-imp' );
        }
        ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========== PLACEMENTS ========== -->
<section class="smu-section<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="placements">
  <div class="smu-shell">
    <div class="smu-center">
      <span class="smu-eyebrow"><?php esc_html_e( 'Placements', 'mba-admission-guide' ); ?></span>
      <h2><?php esc_html_e( 'Placement', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Support', 'mba-admission-guide' ); ?></span></h2>
      <div class="smu-gold-bar"></div>
      <p class="smu-lede" style="margin-top:24px"><?php esc_html_e( 'Resume reviews, mock interviews and access to a shared recruiter pool — from your first semester, not just at the end.', 'mba-admission-guide' ); ?></p>
    </div>

    <?php
    /* The supplied logo strip is the primary display. The Customizer's
       comma-separated partner list is the fallback for when that image is
       not there, so the section still says something without it. */
    $mbag_smu_strip = mbag_smu_img( 'placement.webp' );

    if ( '' !== $mbag_smu_strip ) :
    ?>
      <div class="smu-placement-strip">
        <img src="<?php echo esc_url( $mbag_smu_strip ); ?>" width="1600" height="533" decoding="async" loading="lazy"
          alt="<?php esc_attr_e( 'Recruiters that hire from this programme', 'mba-admission-guide' ); ?>">
      </div>
    <?php elseif ( $mbag_smu_partners ) : ?>
      <div class="smu-logos">
        <?php foreach ( $mbag_smu_partners as $mbag_smu_partner ) : ?>
          <div class="smu-logo-cell"><?php echo esc_html( $mbag_smu_partner ); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="smu-money">
      <div class="smu-money-card">
        <h3><?php esc_html_e( 'Easy Financing Options', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Payment plans let you spread the fee rather than find it all up front. Ask a counsellor which options apply to the current intake.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-money-card">
        <h3><?php esc_html_e( 'Scholarships', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Scholarships are commonly offered to defence personnel, government employees, differently-abled applicants and meritorious students. We will check what you qualify for.', 'mba-admission-guide' ); ?></p>
      </div>
    </div>
  </div>
</section>

<?php if ( $mbag_smu_has_fee ) : ?>
<!-- ========== 4 · FEE &amp; SCHOLARSHIPS ========== -->
<section class="smu-section smu-fees<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="fees">
  <div class="smu-shell">
    <span class="smu-eyebrow"><?php esc_html_e( 'Pricing', 'mba-admission-guide' ); ?></span>
    <h2><?php esc_html_e( 'Fee &amp;', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Scholarships', 'mba-admission-guide' ); ?></span></h2>
    <div class="smu-gold-bar"></div>
    <p class="smu-lede" style="margin-top:24px"><?php esc_html_e( 'Flexi-payment options let the fee be paid in instalments rather than one lump sum.', 'mba-admission-guide' ); ?></p>

    <div class="smu-fee-tab">
      <?php mbag_icon( 'university' ); ?>
      <?php esc_html_e( 'Indian Students', 'mba-admission-guide' ); ?>
    </div>

    <?php
    $mbag_smu_has_side = ( '' !== $mbag_smu_fee_sem || '' !== $mbag_smu_fee_emi );
    ?>
    <div class="smu-fee-layout<?php echo ( ! $mbag_smu_has_side || '' === $mbag_smu_fee_total ) ? ' smu-fee-layout--solo' : ''; ?>">
      <?php if ( '' !== $mbag_smu_fee_total ) : ?>
      <div class="smu-fee-main">
        <span class="smu-badge"><?php esc_html_e( 'Full Programme', 'mba-admission-guide' ); ?></span>
        <p class="smu-k"><?php esc_html_e( 'Full Course Fee', 'mba-admission-guide' ); ?></p>
        <?php if ( '' !== $mbag_smu_duration ) : ?>
          <p class="smu-k2"><?php echo esc_html( $mbag_smu_duration ); ?></p>
        <?php endif; ?>
        <p class="smu-big"><?php echo esc_html( $mbag_smu_fee_total ); ?></p>
        <p class="smu-fine"><?php esc_html_e( 'Indicative. Confirm the current fee before you pay.', 'mba-admission-guide' ); ?></p>
      </div>
      <?php endif; ?>

      <?php if ( $mbag_smu_has_side ) : ?>
      <div class="smu-fee-side">
        <?php if ( '' !== $mbag_smu_fee_sem ) : ?>
        <div class="smu-fee-row smu-fee-row--navy">
          <span class="smu-ic"><?php mbag_icon( 'clock' ); ?></span>
          <span class="smu-k"><?php esc_html_e( 'Each Semester', 'mba-admission-guide' ); ?></span>
          <span class="smu-right">
            <span class="smu-amt"><?php echo esc_html( $mbag_smu_fee_sem ); ?></span>
            <span class="smu-fine"><?php esc_html_e( 'Indicative', 'mba-admission-guide' ); ?></span>
          </span>
        </div>
        <?php endif; ?>
        <?php if ( '' !== $mbag_smu_fee_emi ) : ?>
        <div class="smu-fee-row smu-fee-row--gold">
          <span class="smu-ic"><?php mbag_icon( 'chart' ); ?></span>
          <span class="smu-k"><?php esc_html_e( 'EMI Starting', 'mba-admission-guide' ); ?></span>
          <span class="smu-right">
            <span class="smu-amt"><?php echo esc_html( $mbag_smu_fee_emi ); ?></span>
            <span class="smu-fine"><?php esc_html_e( 'Terms &amp; conditions apply', 'mba-admission-guide' ); ?></span>
          </span>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="smu-schol-bar">
      <?php mbag_icon( 'star' ); ?>
      <p><?php esc_html_e( 'Scholarships are commonly available for defence personnel, government employees, differently-abled applicants and meritorious students.', 'mba-admission-guide' ); ?></p>
      <button type="button" class="smu-btn smu-btn-orange" data-mbag-popup
        data-mbag-popup-title="<?php echo esc_attr( sprintf( /* translators: %s: university name. */ __( 'Fees — %s', 'mba-admission-guide' ), $mbag_smu_name ) ); ?>"
        data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will send the current fee structure, EMI and scholarship options.', 'mba-admission-guide' ); ?>"
        data-mbag-source="<?php esc_attr_e( 'SMU fees — Check eligibility', 'mba-admission-guide' ); ?>"
        data-mbag-university="<?php echo esc_attr( $mbag_smu_name ); ?>"><?php esc_html_e( 'Check Eligibility', 'mba-admission-guide' ); ?> <span class="smu-arr" aria-hidden="true">&rarr;</span></button>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========== 5 · ADMISSION PROCESS ========== -->
<section class="smu-section<?php echo esc_attr( $mbag_smu_bg() ); ?>" id="admission">
  <div class="smu-shell">
    <div class="smu-center">
      <span class="smu-eyebrow"><?php esc_html_e( 'How to Apply', 'mba-admission-guide' ); ?></span>
      <h2><?php esc_html_e( 'Admission', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Process', 'mba-admission-guide' ); ?></span></h2>
      <div class="smu-gold-bar"></div>
      <p class="smu-lede" style="margin-top:24px;max-width:46ch"><?php
        printf(
          /* translators: %s: university name. */
          esc_html__( 'A simple four-step process to begin your journey with %s.', 'mba-admission-guide' ),
          esc_html( $mbag_smu_name )
        );
      ?></p>
    </div>

    <div class="smu-timeline">
      <?php for ( $mbag_smu_n = 1; $mbag_smu_n <= 4; $mbag_smu_n++ ) : ?>
        <div class="smu-tl">
          <span class="smu-tl-dot"><?php echo esc_html( number_format_i18n( $mbag_smu_n ) ); ?></span>
          <span class="smu-tl-tag"><?php
            printf(
              /* translators: %s: step number. */
              esc_html__( 'Step %s', 'mba-admission-guide' ),
              esc_html( number_format_i18n( $mbag_smu_n ) )
            );
          ?></span>
        </div>
      <?php endfor; ?>
    </div>

    <div class="smu-step-cards">
      <div class="smu-step-card">
        <span class="smu-ic"><?php mbag_icon( 'laptop' ); ?></span>
        <h3><?php esc_html_e( 'Choose a Programme', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Pick the programme and register by filling in your basic details.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-step-card">
        <span class="smu-ic"><?php mbag_icon( 'documents' ); ?></span>
        <h3><?php esc_html_e( 'Provide Educational Details', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Fill in your education and work experience details.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-step-card">
        <span class="smu-ic"><?php mbag_icon( 'chart' ); ?></span>
        <h3><?php esc_html_e( 'Pay the Programme Fee', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Pay the admission fee for the first semester or for the full programme.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="smu-step-card">
        <span class="smu-ic"><?php mbag_icon( 'download' ); ?></span>
        <h3><?php esc_html_e( 'Upload Documents', 'mba-admission-guide' ); ?></h3>
        <p><?php esc_html_e( 'Upload the supporting documents and submit your application to complete the process.', 'mba-admission-guide' ); ?></p>
      </div>
    </div>

    <div class="smu-steps-foot">
      <button type="button" class="smu-btn smu-btn-orange smu-btn-pill" data-mbag-popup
        data-mbag-popup-title="<?php echo esc_attr( sprintf( /* translators: %s: university name. */ __( 'Start your admission — %s', 'mba-admission-guide' ), $mbag_smu_name ) ); ?>"
        data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will guide you through each step.', 'mba-admission-guide' ); ?>"
        data-mbag-source="<?php esc_attr_e( 'SMU admission — Start process', 'mba-admission-guide' ); ?>"
        data-mbag-university="<?php echo esc_attr( $mbag_smu_name ); ?>"><?php esc_html_e( 'Start Your Admission Process', 'mba-admission-guide' ); ?></button>
      <?php if ( '' !== $mbag_smu_mode ) : ?>
        <small><?php echo esc_html( $mbag_smu_mode ); ?></small>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ========== DEGREE ========== -->
<section class="smu-section smu-degree<?php echo esc_attr( $mbag_smu_bg() ); ?>">
  <div class="smu-shell smu-deg-grid">
    <div>
      <span class="smu-eyebrow"><?php esc_html_e( 'The Degree', 'mba-admission-guide' ); ?></span>
      <h2><?php esc_html_e( 'Get a Prestigious', 'mba-admission-guide' ); ?> <span class="smu-h-orange smu-h-orange--inline"><?php esc_html_e( 'Degree', 'mba-admission-guide' ); ?></span></h2>
      <div class="smu-gold-bar"></div>
      <p class="smu-lede" style="margin-top:24px"><?php
        printf(
          /* translators: %s: university name. */
          esc_html__( 'The online degrees offered by %s are awarded on the same terms as their on-campus equivalents. Recognition depends on the approvals the university holds for the current intake — ask us and we will confirm the position in writing before you pay anything.', 'mba-admission-guide' ),
          esc_html( $mbag_smu_name )
        );
      ?></p>
      <?php if ( $mbag_smu_approvals ) : ?>
      <div class="smu-pills">
        <?php foreach ( $mbag_smu_approvals as $mbag_smu_approval ) : ?>
          <span class="smu-pill"><?php echo esc_html( $mbag_smu_approval ); ?></span>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="smu-cert">
      <?php $mbag_smu_cert = mbag_smu_img( 'certificate.webp' ); ?>
      <?php if ( $mbag_smu_has_cert ) : ?>
        <?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( sprintf( /* translators: %s: university name. */ __( '%s degree certificate', 'mba-admission-guide' ), $mbag_smu_name ) ) ) ); ?>
      <?php elseif ( '' !== $mbag_smu_cert ) : ?>
        <img src="<?php echo esc_url( $mbag_smu_cert ); ?>" width="1100" height="842" decoding="async" loading="lazy"
          alt="<?php echo esc_attr( sprintf( /* translators: %s: university name. */ __( 'Sample %s degree certificate', 'mba-admission-guide' ), $mbag_smu_name ) ); ?>">
      <?php else : ?>
        <div>
          <div class="smu-seal"><?php esc_html_e( 'DEGREE', 'mba-admission-guide' ); ?></div>
          <p><?php esc_html_e( 'Set a featured image on this page to show the degree certificate here.', 'mba-admission-guide' ); ?></p>
        </div>
      <?php endif; ?>
      <?php if ( '' !== $mbag_smu_legacy ) : ?>
        <div class="smu-legacy"><?php echo esc_html( $mbag_smu_legacy ); ?><span><?php esc_html_e( 'of legacy', 'mba-admission-guide' ); ?></span></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ========== FINAL CTA ========== -->
<div class="smu-final<?php echo esc_attr( $mbag_smu_bg() ); ?>">
  <div class="smu-shell">
    <div class="smu-cta">
      <span class="smu-cta__glow" aria-hidden="true"></span>

      <div class="smu-cta__body">
        <span class="smu-cta__eyebrow"><?php esc_html_e( 'Free counselling', 'mba-admission-guide' ); ?></span>

        <h2><?php esc_html_e( 'Still deciding between specialisations?', 'mba-admission-guide' ); ?></h2>

        <p><?php esc_html_e( 'That is exactly what a counselling call is for. Fifteen minutes, no obligation, and you will know whether this programme fits your plan.', 'mba-admission-guide' ); ?></p>

        <div class="smu-cta__actions">
          <button type="button" class="smu-btn smu-btn-orange smu-btn-pill" data-mbag-popup
            data-mbag-popup-title="<?php esc_attr_e( 'Book your free counselling call', 'mba-admission-guide' ); ?>"
            data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will call you back.', 'mba-admission-guide' ); ?>"
            data-mbag-source="<?php esc_attr_e( 'SMU final CTA — Book call', 'mba-admission-guide' ); ?>"
            data-mbag-university="<?php echo esc_attr( $mbag_smu_name ); ?>"><?php esc_html_e( 'Book My Free Call', 'mba-admission-guide' ); ?></button>

          <a class="smu-btn smu-btn-ghost smu-btn-pill" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>">
            <?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?>
          </a>
        </div>

        <ul class="smu-cta__trust">
          <li><?php esc_html_e( '100% free, always', 'mba-admission-guide' ); ?></li>
          <li><?php esc_html_e( 'No spam, no obligation', 'mba-admission-guide' ); ?></li>
          <li><?php esc_html_e( 'Takes about 15 minutes', 'mba-admission-guide' ); ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>

</div><!-- .smu -->

<?php get_footer( 'landing' ); ?>
