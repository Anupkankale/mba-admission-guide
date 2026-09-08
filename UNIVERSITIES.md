# Editing the university cards

The seven cards, the comparison table rows and the university dropdown all
come from **one place**, so a change made once shows up in all three.

---

## 1. Replace a logo — from wp-admin, no files

**Appearance → Customize → University Logos**

There is one image picker per university. Click **Select image**, upload or
choose from the Media Library, **Publish**. Done — it overrides whatever the
theme ships, and it survives theme updates.

Leave a picker empty and the theme falls back to its own file, then to the
placeholder.

**Logo shape:** the card plate is **2:1**, so a wide logo (roughly 400×200)
fills it best. A square logo still works, it just shows smaller. Use **WebP
or PNG**. Avoid AVIF — this server sends `.avif` as `text/plain`, which some
browsers refuse to render. (Fixable with `AddType image/avif .avif` in
`.htaccess`.)

Currently on the placeholder: **Manipal University Jaipur, VIT University,
Dayananda Sagar University Online.**

### The other two ways (if you prefer files)

| Where | When to use it | Survives a theme update? |
|---|---|---|
| Customizer picker | normal case | yes |
| `wp-content/uploads/university-logos/<slug>.webp` | bulk upload over FTP | yes |
| `wp-content/themes/mba-admission-guide/images/universities/<slug>.webp` | ships with the theme, for me to add | yes, it is in the repo |

Checked in that order — the first one found wins.

The slugs are:

```
amity   manipal-jaipur   sikkim-manipal   vit   nmims   gla   dayananda-sagar
```

---

## 2. Change a card's words

Card text lives in **`js/main.js`**, in the `U` array right at the top:

```js
var U=[
 {n:"Amity University Online",   // name on the card, the table and the dropdown
  s:"AU",                        // short code (kept for reference, not shown)
  slug:"amity",                  // must match the logo filename / Customizer key
  t:"Online MBA available",      // the small tag under the name
  p:["Multiple specializations", // the two bullet points
     "Fully online learning format"]},
 ...
];
```

Edit the text, save, upload. That one array drives:

- the university **cards**
- the **comparison table** rows
- the **university dropdown** in the middle form

The tag has two looks: plain green by default, amber if you add `w:1` to
that university — use it for "Check current intake" style wording.

---

## 3. Add or remove a university

Three places, and they must agree:

1. **`js/main.js`** — add an entry to the `U` array (name, slug, tag, bullets).
2. **`functions.php`** — add `'slug' => 'Display Name'` to `mbag_universities()`.
   That drives the Customizer label and the logo lookup.
3. **The CF7 popup form** — add the name to the `[select university …]` list
   so the "Enquire Now" and "Check Current Fee" buttons can pre-select it.
   The option text must match the name in the `U` array exactly.

Then add a logo by any of the three routes above.

---

## 4. What each card button does

| Button | Action |
|---|---|
| **Download Brochure** | Scrolls to the middle form with that university selected |
| **Enquire Now** | Opens the lead popup, carrying that university and a per-university source line |
| **Check Current Fee** (in the table) | Opens the popup with a fee-specific heading |

All three record which university produced the lead, provided the popup form
has a `university` select and a `[hidden source]` field — see CF7-SETUP.md
sections 8 and 9.
