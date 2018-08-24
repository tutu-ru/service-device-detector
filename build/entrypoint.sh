#!/usr/bin/env bash

set -eu
set -o pipefail

/opt/configure-php-env
/usr/bin/supervisord -n -c /etc/supervisord.conf
