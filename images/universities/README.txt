University logos
================

Drop a logo file here (or, better, in wp-content/uploads/university-logos/)
named after the university's slug. The card and the marquee pick it up on
the next page load — no code change, no admin setting.

  amity.png             Amity University Online
  manipal-jaipur.png    Manipal University Jaipur
  sikkim-manipal.png    Sikkim Manipal University
  vit.png               VIT University
  nmims.png             NMIMS Online
  gla.png               GLA University Online
  dayananda-sagar.png   Dayananda Sagar University Online

Accepted extensions: .svg .avif .webp .png .jpg .jpeg (checked in that order)

WHERE TO PUT THEM
Put them HERE, in the theme. They then travel with the zip / git deploy,
which is what makes them appear on the live site with no extra upload step.
The theme is the source of truth: a theme update restores these files
rather than losing them, because they are committed to the repo.

wp-content/uploads/university-logos/ is still checked FIRST, so you can
override any single logo on the server without touching the theme. Use it
when someone needs to swap a logo without a deploy.

FORMAT NOTE
Prefer .webp or .png over .avif. The live server currently sends .avif as
Content-Type: text/plain because the MIME type is not mapped, which some
browsers refuse to render. Adding this to .htaccess fixes it:

  AddType image/avif .avif

FILE GUIDANCE
- The card plate is 2:1 and the logo is contained inside it, so a wide
  wordmark (roughly 400x200, like the ones already in place) fills it best.
  A square logo still works — it centres and shows at plate height.
- Logos are contained, never cropped, and never upscaled past their own
  size — so a small file looks small rather than fuzzy.
- A university with no file of its own falls back to placeholder.png in
  this folder — a neutral building mark, so the row of cards stays even
  while logos are still arriving. Drop in <slug>.<ext> and the real logo
  takes over automatically; nothing else to change.
- placeholder.png ships with the theme. Per-university logos should go in
  wp-content/uploads/university-logos/ instead, because a theme update
  replaces this folder.

ADDING OR REMOVING A UNIVERSITY
Two places, and they must match:
  functions.php  mbag_university_slugs()   -> the slug
  js/main.js     the U array at the top    -> name, lettermark, slug, tag,
                                              and the two bullet points
The cards, the comparison table and the university dropdown in the middle
form all read from that one array, so they stay in sync automatically.

Use each university's official logo per their brand guidelines.
