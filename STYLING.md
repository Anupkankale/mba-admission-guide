# Styling & icon conventions — MBA Admission Guide

Two rules cover most of this file:

1. **A raw hex value appears exactly once**, in the primitive layer of
   `style.css`. Everywhere else uses a token.
2. **Templates never hard-code an icon class.** They call `mbag_icon()`.

---

## 1. Colour tokens

`style.css` opens with four layers. Read them top to bottom; never skip a
layer upward.

| Layer | Example | What it is |
|---|---|---|
| **1. Primitives** | `--navy-900:#0B1235` | The palette. Named by hue + lightness, carries no meaning. The only place a hex literal is allowed. |
| **2. Semantic** | `--color-text-muted:var(--ink-500)` | What a colour is *for*. **New rules should use these.** |
| **3. Legacy aliases** | `--night:var(--navy-900)` | The original short names, kept so the pre-existing component rules keep resolving. Don't add new ones. |
| **4. Scales** | `--space-4`, `--r-lg`, `--z-header` | Spacing, radius, shadow, type, motion, layering. |

### Why the indirection is worth it

Retuning the brand means editing ~20 primitives, not hunting 400 hex
values. And because `--color-accent-text` exists separately from
`--color-accent`, the amber can stay bright as a *button fill* while its
text variant stays dark enough to be readable — one concept, two jobs,
no accidental 3:1 text.

### Rules of thumb

- **One accent.** Amber (`--color-accent`) means "this is the action".
  Using it for decoration devalues every CTA on the page.
- **Muted text is `--color-text-muted`,** never a one-off lighter grey.
- **Dark sections** use `--color-text-invert` / `--color-text-invert-muted`.
- **Spacing** comes off the 4px scale (`--space-1` … `--space-8`).
- **z-index** only from the five `--z-*` tokens. If something needs a new
  layer, add a token rather than a number.
- **Motion** uses `--dur` and `--ease` so nothing feels out of step, and
  everything is disabled under `prefers-reduced-motion`.

### Contrast is a requirement, not a preference

Every text pair in the theme meets **WCAG 2.1 AA** — 4.5:1 for body text,
3:1 for 24px+ or bold 19px+. Two colours were corrected to get there:

| Was | Now | Where |
|---|---|---|
| `#B0630B` on `#FFF3DF` — 4.13:1 | `#9C5709` — **5.06:1** | `.eyebrow`, `.fee` |
| `#16A34A` on white — 3.3:1 | `#12833B` — **4.84:1** | success text (`--color-success`) |

`#16A34A` is still available as `--green-500` for dots and fills, where
contrast rules don't apply. Before changing any colour, check the pair:

```bash
# quick check — any two hex values
python3 -c "
def l(h):
    h=h.lstrip('#');c=[int(h[i:i+2],16)/255 for i in (0,2,4)]
    c=[x/12.92 if x<=.04045 else ((x+.055)/1.055)**2.4 for x in c]
    return .2126*c[0]+.7152*c[1]+.0722*c[2]
a,b=l('#9C5709'),l('#FFF3DF')
print(round((max(a,b)+.05)/(min(a,b)+.05),2))"
```

### Block editor

`add_theme_support( 'editor-color-palette' )` in `functions.php` mirrors
these tokens, so colours picked in the editor are brand colours. If you
retune a primitive, update that array too — it takes literal hex values,
because the editor cannot read CSS custom properties.

---

## 2. Icons — Font Awesome 6 Free

### Using one

```php
<?php mbag_icon( 'phone' ); ?>                        // echo
<?php echo mbag_get_icon( 'arrow-right' ); ?>         // return a string
<?php mbag_icon( 'whatsapp', 'my-class' ); ?>         // extra CSS class
<?php mbag_icon( 'search', '', 'Search' ); ?>         // meaningful, not decorative
```

Output is `<i class="fa-solid fa-phone-volume mbag-i" aria-hidden="true">`.

