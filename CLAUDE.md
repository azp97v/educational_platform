# Project: Educational Platform (Laravel)

## Stack
- Laravel 12 + PHP 8.3
- MySQL + Redis
- Blade templates (no React/Vue)
- Laravel Reverb for WebSockets
- XAMPP (local dev) → production on VPS

## Key constraints
- `APP_DEBUG=false` in production always
- OPcache `validate_timestamps=0` → ALWAYS `systemctl restart php8.3-fpm`, NEVER `reload`
- No hardcoded metered.ca TURN credentials
- Teacher test credentials: teacher@iglal.com / password

## gstack

Use the `/browse` skill from gstack for all web browsing. Never use `mcp__claude-in-chrome__*` tools when gstack browse is available.

Available gstack skills:
/office-hours, /plan-ceo-review, /plan-eng-review, /plan-design-review, /design-consultation, /design-shotgun, /design-html, /review, /ship, /land-and-deploy, /canary, /benchmark, /browse, /connect-chrome, /qa, /qa-only, /design-review, /setup-browser-cookies, /setup-deploy, /setup-gbrain, /retro, /investigate, /document-release, /document-generate, /codex, /cso, /autoplan, /plan-devex-review, /devex-review, /careful, /freeze, /guard, /unfreeze, /gstack-upgrade, /learn, /spec, /diagram, /health
