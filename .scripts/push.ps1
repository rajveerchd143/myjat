Write-Host ""
Write-Host "==============================="
Write-Host " MYJAT AUTO PUSH"
Write-Host "==============================="
Write-Host ""

git add .

$status = git status --porcelain

if ([string]::IsNullOrWhiteSpace($status)) {
    Write-Host "No changes to commit."
    exit
}

$date = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

git commit -m "Auto Update $date"

if ($LASTEXITCODE -ne 0) {
    exit
}

git push

Write-Host ""
Write-Host "Done!"