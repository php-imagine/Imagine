<?php
namespace Imagine\Filter\Chart;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\Chart\Data\DataPoint;
use Imagine\Filter\Chart\Data\DataSet;
use Imagine\Image\FontInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class ChartConfig
{
    /** @var  float */
    protected $xMin;

    /** @var  float */
    protected $xMax;

    /** @var  float */
    protected $yMax;

    /** @var  float */
    protected $yMin;

    /** @var  float */
    protected $scaleFactorX;

    /** @var  float */
    protected $scaleFactorY;

    /** @var  float */
    protected $originY;

    /** @var  float */
    protected $originX;

    /** @var int */
    protected $imageWidth;

    /** @var int */
    protected $imageHeight;

    /** @var  float */
    protected $negativeXRange;

    /** @var  float */
    protected $positiveXRange;

    /** @var  float */
    protected $negativeYRange;

    /** @var  float */
    protected $positiveYRange;

    /** @var float */
    protected $labelMargin;

    /** @var float */
    private $marginPercent;

    /** @var float */
    private $paddingPercent;

    /** @var  bool */
    private $labelAxes;

    /** @var FontInterface */
    private $font;

    /**
     * @param ImageInterface $image
     * @param array $dataSets
     * @param bool $fitViewToData
     * @param float $marginPercent
     * @param float $paddingPercent
     * @param bool $labelAxes
     * @param FontInterface $font
     */
    public function __construct(
        ImageInterface $image,
        $dataSets,
        $fitViewToData = false,
        $marginPercent = 0.0,
        $paddingPercent = 0.0,
        $labelAxes = false,
        FontInterface $font = null
    )
    {
        $this->font = $font;
        $this->labelAxes = $labelAxes;

        $this->imageWidth = $image->getSize()->getWidth();
        $this->imageHeight = $image->getSize()->getHeight();

        if ($marginPercent < 1 && $marginPercent > 0) {
            $this->marginPercent = $marginPercent;
        } else {
            $this->marginPercent = (abs($marginPercent) / 100) > 1 ? 0 : abs($marginPercent) / 100;
        }

        if ($paddingPercent < 1 && $paddingPercent > 0) {
            $this->paddingPercent = $paddingPercent;
        } else {
            $this->paddingPercent = (abs($paddingPercent) / 100) > 1 ? 0 : abs($paddingPercent) / 100;
        }

        $this->resolveLabelMargin();
        $this->resolveExtrema($dataSets);
        $this->resolveScaleFactorsAndOrigin($fitViewToData);
    }

    private function resolveLabelMargin()
    {
        if ($this->labelAxes && null !== $this->font) {
            $this->labelMargin = $this->font->getSize() * 3 / 4 * 3;
        } else {
            $this->labelMargin = 0.0;
        }
    }

    /**
     * @return float
     */
    public function getMarginX()
    {
        return $this->imageWidth * $this->marginPercent / 2 + $this->labelMargin;
    }

    /**
     * @return float
     */
    public function getMarginY()
    {
        return $this->imageHeight * $this->marginPercent / 2 + $this->labelMargin;
    }

    /**
     * @return float
     */
    public function getPaddingX()
    {
        return $this->imageWidth * $this->paddingPercent / 2;
    }

    /**
     * @return float
     */
    public function getPaddingY()
    {
        return $this->imageHeight * $this->paddingPercent / 2;
    }

    /**
     * @return float
     */
    public function getScaleFactorX()
    {
        return $this->scaleFactorX;
    }

    /**
     * @return float
     */
    public function getScaleFactorY()
    {
        return $this->scaleFactorY;
    }

    /**
     * @return float
     */
    public function getOriginY()
    {
        return $this->originY;
    }

    /**
     * @return float
     */
    public function getOriginX()
    {
        return $this->originX;
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * @return float
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * @return float
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * @return float
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * @return float
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * @return float
     */
    public function getNegativeXRange()
    {
        return $this->negativeXRange;
    }

    /**
     * @return float
     */
    public function getPositiveXRange()
    {
        return $this->positiveXRange;
    }

    /**
     * @return float
     */
    public function getNegativeYRange()
    {
        return $this->negativeYRange;
    }

    /**
     * @return float
     */
    public function getPositiveYRange()
    {
        return $this->positiveYRange;
    }

    /**
     * @return float
     */
    public function getLabelMargin()
    {
        return $this->labelMargin;
    }

    /**
     * @return Point
     */
    public function getOrigin()
    {
        return new Point($this->originX, $this->originY);
    }

    /**
     * @return FontInterface
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * @param DataPoint $point
     * @return Point|bool
     */
    public function scale(DataPoint $point)
    {
        try {
            return new Point(
                $this->scaleFactorX * $point->getX() + $this->originX,
                $this->originY - $this->scaleFactorY * $point->getY()
            );
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param array $dataSets
     */
    private function resolveExtrema(array $dataSets)
    {
        $minX = INF;
        $maxX = -INF;
        $maxY = -INF;
        $minY = INF;

        /** @var DataSet $set */
        foreach ($dataSets as $set) {
            if ($set->getMaxX() > $maxX) {
                $maxX = $set->getMaxX();
            }
            if ($set->getMinX() < $minX) {
                $minX = $set->getMinX();
            }
            if ($set->getMaxY() > $maxY) {
                $maxY = $set->getMaxY();
            }
            if ($set->getMinY() < $minY) {
                $minY = $set->getMinY();
            }
        }

        $this->xMax = $maxX;
        $this->xMin = $minX;
        $this->yMax = $maxY;
        $this->yMin = $minY;
    }

    /**
     * @param bool $fitViewToRange
     */
    private function resolveScaleFactorsAndOrigin($fitViewToRange = false)
    {
        $positiveXRange = ($this->xMax > 0) ? $this->xMax : 0;
        $negativeXRange = ($this->xMin < 0) ? $this->xMin : 0;
        $positiveYRange = ($this->yMax > 0) ? $this->yMax : 0;
        $negativeYRange = ($this->yMin < 0) ? $this->yMin : 0;

        if (false === $fitViewToRange) {
            if ($positiveXRange > abs($negativeXRange)) {$negativeXRange = -$positiveXRange;}
            if (abs($negativeXRange) > $positiveXRange) {$positiveXRange =  abs($negativeXRange);}
            if ($positiveYRange > abs($negativeYRange)) {$negativeYRange = -$positiveYRange;}
            if (abs($negativeYRange) > $positiveYRange) {$positiveYRange =  abs($negativeYRange);}
        }

        $xRange = $positiveXRange + abs($negativeXRange);
        $yRange = $positiveYRange + abs($negativeYRange);

        try {
            $this->scaleFactorX = ($this->imageWidth - (2 * $this->getMarginX()) - 2 * $this->getPaddingX()) / $xRange;
            $this->scaleFactorY = ($this->imageHeight - (2 * $this->getMarginY()) - 2 * $this->getPaddingY()) / $yRange;

            $leftoverX = ($this->getPaddingX()) / $this->scaleFactorX;
            $leftoverY = ($this->getPaddingY()) / $this->scaleFactorY;
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid chart configuration');
        }

        $this->originX = $this->getMarginX() + $this->getPaddingX() + abs($negativeXRange) * $this->scaleFactorX;
        $this->originY = $this->imageHeight - $this->getMarginY() - $this->getPaddingY() - abs($negativeYRange) * $this->scaleFactorY;

        $this->negativeXRange = $negativeXRange - $leftoverX;
        $this->positiveXRange = $positiveXRange + $leftoverX;
        $this->negativeYRange = $negativeYRange - $leftoverY;
        $this->positiveYRange = $positiveYRange + $leftoverY;
    }
}

