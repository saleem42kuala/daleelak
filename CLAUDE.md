# Daleelak API — Project Brief

## What this is
Backend + web admin for an Arabic-only mobile review app for Arab travelers.
Travelers rate restaurants and tourism companies across multiple countries,
scored on Arab-traveler criteria (halal, prayer room, family section, etc).

## Stack (do not deviate without asking)
- Laravel 12, PHP 8.2+ (bumped from the originally-specified Laravel 11 on
  2026-07-09: Laravel 11's latest release had an unpatched high-severity CRLF
  injection, CVE-2026-48019, and a medium signed-URL bug, fixed only in
  12.60+/13.10+ — approved by user, see chat)
- MySQL, database name: daleelak, charset utf8mb4, dedicated app DB user
  `daleelak_app` (not root)
- Auth: Sanctum (token-based API for the mobile app) — set up via
  `php artisan install:api`. Social sign-in (Google/Apple) verifies provider
  ID tokens via firebase/php-jwt against provider JWKS (see
  App\Services\SocialTokenVerifier; needs GOOGLE_CLIENT_ID / APPLE_CLIENT_ID)
- Admin panel: Filament v3 at /admin (installed). Access gated to
  users.is_admin via User::canAccessPanel. Login: admin@daleelak.test / password
- API: versioned under /api/v1, returns JSON via API Resources.
  Controllers in App\Http\Controllers\Api\V1, thin; Form Requests in
  App\Http\Requests\Api carry Arabic validation messages

## Data model
Country > City > Area > Listing. Listings belong to a Category
(restaurant | tourism_company). Reviews belong to a Listing and a User,
carry a 1-5 rating + Arabic comment. Criteria are a lookup table
(NOT hardcoded columns) with a pivot for aggregated scores per listing.
The 6 criteria: halal, prayer_room, family_section, arabic_staff,
alcohol_free, modest_friendly.

## Rules
- All user-facing text fields are Arabic (utf8mb4). Comments/labels can be English.
- When a review is created or moderated, recalculate the listing's cached
  overall_rating, reviews_count, and per-criterion averages (use an Observer).
- Keep controllers thin; use Form Requests for validation and API Resources
  for responses.
- Never commit .env or credentials.

## Conventions
- Follow the phased build plan. Build only the current phase's scope.
- Ask before adding packages not already in composer.json.

## Known TODOs
- `areas` are placeholder — one generic "city centre" area per real city
  (121 total, via `GeographySeeder` + `DemoDataSeeder`), since no
  neighborhood-level area data has been provided yet. Replace with real
  areas per city when that data is available.
- `countries.name_en`/`phone_code` and `users.country_id` are not backfilled
  for the real geography data (nullable, left empty).
- Social sign-in is wired but GOOGLE_CLIENT_ID / APPLE_CLIENT_ID are unset,
  so /auth/social can't verify real tokens until those are configured.
- Listings criteria filter matches when a listing's aggregated criterion
  score is >= 50% (ListingController::CRITERIA_MATCH_THRESHOLD).