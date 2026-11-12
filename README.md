# StackCraft Studio Portfolio

**We Build. We Maintain. We Grow.**

Official freelance web development and website maintenance portfolio website for **StackCraft Studio** (Hyderabad, Telangana, India).

## Services Showcased

1. **WordPress Website Development** — Business sites, landing pages, portfolios, and custom WordPress customization.
2. **React Development** — Responsive, interactive frontend web applications.
3. **Node.js Development** — REST APIs, database integrations, and server-side backend functionality.
4. **Custom Website Development** — Tailor-made websites crafted to exact business requirements.
5. **Website Maintenance & Support** — Bug fixes, security updates, speed optimization, backups, content updates, and ongoing support.

## Quick Commands

```bash
npm install
npm run dev      # Start local dev server
npm run build    # Build production static bundle into dist/
npm run preview  # Preview built static site locally
```

## Key Configuration Files

- **Brand, Contact & Socials**: `src/data/global/company.json` (`showPhone`, `showWhatsApp`, `email`, `socials`)
- **Homepage Copy & Sections**: `src/data/i18n/nova.json` and `src/data/pages/index.json`
- **Contact Form Fields & Endpoint**: `src/data/sections/contact.json` (`formEndpoint`, `web3formsKey`)
- **SEO & Site URL**: `src/data/global/seo.json` and `site.config.mjs` (set `index: true` in `seo.json` and update `SITE_URL` when publishing to your final domain)
