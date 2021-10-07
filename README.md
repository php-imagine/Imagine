# Imagine
[![PHPUnit](https://github.com/php-imagine/Imagine/actions/workflows/phpunit.yml/badge.svg)](https://github.com/php-imagine/Imagine/actions/workflows/phpunit.yml)
[![Coding Style](https://github.com/php-imagine/Imagine/actions/workflows/coding-style.yml/badge.svg)](https://github.com/php-imagine/Imagine/actions/workflows/coding-style.yml)

Tweet about it using the [#php_imagine](https://twitter.com/search?q=%23php_imagine) hashtag.

Image manipulation library for PHP 5.3 inspired by Python's PIL and other image
libraries.

## Requirements

The Imagine library has the following requirements:

 - PHP 5.3+

Depending on the chosen Image implementation, you may need one of the following PHP extensions:

 - GD2
 - Imagick (with ImageMagick version 6.2.9 or later, except version 7.0.7-32)
 - Gmagick

### Installation using composer
`php composer.phar require imagine/imagine`

## Basic Principles

The main purpose of Imagine is to provide all the necessary functionality to bring all native low level image processing libraries in PHP to the same simple and intuitive OO API.

Several things are necessary to accomplish that:

* Image manipulation tools, such as resize, crop, etc.
* Drawing API - to create basic shapes and advanced charts, write text on the image
* Masking functionality - ability to apply black&white or grayscale images as masks, leading to semi-transparency or absolute transparency of the image the mask is being applied to

The above tools should be the basic foundation for a more powerful set of tools that are called ``Filters`` in Imagine.

Some of the ideas for upcoming filters:

* Charting and graphing filters - pie and bar charts, linear graphs with annotations
* Reflection - apple style
* Rounded corners - web 2.0

## Documentation ##

 - [Hosted by Read The Docs](http://imagine.readthedocs.org/)

## Presentations ##

 - [Introduction to Imagine](http://www.slideshare.net/avalanche123/introduction-toimagine)
 - [How to Take Over the World with Lithium](http://speakerdeck.com/u/nateabele/p/how-to-take-over-the-world-with-lithium?slide=33)

## Articles ##

 - [Image Processing with Imagine](http://www.phparch.com/2011/03/image-processing-with-imagine)

## Contributing ##

New pull requests should be based on the `develop` branch.
The `master` branch is the stable branch: it usually matches the latest a release but in can be a bit ahead.

Setting up an environment with all the required libraries may be very hard.
In order to run the tests locally, you can use the same [docker images](https://github.com/php-imagine/docker-builder/pkgs/container/test) used by Imagine to test the pull requests.

For example, if you have Imagine locally in the `/home/me/imagine` folder, you can run tests for PHP 8.1 with the GD and Imagick with this very simple approach:

1. Launch a temporary docker container with:
   ```sh
   docker run --rm -it -v /home/me/imagine:/app ghcr.io/php-imagine/test:8.1-gd-imagick bash
   ```
2. Inside the docker container, run these commands:
   ```sh
   cd /app
   composer update
   compoer run test -- --exclude-group always-skipped,gmagick
   ```

PS: This approach works on Windows too: simply launch the docker container with

```
docker run --rm -it -v C:\Path\To\Imagine:/app ghcr.io/php-imagine/test:8.1-gd-imagick bash
```
