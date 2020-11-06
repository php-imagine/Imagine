#!/bin/bash

set -xe

IMAGEMAGICK_VERSION='6.8.9-10'
IMAGEMAGICK_MAJORVERSION=`printf %s "$IMAGEMAGICK_VERSION" | cut -f1 -d'.'`
PHP_VERSION=`php -r 'echo PHP_VERSION_ID;'`
CUSTOM_CFLAGS='-Wno-deprecated-declarations -Wno-misleading-indentation -Wno-nonnull-compare -Wno-tautological-compare -Wno-unused-but-set-variable'
if [ $PHP_VERSION -ge 80000 ]; then
    IMAGICK_VERSION='06116aa24b76edaf6b1693198f79e6c295eda8a9'
else
    IMAGICK_VERSION='3.4.3'
fi

mkdir -p cache
cd cache

if [ ! -e ./ImageMagick-$IMAGEMAGICK_VERSION ]; then
    rm -rf ./ImageMagick-* || true
    wget http://www.imagemagick.org/download/releases/ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    tar -xf ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    rm ImageMagick-$IMAGEMAGICK_VERSION.tar.xz
    cd ImageMagick-$IMAGEMAGICK_VERSION
    CFLAGS="${CFLAGS:-} ${CUSTOM_CFLAGS:-}" ./configure --disable-docs --prefix=/opt/imagemagick
    make -j V=0
else
    cd ImageMagick-$IMAGEMAGICK_VERSION
fi

sudo make install
export PKG_CONFIG_PATH=$PKG_CONFIG_PATH:/opt/imagemagick/lib/pkgconfig
if [ -L /opt/imagemagick/include/ImageMagick ]; then
    sudo unlink /opt/imagemagick/include/ImageMagick
fi 
sudo ln -s /opt/imagemagick/include/ImageMagick-$IMAGEMAGICK_MAJORVERSION /opt/imagemagick/include/ImageMagick
cd ..

if [ ! -e ./imagick-$IMAGICK_VERSION-$PHP_VERSION-$IMAGEMAGICK_VERSION ]; then
    rm -rf ./imagick-* || true
    if [ -z "${IMAGICK_VERSION##*.*}" ]; then
        wget https://pecl.php.net/get/imagick-$IMAGICK_VERSION.tgz
    else
    	wget --output-document=imagick-$IMAGICK_VERSION.tgz https://github.com/Imagick/imagick/archive/$IMAGICK_VERSION.tar.gz
    fi
    tar -xzf imagick-$IMAGICK_VERSION.tgz
    rm imagick-$IMAGICK_VERSION.tgz
    mv imagick-$IMAGICK_VERSION imagick-$IMAGICK_VERSION-$PHP_VERSION-$IMAGEMAGICK_VERSION
    cd imagick-$IMAGICK_VERSION-$PHP_VERSION-$IMAGEMAGICK_VERSION
    phpize
    if [ ! -f "./configure" ]; then
        autoconf
    fi
    CFLAGS="${CFLAGS:-} ${CUSTOM_CFLAGS:-}" ./configure --with-imagick=/opt/imagemagick
    make -j V=0
else
    cd imagick-$IMAGICK_VERSION-$PHP_VERSION-$IMAGEMAGICK_VERSION
fi

sudo make install
echo 'extension=imagick.so' >> `php --ini | grep 'Loaded Configuration' | sed -e 's|.*:\s*||'`
php --ri imagick
