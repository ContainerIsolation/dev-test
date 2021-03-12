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
     * The error that caused this action.
     * Should probably not use this?
     * $request->withAttribute - maybe better?
     * Keeping for now as not sure what would be a better solution.
     * @var \Exception
     */
    protected $error = null;

    /**
     * Sets the error that caused this action.
     * @param \Exception $error
     * @return self
     */
    public function setError(\Exception $error)
    {
        $this->error = $error;
    }

    /**
     * @inheritDoc
     */
    protected function getData(ServerRequestInterface $request): array
    {
        $data = [
            'request' => $request,
            'title' => $this->title,
            'error' => [
                'message' => $this->error->getMessage(),
                'stackTrace' => $this->error->getTraceAsString()
            ]
        ];
        if ($this->dataProvider !== null) {
            $providedData = $this->dataProvider->getData(['request' => $request]);
            return array_merge([], $data, $providedData);
        }
        return $data;
    }
}