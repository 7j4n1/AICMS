@echo off
cd /d C:\xampp\htdocs
:: Start queue worker minimized, redirect output to log file
start /min "AICMS Import QueueWorker" cmd /c "php artisan queue:work --queue=member-imports,loan-imports,ledger-imports,csv-imports,excel-reports,default --sleep=2 >> storage/logs/queue.log 2>&1"
