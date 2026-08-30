# Creating pages on the live site — MBA Admission Guide

The theme is no longer a single-page theme. It now ships the full WordPress
template hierarchy, so any page or post you create in wp-admin renders with the
site header, footer, WhatsApp button and mobile CTA bar — instead of showing the
landing page again.

---

## 1. Set the front page (do this first)

**Settings → Reading**

- *Your homepage displays* → **A static page**
- *Homepage* → the page you want as the landing page (its content is ignored —
  the landing sections come from `front-page.php`)
- *Posts page* → create an empty page called **Blog** and select it here, if you
  plan to publish articles. Without this, the blog index has no URL.

`front-page.php` always wins on the front page, so the homepage keeps its
landing layout no matter which page you assign.

---

## 2. Create the Thank You page

1. **Pages → Add New**, title it `Thank You`.
2. In the right sidebar → **Page Attributes → Template** →
   **Thank You (Lead Confirmation)**.
3. Optional: type a short paragraph in the editor. It appears as an extra
   section between "What happens next" and "While you wait". Leave it empty and
   that section is skipped entirely.
4. **Publish**.
5. **Appearance → Customize → Lead Forms → Thank You page** → select it →
   **Publish**.

That last step is what wires it up. Every successful Contact Form 7 submit —
hero, middle or bottom form — now redirects to this page automatically, about
0.7s after the success message shows.

The page is set to `noindex, nofollow` automatically. Do not remove that: a
thank-you page that ranks in Google means strangers landing on it and firing
your conversion tag, which quietly ruins the numbers you make decisions on.

### Conversion tracking on the Thank You page

Do not paste `<script>` tags into the page editor. Use a code-snippets plugin or
a child theme and hook into the action the template fires:

```php
add_action( 'mbag_thank_you', function () {
	?>
	<!-- GA4 / Google Ads / Meta Pixel conversion snippet -->
	<script>gtag('event','conversion',{'send_to':'AW-XXXXXXX/XXXX'});</script>
	<?php
} );
```

---

## 3. Create the other pages

**Pages → Add New**, then pick a template under **Page Attributes → Template**:

| Page | Template to choose | What you get |
|---|---|---|
| About Us, Contact, Universities, any content page | **Default template** | Dark title banner with breadcrumbs, readable 860px content column, counselling CTA band at the bottom |
| Privacy Policy, Terms, Refund Policy, Disclaimer | **Legal / Policy (Narrow)** | Same layout, plus a "Last updated" line, and **no** CTA band — quiet and compliant |
| Pages you design in Elementor or the block editor | **Full Width (No Hero)** | No banner, no CTA band, edge-to-edge container so your own layout takes over |

Pages you build in Elementor keep working as they always did — pick Elementor's
own *Full Width* or *Canvas* template on those pages instead.

**Pages worth creating before you go live:** Privacy Policy, Terms &
Conditions, Disclaimer, About Us, Contact Us, Thank You. Google Ads and Meta ad
accounts check for the first three before approving education-lead campaigns.

---

## 4. Menus

**Appearance → Menus** — two locations are available:

- **Primary Menu** — in the top bar. On mobile it collapses into a hamburger.
- **Footer Menu** — the footer links row.

To link a menu item to a section of the landing page, add a **Custom Link**:

| Label | URL |
|---|---|
| Universities | `/#universities` |
| Compare | `/#compare` |
| Specializations | `/#specializations` |
| FAQs | `/#faqs` |

These work from any page — the visitor is taken to the homepage and scrolled to
that section. If you assign no menu, the theme shows those four links by
default, so the header is never empty.

---

## 5. What each template file does

| File | Used for |
|---|---|
| `front-page.php` | The landing page (hero, universities, compare, FAQs, CTAs) |
| `page.php` | Any page with the default template |
| `page-templates/template-thank-you.php` | Thank You / lead confirmation |
| `page-templates/template-legal.php` | Policy and terms pages |
| `page-templates/template-full-width.php` | Page-builder pages |
| `single.php` | A blog post |
| `index.php` | The blog index, and the fallback for anything else |
| `archive.php` | Category, tag, author and date archives |
| `search.php` + `searchform.php` | Search results |
| `404.php` | Broken or removed URLs |
| `comments.php` | Comments, where they are enabled |
| `template-parts/` | Shared pieces: page hero, post cards, CTA band, empty state |

---

## 6. After you go live — quick checklist

- [ ] Settings → Reading: homepage and posts page set
- [ ] Appearance → Customize → **Contact Details**: real phone and WhatsApp number
- [ ] Appearance → Customize → **Lead Forms**: three CF7 form IDs + Thank You page
- [ ] Submit a real test lead and confirm you land on the Thank You page
- [ ] Confirm the lead arrives by email **and** appears in Flamingo
- [ ] Privacy Policy, Terms and Disclaimer pages published and linked in the footer menu
- [ ] Open the site on a phone: hamburger menu, sticky call bar and WhatsApp button all work
