<?php
namespace Totallywicked\DevTest\Template;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;

/**
 * A simple class which can be used to generate HTML pages out of the provided template.
 */
interface TemplateInterface
{
    /**
     * Sets data that will be provided to the template during rendering.
     * @param array $data
     * @return self
     * @throws InvalidArgumentException When $data is invalid
     */
    function setData(array $data): self;

    /**
     * Sets the template file that will be rendered.
     * @param string $filename
     * @return self
     * @throws NotFoundException When the filename does not exist
     */
    function setTemplate(array $filename): self;

    /**
     * Sets the layout template file that will be rendered.
     * @param string $filename
     * @return self
     * @throws NotFoundException When the filename does not exist
     */
    function setLayout(array $filename): self;

    /**
     * Renders the template.
     * @return string
     * @throws \Exception When we don't know what happened
     */
    function render(): string;
}
