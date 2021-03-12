<?php
namespace Totallywicked\DevTest\Service;

use Totallywicked\DevTest\Model\Resource\CharacterResource;
use Totallywicked\DevTest\Model\Resource\LocationResource;
use Totallywicked\DevTest\Model\Resource\EpisodeResource;
use Totallywicked\DevTest\Exception\OutOfBoundsException;

/**
 * SearchAggregator for searching and aggregating resources.
 */
class SearchAggregator
{
    /**
     * @var CharacterResource
     */
    protected $characterResource;

    /**
     * @var LocationResource
     */
    protected $locationResource;

    /**
     * @var EpisodeResource
     */
    protected $episodeResource;

    /**
     * Constructor
     * @param CharacterResource $characterResource
     * @param LocationResource $locationResource
     * @param EpisodeResource $episodeResource
     * @param array $data
     */
    public function __construct(
        CharacterResource $characterResource,
        LocationResource $locationResource,
        EpisodeResource $episodeResource
    ) {
        $this->characterResource = $characterResource;
        $this->locationResource = $locationResource;
        $this->episodeResource = $episodeResource;
    }

    /**
     * Queries and aggregates all resources.
     * TODO: I should probably make something like AggregatedArrayAccess,
     * it would make this method smaller and easier to manage.
     * 
     * It will return results in the following order:
     *  - CharacterResource
     *  - LocationResource
     *  - EpisodeResource
     * 
     * @param string $query
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @throws OutOfBoundsException When the page argument is invalid
     */
    public function search($query, $page = 1, $pageSize = 20)
    {
        $requestStartTime = hrtime(true);
        $totalItems = 0;
        $totalPages = 0;
        $resultItems = [];

        // Out of bounds check
        if ($page < 1) {
            throw new OutOfBoundsException("Page must be greater than 0");
        }

        // This looks like wolverine claws
        $collections = [
            [
                'collection' => $this->characterResource->search($query),
                'type' => 'character',
                'start' => 0,
                'end' => 0
            ],
            [
                'collection' => $this->locationResource->search($query),
                'type' => 'location',
                'start' => 0,
                'end' => 0
            ],
            [
                'collection' => $this->episodeResource->search($query),
                'type' => 'episode',
                'start' => 0,
                'end' => 0
            ]
        ];

        // Compute the number of items, and starting positions for each collection
        foreach ($collections as $key => $collection) {
            $totalItems += count($collection['collection']);
            $collections[$key]['end'] = $totalItems;
            if (isset($collections[$key+1])) {
                $collections[$key+1]['start'] = $totalItems;
            }
        }

        // Not found anything
        if ($totalItems < 1) {
            $requestEndTime = hrtime(true);
            return [
                'totalItems' => 0,
                'totalPages' => 0,
                'requestTime' => ($requestEndTime - $requestStartTime) / 1e+6, // Milliseconds
                'previousPage' => null,
                'currentPage' => $page,
                'nextPage' => null,
                'currentPageCount' => 0,
                'result' => []
            ];
        }

        // Total number of pages
        $totalPages = ceil($totalItems / $pageSize);

        // Out of bounds check
        if ($page > $totalPages) {
            throw new OutOfBoundsException("Page must be less than $totalPages");
        }

        // Start index
        $index = ($page - 1) * $pageSize;
        $end = (($page) * $pageSize) - 1;
        
        // Fetch items for this page
        foreach ($collections as $collection) {
            for (; $index >= $collection['start'] && $index < $collection['end']; $index++) { 
                $accessIndex = $index - $collection['start'];
                $resultItems[] = [
                    'type' => $collection['type'],
                    'data' => $collection['collection'][$accessIndex]
                ];
                if ($index >= $end) {
                    break;
                }
            }
            if ($index >= $end) {
                break;
            }
        }

        $requestEndTime = hrtime(true);
        return [
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'requestTime' => ($requestEndTime - $requestStartTime) / 1e+6, // Milliseconds
            'previousPage' => $page > 1 ? $page - 1 : null,
            'currentPage' => $page,
            'nextPage' => $page < $totalPages ? $page + 1 : null,
            'currentPageCount' => count($resultItems),
            'result' => $resultItems
        ];
    }
}
