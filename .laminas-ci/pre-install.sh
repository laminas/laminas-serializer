#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
apt update && apt install -y --no-install-recommends libxml2-dev

CURRENT_DIRECTORY=$(pwd)
cd $TMPDIR
git clone https://github.com/php/pecl-text-wddx.git wddx
cd wddx
phpize
./configure
make
make install
cd $CURRENT_DIRECTORY
