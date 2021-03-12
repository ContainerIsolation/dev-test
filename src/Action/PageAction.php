<?php
namespace Totallywicked\DevTest\Action;

use Totallywicked\DevTest\Http\Response\ResponseFactory;
use Totallywicked\DevTest\Template\HandlebarsTemplateFactory;
use Totallywicked\DevTest\Template\HandlebarsTemplate;
use Totallywicked\DevTest\Model\DataProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Returns a page response.
 */
class PageAction implements RequestHandlerInterface
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var HandlebarsTemplateFactory
     */
    protected $handlebarsTemplateFactory;

    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * Store the title of this page
     * @var string
     */
    protected $title = '';

    /**
     * Store the layout filename
     * @var string
     */
    protected $layoutFile = '';

    /**
     * Store the template filename
     * @var string
     */
    protected $templateFile = '';

    /**
     * Response code for this action
     * @var int
     */
    protected $code = 200;

    /**
     * Constructor
     * @param ResponseFactory $responseFactory
     * @param HandlebarsTemplateFactory $handlebarsTemplateFactory
     * @param DataProviderInterface $dataProvider
     * @param string $layoutFile
     * @param string $templateFile
     * @param string $title
     */
    public function __construct(
        ResponseFactory $responseFactory,
        HandlebarsTemplateFactory $handlebarsTemplateFactory,
        DataProviderInterface $dataProvider = null,
        $templateFile = null,
        $layoutFile = null,
        $title = ''
    ) {
        $this->responseFactory = $responseFactory;
        $this->handlebarsTemplateFactory = $handlebarsTemplateFactory;
        if ($dataProvider !== null) {
            $this->dataProvider = $dataProvider;
        }
        if ($templateFile !== null) {
            $this->templateFile = $templateFile;
        }
        if ($layoutFile !== null) {
            $this->layoutFile = $layoutFile;
        }
        if (!empty($title)) {
            $this->title = $title;
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $template = $this->getTemplate($request);
        return $this->getResponse($template, $request);
    }

    /**
     * Creates and returns a new template response
     * @param HandlebarsTemplate $template
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function getResponse(HandlebarsTemplate $template, ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->make();
        $response->getBody()->write($template->render());
        return $response->withStatus($this->code);
    }

    /**
     * Creates and returns a new template
     * @param ServerRequestInterface $request
     * @return HandlebarsTemplate
     */
    protected function getTemplate(ServerRequestInterface $request): HandlebarsTemplate
    {
        $template = $this->handlebarsTemplateFactory->make();
        $template->setTemplate($this->templateFile);
        $template->setLayout($this->layoutFile);
        $template->setData($this->getData($request));
        return $template;
    }

    /**
     * Returns data for this page, can be overriden in the parent action.
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getData(ServerRequestInterface $request): array
    {
        $data = [
            'request' => $request,
            'title' => $this->title
        ];
        if ($this->dataProvider !== null) {
            $providedData = $this->dataProvider->getData(['request' => $request]);
            return array_merge([], $data, $providedData);
        }
        return $data;
    }
}
