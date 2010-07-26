<?php

namespace Imagine;

/**
 * Command inteface
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
interface Command
{
    /**
     * Process the command
     *
     * @param \Imagine\Image $image
     */
    public function process(Image $image);
}
