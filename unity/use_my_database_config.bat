@echo off
del /f /s /q self_config.php
echo F| xcopy /Y /F self_config-����.php self_config.php
exit