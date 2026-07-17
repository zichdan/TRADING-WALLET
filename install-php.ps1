$ErrorActionPreference = 'Stop'

Write-Host "=== Installing PHP 8.1 + Composer for Windows ===" -ForegroundColor Cyan

# Step 1: Download PHP 8.1
$phpDir = "C:\php"
$phpZip = "$env:TEMP\php.zip"

if (Test-Path "$phpDir\php.exe") {
    Write-Host "PHP already exists at $phpDir\php.exe - skipping download" -ForegroundColor Yellow
} else {
    Write-Host "Step 1: Downloading PHP 8.1 for Windows..." -ForegroundColor Green

    # Try to find the latest PHP 8.1 NTS VS16 x64 build
    $releasesPage = Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/' -UseBasicParsing
    $links = $releasesPage.Links | Where-Object { $_.href -match 'php-8\.1\.\d+-nts-Win32-VS16-x64\.zip' } | Select-Object -ExpandProperty href -First 1

    if (-not $links) {
        # Try VS17 builds
        $links = $releasesPage.Links | Where-Object { $_.href -match 'php-8\.1\.\d+-nts-Win32-VS17-x64\.zip' } | Select-Object -ExpandProperty href -First 1
    }

    if (-not $links) {
        # Fallback to a known working version
        $links = "php-8.1.29-nts-Win32-VS16-x64.zip"
    }

    $phpUrl = "https://windows.php.net/downloads/releases/$links"
    Write-Host "Downloading from: $phpUrl"

    Invoke-WebRequest -Uri $phpUrl -OutFile $phpZip
    Write-Host "Downloaded PHP zip: $phpZip"

    # Step 2: Extract PHP
    Write-Host "Step 2: Extracting PHP to $phpDir..." -ForegroundColor Green
    if (Test-Path $phpDir) { Remove-Item $phpDir -Recurse -Force }
    Expand-Archive -Path $phpZip -DestinationPath $phpDir -Force
    Write-Host "PHP extracted to $phpDir"
}

# Step 3: Configure php.ini
$phpIni = "$phpDir\php.ini"
if (-not (Test-Path $phpIni)) {
    Write-Host "Step 3: Configuring php.ini..." -ForegroundColor Green
    Copy-Item "$phpDir\php.ini-development" $phpIni

    # Enable required extensions
    $content = Get-Content $phpIni
    $content = $content -replace ';extension=curl', 'extension=curl'
    $content = $content -replace ';extension=mbstring', 'extension=mbstring'
    $content = $content -replace ';extension=openssl', 'extension=openssl'
    $content = $content -replace ';extension=pdo_mysql', 'extension=pdo_mysql'
    $content = $content -replace ';extension=gd', 'extension=gd'
    $content = $content -replace ';extension=fileinfo', 'extension=fileinfo'
    $content = $content -replace ';extension=zip', 'extension=zip'
    $content = $content -replace ';extension=xml', 'extension=xml'
    $content = $content -replace ';extension=bcmath', 'extension=bcmath'
    $content = $content -replace ';extension=intl', 'extension=intl'
    $content = $content -replace 'extension_dir = "ext"', 'extension_dir = "ext"'
    $content = $content -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
    Set-Content $phpIni $content
    Write-Host "php.ini configured with required extensions"
} else {
    Write-Host "php.ini already exists - skipping configuration" -ForegroundColor Yellow
}

# Step 4: Add PHP to PATH for current session
$env:PATH = "$phpDir;$env:PATH"

# Step 5: Add PHP to system PATH permanently
$systemPath = [Environment]::GetEnvironmentVariable('PATH', 'User')
if ($systemPath -notlike "*$phpDir*") {
    Write-Host "Step 4: Adding $phpDir to system PATH..." -ForegroundColor Green
    [Environment]::SetEnvironmentVariable('PATH', "$phpDir;$systemPath", 'User')
    Write-Host "PHP added to system PATH (restart terminal to use globally)"
} else {
    Write-Host "$phpDir already in system PATH" -ForegroundColor Yellow
}

# Step 6: Verify PHP
Write-Host "Step 5: Verifying PHP..." -ForegroundColor Green
& "$phpDir\php.exe" -v

# Step 7: Download Composer
$composerPhar = "$phpDir\composer.phar"
if (Test-Path $composerPhar) {
    Write-Host "Composer already exists at $composerPhar - skipping download" -ForegroundColor Yellow
} else {
    Write-Host "Step 6: Downloading Composer..." -ForegroundColor Green
    $installerUrl = 'https://getcomposer.org/installer'
    $installerPath = "$env:TEMP\composer-setup.php"
    Invoke-WebRequest -Uri $installerUrl -OutFile $installerPath
    & "$phpDir\php.exe" $installerPath --install-dir="$phpDir" --filename=composer.phar
    Remove-Item $installerPath -Force
    Write-Host "Composer downloaded to $composerPhar"
}

# Step 8: Create composer.bat wrapper
$composerBat = "$phpDir\composer.bat"
if (-not (Test-Path $composerBat)) {
    Write-Host "Step 7: Creating composer.bat wrapper..." -ForegroundColor Green
    Set-Content $composerBat "@php `"%~dp0composer.phar`" %*"
    Write-Host "composer.bat created at $composerBat"
}

# Step 9: Verify Composer
Write-Host "Step 8: Verifying Composer..." -ForegroundColor Green
& "$phpDir\php.exe" "$composerPhar" --version

Write-Host ""
Write-Host "=== PHP + Composer Installation Complete ===" -ForegroundColor Cyan
Write-Host "PHP location: $phpDir\php.exe" -ForegroundColor White
Write-Host "Composer location: $phpDir\composer.phar" -ForegroundColor White
Write-Host ""
Write-Host "Next: Run composer install in the core/ directory" -ForegroundColor Yellow
