@echo off
setlocal enabledelayedexpansion

:: Dossier contenant les fichiers .po et .mo
set "folder=languages"

cd /d "%folder%"

for %%F in (jquery-colorbox*.po jquery-colorbox*.mo) do (
    set "filename=%%~nF"
    set "ext=%%~xF"

    :: Remplacer "jquery-colorbox" par "jquery-colorbox-dafap-edition"
    set "newname=!filename:jquery-colorbox=jquery-colorbox-dafap-edition!!ext!"

    ren "%%F" "!newname!"
)

echo ✅ Tous les fichiers ont été renommés.
pause
