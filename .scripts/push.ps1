$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "          MYJAT GIT PUSH" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Staging changes..." -ForegroundColor Yellow
git add .

$status = git status --porcelain

if (-not $status) {
    Write-Host ""
    Write-Host "No changes to commit." -ForegroundColor Gray
    Write-Host ""
    exit 0
}

Write-Host ""
Write-Host "Creating commit..." -ForegroundColor Yellow

$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm"
git commit -m "Update: $timestamp"

Write-Host ""
Write-Host "Pushing to GitHub..." -ForegroundColor Yellow

git push

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "          PUSH COMPLETED" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""