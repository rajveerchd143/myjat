$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "          MYJAT GIT PUSH" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

try {
    Write-Host "Staging changes..." -ForegroundColor Yellow
    git add .

    if ($LASTEXITCODE -ne 0) {
        throw "Git staging failed."
    }

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

    if ($LASTEXITCODE -ne 0) {
        throw "Git commit failed."
    }

    Write-Host ""
    Write-Host "Pushing to GitHub..." -ForegroundColor Yellow

    git push

    if ($LASTEXITCODE -ne 0) {
        throw "GitHub push failed."
    }

    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "          PUSH COMPLETED" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
}
catch {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "             PUSH FAILED" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
    Write-Host ""
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    exit 1
}