@echo off
del /f /s /q database.php
echo F| xcopy /Y /F database-����.php database.php
exit