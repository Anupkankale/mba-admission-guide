# Contact Form 7 setup — MBA Admission Guide

This theme is a **classic theme**, and Contact Form 7 works with it exactly as it
works anywhere else. You do not need a block/FSE theme to use plugins.

The three lead forms on the page (hero, middle, bottom) are now **slots**. Assign a
CF7 form to a slot and it replaces the static form. Leave a slot blank and the
original static markup stays, so the page never breaks half-finished.

---

## 1. Install the plugins

| Plugin | Why you need it |
|---|---|
| **Contact Form 7** | The forms themselves. |
| **Flamingo** (same author) | **Important.** CF7 does *not* save submissions anywhere. If an email fails, the lead is gone forever. Flamingo stores every submission in the database. |
| **FluentSMTP** or **WP Mail SMTP** | **Important.** PHP `mail()` on shared hosting is unreliable and lands in spam. Route mail through a real SMTP service or you will lose leads silently. |
| **Honeypot for Contact Form 7** | Spam filtering without making users solve a puzzle. |

---

## 2. Create the form

Contact → Add New. Name it `Hero — Free Counselling`. Paste this into the **Form** tab:

```html
<div class="field">
  <label>Full Name <span class="req">*</span></label>
  [text* your-name placeholder "Enter your full name"]
</div>

<div class="row">
  <div class="field">
    <label>Mobile Number <span class="req">*</span></label>
    [tel* phone minlength:10 maxlength:10 placeholder "10-digit mobile"]
  </div>
  <div class="field">
    <label>City</label>
    [text city placeholder "Your city"]
  </div>
</div>

<div class="field">
  <label>Email Address <span class="req">*</span></label>
  [email* your-email placeholder "you@example.com"]
</div>

<div class="row">
  <div class="field">
    <label>Graduation Status <span class="req">*</span></label>
    [select* graduation include_blank "Final year student" "Graduate" "Post Graduate" "Working professional" "Other"]
  </div>
  <div class="field">
    <label>Specialization <span class="req">*</span></label>
    [select* spec include_blank "Marketing" "Finance" "Human Resource Management" "Business Analytics" "Operations Management" "International Business" "IT / Information Systems" "Not decided yet"]
  </div>
</div>

[submit class:btn class:btn--hot class:btn--block class:btn--lg class:shine "Get Free Counselling →"]

<p class="secure">🔒 We use your details only for admission guidance. No spam, ever.</p>
[honeypot website-url]
```

Keep the field name **`spec`** — the theme's specialization chips scroll to the form
and preselect that select by name.

For the **bottom** form, drop the `city` and `graduation` fields; for the **middle**
form, add a university select:

```
[select universities include_blank "Amity University Online" "Manipal University Jaipur" "Sikkim Manipal University" "VIT University" "NMIMS Online" "GLA University Online" "Dayananda Sagar University Online"]
```

Keep this list in step with the `U` array in `js/main.js` — that array drives
the cards, the comparison table and the theme's own university dropdown.

---

## 3. Mail tab

```
To:        your-inbox@yourdomain.com
From:      MBA Admission Guide <no-reply@yourdomain.com>
Subject:   New Online MBA lead — [your-name] ([spec])
Reply-To:  [your-email]

Message body:
Name:            [your-name]
Mobile:          [phone]
Email:           [your-email]
City:            [city]
Graduation:      [graduation]
Specialization:  [spec]

Page: [_url]
Sent: [_date] [_time]
IP:   [_remote_ip]
```

The **From** address must be on your own domain. Using the visitor's address there
fails SPF/DKIM and the mail gets rejected or spam-foldered.

Tick **Use Flamingo** at the bottom of the Mail tab if the option appears.

---

## 4. Assign the form to a slot

Contact → Forms shows a shortcode like `[contact-form-7 id="a1b2c3d" title="Hero"]`.
Copy just the value inside `id="..."`.

Then: **Appearance → Customize → Lead Forms** and paste that ID into:

- Hero form (top of page)
- Talk to a Counsellor form (middle of page)
- Final CTA form (bottom of page)

Publish. The static form is now replaced by CF7 in that slot.

You can use one CF7 form for all three slots, but three separate forms are better —
the subject line then tells you which section the lead came from, which is the
cheapest conversion data you will ever get.

---

## 5. Check it works

1. Submit a test lead from each slot.
2. Confirm the email arrives — check spam.
3. Confirm it appears under **Flamingo → Inbound Messages**.
4. Confirm validation errors look right (submit empty, check the red messages).

---

## Notes

- Styling is already handled. The theme renders CF7 with `html_class="form mbag-form"`,
  so CF7 inherits the existing input, label and button design. The `wpcf7-*` classes
  for validation tips and the response banner are styled in `style.css` under the
  `CONTACT FORM 7` section.
- The old JS mock submit (`setTimeout` + fake success message) only runs on the static
  fallback forms. CF7 forms handle their own AJAX submit and are untouched by it.
- The phone field is still forced to 10 digits by `js/main.js`, on CF7 forms too.

---

## 6. Redirect to the Thank You page

CF7 removed `on_sent_ok` in version 5.0, so redirects are handled by the theme
on the supported `wpcf7mailsent` DOM event — no extra plugin needed.

1. Create the Thank You page (see **PAGES-SETUP.md**, section 2).
2. **Appearance → Customize → Lead Forms → Thank You page** → select it.

Every form slot then redirects there on a successful send. Leave the setting
empty and nothing redirects — CF7's own inline success message stays, which is
also a valid setup while you are still testing.

