# Test Financial Account Filter API
$BaseURL = "http://localhost:8000/api"

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "TEST: Financial Account Filter API" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Test 1
Write-Host "1. Get all accounts for user_id=1" -ForegroundColor Yellow
try {
    $url = $BaseURL + "/financial-account/filter/by-user?user_id=1"
    $response = Invoke-RestMethod -Uri $url -Method Get
    Write-Host "   Success - Count: $($response.count)" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 2
Write-Host "2. Get Assets (AS) for user_id=1" -ForegroundColor Yellow
try {
    $url = $BaseURL + "/financial-account/filter/by-user?user_id=1" + "&" + "type=AS"
    $response = Invoke-RestMethod -Uri $url -Method Get
    Write-Host "   Success - Count: $($response.count)" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 3
Write-Host "3. Get Income (IN) for user_id=1" -ForegroundColor Yellow
try {
    $url = $BaseURL + "/financial-account/filter/by-user?user_id=1" + "&" + "type=IN"
    $response = Invoke-RestMethod -Uri $url -Method Get
    Write-Host "   Success - Count: $($response.count)" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 4
Write-Host "4. Get Multiple types (AS,IN)" -ForegroundColor Yellow
try {
    $url = $BaseURL + "/financial-account/filter/by-user?user_id=1" + "&" + "type=AS,IN"
    $response = Invoke-RestMethod -Uri $url -Method Get
    Write-Host "   Success - Count: $($response.count)" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 5
Write-Host "5. Test all types" -ForegroundColor Yellow
$types = @('AS', 'IN', 'EX', 'SP', 'LI')
foreach ($type in $types) {
    try {
        $url = $BaseURL + "/financial-account/filter/by-user?user_id=1" + "&" + "type=$type"
        $response = Invoke-RestMethod -Uri $url -Method Get
        Write-Host "   $type : Found $($response.count) accounts" -ForegroundColor Green
    } catch {
        Write-Host "   $type : Error" -ForegroundColor Red
    }
}
Write-Host ""

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "DONE" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
