#!/bin/bash

set -e

service php7.1-fpm start

exec "$@"
