#!/bin/bash

set -xe

IMAGEMAGICK_VERSION="6.8.9-10"
IMAGEMAGICK_DIR="ImageMagick-$IMAGEMAGICK_VERSION"
if [ "$WITHOUT_LCMS" = "true" ]
then
    IMAGEMAGICK_DIR="$IMAGEMAGICK_DIR-no-lcms"
fi
IMAGICK_VERSION="3.4.3"

mkdir -p cache
cd cache

if [ ! -e ./$IMAGEMAGICK_DIR ]
then
    wget http://www.imagemagick.org/download/releases/ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    tar -xf ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    rm ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    mv ImageMagick-$IMAGEMAGICK_VERSION $IMAGEMAGICK_DIR
    cd $IMAGEMAGICK_DIR
    if [ "$WITHOUT_LCMS" = "true" ]
    then
        ./configure --prefix=/opt/imagemagick --without-lcms --without-lcms2
    else
        ./configure --prefix=/opt/imagemagick
    fi
    make -j
else
    cd $IMAGEMAGICK_DIR
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
