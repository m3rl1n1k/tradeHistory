@echo off
setlocal enabledelayedexpansion

:: Приймаємо аргументи
set COMMAND=%1
set ARGUMENT=%2
set FILE=%3

:: Відображення отриманих аргументів
echo Running command: %COMMAND% with argument: %ARGUMENT%
echo.

:: Опис команд
if "%COMMAND%"=="--start" (
    echo Executing: docker compose --env-file .env.local up %ARGUMENT%
    docker compose --env-file .env.local up %ARGUMENT%
) else if "%COMMAND%"=="--restart" (
    echo Executing: docker compose --env-file .env.local restart
    docker compose --env-file .env.local restart
) else if "%COMMAND%"=="--stop" (
    echo Executing: docker compose --env-file .env.local stop
    docker compose --env-file .env.local stop
) else if "%COMMAND%"=="--down" (
    echo Executing: docker compose --env-file .env.local stop
    docker compose --env-file .env.local down
) else if "%COMMAND%"=="--connect" (
    echo Executing: docker compose --env-file .env.local exec -it %ARGUMENT% /bin/bash
    docker compose --env-file .env.local exec -it %ARGUMENT% /bin/bash
) else if "%COMMAND%"=="--build" (
    echo Executing: docker compose --env-file %ARGUMENT% up --build
    docker compose --env-file %ARGUMENT% up --build
) else if "%COMMAND%"=="--kill" (
    call :killContainers
) else if "%COMMAND%"=="--remove" (
    call :removeContainers
) else (
    echo Command %COMMAND% not found!
)

goto :EOF

:: Функція для зупинки контейнерів
:killContainers
echo Killing containers: %ARGUMENT%
for %%C in (%ARGUMENT%) do (
    echo Executing: docker kill %%C
    docker kill %%C
)
goto :EOF

:: Функція для видалення контейнерів
:removeContainers
echo Removing containers: %ARGUMENT%
for %%C in (%ARGUMENT%) do (
    echo Executing: docker rm %%C
    docker rm %%C
)
goto :EOF