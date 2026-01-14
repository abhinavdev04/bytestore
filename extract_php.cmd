@echo off
setlocal enabledelayedexpansion

:: Define the output file
set "outputFile=all_php_contents.txt"

:: Clear the output file if it already exists
if exist "%outputFile%" del "%outputFile%"

echo Processing files...

:: Loop through all .php files in the current folder and subfolders
for /r %%f in (*.php) do (
    echo. >> "%outputFile%"
    echo ########## >> "%outputFile%"
    echo %%~nxf >> "%outputFile%"
    echo ########## >> "%outputFile%"
    echo. >> "%outputFile%"
    
    :: Append the content of the file to the output
    type "%%f" >> "%outputFile%"
    
    :: Add extra line breaks between files
    echo. >> "%outputFile%"
    echo. >> "%outputFile%"
)

echo Done! All text saved to %outputFile%.
pause