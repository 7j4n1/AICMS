@echo off
cd /d C:\xampp\htdocs\AICMS
:: Start queue worker minimized, redirect output to log file
start /min "AICMS Import QueueWorker" cmd /c "php artisan queue:work --sleep=2 >> storage/logs/queue.log 2>&1"
