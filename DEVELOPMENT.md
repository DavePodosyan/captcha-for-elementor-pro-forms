# Development Guide

This guide covers how to develop, build, and release the Captcha for Elementor Pro Forms plugin.

## Quick Start

1. **Install dependencies:**

    ```bash
    npm install
    ```

2. **Set up development environment:**

    ```bash
    npm run dev-setup
    ```

3. **Start developing:**
    ```bash
    npm run dev
    ```

## Available Scripts

### Development

- `npm run dev` - Lint code and create a build
- `npm run watch` - Watch JavaScript files for changes
- `npm run format` - Format all code with Prettier
- `npm run lint` - Run all linting (JS + formatting)

### Building & Releasing

- `npm run build` - Create a clean distribution ZIP
- `npm run release` - Prepare a complete release (lint + build)
- `npm run version-sync` - Sync version across all files

### Individual Tasks

- `npm run lint:js` - ESLint JavaScript files
- `npm run lint:format` - Check Prettier formatting
- `npm run clean` - Remove build artifacts

## Project Structure

```
captcha-for-elementor-pro-forms/
├── assets/js/               # Frontend JavaScript
├── includes/                # PHP classes
├── scripts/                 # Build/development scripts
├── .github/workflows/       # GitHub Actions
├── dist/                    # Build output (gitignored)
├── releases/                # Release ZIPs (gitignored)
└── languages/               # Future translation files
```

## Version Management

Versions are managed in `package.json` and automatically synced to:

- Main plugin file header
- `CEPF_VERSION` constant
- `readme.txt` stable tag

To update version:

```bash
npm version patch  # or minor, major
npm run version-sync
```

## Release Process

### Manual Release

1. Update version: `npm version [patch|minor|major]`
2. Create release: `npm run release`
3. Upload `releases/captcha-for-elementor-pro-forms-X.X.X.zip` to WordPress.org

### Automated Release (GitHub)

1. Push changes to main branch
2. Create and push a version tag: `git tag v1.0.1 && git push origin v1.0.1`
3. GitHub Actions will automatically create a release with ZIP file

## Code Quality

### JavaScript

- **ESLint** enforces code quality rules
- **Prettier** handles code formatting
- Global variables (jQuery, elementorModules, etc.) are pre-configured

### PHP

- Follow WordPress Coding Standards
- Use proper escaping and sanitization
- Maintain object-oriented architecture

## File Structure Guidelines

### JavaScript Files (`assets/js/`)

- One handler per CAPTCHA provider
- Extend `elementorModules.frontend.handlers.Base`
- Use modern ES6+ features where supported

### PHP Classes (`includes/`)

- Extend `CEPF_Base_Captcha_Handler` for new CAPTCHA providers
- Follow WordPress plugin architecture
- Use proper namespacing and autoloading

## Adding New CAPTCHA Providers

1. Create JavaScript handler in `assets/js/provider-handler.js`
2. Create PHP class in `includes/class-provider-handler.php`
3. Extend `CEPF_Base_Captcha_Handler`
4. Implement all abstract methods
5. Register in main plugin file
6. Update documentation

## Testing

Currently no automated tests are set up. Manual testing checklist:

- [ ] Plugin activates without errors
- [ ] Settings appear in Elementor > Integrations
- [ ] CAPTCHA fields render in form builder
- [ ] CAPTCHA validation works on form submission
- [ ] Forms work in popups/modals
- [ ] No JavaScript errors in console

## Deployment

The plugin creates clean, distribution-ready ZIP files that include:

- All PHP files
- Frontend assets (CSS/JS)
- WordPress plugin headers
- Translation-ready structure

Files excluded from releases:

- Development dependencies (`node_modules`)
- Build scripts and configs
- Source control files
- IDE/OS files
