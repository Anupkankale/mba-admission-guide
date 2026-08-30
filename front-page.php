<?php
/**
 * Front page — Online MBA Admission landing page.
 *
 * Shown on the site front page only. Every other request (pages, posts,
 * archives, search, 404) falls through to the dedicated templates below,
 * so new pages created in wp-admin no longer render the landing page.
 */
get_header();
?>

<!-- ========== HERO ========== -->
<section class="hero" id="top">
  <span class="orb orb--a"></span><span class="orb orb--b"></span><span class="orb orb--c"></span>
  <div class="container hero__in">
    <div>
      <span class="pill"><span class="pulse-dot"></span> Online MBA Admission 2026 · Open</span>
      <h1>Find the Right <span class="grad">Online MBA</span> for Your Career</h1>
      <p class="hero__sub">Compare leading Online MBA programs, universities, specializations, fees and eligibility — and get expert guidance for your admission.</p>

      <div class="rating">
        <span class="stars" role="img" aria-label="Rated 4.8 out of 5"><?php mbag_icon( 'star' ); ?><?php mbag_icon( 'star' ); ?><?php mbag_icon( 'star' ); ?><?php mbag_icon( 'star' ); ?><?php mbag_icon( 'star' ); ?></span>
        <b>4.8 / 5</b>
        <span>from 2,400+ learners guided since 2019</span>
      </div>

      <div class="hero__cta">
        <a href="#apply" class="btn btn--hot btn--lg shine">Get MBA Admission Guidance</a>
        <a href="#compare" class="btn btn--glass btn--lg">Compare Universities</a>
      </div>

      <ul class="usp">
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> 7 university options in one place</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Guidance on eligibility &amp; documents</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Specialization shortlisting help</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Support through the admission process</li>
      </ul>
    </div>

    <!-- LEAD FORM -->
    <div class="card" id="apply">
      <div class="card__head">
        <h2>Get Free Counselling</h2>
        <p>Share a few details and a counsellor will guide you on the best-fit Online MBA.</p>
      </div>
      <div class="card__body">
        <div class="timer"><?php mbag_icon( 'clock' ); ?> Today's counselling slots close in <b id="clock">02:00:00</b></div>

        <?php
        /* Slot: hero. Uses the Contact Form 7 form assigned in
           Appearance > Customize > Lead Forms. Falls back to the
           built-in static markup below when no form is assigned. */
        if ( ! mbag_lead_form( 'hero' ) ) : ?>
        <form class="form js-form" novalidate>
            <div class="field">
              <label for="h-name">Full Name <span class="req">*</span></label>
              <input id="h-name" type="text" placeholder="Enter your full name" data-req />
              <span class="emsg">Enter your name</span>
            </div>
            <div class="row">
              <div class="field">
                <label for="h-phone">Mobile Number <span class="req">*</span></label>
                <input id="h-phone" type="tel" inputmode="numeric" maxlength="10" placeholder="10-digit mobile" data-req data-t="phone" />
                <span class="emsg">Enter a valid 10-digit number</span>
              </div>
              <div class="field">
                <label for="h-city">City</label>
                <input id="h-city" type="text" placeholder="Your city" />
                <span class="emsg">Enter your city</span>
              </div>
            </div>
            <div class="field">
              <label for="h-email">Email Address <span class="req">*</span></label>
              <input id="h-email" type="email" placeholder="you@example.com" data-req data-t="email" />
              <span class="emsg">Enter a valid email address</span>
            </div>
            <div class="row">
              <div class="field">
                <label for="h-grad">Graduation Status <span class="req">*</span></label>
                <select id="h-grad" data-req>
                  <option value="">Select status</option>
                  <option>Final year student</option><option>Graduate</option>
                  <option>Post Graduate</option><option>Working professional</option><option>Other</option>
                </select>
                <span class="emsg">Select your status</span>
              </div>
              <div class="field">
                <label for="h-spec">Specialization <span class="req">*</span></label>
                <select id="h-spec" class="js-spec" data-req>
                  <option value="">Select specialization</option>
                </select>
                <span class="emsg">Select a specialization</span>
              </div>
            </div>

            <button type="submit" class="btn btn--hot btn--block btn--lg shine">Get Free Counselling <?php mbag_icon( 'arrow-right' ); ?></button>
            <p class="secure"><?php mbag_icon( 'secure' ); ?> We use your details only for admission guidance. No spam, ever.</p>

            <div class="ok" role="status" aria-live="polite"><?php mbag_icon( 'check', 'ok__i' ); ?><div>Thanks — your details are in. A counsellor will call you within one working day.</div></div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ========== LOGO MARQUEE ========== -->
<section class="strip">
  <div class="strip__label">Universities you can compare here</div>
  <div class="marquee" id="marquee"></div>
</section>

<!-- ========== STATS ========== -->
<section class="stats">
  <div class="container stats__g">
    <div class="stat"><b data-count="9" data-suffix="+">0</b><span>Universities to compare</span></div>
    <div class="stat"><b data-count="7" data-suffix="">0</b><span>MBA specializations</span></div>
    <div class="stat"><b data-count="2400" data-suffix="+">0</b><span>Learners guided</span></div>
    <div class="stat"><b data-count="24" data-suffix="hr">0</b><span>Callback turnaround</span></div>
  </div>
