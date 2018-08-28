# Setup a sane environment
$ProgressPreference = 'SilentlyContinue'
$ErrorActionPreference = 'Stop'
$ConfirmPreference = 'None'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12 + [Net.SecurityProtocolType]::Tls11 + [Net.SecurityProtocolType]::Tls

# Setup the directory structure
if (-Not(Test-Path -PathType Container -Path C:\tools)) {
	Write-Output -InputObject 'Creating tools directory'
	New-Item -ItemType Directory -Path C:\tools | Out-Null
}
if (-Not(Test-Path -PathType Container -Path C:\tools\downloads)) {
	Write-Output -InputObject 'Creating download directory'
	New-Item -ItemType Directory -Path 'C:\tools\downloads' | Out-Null
}
if (-Not(Test-Path -PathType Container -Path C:\tools\bin)) {
	Write-Output -InputObject 'Creating bin directory'
	New-Item -ItemType Directory -Path C:\tools\bin | Out-Null
}
$Env:Path = 'C:\tools\bin;' + $Env:Path

# Setup PHP
if (-Not(Test-Path 'Env:PHP_VERSION')) {
	throw 'The PHP_VERSION environment variable is not set'
}
if (-Not(Test-Path 'Env:PHP_ARCHITECTURE')) {
	throw 'The PHP_ARCHITECTURE environment variable is not set'
}
$phpInstallPath = 'C:\tools\php-' + $Env:PHP_VERSION + '-' + $Env:PHP_ARCHITECTURE
if (-Not(Get-Module -ListAvailable -Name VcRedist)) {
	Write-Output -InputObject 'Installing VcRedist PowerShell module'
	Install-Module -Name VcRedist -Repository PSGallery -Scope AllUsers -Force
}
if (-Not(Get-Module -ListAvailable -Name PhpManager)) {
	Write-Output -InputObject 'Installing PhpManager PowerShell module'
	Install-Module -Name PhpManager -Repository PSGallery -Scope AllUsers -Force
}
Set-PhpDownloadCache -Path C:\tools\downloads
if (Test-Path -PathType Leaf -Path "$phpInstallPath\php-installed.txt") {
	Write-Output -InputObject 'Checking for PHP updates'
	Update-Php -Path $phpInstallPath -Verbose | Out-Null
} else {
	if (-Not(Test-Path 'Env:PHP_ARCHITECTURE')) {
		throw 'The PHP_ARCHITECTURE environment variable is not set'
	}
	Write-Output -InputObject 'Installing PHP'
	if (Test-Path -Path $phpInstallPath) {
		Remove-Item -Recurse -Force $phpInstallPath
	}
	Install-Php -Version $Env:PHP_VERSION -Architecture $Env:PHP_ARCHITECTURE -ThreadSafe $false -Path $phpInstallPath -TimeZone UTC -InitialPhpIni Production -InstallVC -Force -Verbose
	Set-PhpIniKey -Path $phpInstallPath -Key zend.assertions -Value 1
	Set-PhpIniKey -Path $phpInstallPath -Key assert.exception -Value On
	Enable-PhpExtension -Path $phpInstallPath -Extension mbstring,curl,openssl,gd,exif,zlib -Verbose
	Install-PhpExtension -Path $phpInstallPath -Extension imagick -Verbose
	New-Item -ItemType File -Path "$phpInstallPath\php-installed.txt" | Out-Null
}
Write-Output -InputObject 'Refreshing CA Certificates'
Update-PhpCAInfo -Path $phpInstallPath -Verbose

$Env:Path = $phpInstallPath + ';' + $Env:Path

# Setup composer
if (-Not(Test-Path -PathType Leaf -Path C:\tools\bin\composer.bat)) {
	Write-Output -InputObject 'Installing Composer'
	Install-Composer -Path C:\tools\bin -PhpPath $phpInstallPath -NoAddToPath -Verbose
}
