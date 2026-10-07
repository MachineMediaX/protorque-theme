#!/bin/bash
# Usage: ./shot.sh <path> <out.png> [width]   e.g. ./shot.sh / home.png 1440
cd "$(dirname "$0")"
curl -s --noproxy '*' -o /dev/null http://localhost:8080/ || ./serve.sh >/dev/null
cd theme-src && NO_PROXY=localhost,127.0.0.1 timeout 90 node shot.mjs "http://localhost:8080$1" "$2" "${3:-1440}" 2>&1 | grep -vE "^\s+at " | tail -1
