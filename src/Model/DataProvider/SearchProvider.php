<?php
namespace Totallywicked\DevTest\Model\DataProvider;

use Psr\Http\Message\UriFactoryInterface;
use Totallywicked\DevTest\Service\SearchAggregator;
use Totallywicked\DevTest\Exception\InvalidArgumentException;

/**
 * Requests search data
 */
class SearchProvider implements DataProviderInterface
{
    /**
     * @var SearchAggregator
     */
    protected $searchAggregator;

    /**
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * Constructor
     * @param SearchAggregator $searchAggregator
     * @param UriFactoryInterface $uriFactory
     */
    public function __construct(
        SearchAggregator $searchAggregator,
        UriFactoryInterface $uriFactory
    ) {
        $this->searchAggregator = $searchAggregator;
        $this->uriFactory = $uriFactory;
    }

    /**
     * Search for things
     * @inheritDoc
     */
    function getData(array $data): array
    {
        $query = $data['request']->getQueryParams();
        if (!isset($query['q'])) {
            throw new \InvalidArgumentException('Query parameter must be specified');
        }
        if (!isset($query['page'])) {
            $query['page'] = 1;
        }
        $results = $this->searchAggregator->search(['name' => $query['q']], $query['page']);
        $results['requestTime'] = $results['requestTime'] / 1000; // Seconds
        foreach ($results['result'] as $key => $entry) {
            $results['result'][$key]['data'] = $entry['data']->getData();
        }
        if ($results['previousPage'] !== null) {
            $results['previousPage'] = $this->buildNavUrl($query['q'], $results['previousPage']);
        }
        if ($results['nextPage'] !== null) {
            $results['nextPage'] = $this->buildNavUrl($query['q'], $results['nextPage']);
        }
        return [
            'results' => $results
        ];
    }

    /**
     * Returns URL for previous and next queries
     * @param array $query
     * @param integer $page
     * @return string
     */
    public function buildNavUrl($query, $page): string
    {
        $uri = $this->uriFactory->createUri("/search");
        return (string) $uri->withQuery(
            http_build_query([
                'q' => $query,
                'page' => $page
            ])
        );
    }
}
