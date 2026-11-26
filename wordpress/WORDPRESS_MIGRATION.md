# WordPress Migration Blueprint

This folder contains the WordPress theme and core plugin that mirror the Laravel church site.

## 1) Enable Theme + Plugin

Activate theme: `st-francis-parish`
Activate plugin: `st-francis-core`

## 2) Content Model Mapping

Laravel Model -> WordPress Post Type
- `Ministry` -> `ministry`
- `Event` -> `event`
- `Sermon` -> `sermon`
- `GalleryItem` -> `gallery_item`
- `Society` -> `society`
- `Friar` -> `friar`
- `PastoralCouncilChairperson` -> `chairperson`
- `ServiceSchedule` -> `service_schedule`
- `DailyVerse` -> `daily_verse`
- `ContactMessage` -> `contact_message` (private admin entries)

## 3) Field Mapping (Meta Keys)

Ministry:
- `contact_email`, `contact_phone`, `meeting_time`, `is_active`, `image_url`

Event:
- `event_date`, `event_time`, `location`, `category`, `is_featured`, `approval_status`, `image_url`

Sermon:
- `preacher`, `video_url`, `audio_url`, `notes_url`, `summary`, `verses`

Gallery Item:
- `media_url`, `thumbnail_url`, `category`, `type`, `approval_status`

Service Schedule:
- `day_of_week`, `time`, `location`

Friar / Chairperson:
- `service_period`

Site Options:
- stored in `stf_site_options` via Settings -> Parish Settings

## 4) Theme Templates

Main templates live in `wordpress/wp-content/themes/st-francis-parish/`:
- `front-page.php` (home)
- `page-about.php` (about)
- `page-contact.php` (contact)
- `archive-*.php` for ministries, events, sermons, gallery, societies, friars, chairpersons
- `single-ministry.php`, `single-sermon.php`

The theme supports ACF if installed. Otherwise, it falls back to native post meta.

## 5) Contact Form

Shortcode: `[stf_contact_form]`
Messages are saved as private `contact_message` posts.

## 6) Data Import Notes

If you export from Laravel, map each table to the CPT listed above and write into the meta keys listed.
For media, store the full URL in `image_url` or set the featured image.

## 7) Demo Seed

Seed button: `Tools -> Seed Parish Content`
Sample CSVs: `wordpress/seed/*.csv`

## 8) One-Click Local Run

Run `powershell -File wordpress/run-local.ps1`
