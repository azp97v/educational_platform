# Load Tests

## Prerequisites
- [k6](https://k6.io/docs/get-started/installation/) installed
- Laravel dev server running: `php artisan serve`
- Test users seeded: `php artisan db:seed`

## Quick Start

# Smoke test (1 VU, 30s)
k6 run tests/Load/smoke.js

# Load test (ramp to VUS, steady for STEADY)
k6 run tests/Load/scenarios.js

# Stress test (50 → 100 → 200 VUs)
k6 run tests/Load/stress.js

# Customize
k6 run -e BASE_URL=http://192.168.1.100:8000 -e VUS=50 -e STEADY=3m tests/Load/scenarios.js

## Environments
- `BASE_URL` – target URL (default: http://localhost:8000)
- `VUS` – virtual users (default: 20)
- `RAMP_UP` – ramp-up duration (default: 30s)
- `STEADY` – steady-state duration (default: 1m)
- `STUDENT_EMAIL` / `STUDENT_PASSWORD` – test student credentials
- `TEACHER_EMAIL` / `TEACHER_PASSWORD` – test teacher credentials
- `ADMIN_EMAIL` / `ADMIN_PASSWORD` – test admin credentials

## Output
k6 outputs real-time metrics: request rate, duration (avg/p50/p95/p99), failure rate.
Use `k6 run --out json=results.json` to save results for analysis.
