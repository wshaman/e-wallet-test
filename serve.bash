#!/usr/bin/env bash
cd "$( dirname "${BASH_SOURCE[0]}" )"
mydir=$(pwd)
echo "php -S 127.0.0.1:8080 -t $mydir/web/ $mydir/web/router.php"