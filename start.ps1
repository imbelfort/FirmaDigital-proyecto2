# ============================================================
# Script de Inicio: Sistema de Firma Digital (UAGRM)
# ============================================================

$Host.UI.RawUI.WindowTitle = "Sistema Firma Digital - UAGRM"

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "   INICIANDO SISTEMA DE FIRMA DIGITAL - VICERRECTORADO   " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host ""

$projectDir = $PSScriptRoot
Set-Location $projectDir

# Limpiar procesos previos de PHP/Node y caché
Write-Host "[1/4] Limpiando procesos previos y cache..." -ForegroundColor Yellow
Get-Process -Name php -ErrorAction SilentlyContinue | Where-Object { $_.CommandLine -like "*artisan serve*" } | Stop-Process -Force -ErrorAction SilentlyContinue
php artisan optimize:clear | Out-Null
Remove-Item -Path "$projectDir\public\hot" -Force -ErrorAction SilentlyContinue

# Comprobar PostgreSQL
Write-Host "[2/4] Verificando servicio de PostgreSQL..." -ForegroundColor Yellow
$pgService = Get-Service -Name *postgres* -ErrorAction SilentlyContinue | Select-Object -First 1
if ($pgService -and $pgService.Status -ne 'Running') {
    Write-Host "Iniciando servicio de PostgreSQL..." -ForegroundColor Yellow
    Start-Service $pgService.Name
}
Write-Host "PostgreSQL listo." -ForegroundColor Green

# 1. Iniciar Backend (Laravel artisan serve)
Write-Host "[3/4] Levantando Backend (Laravel: http://127.0.0.1:8000)..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Set-Location '$projectDir'; Write-Host '--- BACKEND LARAVEL ---' -ForegroundColor Green; php artisan serve --port=8000"

# 2. Iniciar Frontend (Vite)
Write-Host "[4/4] Levantando Frontend (Vite Hot-Reload)..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Set-Location '$projectDir'; Write-Host '--- FRONTEND VITE ---' -ForegroundColor Cyan; npm run dev"

Start-Sleep -Seconds 2

# Abrir el navegador automáticamente
Write-Host ""
Write-Host "Abriendo la aplicacion en tu navegador..." -ForegroundColor Green
Start-Process "http://127.0.0.1:8000"

Write-Host ""
Write-Host "==========================================================" -ForegroundColor Green
Write-Host "   SISTEMA EJECUTANDOSE CON EXITO                         " -ForegroundColor Green
Write-Host '   - Backend API y App : http://127.0.0.1:8000            ' -ForegroundColor White
Write-Host '   - Frontend Vite Dev : http://localhost:5173            ' -ForegroundColor White
Write-Host "==========================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Presiona Enter para cerrar este lanzador..." -ForegroundColor Gray
Read-Host
