#!/bin/sh
groupadd -g $GID shimmie
useradd -ms /bin/bash -u $UID -g $GID shimmie
mkdir /app/data
chown shimmie:shimmie /app/data
export PHP_CLI_SERVER_WORKERS=8
exec /usr/local/bin/su-exec shimmie:shimmie \
  /usr/bin/php \
    -d upload_max_filesize=50M \
    -d post_max_size=50M \
    -S 0.0.0.0:8000 -q \
    tests/router.php
