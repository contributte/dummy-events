@echo off
%CD%\..\vendor\bin\tester.bat %CD%\Events -s -j 40 -log %CD%\events.log %*
rmdir %CD%\tmp /Q /S
