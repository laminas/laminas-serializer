#!/bin/bash

PHP_VERSION=$4

if [ "${PHP_VERSION}" != "8.3" ]; then
  exit 0;
fi

set -e -o pipefail

pecl install igbinary

echo "extension = igbinary.so" > /etc/php/${PHP_VERSION}/mods-available/igbinary.ini
