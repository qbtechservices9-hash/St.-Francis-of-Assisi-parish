# Supabase Setup

This project can run locally with SQLite and can also use Supabase PostgreSQL in production or shared development.

## 1. Update `.env`

Use these database settings:

```env
DB_CONNECTION=pgsql
DB_HOST=db.your-project-ref.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-database-password
DB_SCHEMA=public
DB_SSLMODE=require
```

Keep `APP_URL` set to the correct site URL for the environment.

## 2. Make sure PostgreSQL support is enabled in PHP

Required PHP extensions:

- `pgsql`
- `pdo_pgsql`

## 3. Create the schema in Supabase

Run:

```bash
php artisan migrate
```

If you want demo or starter data:

```bash
php artisan db:seed
```

## 4. Move existing local SQLite data to Supabase

If you already have data in the local SQLite database, first switch `.env` to the Supabase PostgreSQL connection, then run:

```bash
php artisan db:sync-sqlite-to-pgsql
```

This command:

- reads content from the local `sqlite` connection
- writes into the `pgsql` connection
- skips runtime tables like cache, sessions, and jobs by default

If you also want to include runtime tables:

```bash
php artisan db:sync-sqlite-to-pgsql --include-runtime
```

## 5. Clear cached configuration

After changing database settings:

```bash
php artisan optimize:clear
```

## 6. Move media storage to Supabase Storage

This project now supports a separate configurable media disk.

Set these values in `.env` once you create Supabase Storage access keys:

```env
MEDIA_DISK=supabase
SUPABASE_STORAGE_ENDPOINT=https://your-project-ref.supabase.co/storage/v1/s3
SUPABASE_STORAGE_URL=https://your-project-ref.supabase.co/storage/v1/object/public/media
SUPABASE_STORAGE_REGION=eu-west-2
SUPABASE_STORAGE_BUCKET=media
SUPABASE_STORAGE_ACCESS_KEY_ID=your-access-key
SUPABASE_STORAGE_SECRET_ACCESS_KEY=your-secret-key
SUPABASE_STORAGE_USE_PATH_STYLE_ENDPOINT=true
```

Then run:

```bash
php artisan media:create-supabase-bucket media
php artisan media:sync-to-cloud
php artisan optimize:clear
```

After that, existing DB file paths will resolve from the cloud bucket automatically.
