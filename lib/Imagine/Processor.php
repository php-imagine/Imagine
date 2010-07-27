<?php

namespace Imagine;

/**
 * Processor
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jeremy Mikola <jmikola@gmail.com>
 */
class Processor
{
    /**
     * Commands
     *
     * @var array
     */
    protected $commands = array();

    /**
     * Adds a command to the processor
     *
     * @param string $name Command name (short class name)
     * @param array  $args Command constructor arguments
     * @return ImageProcessor
     * @throws \BadMethodCallException
     */
    public function __call($name, array $args) {
        // TODO: Allow support for multiple adapter libraries
        $commandClass = 'Imagine\\GD\\Command\\' . ucfirst($name);

        if (! class_exists($commandClass)) {
            throw new \BadMethodCallException('Command class not exist: ' . $commandClass);
        }

        return $this->addCommand($this->createCommand($commandClass, $args));
    }

    /**
     * Execute the commands on an image
     *
     * @param \Imagine\Image $image
     * @return ImageProcessor
     */
    public function process(Image $image)
    {
        foreach ($this->commands as $command) {
            $command->process($image);
        }
        return $this;
    }

    /**
     * Add a command to the processor
     *
     * @param \Imagine\Command $command
     * @return ImageProcessor
     */
    protected function addCommand(Command $command)
    {
        $this->commands[] = $command;
        return $this;
    }

    /**
     * Creates a command
     *
     * @param string $commandClass Command class name
     * @param array  $args         Command constructor arguments
     * @return \Imagine\Command
     */
    protected function createCommand($commandClass, array $args = array())
    {
        $commandReflection = new \ReflectionClass($commandClass);
        return $commandReflection->newInstanceArgs($args);
    }
}
