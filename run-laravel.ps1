# Run St. Francis Of Assisi Laravel App
$php = "C:\xampp\php\php.exe"
if (-not (Test-Path $php)) {
    if (Get-Command php -ErrorAction SilentlyContinue) {
        $php = "php"
    } else {
        Write-Error "PHP not found. Please install PHP or ensure C:\xampp\php\php.exe exists."
        exit 1
    }
}

Set-Location -Path "$PSScriptRoot\laravel"
Write-Host "============================================================" -ForegroundColor Green
Write-Host " Starting St. Francis of Assisi Parish Website (Laravel)    " -ForegroundColor Green
Write-Host "============================================================" -ForegroundColor Green
Write-Host "Public Website:  http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "Admin Portal:    http://127.0.0.1:8000/admin" -ForegroundColor Cyan
Write-Host "Database:        Supabase PostgreSQL" -ForegroundColor Magenta
Write-Host "Press Ctrl+C to stop the server." -ForegroundColor Yellow
Write-Host ""

& $php -d extension=pdo_pgsql -d extension=pgsql artisan serve --port=8000
