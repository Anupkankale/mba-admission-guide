<?php
/**
 * Template Name: Manipal Jaipur Landing
 * Template Post Type: page
 *
 * Dedicated landing page for the Manipal University Jaipur Online MBA.
 *
 * The factual blocks — approvals, duration, eligibility, fee — read from
 * Appearance > Customize > Manipal Jaipur Landing and each one hides itself
 * when its field is empty. Nothing on this page asserts a number the site
 * owner has not entered, which matters because paid traffic lands here.
 *
 * The page body from the editor, if any, renders as an extra prose section
 * between the programme snapshot and the specializations.
 *
 * @package MBA_Admission_Guide
 */

get_header();

$mbag_muj_name        = __( 'Manipal University Jaipur', 'mba-admission-guide' );
$mbag_muj_approvals   = mbag_muj_list( 'approvals' );
$mbag_muj_duration    = mbag_muj_field( 'duration' );
$mbag_muj_mode        = mbag_muj_field( 'mode' );
$mbag_muj_eligibility = mbag_muj_field( 'eligibility' );
$mbag_muj_fee         = mbag_muj_field( 'fee' );
$mbag_muj_emi         = mbag_muj_field( 'emi' );
$mbag_muj_specs       = mbag_muj_specializations();

// The snapshot strip only earns its space once at least one fact exists.
$mbag_muj_snapshot = array_filter( array(
	__( 'Duration', 'mba-admission-guide' )    => $mbag_muj_duration,
	__( 'Mode', 'mba-admission-guide' )        => $mbag_muj_mode,
	__( 'Eligibility', 'mba-admission-guide' ) => $mbag_muj_eligibility,
	__( 'Indicative fee', 'mba-admission-guide' ) => $mbag_muj_fee,
) );
?>

<!-- ========== HERO ========== -->
<section class="hero" id="top">
  <span class="orb orb--a" aria-hidden="true"></span><span class="orb orb--b" aria-hidden="true"></span>
  <div class="container hero__in">
    <div>
      <span class="pill"><span class="pulse-dot" aria-hidden="true"></span> <?php esc_html_e( 'Online MBA Admission 2026 · Open', 'mba-admission-guide' ); ?></span>

      <h1><?php
        printf(
          /* translators: %s: university name wrapped in the gradient span. */
          esc_html__( 'Online MBA at %s', 'mba-admission-guide' ),
          '<span class="grad">' . esc_html( $mbag_muj_name ) . '</span>'
        );
      ?></h1>

      <p class="hero__sub"><?php esc_html_e( 'Understand the programme, eligibility, specializations and fees — and get independent guidance before you apply.', 'mba-admission-guide' ); ?></p>

      <?php if ( $mbag_muj_approvals ) : ?>
      <ul class="usp">
        <?php foreach ( $mbag_muj_approvals as $mbag_muj_item ) : ?>
          <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> <?php echo esc_html( $mbag_muj_item ); ?></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>

      <div class="hero__cta">
        <a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--hot btn--lg shine"><?php esc_html_e( 'Enquire Now', 'mba-admission-guide' ); ?></a>
        <?php mbag_brochure_button( 'btn btn--glass btn--lg', __( 'Download Brochure', 'mba-admission-guide' ) ); ?>
      </div>
    </div>

    <!-- LEAD FORM -->
    <div class="card" id="apply">
      <div class="card__head">
        <h2><?php esc_html_e( 'Get Free Counselling', 'mba-admission-guide' ); ?></h2>
        <p><?php esc_html_e( 'Share a few details and a counsellor will call you back about this programme.', 'mba-admission-guide' ); ?></p>
      </div>
      <div class="card__body">
        <?php
        /* Reuses the hero lead form assigned in Customize > Lead Forms. When
           no Contact Form 7 form is assigned, point the visitor at the form
           that definitely exists rather than printing an empty card. */
        if ( ! mbag_lead_form( 'hero' ) ) :
        ?>
          <p class="lead"><?php esc_html_e( 'Call or WhatsApp us and a counsellor will take you through eligibility, fees and the admission process.', 'mba-admission-guide' ); ?></p>
          <p><a class="btn btn--hot btn--block" href="tel:<?php echo esc_attr( mbag_phone_link() ); ?>"><?php mbag_icon( 'phone' ); ?> <?php echo esc_html( mbag_phone_display() ); ?></a></p>
        <?php endif; ?>
        <p class="secure"><?php mbag_icon( 'secure' ); ?> <?php esc_html_e( 'Your details stay private. No spam.', 'mba-admission-guide' ); ?></p>
      </div>
    </div>
  </div>
