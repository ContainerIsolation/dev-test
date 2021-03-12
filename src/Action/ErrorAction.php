<?php
namespace Totallywicked\DevTest\Action;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Used when application has thrown an exception.
 */
class ErrorAction extends PageAction
{
    /**
     * Store the title of this page
     * @var string
     */
    protected $title = 'Error';

    /**
     * Store the layout filename
     * @var string
     */
    protected $layoutFile = 'layout/standard';

    /**
     * Store the template filename
     * @var string
     */
    protected $templateFile = '500';

    /**
     * Response code for this action
     * @var int
     */
    protected $code = 500;

    /**
     * @inheritDoc
     */
    protected function getData(ServerRequestInterface $request): array
    {
        $data = [
            'request' => $request,
            'title' => $this->title,
            'error' => [
                'message' => $request->getAttribute("error")->getMessage(),
                'class' => get_class($request->getAttribute("error")),
                'file' => $request->getAttribute("error")->getFile(),
                'line' => $request->getAttribute("error")->getLine(),
                'stackTrace' => $request->getAttribute("error")->getTraceAsString()
            ]
        ];
        if ($this->dataProvider !== null) {
            $providedData = $this->dataProvider->getData(['request' => $request]);
            return array_merge([], $data, $providedData);
        }
        return $data;
    }
}