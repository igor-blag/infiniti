# AGENTS.md — ИнфинИТи Theme

## Project Overview

**ИнфинИТи** is an open-source WordPress block theme for children's digital education centers — centers that teach programming, robotics, 3D modeling, and digital creativity to school students aged 7–17. Features a bold diagonal-split hero, deep blue and red accent palette, and confident forward-looking design.

- **License:** GPLv2 or later
- **Requires WordPress:** 6.4+
- **Requires PHP:** 7.4+
- **Text Domain:** `infiniti`
- **Demo:** https://infiniti.no-it.ru
- **Repository:** https://github.com/igor-blag/infiniti

## Demo Content

The theme includes a WP-CLI script to auto-generate demo content. From the Studio site root:

```bash
studio wp eval-file wp-content/themes/infiniti/bin/setup-demo.php
```

This creates pages with patterns, sample posts with thumbnails, categories, and configures front page / posts page settings.

## Local Development

The theme is symlinked into a local WordPress Studio site:

- **Theme source:** `/Users/igorblagovesenskij/projects/infiniti`
- **Studio symlink:** `/Users/igorblagovesenskij/Studio/инфинити/wp-content/themes/infiniti`

For Studio CLI usage and constraints (SQLite, `studio wp` prefix, etc.), see [`/Users/igorblagovesenskij/Studio/инфинити/STUDIO.md`](/Users/igorblagovesenskij/Studio/инфинити/STUDIO.md).

## Architecture

- **Block theme** (Full Site Editing) — no classic PHP templates
- `theme.json` v3 — colors, typography, spacing, element styles
- `templates/` — HTML templates (`index`, `home`, `single`, `page`, `archive`, `search`, `404`)
- `parts/` — `header.html`, `footer.html`
- `patterns/` — reusable content patterns (hero, programs, advantages, CTA, page layouts)
- `style.css` — theme header + custom CSS utilities
- `functions.php` — theme setup, fonts, style enqueues, `theme:./` URL rewriting
- `bin/setup-demo.php` — demo content generator

## Key Design Decisions

- Page content uses `<!-- wp:pattern -->` blocks in `post_content` (not hardcoded in templates) — fully editable in the block editor
- `index.html` is a minimal fallback (header → post-content → footer)
- Images use `theme:./assets/` relative paths, rewritten via `theme-assets-rewrite.php` + `theme-assets-editor-rewrite.js`

## Preparing for SVN Publication

Target: WordPress.org theme directory. Before submission:

- [x] `readme.txt` with changelog and Resources section
- [x] Copyright notice in `style.css`
- [x] Theme passes Theme Check plugin
- [x] All strings internationalized (`__()`, `_e()`, etc.)
- [x] All handles properly prefixed (`infiniti-`)
- [x] No `.DS_Store` or other dev artifacts
- [x] `screenshot.png` (1200x900, 4:3 ratio)
- [x] Verify all templates render correctly
- [ ] Test demo content script on clean install
- [ ] Confirm image licensing for Resources section

## Roadmap

Ideas for future releases to improve the theme quality and reach:

- [ ] More `register_block_style` variations (e.g. shadow cards, rounded overlays)
- [ ] `accessibility-ready` tag — pass [additional accessibility requirements](https://make.wordpress.org/themes/handbook/review/accessibility/)
- [ ] Additional templates: `author.html`, `category.html`, `tag.html`
- [ ] RTL stylesheet support (`rtl.css`)
- [ ] Editor-only styles for better Site Editor UX (`.editor-styles-wrapper`)
- [ ] More block patterns (team section, pricing table, testimonials)
- [ ] Wide/full-width pattern variations
- [ ] Dark mode support via `prefers-color-scheme`
- [ ] Custom 404 page with search and suggested links pattern
- [ ] Theme preview improvements for WordPress.org directory
