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

Accepted extensions: .svg .png .webp .jpg .jpeg  (checked in that order)

WHERE TO PUT THEM
Prefer  wp-content/uploads/university-logos/
Files in this theme folder are DELETED when the theme is re-uploaded or
updated. The uploads folder survives. Both work; uploads is checked first.

FILE GUIDANCE
- Square-ish, transparent PNG or SVG, at least 120x120px.
- Logos are contained, never cropped, so a wide logo will letterbox
  inside the square plate rather than being cut.
- A university with no file here simply shows no logo — the card is the
  name and its tag. So you can add logos one at a time without any card
  looking half-finished.

ADDING OR REMOVING A UNIVERSITY
Two places, and they must match:
  functions.php  mbag_university_slugs()   -> the slug
  js/main.js     the U array at the top    -> name, lettermark, slug, tag,
                                              and the two bullet points
The cards, the comparison table and the university dropdown in the middle
form all read from that one array, so they stay in sync automatically.

Use each university's official logo per their brand guidelines.
