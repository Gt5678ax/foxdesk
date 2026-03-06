# Build foxdesk-0.3.63.zip with FORWARD slashes (required for Linux PHP extraction)
$src = 'C:/Dev/FoxDesk-dev/deploy/foxdesk'
$prefix = 'foxdesk-0.3.63'
$zipOut = 'C:/Dev/FoxDesk-dev/tmp/foxdesk-0.3.63.zip'

# Clean
if (Test-Path $zipOut) { Remove-Item $zipOut -Force }

# Dirs/files to EXCLUDE from files/
$excludeDirs = @('backups', 'uploads', 'storage', 'bin', 'build')
$excludeFiles = @('version.json', 'MANUAL.md', 'CLAUDE.md', 'AGENT-GUIDE.md', 'APP-FEATURES.md', 'UPDATE-GUIDE.md', 'HANDOFF.md')

Add-Type -Assembly System.IO.Compression
Add-Type -Assembly System.IO.Compression.FileSystem

# Create ZIP manually with forward slashes
$fileStream = [System.IO.File]::Create($zipOut)
$archive = New-Object System.IO.Compression.ZipArchive($fileStream, [System.IO.Compression.ZipArchiveMode]::Create)

# Add version.json at package root
$versionJsonPath = Join-Path $src 'version.json'
$entryName = "$prefix/version.json"
$entry = $archive.CreateEntry($entryName)
$entryStream = $entry.Open()
$sourceBytes = [System.IO.File]::ReadAllBytes($versionJsonPath)
$entryStream.Write($sourceBytes, 0, $sourceBytes.Length)
$entryStream.Close()

# Add all other files under files/
$added = 0
Get-ChildItem $src -Recurse -File | ForEach-Object {
    $fullPath = $_.FullName
    $relativePath = $fullPath.Substring($src.Length + 1).Replace('\', '/')

    # Check exclusions
    $skip = $false

    # Check if file is version.json at root
    if ($relativePath -eq 'version.json') { $skip = $true }

    # Check excluded files
    foreach ($ef in $excludeFiles) {
        if ($relativePath -eq $ef) { $skip = $true; break }
    }

    # Check excluded directories
    foreach ($ed in $excludeDirs) {
        if ($relativePath.StartsWith("$ed/")) { $skip = $true; break }
    }

    if (-not $skip) {
        $entryName = "$prefix/files/$relativePath"
        $entry = $archive.CreateEntry($entryName)
        $entryStream = $entry.Open()
        $sourceBytes = [System.IO.File]::ReadAllBytes($fullPath)
        $entryStream.Write($sourceBytes, 0, $sourceBytes.Length)
        $entryStream.Close()
        $added++
    }
}

$archive.Dispose()
$fileStream.Close()

$zipSize = (Get-Item $zipOut).Length
$zipKB = [math]::Round($zipSize / 1024)
Write-Host "Files added: $added"
Write-Host "ZIP created: $zipOut ($zipKB KB)"

# Verify structure - check for forward slashes
Write-Host ""
Write-Host "First 15 entries in ZIP:"
$verifyStream = [System.IO.File]::OpenRead($zipOut)
$verifyArchive = New-Object System.IO.Compression.ZipArchive($verifyStream, [System.IO.Compression.ZipArchiveMode]::Read)
$verifyArchive.Entries | Select-Object -First 15 -Property FullName | Format-Table -AutoSize
$verifyArchive.Dispose()
$verifyStream.Close()
