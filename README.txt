MBA Admission Guide — WordPress Theme
======================================

INSTALL
1. In wp-admin go to Appearance > Themes > Add New > Upload Theme.
2. Upload mba-admission-guide.zip and Activate.
3. Go to Settings > General and set "Site Title" to what you want shown
   in the header/footer (e.g. "MBA Admission Guide").

CUSTOMIZE CONTACT DETAILS
The theme ships with +91 63629 46008 as the default number (display,
tel: link and WhatsApp). To change it, go to Appearance > Customize >
Contact Details and set:
  - Displayed phone number
  - Phone number used for the tel: link
  - WhatsApp number (used for the floating WhatsApp button and forms)

SITE LOGO
The theme ships the logo in images/ and uses it in the header and footer.
Three levels, in order:
  1. a logo set in Appearance > Customize > Site Identity (wins)
  2. images/logo.<svg|webp|png|jpg> bundled with the theme
  3. the "M" lettermark, so the brand is never blank

TWO VARIANTS
  images/logo.png        the logo as supplied (dark navy wordmark)
  images/logo-light.png  same mark, wordmark in white
The header and footer are dark navy bars, so the light variant is used
there - the navy wordmark is close to invisible against them. If you drop
in a new logo that is already legible on dark, delete logo-light.* and the
standard file is used everywhere.

Any of .svg .webp .png .jpg works; SVG is preferred when present. The URL
carries the file's timestamp, so a replacement is picked up without a
version bump.

LEFT CTA RAIL (desktop / tablet)
Two vertical buttons pinned to the left edge, above 760px wide:
  Enquire Now       - dials the Contact Details number
  Download Brochure - opens the lead popup, with its own heading
                      ("Download the Brochure") so the modal matches
                      what was clicked
Switch it off in Appearance > Customize > Contact Details.

On phones the rail is hidden - it would overlap the left edge of the
content - and the sticky bottom bar carries all four CTAs instead:
Call / WhatsApp / Brochure / Apply Free. To bring the rail back on
mobile, delete the .rail{display:none} rule in style.css.
If Contact Form 7 is inactive, Download Brochure falls back to the
on-page lead form instead of becoming a dead button.

LEAD POPUP
A modal holding the Popup-slot CF7 form (default ID 570b038). Opens 15
seconds in, at 50% scroll depth, or from any #apply / #talk CTA and any
button marked data-mbag-popup / class mbag-popup. Automatic opens happen once per
browser session and never on the Thank You page. Timings, heading and
on/off are in Appearance > Customize > Lead Forms. See CF7-SETUP.md
section 7.

UNIVERSITY CARDS AND LOGOS
Replace any university logo from Appearance > Customize > University
Logos - one image picker per university, no files, survives updates.
Card text (name, tag, bullets) lives in the U array at the top of
js/main.js. Full guide in UNIVERSITIES.md.

UNIVERSITY LOGOS
The university cards and the scrolling logo strip show a real logo image
when one is available, and nothing at all when it is not — so you can add
logos one at a time without any card looking half-finished.

Drop files named by slug into wp-content/uploads/university-logos/
(preferred — it survives theme updates) or into the theme's
images/universities/ folder. Both README.txt files list the exact
filenames.

ICONS
Icons are Font Awesome 6 Free, loaded from cdnjs. Templates call
mbag_icon( 'phone' ) rather than writing <i> tags, so the icon set can be
swapped in one place. If a plugin already loads Font Awesome, the theme
detects it and stands down. See STYLING.md.

COLOURS
Colours are design tokens in style.css, layered primitives -> semantic ->
legacy aliases. Every text pair meets WCAG AA contrast. Do not add raw
hex values to component rules. See STYLING.md.

MENUS
Go to Appearance > Menus. Two locations are available: "Primary Menu"
(top bar, collapses to a hamburger on mobile) and "Footer Menu". With no
menu assigned, both fall back to Universities / Compare / Specializations
/ FAQs. To link to a landing-page section from any page, add a Custom
Link such as /#compare.

FORMS — CONTACT FORM 7
The three lead forms are now plugin slots. Install Contact Form 7, build
your form, then paste its ID under Appearance > Customize > Lead Forms.
A slot with no ID keeps the built-in static form, so nothing breaks
mid-setup. Full instructions with ready-to-paste form markup and a mail
template are in CF7-SETUP.md.

Also install Flamingo (stores submissions in the database) and an SMTP
plugin (FluentSMTP / WP Mail SMTP). Without these, a failed email means
the lead is lost with no record.

PAGES (Thank You, About, Privacy Policy, Blog...)
The theme now ships the full WordPress template hierarchy, so any page or
post you create in wp-admin renders properly instead of repeating the
landing page. Three page templates are included:

  Thank You (Lead Confirmation) - post-submit page, auto-noindexed, and
                                  set as the CF7 redirect target
  Legal / Policy (Narrow)       - privacy policy, terms, disclaimer
  Full Width (No Hero)          - pages you lay out in Elementor/blocks

Step-by-step instructions are in PAGES-SETUP.md. Read that before you
publish the live site.

FILE STRUCTURE
  style.css         Theme header + all site CSS
  functions.php     Theme supports, enqueues, Customizer, template helpers
  header.php        <head>, ticker, primary nav (with mobile menu), <main>
  front-page.php    The landing page (hero, universities, compare, FAQs)
  index.php         Blog index + fallback template
  page.php          Default page template
  single.php        Single blog post
  archive.php       Category / tag / author / date archives
  search.php        Search results
  searchform.php    Search form markup
  404.php           Not-found page
  comments.php      Comment list + comment form
  footer.php        Footer, WhatsApp button, mobile sticky bar, wp_footer()
  page-templates/   Thank You, Legal, Full Width page templates
  template-parts/   Page hero, post card, CTA band, empty state
  js/main.js        Page interactivity + mobile menu + Thank You redirect
  CF7-SETUP.md      Contact Form 7 setup guide + form markup to copy
  PAGES-SETUP.md    Creating the Thank You page and every other page
  STYLING.md        Colour tokens, contrast rules and icon conventions

The landing page itself is a direct, section-for-section conversion of the
supplied static HTML. Everything around it now follows standard WordPress
template conventions, so plugins and the block editor behave normally.
