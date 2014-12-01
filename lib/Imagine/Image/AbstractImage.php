<?php

/*
 * This file is part of the Imagine package.
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Imagine\Image;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Factory\ClassFactory;
use Imagine\Factory\ClassFactoryAwareInterface;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\Metadata\MetadataBag;

abstract class AbstractImage implements ImageInterface, ClassFactoryAwareInterface
{
    /**
     * @var MetadataBag
     */
    protected $metadata;

    /**
     * @var \Imagine\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     *
     * @return ImageInterface
     */
    public function thumbnail(BoxInterface $size, $settings = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $settings = $this->checkThumbnailSettings($settings);

        $mode = $settings & (ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_OUTBOUND);

        if (!$mode) {
            $mode = ImageInterface::THUMBNAIL_INSET;
        }

        $allowUpscale = (bool) ($settings & ImageInterface::THUMBNAIL_UPSCALE);

        $imageSize = $this->getSize();
        $ratios = array(
            $size->getWidth() / $imageSize->getWidth(),
            $size->getHeight() / $imageSize->getHeight(),
        );

        $thumbnail = $this->copy();

        $thumbnail->usePalette($this->palette());
        $thumbnail->strip();
        // if target width is larger than image width
        // AND target height is longer than image height
        if ($size->contains($imageSize) && !$allowUpscale) {
            return $thumbnail;
        }

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $imageSize = $imageSize->scale($ratio);
            $thumbnail->resize($imageSize, $filter);
        } else {
            if (!$imageSize->contains($size)) {
                if ($allowUpscale) {
                    $imageSize = $imageSize->scale($ratio);
                    $thumbnail->resize($imageSize, $filter);
                }
                $size = new Box(
                    min($imageSize->getWidth(), $size->getWidth()),
                    min($imageSize->getHeight(), $size->getHeight())
                );
            } else {
                $imageSize = $imageSize->scale($ratio);
                $thumbnail->resize($imageSize, $filter);
            }
            $thumbnail->crop(new Point(
                max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
            ), $size);
        }

        return $thumbnail;
    }

    /**
     * Check the settings argument in thumbnail() method
     */
    private function checkThumbnailSettings($settings)
    {
        // Preserve BC until version 1.0
        if ($settings === 'inset') {
            $settings = ImageInterface::THUMBNAIL_INSET;
        } elseif ($settings === 'outbound') {
            $settings = ImageInterface::THUMBNAIL_OUTBOUND;
        }

        $allSettings = ImageInterface::THUMBNAIL_INSET | ImageInterface::THUMBNAIL_OUTBOUND | ImageInterface::THUMBNAIL_UPSCALE;

        if (!is_int($settings) || ($settings & ~$allSettings)) {
            throw new InvalidArgumentException('Invalid setting specified');
        }

        if ($settings & ImageInterface::THUMBNAIL_INSET &&
            $settings & ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Only one mode should be specified');
        }

        return $settings;
    }

    /**
     * Updates a given array of save options for backward compatibility with legacy names.
     *
     * @param array $options
     *
     * @return array
     */
    protected function updateSaveOptions(array $options)
    {
        // Preserve BC until version 1.0
        if (isset($options['quality']) && !isset($options['jpeg_quality'])) {
            $options['jpeg_quality'] = $options['quality'];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function metadata()
    {
        return $this->metadata;
    }

    /**
     * Assures the metadata instance will be cloned, too.
     */
    public function __clone()
    {
        if ($this->metadata !== null) {
            $this->metadata = clone $this->metadata;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryAwareInterface::getClassFactory()
     */
    public function getClassFactory()
    {
        if ($this->classFactory === null) {
            $this->classFactory = new ClassFactory();
        }

        return $this->classFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Factory\ClassFactoryAwareInterface::setClassFactory()
     */
    public function setClassFactory(ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;

        return $this;
    }
}
