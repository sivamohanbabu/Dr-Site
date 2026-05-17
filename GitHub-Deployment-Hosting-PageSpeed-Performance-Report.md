# GitHub Deployment, Hosting, and PageSpeed Performance Report

Date: 2026-05-17  
Site: https://drranjithneuro.com/  
Repository: https://github.com/sivamohanbabu/Dr-Site  
Hosting: GitHub Pages with custom domain

## Executive Summary

The PageSpeed issue is not caused by a failed GitHub deployment. The latest GitHub Pages deployment completed successfully and is serving the latest `main` branch commit.

The remaining mobile PageSpeed issue is caused by live-site performance and hosting constraints:

- Mobile PageSpeed score from the latest screenshot: Performance `63`, Accessibility `89`, Best Practices `100`, SEO `92`.
- GitHub Pages deployment is successful for commit `aa1794044d21616d5881a932f008b3b095f7b854`.
- DNS points to GitHub Pages IPs.
- The repo contains an `.htaccess` file, but GitHub Pages does not run Apache `.htaccess` rules, so the intended cache, compression, redirect, and security header configuration cannot be relied on in this hosting environment.
- The homepage still has a large render path: Google Fonts, Bootstrap CSS, custom CSS, Font Awesome, many homepage sections, and many image cards.
- A public fetch path showed a bot-verification interstitial for the root domain during review. If Google PageSpeed receives that page instead of the real homepage, performance scoring can be distorted.

## Deployment Chain

Source branch:

- Branch: `main`
- Local HEAD: `aa1794044d21616d5881a932f008b3b095f7b854`
- Remote `origin/main`: `aa1794044d21616d5881a932f008b3b095f7b854`
- Working tree at review time: clean

GitHub Actions / Pages deployment:

- Workflow: `pages build and deployment`
- Run: `25985728950`
- Run URL: https://github.com/sivamohanbabu/Dr-Site/actions/runs/25985728950
- Status: `completed`
- Conclusion: `success`
- Created: `2026-05-17T08:21:26Z`
- Updated: `2026-05-17T08:22:04Z`

Deployment record:

- Deployment ID: `4716881317`
- Environment: `github-pages`
- Ref: `main`
- SHA: `aa1794044d21616d5881a932f008b3b095f7b854`
- Status: `success`
- Environment URL reported by GitHub: `http://drranjithneuro.com/`
- Success time: `2026-05-17T08:22:04Z`

Conclusion: GitHub has deployed the current `main` branch successfully. The PageSpeed problem is downstream of deployment success.

## Hosting and DNS Findings

The domain is configured as an apex custom domain through GitHub Pages.

Observed DNS records:

- `185.199.108.153`
- `185.199.109.153`
- `185.199.110.153`
- `185.199.111.153`

These are GitHub Pages IPs.

GitHub Pages is a static hosting service. It does not behave like Apache hosting, so Apache-specific `.htaccess` directives in this repo should not be treated as active production configuration.

Important impact:

- `ExpiresByType` rules in `.htaccess` may not apply.
- `Cache-Control` rules in `.htaccess` may not apply.
- `mod_deflate` / `mod_brotli` rules in `.htaccess` may not apply.
- Rewrite rules in `.htaccess` may not apply.
- Security headers in `.htaccess` may not apply.

This matters because PageSpeed depends heavily on cache headers, compression, redirects, and asset delivery.

## PageSpeed Status

Latest screenshot result:

| Category | Score |
|---|---:|
| Performance | 63 |
| Accessibility | 89 |
| Best Practices | 100 |
| SEO | 92 |

PageSpeed API status:

- A live PageSpeed API request was attempted.
- Google returned `429 Too Many Requests`, so a fresh full JSON Lighthouse report could not be downloaded during this review.
- The score above is therefore taken from the provided PageSpeed screenshot.

Google PageSpeed scores are Lighthouse-based lab scores plus field data when available. The screenshot also shows “No Data” for real-user data, so the visible score is primarily a lab diagnosis for this URL.

## Performance Work Already Present

The current homepage includes several optimization attempts:

- Hero image is preloaded:
  - `images/sliders/4.webp`
- Below-fold images use `loading="lazy"`.
- Homepage uses optimized replacements:
  - `images/abouttt-optimized.jpg`
  - `images/wjy-choose-optimized.jpg`
- Google Fonts are loaded with `display=swap`.
- Some CSS is preloaded with async stylesheet activation.
- JavaScript is deferred.
- Bootstrap JS is delayed until after page load / idle.
- Local homepage asset references were checked and all exist.

Image savings from current optimized replacements:

