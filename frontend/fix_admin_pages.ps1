$files = Get-ChildItem -Path "d:\laragon\www\dressnio\frontend\app\pages\admin" -Filter *.vue -Recurse
foreach ($file in $files) {
    try {
        $content = Get-Content -LiteralPath $file.FullName
        $content = $content -join "`n"
        $content = $content -replace '<DataTable', '<AdminDataTable'
        $content = $content -replace '</DataTable', '</AdminDataTable'
        $content = $content -replace '<HtmlForm', '<AdminHtmlForm'
        $content = $content -replace '</HtmlForm', '</AdminHtmlForm'
        $content = $content -replace '<FormElement', '<AdminFormElement'
        $content = $content -replace '</FormElement', '</AdminFormElement'
        
        $content = $content -replace 'pageBackLink: "/', 'pageBackLink: "/admin/'
        $content = $content -replace 'pageBackLink: "/admin/admin/', 'pageBackLink: "/admin/'
        
        Set-Content -LiteralPath $file.FullName $content
    } catch {
        Write-Host "Error processing $($file.FullName): $_"
    }
}
