@echo off
REM Delete database.sqlite if exists
if exist "c:\Users\Admin\Desktop\coursework\db\database.sqlite" (
    del /f /q "c:\Users\Admin\Desktop\coursework\db\database.sqlite"
    echo deleted
) else (
    echo not found
)
