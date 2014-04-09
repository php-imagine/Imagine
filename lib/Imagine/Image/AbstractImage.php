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

abstract class AbstractImage implements ImageInterface
{
    /**
     * @var Metadata\MetadataInterface
     */
    protected $metadata;

    /**
     * {@inheritdoc}
     */
    public function thumbnail(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if ($mode !== ImageInterface::THUMBNAIL_INSET &&
            $mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }

        $imageSize = $this->getSize();
        $ratios = array(
            $size->getWidth() / $imageSize->getWidth(),
            $size->getHeight() / $imageSize->getHeight()
        );

        $thumbnail = $this->copy();

        $thumbnail->usePalette($this->palette());
        $thumbnail->strip();
        // if target width is larger than image width
        // AND target height is longer than image height
        if ($size->contains($imageSize)) {
            return $thumbnail;
        }

        if ($mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        if ($mode === ImageInterface::THUMBNAIL_OUTBOUND) {
            if (!$imageSize->contains($size)) {
                $size = new Box(
                    min($imageSize->getWidth(), $size->getWidth()),
                    min($imageSize->getHeight(), $size->getHeight())
                );
            } else {
                $imageSize = $thumbnail->getSize()->scale($ratio);
                $thumbnail->resize($imageSize, $filter);
            }
            $thumbnail->crop(new Point(
                max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
            ), $size);
        } else {
            if (!$imageSize->contains($size)) {
                $imageSize = $imageSize->scale($ratio);
                $thumbnail->resize($imageSize, $filter);
            } else {
                $imageSize = $thumbnail->getSize()->scale($ratio);
                $thumbnail->resize($imageSize, $filter);
            }
        }

        return $thumbnail;
    }

    /**
     * {@inheritdoc}
     */
    public function metadata()
    {
        if ($this->metadata === null) {
            $this->metadata = new Metadata\ExifMetadata($this);
        }
        return $this->metadata;
    }

    /**
     * Assures the metadata instance will be cloned, too
     */
    function __clone()
    {
        if ($this->metadata !== null) {
            $this->metadata = clone $this->metadata;
        }
    }

}