</section>

<?php if ( $mbag_muj_snapshot ) : ?>
<!-- ========== PROGRAMME SNAPSHOT ========== -->
<section class="section" id="snapshot">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow"><?php esc_html_e( 'Programme snapshot', 'mba-admission-guide' ); ?></span>
      <h2 class="h2"><?php esc_html_e( 'The Programme at a Glance', 'mba-admission-guide' ); ?></h2>
    </div>
    <div class="mujfacts">
      <?php foreach ( $mbag_muj_snapshot as $mbag_muj_label => $mbag_muj_value ) : ?>
        <div class="mujfact rv">
          <span><?php echo esc_html( $mbag_muj_label ); ?></span>
          <b><?php echo esc_html( $mbag_muj_value ); ?></b>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if ( '' !== $mbag_muj_emi ) : ?>
      <p class="note rv"><?php echo esc_html( $mbag_muj_emi ); ?></p>
    <?php endif; ?>
    <p class="mujdisc rv"><?php esc_html_e( 'Fees and programme details are indicative and change from intake to intake. Confirm the current figures with a counsellor before you apply.', 'mba-admission-guide' ); ?></p>
  </div>
</section>
<?php endif; ?>

<?php
// Anything typed into the page editor renders here, or nothing at all.
while ( have_posts() ) :
	the_post();

	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="section tint">
		  <div class="container">
		    <div class="mujprose rv"><?php the_content(); ?></div>
		  </div>
		</section>
		<?php
	endif;
endwhile;
?>