</section>

<!-- ========== UNIVERSITIES ========== -->
<section class="section tint" id="universities">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">Online MBA Universities</span>
      <h2 class="h2">Explore Leading Online MBA Universities</h2>
      <p class="lead">Shortlist from multiple Online MBA and Distance MBA options with counsellor support.</p>
    </div>
    <div class="ugrid" id="ugrid"></div>
    <p class="note rv">Programme availability, fees, eligibility and recognition should be confirmed from each university's official sources before applying.</p>
  </div>
</section>

<!-- ========== WHY ========== -->
<section class="section" id="why">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">Why Online MBA</span>
      <h2 class="h2">Why Choose an Online MBA</h2>
    </div>
    <div class="fgrid">
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'anywhere' ); ?></div><h3>Learn from anywhere</h3><p>Study online without relocating or attending daily classes.</p></article>
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'flexible' ); ?></div><h3>Flexible for working professionals</h3><p>Fit sessions and self-study around your job schedule.</p></article>
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'target' ); ?></div><h3>Multiple specializations</h3><p>Choose a stream aligned with your career direction.</p></article>
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'chart' ); ?></div><h3>Industry-relevant curriculum</h3><p>Management subjects framed around current business practice.</p></article>
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'laptop' ); ?></div><h3>Online exams &amp; learning support</h3><p>Digital study material, online sessions and online assessments.</p></article>
      <article class="feat rv"><div class="fico"><?php mbag_icon( 'rocket' ); ?></div><h3>Career-focused programs</h3><p>Build management skills for growth or a role transition.</p></article>
    </div>
  </div>
</section>

<!-- ========== SPECIALIZATIONS ========== -->
<section class="section tint" id="specializations">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">Specializations</span>
      <h2 class="h2">Popular Online MBA Specializations</h2>
      <p class="lead">Not sure which stream fits your background? Tap one and a counsellor will guide you.</p>
    </div>
    <div class="chips rv" id="chips"></div>
    <div class="center rv"><a href="#talk" class="btn btn--dark btn--lg">Get Specialization Guidance</a></div>
  </div>
</section>

<!-- ========== COMPARE ========== -->
<section class="section" id="compare">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">Compare</span>
      <h2 class="h2">Compare Your Online MBA Options</h2>
      <p class="lead">A quick side-by-side view. Exact fees, eligibility and specialization lists are shared by your counsellor from official university information.</p>
    </div>
    <div class="twrap rv">
      <table>
        <thead><tr>
          <th>University</th><th>Program</th><th>Duration</th><th>Eligibility</th>
          <th>Specializations</th><th>Approx. fee</th><th>Learning format</th><th>Admission process</th>
        </tr></thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>
    <p class="hint">Swipe the table sideways to see all columns <?php mbag_icon( 'arrow-right' ); ?></p>
    <div class="actions rv">
      <a href="#talk" class="btn btn--hot btn--lg shine">Compare MBA Options</a>
      <a href="#talk" class="btn btn--line btn--lg">Check Eligibility</a>
    </div>
  </div>
</section>

<!-- ========== PROCESS ========== -->
<section class="section tint">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">How it works</span>
      <h2 class="h2">Four Steps to Your Admission</h2>
    </div>
    <div class="steps">
      <div class="step rv"><b>1</b><h3>Share your details</h3><p>Fill the form with your education background and preferred stream.</p></div>
      <div class="step rv"><b>2</b><h3>Get a counsellor call</h3><p>We discuss your goals, budget and eligibility within one working day.</p></div>
      <div class="step rv"><b>3</b><h3>Shortlist universities</h3><p>Compare two or three genuine fits instead of guessing from ads.</p></div>
      <div class="step rv"><b>4</b><h3>Apply with support</h3><p>Help with documents, application steps and fee payment.</p></div>
    </div>
  </div>
</section>

<!-- ========== TESTIMONIALS ========== -->
<section class="section">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">Student stories</span>
      <h2 class="h2">What Learners Say</h2>
    </div>
    <div class="tgrid" id="tgrid"></div>
  </div>
</section>

