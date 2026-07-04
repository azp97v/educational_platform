@echo off
setlocal enabledelayedexpansion

set "K6_PATH=%LOCALAPPDATA%\k6"
if not exist "%K6_PATH%\k6.exe" (
    echo Downloading k6...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/grafana/k6/releases/download/v0.54.0/k6-v0.54.0-windows-amd64.zip' -OutFile '%TEMP%\k6.zip'; Expand-Archive -Path '%TEMP%\k6.zip' -DestinationPath '%K6_PATH%' -Force; Move-Item '%K6_PATH%\k6-v0.54.0-windows-amd64\k6.exe' '%K6_PATH%\k6.exe' -Force; Remove-Item '%K6_PATH%\k6-v0.54.0-windows-amd64' -Recurse -Force -ErrorAction SilentlyContinue; Remove-Item '%TEMP%\k6.zip' -Force}"
)

set "TEST=%1"
if "%TEST%"=="" set "TEST=smoke"

if "%TEST%"=="smoke" set "SCRIPT=smoke.js"
if "%TEST%"=="scenarios" set "SCRIPT=scenarios.js"
if "%TEST%"=="stress" set "SCRIPT=stress.js"

echo Running %TEST% test: %SCRIPT%
"%K6_PATH%\k6.exe" run "%~dp0%SCRIPT%"
