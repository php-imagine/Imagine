<?php
namespace Imagine\Draw;

use Imagine\Image\Palette\Color\ColorInterface;

class LineStyle
{
    const LINE_SOLID = 'solid';
    const LINE_DASHED = 'dashed';
    const LINE_DOTTED = 'dotted';

    /**
     * @var int
     */
    private $thickness;

    /**
     * @var float
     */
    private $spacing;

    /**
     * @var string
     */
    private $style;

    /**
     * @var ColorInterface
     */
    private $color;

    /**
     * LineStyle constructor.
     * @param ColorInterface $color
     * @param string $style
     * @param int $thickness
     * @param float $spacing
     */
    public function __construct(
        ColorInterface $color,
        $style = self::LINE_SOLID,
        $thickness = 1,
        $spacing = 1.0
    )
    {
        $this->style = $style;
        $this->thickness = $thickness;
        $this->spacing = $spacing;
        $this->color = $color;
    }

    /**
     * @return ColorInterface
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @return float
     */
    public function getSpacing()
    {
        return $this->spacing;
    }

    /**
     * @return int
     */
    public function getThickness()
    {
        return $this->thickness;
    }
}