<!-- ========== SPLIT CTA ========== -->
<section class="section final dark" id="talk" style="text-align:left">
  <span class="orb orb--a"></span><span class="orb orb--b"></span>
  <div class="container split" style="position:relative;z-index:2">
    <div class="rv">
      <span class="eyebrow">Free counselling</span>
      <h2 class="h2">Not Sure Which Online MBA Is Right for You?</h2>
      <p class="lead">Tell us your education background and career goals. Our admission counsellor will help you shortlist suitable Online MBA options based on your requirements.</p>
      <ul>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> University shortlisting based on your budget and goals</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Clarity on eligibility and required documents</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Specialization guidance for your career path</li>
        <li><span class="tick"><?php mbag_icon( 'check' ); ?></span> Step-by-step help with the admission process</li>
      </ul>
    </div>

    <div class="card rv">
      <div class="card__head"><h2>Talk to an MBA Counsellor</h2><p>A counsellor will call you to discuss suitable Online MBA options.</p></div>
      <div class="card__body">
        <?php
        /* Slot: talk. Uses the Contact Form 7 form assigned in
           Appearance > Customize > Lead Forms. Falls back to the
           built-in static markup below when no form is assigned. */
        if ( ! mbag_lead_form( 'talk' ) ) : ?>
        <form class="form js-form" novalidate>
            <div class="field">
              <label for="t-name">Full Name <span class="req">*</span></label>
              <input id="t-name" type="text" placeholder="Enter your full name" data-req />
              <span class="emsg">Enter your name</span>
            </div>
            <div class="row">
              <div class="field">
                <label for="t-phone">Mobile Number <span class="req">*</span></label>
                <input id="t-phone" type="tel" inputmode="numeric" maxlength="10" placeholder="10-digit mobile" data-req data-t="phone" />
                <span class="emsg">Enter a valid 10-digit number</span>
              </div>
              <div class="field">
                <label for="t-email">Email Address <span class="req">*</span></label>
                <input id="t-email" type="email" placeholder="you@example.com" data-req data-t="email" />
                <span class="emsg">Enter a valid email address</span>
              </div>
            </div>
            <div class="field">
              <label for="t-uni">University of Interest</label>
              <select id="t-uni"><option value="">Any / help me choose</option></select>
              <span class="emsg">Select a university</span>
            </div>
            <div class="row">
              <div class="field">
                <label for="t-grad">Graduation Status <span class="req">*</span></label>
                <select id="t-grad" data-req>
                  <option value="">Select status</option>
                  <option>Final year student</option><option>Graduate</option>
                  <option>Post Graduate</option><option>Working professional</option><option>Other</option>
                </select>
                <span class="emsg">Select your status</span>
              </div>
              <div class="field">
                <label for="t-spec">Specialization <span class="req">*</span></label>
                <select id="t-spec" class="js-spec" data-req><option value="">Select specialization</option></select>
                <span class="emsg">Select a specialization</span>
              </div>
            </div>
            <button type="submit" class="btn btn--hot btn--block btn--lg shine">Talk to an MBA Counsellor <?php mbag_icon( 'arrow-right' ); ?></button>
            <p class="secure"><?php mbag_icon( 'secure' ); ?> No spam, ever. Your details are used only for admission guidance.</p>
            <div class="ok" role="status" aria-live="polite"><?php mbag_icon( 'check', 'ok__i' ); ?><div>Request received. A counsellor will call you shortly to discuss your options.</div></div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ========== FAQ ========== -->
<section class="section tint" id="faqs">
  <div class="container">
    <div class="head rv">
      <span class="eyebrow">FAQs</span>
      <h2 class="h2">Online MBA — Frequently Asked Questions</h2>
    </div>
    <div class="faq" id="faq"></div>
  </div>
</section>

<!-- ========== FINAL CTA ========== -->
<section class="final">
  <span class="orb orb--b"></span><span class="orb orb--c"></span>
  <div class="container" style="position:relative;z-index:2">
    <span class="eyebrow">Get MBA Details</span>
    <h2 class="h2" style="color:#fff">Get Personalized Online MBA Guidance</h2>
    <p class="lead" style="max-width:620px;margin:14px auto 0;color:#B6C1E2">Submit your details and get help choosing the right university and MBA program.</p>

    <div class="fform">
      <?php
      /* Slot: footer. Uses the Contact Form 7 form assigned in
         Appearance > Customize > Lead Forms. Falls back to the
         built-in static markup below when no form is assigned. */
      if ( ! mbag_lead_form( 'footer' ) ) : ?>
      <form class="form js-form" novalidate>
          <div class="row">
            <div class="field">
              <label for="f-name">Full Name <span class="req">*</span></label>
              <input id="f-name" type="text" placeholder="Enter your full name" data-req />
              <span class="emsg">Enter your name</span>
            </div>
            <div class="field">
              <label for="f-phone">Mobile Number <span class="req">*</span></label>
              <input id="f-phone" type="tel" inputmode="numeric" maxlength="10" placeholder="10-digit mobile" data-req data-t="phone" />
              <span class="emsg">Enter a valid 10-digit number</span>
            </div>
          </div>
          <div class="row">
            <div class="field">
              <label for="f-email">Email Address <span class="req">*</span></label>
              <input id="f-email" type="email" placeholder="you@example.com" data-req data-t="email" />
              <span class="emsg">Enter a valid email address</span>
            </div>
            <div class="field">
              <label for="f-spec">Specialization <span class="req">*</span></label>
              <select id="f-spec" class="js-spec" data-req><option value="">Select specialization</option></select>
              <span class="emsg">Select a specialization</span>
            </div>
          </div>
          <button type="submit" class="btn btn--hot btn--block btn--lg shine">Get Free MBA Counselling <?php mbag_icon( 'arrow-right' ); ?></button>
          <p class="tiny">Your details are used only for admission guidance.</p>
          <div class="ok" role="status" aria-live="polite"><?php mbag_icon( 'check', 'ok__i' ); ?><div>Submitted. We'll reach out with university and program details.</div></div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
