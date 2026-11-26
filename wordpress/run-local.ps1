$ErrorActionPreference = "Stop"

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "PHP not found in PATH. Install PHP or use XAMPP/WAMP and retry."
    exit 1
}

Write-Host "Writing wp-config.php..."
& powershell -ExecutionPolicy Bypass -File "wordpress\\setup.ps1"

Write-Host "Starting WordPress at http://localhost:8000"
php -S localhost:8000 -t wordpress\\wordpress