### Why a helper instead of writing `<i class="fa-solid fa-phone">`

- **Swapping icon sets is one function, not fifty templates.**
- **Names are intent-based** — `'secure'`, `'flexible'`, `'anywhere'` —
  so a later icon change can't leave a template lying about what it shows.
- **Accessibility is handled once.** Every icon in this theme sits beside
  a text label, so it is decorative and gets `aria-hidden="true"`. Pass
  the third argument only when the icon *is* the label; it then renders
  `role="img"` with an `aria-label` instead.

### Adding an icon

Add one line to `mbag_icon_map()` in `functions.php`:

```php
'documents' => 'fa-solid fa-file-lines',
```

Then call `mbag_icon( 'documents' )`. A name that isn't in the map
returns an empty string rather than broken markup.

### How it loads, and why not `all.min.css`

`functions.php` enqueues **three split files** from cdnjs — core, solid,
brands — instead of `all.min.css`. The theme uses no "regular" weight and
no v4 shims, and skipping them saves roughly 40% of the payload. A
`preconnect` hint to cdnjs is added alongside.

**If a plugin already ships Font Awesome** (Elementor, WPForms, and many
themes do), `mbag_load_font_awesome()` detects its handle and stands
down — two copies of Font Awesome is 200KB of duplicate CSS and a
specificity fight. To force it off yourself:

```php
add_filter( 'mbag_load_font_awesome', '__return_false' );
```

### Icons in CSS pseudo-elements

Three decorative marks are drawn from the font in CSS rather than markup
(ticker star, university list bullets, FAQ open/close toggle):

```css
.fq::after{font-family:"Font Awesome 6 Free";font-weight:900;content:"\2b"}
```

**`font-weight:900` is mandatory** — Font Awesome Free Solid only renders
at weight 900; at any other weight you get a blank box. And check the
codepoint against the version you actually load: FA 6 remapped `fa-plus`
from `\f067` to `\2b`, so a codepoint copied from an FA 5 answer online
renders as tofu. Verify before shipping:

```bash
curl -s https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css \
  | grep -oE '\.fa-plus\{[^}]*\}'
```

Codepoints currently used: `\f005` star, `\f00c` check, `\2b` plus,
`\f068` minus — all verified against 6.7.2.

---

## 3. Styling Contact Form 7

CF7 markup varies with whoever built the form — the theme's own `.field` /
`.row` classes, a plugin's `.dxm-*` classes, or no wrappers at all. The
rules in the **POPUP / CF7 FORM STYLING** block are therefore scoped to
`.mbag-form` and target **elements, not wrappers**:

```css
.mbag-form input[type="email"], .mbag-form select, .mbag-form textarea { … }
```

That way a form keeps the theme's look whichever markup it arrives in, and
a rebuilt form does not need its classes re-learned.

Three CF7 quirks the block handles so form authors never have to:

| Quirk | Handled by |
|---|---|
| `wpautop` wraps fields in `<p>` and separates them with `<br>` | `.mbag-form p{margin:0}` and `.mbag-form br{display:none}` |
| Side-by-side fields land inside one `<p>`, not the row wrapper | the grid goes on `.dxm-row>p` |
| `[submit]` renders a bare `<input type="submit">` with no classes | styled by type, not by class |

Native select arrows differ per browser and OS, so `appearance:none` plus an
inline SVG chevron keeps every select identical. The SVG is a `data:` URI —
no extra request, and it cannot 404.

---

## 4. Before you ship a style change

- [ ] No new hex literal outside the primitive layer
- [ ] Any new text colour checked for 4.5:1 contrast
- [ ] Editor palette updated if a primitive changed
- [ ] Bumped `Version:` in `style.css` **and** `MBAG_VERSION` in
      `functions.php` — they drive the `?ver=` cache-buster, and a live
      site with a caching plugin will serve the old CSS without it
