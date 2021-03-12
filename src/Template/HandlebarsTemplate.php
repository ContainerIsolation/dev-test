<?php
namespace Totallywicked\DevTest\Template;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use LightnCandy\LightnCandy;

/**
 * Handlebars implementation of the template interface.
 */
class HandlebarsTemplate
{
    /**
     * Data passed to the template during rendering.
     * @param array
     */
    protected $data;

    /**
     * Contains absolute path to the view directory
     * @var string
     */
    protected $viewDirectory;

    /**
     * Contains contents of the template file
     * @var string
     */
    protected $template;

    /**
     * Contains contents of the layout file
     * @var string
     */
    protected $layout;

    /**
     * Compiled template ready to be executed
     * @var \callable
     */
    protected $renderTemplate;

    /**
     * Constructor
     * @param string $viewDirectory
     * @param array $data
     */
    public function __construct(string $viewDirectory, array $data = []) {
        $this->viewDirectory = $viewDirectory;
        $this->data = [];
    }

    /**
     * Sets data that will be provided to the template during rendering.
     * @param array $data
     * @return self
     * @throws InvalidArgumentException When $data is invalid
     */
    function setData(array $data): self
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("$data must be array");
        }
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Sets the template file that will be rendered.
     * @param string $filename
     * @return self
     * @throws NotFoundException When the filename does not exist
     */
    function setTemplate(string $filename): self
    {
        $this->template = $this->getTemplate($filename);
        return $this;
    }

    /**
     * Sets the layout template file that will be rendered.
     * @param string $filename
     * @return self
     * @throws NotFoundException When the filename does not exist
     */
    function setLayout(string $filename): self
    {
        $this->layout = $this->getTemplate($filename);
        return $this;
    }

    /**
     * Renders the template.
     * @return string
     * @throws \Exception When we don't know what happened
     */
    function render(): string
    {
        $this->prepare();
        return ($this->renderTemplate)($this->data);
    }

    /**
     * Prepares this template before rendering
     * @throws \Exception When we can't prepare the template
     */
    protected function prepare()
    {
        if ($this->layout === null || $this->layout === false) {
            throw new \Exception("Cannot prepare the template, no layout file provided");
        }
        if ($this->template === null || $this->template === false) {
            throw new \Exception("Cannot prepare the template, no template file provided");
        }
        if ($this->renderTemplate === null) {
            $php = LightnCandy::compile(
                $this->layout,
                [
                    'partials' => [
                        'body' => $this->template
                    ]
                ]
            );
            $this->renderTemplate = LightnCandy::prepare($php);
        }
    }

    /**
     * Opens the template file and reads its contents.
     * @param string $filename
     * @return string
     * @throws NotFoundException When the filename does not exist
     */
    protected function getTemplate($filename): string
    {
        $path = $this->viewDirectory . "/$filename.hbs";
        $realPath = realpath($this->viewDirectory . "/$filename.hbs");
        $data = @file_get_contents($path);
        if ($data === false || $data === null) {
            throw new NotFoundException("Could not find the template file \"$path\"");
        }
        return $data;
    }
}
