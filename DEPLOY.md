# Deploying this theme

The site runs paid ads, so the rule this workflow is built around is:

> **Pushing to GitHub must never change the live site. Only a deliberate
> Deploy click does.**

Host is **Hostinger / hPanel**, which has a built-in Git tool. No plugin, no
FTP, no credentials stored in GitHub.

---

## 0. One-time: publish the repo privately

`gh` is installed but not logged in. The login is interactive, so run these
yourself (in this session, prefix with `!`):

```bash
gh auth login                       # GitHub.com → HTTPS → browser
gh repo create mba-admission-guide --private --source=. --remote=origin --push
git push -u origin production
```

Without `gh`: create the repo on github.com, then

```bash
git remote add origin git@github.com:<you>/mba-admission-guide.git
git push -u origin main
git push -u origin production
```

If your GitHub username is not `Anupkankale`, update the `GitHub Theme URI:`
line in `style.css` to match.

---

## 1. Two branches

| Branch | Role |
|---|---|
| `main` | day-to-day work. Pushing here **never** touches live. |
| `production` | exactly what the server runs. |

Both already exist locally. This separation is the whole safety mechanism —
without it, `main` and the live site are the same thing and every push is a
deploy.

---

## 2. One-time: configure hPanel → Advanced → GIT

- **Repository:** `git@github.com:<you>/mba-admission-guide.git`
- **Branch:** `production`
- **Directory:** `public_html/wp-content/themes/mba-admission-guide`
- hPanel shows an SSH key — paste it into GitHub → your repo →
  **Settings → Deploy keys**. Read-only access is enough.
- **Leave "Auto deployment" / the webhook OFF.**

That last toggle is the difference between this workflow and a typo reaching
paid traffic within seconds. If you ever switch it on, you have opted back
into push-equals-deploy.

**Before the very first deploy:** back up the current theme folder (hPanel →
Files → Backups, or download it from File Manager). The Git tool writes into
that directory and may clear it first, so anything present on the server but
absent from the repo is lost.

`wp-content/uploads/university-logos/` sits outside the theme and is never
touched by a deploy — which is exactly why per-university logo overrides
belong there.

---

## 3. The everyday loop

```bash
cd wp-content/themes/mba-admission-guide

git checkout main
git checkout -b fix/whatever          # optional; small changes can go on main

# ... edit ...
# bump BOTH, every time:
#   style.css     Version: 1.26.0 → 1.26.1
#   functions.php MBAG_VERSION    → '1.26.1'

git commit -am "Describe the change"
git push
```

Nothing has happened to the live site yet. Check the work locally:

```bash
php -S localhost:8090 -t /home/anup/projects/Wordpress
# then open http://localhost:8090/
```

## 4. Release

```bash
git checkout production
git merge --ff-only main
git push
git tag -a v1.26.1 -m "Release 1.26.1" && git push origin v1.26.1
```

Then **hPanel → GIT → Deploy**.

Tag every release. The tag is what rollback aims at; without tags you are
reading commit hashes under pressure.

## 5. Rollback — two commands and a click

```bash
git checkout production
git reset --hard v1.26.0            # the last tag known to be good
git push --force-with-lease
```

Then **hPanel → GIT → Deploy**.

`--force-with-lease`, not `--force`: it refuses if someone else has pushed in
the meantime rather than silently discarding their work.

**Practise this once, deliberately, while nothing is broken.** A rollback you
have never run is not a rollback plan.

---

## 6. Why the version bump is not optional

Hostinger serves this site through its own CDN (`hcdn`), which caches CSS and
JS. The `?ver=` string on those files comes from `Version:` in `style.css` and
`MBAG_VERSION` in `functions.php`.

Deploy without bumping them and the CDN keeps serving the old assets: the code
is on the server, the site looks unchanged, and nothing anywhere explains why.
`.github/workflows/lint.yml` fails a pull request that forgets.

---

## 7. Check `.git` is not readable

The Git tool clones into the web root, so `.git/` ends up inside
`public_html`. An exposed `.git` hands over the entire source history.

Hostinger currently blocks that path at server level (it returns 403 even
before the directory exists). Confirm again right after the first deploy, when
the file is actually there:

```bash
curl -s -o /dev/null -w '%{http_code}\n' \
  https://online-manipalmba.com/wp-content/themes/mba-admission-guide/.git/config
```

Anything other than 403/404 means it is readable. Fix by adding a theme-root
`.htaccess`:

```apache
RedirectMatch 404 /\.git
```

---

## 8. After every deploy, check these four

The universities, comparison table, testimonials and FAQ are all rendered by
`js/main.js`. A JS error does not show as an error — it shows as **empty
sections**, which is precisely the failure mode that has bitten this theme
before.

1. `curl -s https://online-manipalmba.com/ | grep -o 'style.css?ver=[0-9.]*'`
   shows the version you just released.
2. University cards and the comparison table have content.
3. The testimonial carousel scrolls and its dots respond.
4. Submit the hero form; confirm it arrives by email **and** in Flamingo.

---

## 9. Never edit theme files on the server

A deploy replaces the folder. Anything changed through Appearance → Theme File
Editor or File Manager is gone at the next Deploy, with no warning and no copy.

Customizer settings, menus, pages and Contact Form 7 forms live in the
database, not in the theme — those are safe, and are meant to be edited in
wp-admin.
