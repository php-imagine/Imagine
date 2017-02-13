#!/bin/bash

set -xe

GRAPHICSMAGIC_VERSION="1.3.23"
if [ $TRAVIS_PHP_VERSION = '7.0' ] || [ $TRAVIS_PHP_VERSION = '7.1' ]
then
  GMAGICK_VERSION="2.0.4RC1"
else
  GMAGICK_VERSION="1.1.7RC2"
fi

mkdir -p cache
cd cache

if [ ! -e ./GraphicsMagick-$GRAPHICSMAGIC_VERSION ]
then
    wget http://78.108.103.11/MIRROR/ftp/GraphicsMagick/1.3/GraphicsMagick-$GRAPHICSMAGIC_VERSION.tar.xz
    tar -xf GraphicsMagick-$GRAPHICSMAGIC_VERSION.tar.xz
    rm GraphicsMagick-$GRAPHICSMAGIC_VERSION.tar.xz
    cd GraphicsMagick-$GRAPHICSMAGIC_VERSION
    ./configure --prefix=/opt/gmagick --enable-shared --with-lcms2
    make -j
else
    cd GraphicsMagick-$GRAPHICSMAGIC_VERSION
fi

sudo make install
cd ..

if [ ! -e ./gmagick-$GMAGICK_VERSION ]
then
    wget https://pecl.php.net/get/gmagick-$GMAGICK_VERSION.tgz
    tar -xzf gmagick-$GMAGICK_VERSION.tgz
    rm gmagick-$GMAGICK_VERSION.tgz
    cd gmagick-$GMAGICK_VERSION
    phpize
    ./configure --with-gmagick=/opt/gmagick
    make -j
else
    cd gmagick-$GMAGICK_VERSION
fi

sudo make install
echo "extension=gmagick.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
php --ri gmagick
