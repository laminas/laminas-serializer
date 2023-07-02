#!/bin/bash

CURRENT_DIRECTORY=$(pwd)
cd $TMPDIR
git clone https://github.com/php/pecl-text-wddx.git wddx
cd wddx
phpize
./configure
make
make install
cd $CURRENT_DIRECTORY
