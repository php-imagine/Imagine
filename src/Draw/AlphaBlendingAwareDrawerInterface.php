<?php

namespace Imagine\Draw;

/**
 * Interface for the drawers that support configuring the alpha blending.
 */
interface AlphaBlendingAwareDrawerInterface extends DrawerInterface
{
    /**
     * Is the alpha blending activated?
     *
     * @return bool
     */
    public function getAlphaBlending();

    /**
     * Enable/disable the alpha blending.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setAlphaBlending($value);

    /**
     * Create a new instance of this drawer with the specified alpha blending value.
     *
     * @param bool $value
     *
     * @return static
     */
    public function withAlphaBlending($value);
}
