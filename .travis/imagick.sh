#!/bin/bash

set -xe

IMAGEMAGICK_VERSION="6.8.9-10"
IMAGICK_VERSION="3.4.3"

mkdir -p cache
cd cache

if [ ! -e ./ImageMagick-$IMAGEMAGICK_VERSION ]
then
    wget http://www.imagemagick.org/download/releases/ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    tar -xf ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    rm ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    cd ImageMagick-$IMAGEMAGICK_VERSION
    ./configure --prefix=/opt/imagemagick
    make -j
else
    cd ImageMagick-$IMAGEMAGICK_VERSION
fi

sudo make install
export PKG_CONFIG_PATH=$PKG_CONFIG_PATH:/opt/imagemagick/lib/pkgconfig
sudo ln -s /opt/imagemagick/include/ImageMagick-6 /opt/imagemagick/include/ImageMagick
cd ..

if [ ! -e ./imagick-$IMAGICK_VERSION ]
then
    wget https://pecl.php.net/get/imagick-$IMAGICK_VERSION.tgz
    tar -xzf imagick-$IMAGICK_VERSION.tgz
    rm imagick-$IMAGICK_VERSION.tgz
    cd imagick-$IMAGICK_VERSION
    phpize
    ./configure --with-imagick=/opt/imagemagick
    make -j
else
    cd imagick-$IMAGICK_VERSION
fi

sudo make install
echo "extension=imagick.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
php --ri imagick
