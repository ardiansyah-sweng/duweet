# ============================================
# Test API Endpoint untuk Filter Financial Account
# ============================================

$BaseURL = "http://localhost:8000/api"

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "TEST: Financial Account Filter API" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Test 1
Write-Host "1. Testing: Get all accounts for user_id=1" -ForegroundColor Yellow
Write-Host "   URL: $BaseURL/financial-account/filter/by-user?user_id=1" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri "$BaseURL/financial-account/filter/by-user?user_id=1" -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Success" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 2
Write-Host "2. Testing: Get Assets (AS) for user_id=1" -ForegroundColor Yellow
$url2 = "$BaseURL/financial-account/filter/by-user?user_id=1&type=AS"
Write-Host "   URL: $url2" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri $url2 -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Success - Count: $($response.count)" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 3
Write-Host "3. Testing: Get Income (IN) for user_id=1" -ForegroundColor Yellow
$url3 = "$BaseURL/financial-account/filter/by-user?user_id=1&type=IN"
Write-Host "   URL: $url3" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri $url3 -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Success - Count: $($response.count)" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 4
Write-Host "4. Testing: Get Multiple types (AS,IN) for user_id=1" -ForegroundColor Yellow
$url4 = "$BaseURL/financial-account/filter/by-user?user_id=1&type=AS,IN"
Write-Host "   URL: $url4" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri $url4 -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Success - Count: $($response.count)" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 5
Write-Host "5. Testing: Get Expenses (EX) for user_id=1" -ForegroundColor Yellow
$url5 = "$BaseURL/financial-account/filter/by-user?user_id=1&type=EX"
Write-Host "   URL: $url5" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri $url5 -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✓ Success - Count: $($response.count)" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Test 6
Write-Host "6. Testing: Invalid type (should return error)" -ForegroundColor Yellow
$url6 = "$BaseURL/financial-account/filter/by-user?user_id=1&type=INVALID"
Write-Host "   URL: $url6" -ForegroundColor Gray
try {
    $response = Invoke-RestMethod -Uri $url6 -Method Get -Headers @{"Accept"="application/json"}
    Write-Host "   ✗ Should have failed but succeeded!" -ForegroundColor Red
    Write-Host ($response | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "   ✓ Correctly returned error" -ForegroundColor Green
}
Write-Host ""

# Test 7
Write-Host "7. Testing: All account types for user_id=1" -ForegroundColor Yellow
$types = @('AS', 'IN', 'EX', 'SP', 'LI')
foreach ($type in $types) {
    Write-Host "   Testing type: $type" -ForegroundColor Gray
    try {
        $url = "$BaseURL/financial-account/filter/by-user?user_id=1&type=$type"
        $response = Invoke-RestMethod -Uri $url -Method Get -Headers @{"Accept"="application/json"}
        Write-Host "      ✓ $type - Found: $($response.count) accounts" -ForegroundColor Green
    } catch {
        Write-Host "      ✗ $type - Error" -ForegroundColor Red
    }
}
Write-Host ""

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "TEST COMPLETED" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "NOTE:" -ForegroundColor Yellow
Write-Host "- Sesuaikan BaseURL dengan server Anda (default: http://localhost:8000/api)" -ForegroundColor Gray
Write-Host "- Pastikan user_id ada di database" -ForegroundColor Gray
Write-Host "- Pastikan server Laravel sudah running: php artisan serve" -ForegroundColor Gray
