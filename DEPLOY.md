# Deploying this theme with git

No more zip uploads. Push to GitHub, the site pulls.

---

## 0. One-time: create the private repo

`gh` is installed but not logged in. Run these yourself — the login step is
interactive, so type it into this session with a leading `!`:

```bash
gh auth login                       # choose GitHub.com → HTTPS → browser
gh repo create mba-admission-guide --private --source=. --remote=origin --push
```

Or without `gh`: create the repo on github.com, then

```bash
git remote add origin https://github.com/<you>/mba-admission-guide.git
git push -u origin main
```

If your GitHub username is not `Anupkankale`, update the `GitHub Theme URI`
line in `style.css` to match — the update plugins read it.

---

## 1. Pick how the site pulls

You chose a plugin and a **private** repo. Those two do not combine for free:

| Plugin | Private repos | Cost |
|---|---|---|
| **Git Updater** | **yes**, with a GitHub token | free |
| WP Pusher | paid tiers only | free tier is public repos only |

**Recommended: Git Updater** — free, and private repos work with a personal
access token.

### Git Updater setup

1. Download the latest release zip from
   `https://github.com/afragen/git-updater/releases` and install it under
   **Plugins → Add New → Upload Plugin**. Activate.
2. On GitHub: **Settings → Developer settings → Personal access tokens →
   Fine-grained tokens**. Create one with **Contents: Read-only**, scoped to
   just this repository. Copy it.
3. In WordPress: **Settings → Git Updater → GitHub**, paste the token, Save.
4. **Settings → Git Updater → Install Theme**, enter
   `https://github.com/<you>/mba-admission-guide`, branch `main`, Install.

From then on the theme appears under **Dashboard → Updates** whenever the
version changes, and updates like any other theme.

### If you would rather use WP Pusher

Same idea, but either make the repo public or buy a licence. Install the
plugin, connect your GitHub token, then **WP Pusher → Install Theme**.

---

## 2. The everyday loop

```bash
cd wp-content/themes/mba-admission-guide

git checkout -b fix/popup-copy      # branch per change
# ... edit files ...

# bump BOTH, or the site will never see the update:
#   style.css     Version: 1.12.0 → 1.12.1
#   functions.php MBAG_VERSION    → '1.12.1'

git add -A
git commit -m "Reword the popup sub-heading"
git push -u origin fix/popup-copy
```

Open a pull request, let the Lint action pass, merge to `main`. Then in
WordPress: **Dashboard → Updates → Update Theme**.

Working solo and happy to skip the PR? Commit straight to `main` and push.
The version bump still matters.

### Why the version bump is not optional

Both plugins detect a new release by comparing the `Version:` header in
`style.css` against what is installed. Push without bumping it and the site
sees no change — the code is on GitHub and the site keeps serving the old
theme, with nothing to tell you why. The Lint workflow fails a pull request
that forgets it.

---

## 3. What the CI does

`.github/workflows/lint.yml` runs on every push and PR:

- `php -l` on every PHP file
- `node --check` on `js/main.js`
- brace balance on `style.css` — an unbalanced brace silently kills every
  rule after it, which is exactly how a stylesheet "stops working" with no
  error anywhere
- on PRs only: fails if `Version:` was not bumped

---

## 4. Before the first pull, back up what is live

The live site currently has a zip-uploaded copy. If anyone edited it there
(Appearance → Theme File Editor, or over FTP), the first pull overwrites
those edits. Download the live theme folder once, diff it against this repo,
and carry over anything that only exists on the server.

---

## 5. What is deliberately not in the repo

`.gitignore` excludes `*:Zone.Identifier` (WSL metadata that keeps appearing
beside files copied from Windows), `node_modules/`, `*.log`, `.env*` and
`*.zip`.

Not in the repo either:

- `images/universities/*.png` — university logos you drop in. Put those in
  `wp-content/uploads/university-logos/` instead; that folder survives theme
  updates, and a pull will not remove them.
- `logo-original.png` — kept outside the theme at `~/projects/`.

**A theme update replaces the whole folder.** Anything you add to the theme
directory on the server, but not to this repo, is gone on the next pull.