<!-- ========== WHY ========== -->
<section class="section" id="why">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow"><?php esc_html_e( 'Why this programme', 'mba-admission-guide' ); ?></span>
      <h2 class="h2"><?php esc_html_e( 'Built Around a Working Schedule', 'mba-admission-guide' ); ?></h2>
    </div>
    <div class="fgrid">
      <div class="feat rv"><span class="fico"><?php mbag_icon( 'anywhere' ); ?></span><h3><?php esc_html_e( 'Study from anywhere', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'An online format built for people who cannot stop working to study.', 'mba-admission-guide' ); ?></p></div>
      <div class="feat rv"><span class="fico"><?php mbag_icon( 'flexible' ); ?></span><h3><?php esc_html_e( 'Flexible schedule', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Recorded and live sessions you can fit around a job.', 'mba-admission-guide' ); ?></p></div>
      <div class="feat rv"><span class="fico"><?php mbag_icon( 'target' ); ?></span><h3><?php esc_html_e( 'Specialization choice', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Pick a stream that matches the role you are aiming at next.', 'mba-admission-guide' ); ?></p></div>
      <div class="feat rv"><span class="fico"><?php mbag_icon( 'documents' ); ?></span><h3><?php esc_html_e( 'Guided application', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Help with eligibility, documents and each step of the form.', 'mba-admission-guide' ); ?></p></div>
    </div>
  </div>
</section>

<!-- ========== SPECIALIZATIONS ========== -->
<section class="section tint" id="specializations">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow"><?php esc_html_e( 'Specializations', 'mba-admission-guide' ); ?></span>
      <h2 class="h2"><?php esc_html_e( 'Choose Your Specialization', 'mba-admission-guide' ); ?></h2>
      <p class="lead"><?php esc_html_e( 'Not sure which stream fits your background? Ask a counsellor before you commit to one.', 'mba-admission-guide' ); ?></p>
    </div>
    <div class="chips rv">
      <?php foreach ( $mbag_muj_specs as $mbag_muj_spec ) : ?>
        <button class="chip" type="button" data-mbag-popup
          data-mbag-popup-title="<?php echo esc_attr( sprintf( __( '%s — Online MBA', 'mba-admission-guide' ), $mbag_muj_spec ) ); ?>"
          data-mbag-popup-sub="<?php esc_attr_e( 'Share your details and a counsellor will talk you through this specialization.', 'mba-admission-guide' ); ?>"
          data-mbag-source="<?php echo esc_attr( sprintf( __( 'MUJ specialization — %s', 'mba-admission-guide' ), $mbag_muj_spec ) ); ?>"
          data-mbag-university="<?php echo esc_attr( $mbag_muj_name ); ?>"><?php echo esc_html( $mbag_muj_spec ); ?></button>
      <?php endforeach; ?>
    </div>
    <div class="center rv"><a href="<?php echo esc_url( mbag_anchor( 'apply' ) ); ?>" class="btn btn--dark btn--lg"><?php esc_html_e( 'Get Specialization Guidance', 'mba-admission-guide' ); ?></a></div>
  </div>
</section>

<!-- ========== PROCESS ========== -->
<section class="section">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow"><?php esc_html_e( 'How it works', 'mba-admission-guide' ); ?></span>
      <h2 class="h2"><?php esc_html_e( 'Four Steps to Your Admission', 'mba-admission-guide' ); ?></h2>
    </div>
    <div class="steps">
      <div class="step rv"><b>1</b><h3><?php esc_html_e( 'Share your details', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Tell us your education background and the stream you are considering.', 'mba-admission-guide' ); ?></p></div>
      <div class="step rv"><b>2</b><h3><?php esc_html_e( 'Get a counsellor call', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'We check your eligibility and answer questions within one working day.', 'mba-admission-guide' ); ?></p></div>
      <div class="step rv"><b>3</b><h3><?php esc_html_e( 'Confirm the fit', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Compare this programme honestly against the other options open to you.', 'mba-admission-guide' ); ?></p></div>
      <div class="step rv"><b>4</b><h3><?php esc_html_e( 'Apply with support', 'mba-admission-guide' ); ?></h3><p><?php esc_html_e( 'Help with documents, the application steps and fee payment.', 'mba-admission-guide' ); ?></p></div>
    </div>
  </div>
</section>

<!-- ========== FAQ ========== -->
<section class="section tint" id="faqs">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow"><?php esc_html_e( 'FAQs', 'mba-admission-guide' ); ?></span>
      <h2 class="h2"><?php esc_html_e( 'Manipal Jaipur Online MBA — Questions', 'mba-admission-guide' ); ?></h2>
    </div>
    <?php
    /* Static markup. The accordion in main.js is delegated and scoped to the
       nearest .faq, so this behaves exactly like the JS-built front-page list.
       Answers are deliberately written to point at a counsellor rather than
       state figures this template cannot verify. */
    $mbag_muj_faq = array(
      array(
        __( 'Is this Online MBA valid for jobs and further study?', 'mba-admission-guide' ),
        __( 'Recognition depends on the approvals the university holds for the current intake. Ask us and we will confirm the position in writing before you pay anything.', 'mba-admission-guide' ),
      ),
      array(
        __( 'How long does the programme take?', 'mba-admission-guide' ),
        '' !== $mbag_muj_duration
          ? sprintf( /* translators: %s: duration. */ __( 'Typically %s. Confirm the current schedule with a counsellor, as intakes vary.', 'mba-admission-guide' ), $mbag_muj_duration )
          : __( 'Most online MBA programmes run about two years. Ask a counsellor for the exact schedule of the current intake.', 'mba-admission-guide' ),
      ),
      array(
        __( 'What are the fees?', 'mba-admission-guide' ),
        '' !== $mbag_muj_fee
          ? sprintf( /* translators: %s: fee. */ __( 'Indicatively %s for the full programme. Fees change between intakes, so treat this as a starting point and confirm before applying.', 'mba-admission-guide' ), $mbag_muj_fee )
          : __( 'Fees change from intake to intake, so we do not publish a figure that might be out of date. Ask a counsellor for the current fee and any payment options.', 'mba-admission-guide' ),
      ),
      array(
        __( 'Am I eligible?', 'mba-admission-guide' ),
        '' !== $mbag_muj_eligibility
          ? esc_html( $mbag_muj_eligibility )
          : __( 'Most online MBA programmes ask for a bachelor\'s degree from a recognised university. Send us your marksheets and we will tell you where you stand.', 'mba-admission-guide' ),
      ),
      array(
        __( 'Do you charge for this guidance?', 'mba-admission-guide' ),
        __( 'No. Counselling and application support are free. We are an independent platform, not the university.', 'mba-admission-guide' ),
      ),
    );
    ?>
    <div class="faq">
      <?php foreach ( $mbag_muj_faq as $mbag_muj_i => $mbag_muj_row ) : ?>
        <div class="fitem rv">
          <button class="fq" type="button" aria-expanded="false" aria-controls="muj-fa<?php echo (int) $mbag_muj_i; ?>"><?php echo esc_html( $mbag_muj_row[0] ); ?></button>
          <div class="fa" id="muj-fa<?php echo (int) $mbag_muj_i; ?>" role="region"><p><?php echo esc_html( $mbag_muj_row[1] ); ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php get_template_part( 'template-parts/cta-band' ); ?>

<?php get_footer(); ?>
