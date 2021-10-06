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

abstract class AbstractImage implements ImageInterface, ClassFactoryAwareInterface
{
    /**
     * @var \Imagine\Image\Metadata\MetadataBag
     */
    protected $metadata;

    /**
     * @var \Imagine\Factory\ClassFactoryInterface|null
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ManipulatorInterface::thumbnail()
     */
    public function thumbnail(BoxInterface $size, $settings = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $settings = $this->checkThumbnailSettings($settings);

        $mode = $settings & 0xffff;

        $allowUpscale = (bool) ($settings & ImageInterface::THUMBNAIL_FLAG_UPSCALE);
        $noClone = (bool) ($settings & ImageInterface::THUMBNAIL_FLAG_NOCLONE);

        $imageSize = $this->getSize();
        $palette = $this->palette();

        $thumbnail = $noClone ? $this : $this->copy();

        $thumbnail->usePalette($palette);
        $thumbnail->strip();

        if ($size->getWidth() === $imageSize->getWidth() && $size->getHeight() === $imageSize->getHeight()) {
            // The thumbnail size is the same as the wanted size.
            return $thumbnail;
        }
        if (!$allowUpscale && $size->contains($imageSize)) {
            // Thumbnail is smaller than the image and we are not upscaling
            return $thumbnail;
        }

        $ratios = array(
            $size->getWidth() / $imageSize->getWidth(),
            $size->getHeight() / $imageSize->getHeight(),
        );
        switch ($mode) {
            case ImageInterface::THUMBNAIL_OUTBOUND:
                // Crop the image so that it fits the wanted size
                $ratio = max($ratios);
                if ($imageSize->contains($size)) {
                    // Downscale the image
                    $imageSize = $imageSize->scale($ratio);
                    $thumbnail->resize($imageSize, $filter);
                    $thumbnailSize = $size;
                } else {
                    if ($allowUpscale) {
                        // Upscale the image so that the max dimension will be the wanted one
                        $imageSize = $imageSize->scale($ratio);
                        $thumbnail->resize($imageSize, $filter);
                    }
                    $thumbnailSize = new Box(
                        min($imageSize->getWidth(), $size->getWidth()),
                        min($imageSize->getHeight(), $size->getHeight())
                    );
                }
                $thumbnail->crop(
                    new Point(
                        max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                        max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
                    ),
                    $thumbnailSize
                );
                break;
            case ImageInterface::THUMBNAIL_INSET:
            default:
                // Scale the image so that it fits the wanted size
                $ratio = min($ratios);
                $thumbnailSize = $imageSize->scale($ratio);
                $thumbnail->resize($thumbnailSize, $filter);
                break;
        }

        return $thumbnail;
    }

    /**
     * Check the settings argument in thumbnail() method.
     *
     * @param int $settings
     */
    private function checkThumbnailSettings($settings)
    {
        // Preserve BC until version 1.0
        if (is_string($settings)) {
            if ($settings === 'inset') {
                $settings = ImageInterface::THUMBNAIL_INSET;
            } elseif ($settings === 'outbound') {
                $settings = ImageInterface::THUMBNAIL_OUTBOUND;
            } elseif (is_numeric($settings)) {
                $settings = (int) $settings;
            }
        }
        if (!is_int($settings)) {
            throw new InvalidArgumentException('Invalid setting specified');
        }
        $mode = $settings & 0xffff;
        if ($mode === 0) {
            $settings |= ImageInterface::THUMBNAIL_INSET;
        } else {
            if (!in_array($mode, $this->getAllThumbnailModes())) {
                if (strlen(str_replace('0', '', decbin($mode))) === 1) {
                    throw new InvalidArgumentException('Invalid setting specified');
                }
                throw new InvalidArgumentException('Only one mode should be specified');
            }
        }

        return $settings;
    }

    /**
     * Get all the available thumbnail modes.
     *
     * @return int[]
     */
    protected function getAllThumbnailModes()
    {
        return array(
            ImageInterface::THUMBNAIL_INSET,
            ImageInterface::THUMBNAIL_OUTBOUND,
        );
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
     * Get all the available filter defined in ImageInterface.
     *
     * @return string[]
     */
    protected static function getAllFilterValues()
    {
        static $result;
        if (!is_array($result)) {
            $values = array();
            $interface = new \ReflectionClass('Imagine\Image\ImageInterface');
            foreach ($interface->getConstants() as $name => $value) {
                if (strpos($name, 'FILTER_') === 0) {
                    $values[] = $value;
                }
            }
            $result = $values;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Imagine\Image\ImageInterface::metadata()
     */
    public function metadata()
    {
        return $this->metadata;
    }

    /**
     * Clones all the resources associated to this instance.
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
