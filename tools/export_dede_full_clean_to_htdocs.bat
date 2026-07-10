@echo off
setlocal EnableExtensions EnableDelayedExpansion

REM ============================================================
REM DeDe Full Clean Export
REM Creates a review ZIP in D:\xampp\htdocs
REM Approved components are collected dynamically from:
REM tools\cpanel-deploy-allowlist.txt
REM ============================================================

set "PROJECT_ROOT=D:\xampp\htdocs\dede"
set "OUTPUT_DIR=D:\xampp\htdocs"
set "ALLOWLIST=%PROJECT_ROOT%\tools\cpanel-deploy-allowlist.txt"

if not exist "%PROJECT_ROOT%" (
    echo [ERROR] Project root not found:
    echo %PROJECT_ROOT%
    pause
    exit /b 1
)

if not exist "%OUTPUT_DIR%" (
    echo [ERROR] Output directory not found:
    echo %OUTPUT_DIR%
    pause
    exit /b 1
)

REM ------------------------------------------------------------
REM Required governance and deploy files.
REM Stop instead of creating an incomplete review ZIP.
REM ------------------------------------------------------------
for %%F in (
    "README.md"
    "AGENTS.md"
    ".cpanel.yml"
    ".gitignore"
    "docs\PROJECT-MASTER.md"
    "docs\LOCAL_BASELINE_TEST_CHECKLIST.md"
    "tools\cpanel-deploy.sh"
    "tools\cpanel-deploy-allowlist.txt"
    "tools\cpanel-initial-deploy-files.txt"
) do (
    if not exist "%PROJECT_ROOT%\%%~F" (
        echo [ERROR] Required project file is missing:
        echo %PROJECT_ROOT%\%%~F
        echo.
        echo Export stopped so the review ZIP cannot be incomplete.
        pause
        exit /b 1
    )
)

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd-HHmmss"') do set "STAMP=%%i"

set "TEMP_DIR=%TEMP%\DeDe-export-%STAMP%"
set "ZIP_PATH=%OUTPUT_DIR%\DeDe-full-export-%STAMP%.zip"
set "GIT_BRANCH=unknown"
set "GIT_COMMIT=unknown"

for /f "usebackq delims=" %%B in (`git -C "%PROJECT_ROOT%" branch --show-current 2^>nul`) do set "GIT_BRANCH=%%B"
for /f "usebackq delims=" %%C in (`git -C "%PROJECT_ROOT%" rev-parse HEAD 2^>nul`) do set "GIT_COMMIT=%%C"

echo.
echo ==========================================
echo DeDe Full Clean Export
echo Project: %PROJECT_ROOT%
echo Branch : %GIT_BRANCH%
echo Commit : %GIT_COMMIT%
echo Output : %ZIP_PATH%
echo ==========================================
echo.

if exist "%TEMP_DIR%" rmdir /s /q "%TEMP_DIR%"
mkdir "%TEMP_DIR%" || (
    echo [ERROR] Could not create temp directory.
    pause
    exit /b 1
)

REM ------------------------------------------------------------
REM Important root files
REM ------------------------------------------------------------
for %%F in (
    "README.md"
    "AGENTS.md"
    ".cpanel.yml"
    ".gitignore"
) do (
    copy /y "%PROJECT_ROOT%\%%~F" "%TEMP_DIR%\%%~F" >nul
)

REM ------------------------------------------------------------
REM Internal project docs and tools
REM ------------------------------------------------------------
robocopy "%PROJECT_ROOT%\docs" "%TEMP_DIR%\docs" /E ^
    /XD ".git" "node_modules" "vendor" "cache" "logs" "tmp" "temp" ".idea" ".vscode" "__pycache__" ^
    /XF "*.zip" "*.rar" "*.7z" "*.tar" "*.gz" "*.log" "*.tmp" "import-log.txt" >nul

robocopy "%PROJECT_ROOT%\tools" "%TEMP_DIR%\tools" /E ^
    /XD ".git" "node_modules" "vendor" "cache" "logs" "tmp" "temp" ".idea" ".vscode" "__pycache__" ^
    /XF "*.zip" "*.rar" "*.7z" "*.tar" "*.gz" "*.log" "*.tmp" "import-log.txt" >nul

