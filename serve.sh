#!/bin/bash
# Local dev server for the ProTorque build. Usage: ./serve.sh
cd "$(dirname "$0")/wp"
pkill -f "php -S 0.0.0.0:8080" 2>/dev/null
setsid nohup php -S 0.0.0.0:8080 -t . > ../php-server.log 2>&1 < /dev/null &
disown
sleep 1.5
curl -s --noproxy '*' -o /dev/null -w "server up: HTTP %{http_code}\n" http://localhost:8080/