Keep the CF7 mail step working even after the redirect is live: the redirect is
front-end only, and the lead is still recorded by the Mail tab and Flamingo.

---

## 7. The lead popup

A fourth form slot, **Popup form**, drives a modal that opens on three
triggers. It ships enabled and pointed at form ID `570b038`.

### Triggers

| Trigger | Default | Where to change it |
|---|---|---|
| After a delay | **15 seconds** | Customize → Lead Forms → *Open after (seconds)* |
| At scroll depth | **50%** | Customize → Lead Forms → *Open at scroll depth (%)* |
| Button or link | always | add `data-mbag-popup` or the class `mbag-popup` |

Whichever automatic trigger fires first wins; set either to `0` to turn it
off. The automatic triggers fire **once per browser session** and never on
the Thank You page — someone who already submitted should not be asked
again. Button clicks always open it, session or not.

### Opening it from a button

```html
<a href="#" class="btn btn--hot" data-mbag-popup>Get Free Counselling</a>
```

From **Appearance → Menus**, use the CSS class instead: enable *CSS Classes*
in Screen Options and add `mbag-popup` to the menu item. Menu items accept a
class but not a data attribute.

Every `#apply` and `#talk` CTA opens the popup automatically, on the landing
page and everywhere else — those are the two lead asks. Navigation anchors
(`#compare`, `#universities`, `#specializations`, `#faqs`) are deliberately
left alone: they move the visitor around the page, they are not asks.

The inline hero, middle and final forms still sit on the landing page and
still work; they are now reached by scrolling rather than by the buttons.

Two front-page controls are left alone on purpose: the per-university
**Get Details** buttons and the **specialization chips**. Both pre-fill the
university or specialization into the inline form, and a generic popup
would throw that context away. (Each university card also has an **Apply
Now** button that *does* open the popup, carrying that university with it —
so both paths are covered.) To change any of this, edit the one
`TRIGGERS` selector at the top of the popup block in `js/main.js`.

### If the popup does not appear

1. **Check the form ID exists.** Contact → Forms, copy the value inside
   `id="…"`, paste it into Customize → Lead Forms → *Popup form*. A form ID
   from a different site will not resolve — the theme checks the form exists
   and renders nothing rather than showing CF7's "Contact form not found".
2. **Check it is enabled** — Customize → Lead Forms → *Enable the lead popup*.
3. **It already fired this session.** Open a new private window, or clear
   `sessionStorage` in DevTools.
4. **Contact Form 7 must be active.** No CF7, no popup.

---

## 8. Tracking which button a lead came from

The popup adapts to whatever opened it. The **Download Brochure** rail
button, for example, sets its own heading and sub-heading, so someone who
asked for a brochure is not met with a counselling headline.

Any trigger can also name itself:

```html
<button data-mbag-popup
        data-mbag-popup-title="Download the Brochure"
        data-mbag-popup-sub="We will email the brochure and fee structure."
        data-mbag-source="Brochure download">Download Brochure</button>
```

If your popup CF7 form contains a field named `source`, the theme writes
that `data-mbag-source` value into it before the form is submitted, so the
lead itself records which button produced it. Add this to the form:

```
[hidden source]
```

and to the Mail tab:

```
Came from: [source]
```

With no such field, nothing happens — the attribute is simply ignored.

### Pre-selecting a university

A trigger can also name a university with `data-mbag-university`. The
**Check Current Fee** button in the comparison table uses it, so a fee
enquiry arrives already saying which university it is about:

```html
<button data-mbag-popup
        data-mbag-popup-title="Fees — Amity University Online"
        data-mbag-source="Fee enquiry — Amity University Online"
        data-mbag-university="Amity University Online">Check Current Fee</button>
```

The theme writes it into a `universities` (or `university`) field in the
popup form, if there is one — matching against the options the form
actually offers, so a university missing from your select is left blank
rather than setting an invalid value. Add the select from section 2 to
your popup form to switch this on:

```
[select universities include_blank "Amity University Online" "Manipal University Jaipur" "Sikkim Manipal University" "VIT University" "NMIMS Online" "GLA University Online" "Dayananda Sagar University Online"]
```

---

## 9. Adding the university select to the popup form

Paste this into **Contact → Forms → your popup form → Form tab**, above the
submit button:

```html
<div class="field">
  <label>University of Interest</label>
  [select university first_as_label "Any / help me choose" "Amity University Online" "Manipal University Jaipur" "Sikkim Manipal University" "VIT University" "NMIMS Online" "GLA University Online" "Dayananda Sagar University Online"]
</div>

[hidden source]
```

Then in the **Mail tab**, add these two lines to the message body:

```
University of interest: [university]
Came from: [source]
```

`[hidden]` is part of Contact Form 7 core — no extra plugin needed.

### The option text must match the theme exactly

This is the one thing that breaks quietly. The **Check Current Fee** buttons
pass a university name to the form, and the theme only accepts it if the
select actually offers that option — a near-miss is left blank rather than
setting a value the form would reject. So `Manipal University Jaipur` and
`Manipal University Jaipur Online` are *not* interchangeable.

The names live in the `U` array at the top of `js/main.js`. If you edit a
university name there, edit this select to match.

Either field name works: `university` or `universities`.

### Why `first_as_label`

It makes "Any / help me choose" a placeholder with an empty value, so an
untouched select submits blank instead of silently sending the first
university as though the visitor chose it.