| Asset | Before | After | Saved |
|---|---:|---:|---:|
| `abouttt.png` to `abouttt-optimized.jpg` | 1381 KB | 84 KB | 1297 KB |
| `wjy-choose.png` to `wjy-choose-optimized.jpg` | 1202 KB | 98 KB | 1104 KB |

## Remaining Performance Risks

High-priority risks:

1. `.htaccess` is not active on GitHub Pages

   The repo contains performance headers, compression rules, and redirects in `.htaccess`, but GitHub Pages will not apply Apache config. This can keep PageSpeed warnings unresolved even after code changes.

2. Hosting cannot fully control cache/compression headers

   GitHub Pages is simple static hosting. For strict PageSpeed tuning, a host/CDN that supports `_headers`, edge rules, Brotli, and explicit immutable cache headers is better.

3. Possible bot-verification / interstitial behavior

   A public web fetch returned a “Verifying that you are not a robot...” page during review. If PageSpeed receives that response path, Lighthouse may audit the interstitial instead of the homepage.

4. Render path remains heavy on mobile

   The homepage still loads several CSS dependencies and a large DOM:

   - Bootstrap CSS: about 293 KB
   - `custom.css`: about 120 KB
   - Font Awesome CSS: about 121 KB
   - Google Fonts
   - Large HTML document: about 66-68 KB
   - 15 service cards on the homepage

5. External font request can affect first paint

   Google Fonts can still delay text rendering or increase network work on mobile. Self-hosting only the exact used font weights would be more predictable.

## Why the Score Can Stay at 63

The score can remain low even when deployment succeeds because GitHub Pages deployment only confirms files were published. It does not confirm:

- PageSpeed saw the newest uncached HTML.
- PageSpeed saw the real homepage instead of a verification page.
- Intended `.htaccess` performance headers were applied.
- Mobile Lighthouse main-thread and render-blocking time became low enough for a green score.

## Recommended Fix Plan

Priority 1: Hosting / header fix

- Move the site to a static host/CDN that supports custom headers, such as Cloudflare Pages, Netlify, or Vercel.
- Add equivalent `_headers` configuration for:
  - Long cache for images, fonts, CSS, and JS.
  - Short/no-cache for HTML.
  - Brotli/gzip compression where supported.
  - Security headers.

Priority 2: Remove the bot-verification path

- Check domain/DNS/security provider settings.
- Ensure Googlebot, Lighthouse, PageSpeed Insights, and normal anonymous users receive the real homepage HTML without an interstitial.

Priority 3: Reduce render-blocking CSS

- Split critical homepage CSS from full site CSS.
- Load only above-the-fold CSS synchronously.
- Delay Font Awesome or replace above-the-fold icons with inline SVG or lightweight assets.
- Remove unused Bootstrap rules with a build step if staying on Bootstrap.

Priority 4: Reduce homepage work

- Consider showing fewer service cards on the homepage and linking to the full services page.
- Keep homepage first viewport focused on hero, intro, and primary services.
- Move non-essential animated effects out of the mobile experience.

Priority 5: Font optimization

- Self-host only the used `Sora` and `Marcellus` weights.
- Preload WOFF2 font files.
- Use fallback font metrics to reduce layout shifts.

## Verification Checklist After Fixes

Run these checks after the next deployment:

1. Confirm GitHub/hosting deployment status is successful.
2. Fetch the live HTML and confirm it contains:
   - `abouttt-optimized.jpg`
   - `wjy-choose-optimized.jpg`
   - no homepage `animate.css`
   - no homepage `mousecursor.css`
3. Confirm the live response is the homepage, not a bot-verification page.
4. Confirm image/CSS/JS responses have correct `Cache-Control`.
5. Confirm HTML response is not cached too aggressively.
6. Rerun PageSpeed mobile 2-3 times and compare metrics, not only the score:
   - LCP
   - FCP
   - TBT
   - CLS
   - Speed Index

## Final Assessment

Deployment status: Passed  
GitHub Pages publishing: Passed  
DNS to GitHub Pages: Passed  
Best Practices: Passed at `100`  
SEO: Good at `92`  
Accessibility: Needs minor improvement at `89`  
Mobile Performance: Still unresolved at `63`

The blocker is not GitHub deployment failure. The blocker is production delivery and remaining mobile render-path cost, especially because `.htaccess` optimizations are not a dependable production mechanism on GitHub Pages.

## References

- GitHub Pages custom domain documentation: https://docs.github.com/pages/configuring-a-custom-domain-for-your-github-pages-site/about-custom-domains-and-github-pages
- GitHub Pages documentation: https://docs.github.com/pages
- Google PageSpeed Insights overview: https://developers.google.com/speed/docs/insights/v5/about
- PageSpeed Insights API documentation: https://developers.google.com/speed/docs/insights/rest/v5/pagespeedapi/runpagespeed
