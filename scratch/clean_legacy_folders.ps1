$base = "docs/audit documents"

# 1. Move V2_CLIENT_DETAILS into client_drawer
$clientDetailsFiles = Get-ChildItem -Path "$base/V2_CLIENT_DETAILS" -File
foreach ($f in $clientDetailsFiles) {
    git mv "$base/V2_CLIENT_DETAILS/$($f.Name)" "$base/client_drawer/$($f.Name)"
    Write-Host "Moved $($f.Name) -> client_drawer/"
}
Remove-Item -Path "$base/V2_CLIENT_DETAILS" -Force -ErrorAction SilentlyContinue

# 2. Move V2_TEAMS into team_invitations
$teamsFiles = Get-ChildItem -Path "$base/V2_TEAMS" -File
foreach ($f in $teamsFiles) {
    git mv "$base/V2_TEAMS/$($f.Name)" "$base/team_invitations/$($f.Name)"
    Write-Host "Moved $($f.Name) -> team_invitations/"
}
Remove-Item -Path "$base/V2_TEAMS" -Force -ErrorAction SilentlyContinue

# 3. Move V2_SPARTNERLISTING into job_marketplace
$partnerFiles = Get-ChildItem -Path "$base/V2_SPARTNERLISTING" -File
foreach ($f in $partnerFiles) {
    git mv "$base/V2_SPARTNERLISTING/$($f.Name)" "$base/job_marketplace/$($f.Name)"
    Write-Host "Moved $($f.Name) -> job_marketplace/"
}
Remove-Item -Path "$base/V2_SPARTNERLISTING" -Force -ErrorAction SilentlyContinue

# 4. Move V2_Find_Agency into agency_listing
New-Item -ItemType Directory -Path "$base/agency_listing" -Force | Out-Null
$findAgencyFiles = Get-ChildItem -Path "$base/V2_Find_Agency" -File
foreach ($f in $findAgencyFiles) {
    git mv "$base/V2_Find_Agency/$($f.Name)" "$base/agency_listing/$($f.Name)"
    Write-Host "Moved $($f.Name) -> agency_listing/"
}
Remove-Item -Path "$base/V2_Find_Agency" -Force -ErrorAction SilentlyContinue

# 5. Move Feautres into talent_taxonomy
New-Item -ItemType Directory -Path "$base/talent_taxonomy" -Force | Out-Null
$featureFiles = Get-ChildItem -Path "$base/Feautres" -File
foreach ($f in $featureFiles) {
    git mv "$base/Feautres/$($f.Name)" "$base/talent_taxonomy/$($f.Name)"
    Write-Host "Moved $($f.Name) -> talent_taxonomy/"
}
Remove-Item -Path "$base/Feautres" -Force -ErrorAction SilentlyContinue
