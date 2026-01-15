@echo off
REM ============================================
REM Test API Endpoint untuk Filter Financial Account
REM ============================================

echo ============================================
echo TEST: Financial Account Filter API
echo ============================================
echo.

REM Ganti dengan base URL server Anda
set BASE_URL=http://localhost:8000/api

echo 1. Testing: Get all accounts for user_id=1
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1" -H "Accept: application/json"
echo.
echo.

echo 2. Testing: Get Assets (AS) for user_id=1
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1^&type=AS
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1&type=AS" -H "Accept: application/json"
echo.
echo.

echo 3. Testing: Get Income (IN) for user_id=1
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1^&type=IN
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1&type=IN" -H "Accept: application/json"
echo.
echo.

echo 4. Testing: Get Assets and Income (AS,IN) for user_id=1
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1^&type=AS,IN
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1&type=AS,IN" -H "Accept: application/json"
echo.
echo.

echo 5. Testing: Get all types for user_id=2
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=2
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=2" -H "Accept: application/json"
echo.
echo.

echo 6. Testing: Get Expenses (EX) for user_id=1
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1^&type=EX
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1&type=EX" -H "Accept: application/json"
echo.
echo.

echo 7. Testing: Invalid type (should return error)
echo URL: %BASE_URL%/financial-account/filter/by-user?user_id=1^&type=INVALID
curl -X GET "%BASE_URL%/financial-account/filter/by-user?user_id=1&type=INVALID" -H "Accept: application/json"
echo.
echo.

echo ============================================
echo TEST COMPLETED
echo ============================================
echo.
echo NOTE: 
echo - Sesuaikan BASE_URL dengan server Anda
echo - Pastikan user_id ada di database
echo - Pastikan server Laravel sudah running: php artisan serve
echo.

pause