REM ------------------------------------------------------------
REM Export every approved custom theme/plugin from the allowlist.
REM Blank lines and comment lines beginning with # are ignored.
REM Only wp-content/themes/<name>/ and wp-content/plugins/<name>/
REM entries are accepted.
REM ------------------------------------------------------------
set "COMPONENT_COUNT=0"

for /f "usebackq delims=" %%L in (`powershell -NoProfile -ExecutionPolicy Bypass -Command ^
    "$items = Get-Content -LiteralPath '%ALLOWLIST%' | ForEach-Object { $_.Trim() } | Where-Object { $_ -and -not $_.StartsWith('#') }; " ^
    "$invalid = $items | Where-Object { $_ -notmatch '^wp-content/(themes|plugins)/[^/]+/$' -or $_ -match '\.\.' -or $_ -match '[*?\[]' }; " ^
    "if ($invalid) { Write-Error ('Invalid allowlist entries: ' + ($invalid -join ', ')); exit 2 }; " ^
    "if (-not $items) { Write-Error 'Allowlist is empty'; exit 2 }; " ^
    "$items"`) do (

    set "REL=%%L"
    set "REL=!REL:/=\!"

    if "!REL:~-1!"=="\" set "REL=!REL:~0,-1!"

    set "SRC=%PROJECT_ROOT%\!REL!"
    set "DST=%TEMP_DIR%\!REL!"

    if exist "!SRC!" (
        echo [INCLUDE] !REL!
        robocopy "!SRC!" "!DST!" /E ^
            /XD ".git" "node_modules" "vendor" "cache" "logs" "tmp" "temp" ".idea" ".vscode" ".sass-cache" "dist" "build" "__pycache__" ^
            /XF "*.zip" "*.rar" "*.7z" "*.tar" "*.gz" "*.log" "*.tmp" "import-log.txt" >nul

        set /a COMPONENT_COUNT+=1
    ) else (
        echo [SKIP - NOT CREATED YET] !REL!
    )
)

if errorlevel 2 (
    echo.
    echo [ERROR] The allowlist contains an invalid path or is empty.
    echo Export stopped to avoid creating an incomplete or unsafe ZIP.
    if exist "%TEMP_DIR%" rmdir /s /q "%TEMP_DIR%"
    pause
    exit /b 1
)

if "%COMPONENT_COUNT%"=="0" (
    echo.
    echo [ERROR] No approved custom theme or plugin was found.
    echo Export stopped to avoid creating an incomplete ZIP.
    if exist "%TEMP_DIR%" rmdir /s /q "%TEMP_DIR%"
    pause
    exit /b 1
)

REM ------------------------------------------------------------
REM Add snapshot information for reproducible review
REM ------------------------------------------------------------
(
    echo Export timestamp: %STAMP%
    echo Project root: %PROJECT_ROOT%
    echo Branch: %GIT_BRANCH%
    echo Commit: %GIT_COMMIT%
) > "%TEMP_DIR%\EXPORT-INFO.txt"

REM ------------------------------------------------------------
REM Create ZIP
REM ------------------------------------------------------------
if exist "%ZIP_PATH%" del /f /q "%ZIP_PATH%"

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
    "Compress-Archive -Path '%TEMP_DIR%\*' -DestinationPath '%ZIP_PATH%' -Force"

if errorlevel 1 (
    echo.
    echo [ERROR] ZIP creation failed.
    echo Temp folder kept here:
    echo %TEMP_DIR%
    pause
    exit /b 1
)

rmdir /s /q "%TEMP_DIR%"

echo.
echo ==========================================
echo DONE
echo Components included: %COMPONENT_COUNT%
echo Branch: %GIT_BRANCH%
echo Commit: %GIT_COMMIT%
echo ZIP created:
echo %ZIP_PATH%
echo ==========================================
echo.

explorer.exe /select,"%ZIP_PATH%"

pause
endlocal
