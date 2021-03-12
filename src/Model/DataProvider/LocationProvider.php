<?php
namespace Totallywicked\DevTest\Model\DataProvider;

use Totallywicked\DevTest\Model\Resource\LocationResource;

/**
 * Requests location data
 */
class LocationProvider implements DataProviderInterface
{
    /**
     * @var LocationResource
     */
    protected $locationResource;

    /**
     * Constructor
     * @param LocationResource $locationResource
     */
    public function __construct(LocationResource $locationResource)
    {
        $this->locationResource = $locationResource;
    }

    /**
     * Access character id, returns character data.
     * @inheritDoc
     */
    function getData(array $data): array
    {
        $id = $data['request']->getAttribute('id');
        $location = $this->locationResource->getById($id);
        $locationData = $location->getData();
        $locationData['residents'] = [];
        foreach ($location->getResidents() as $location) {
            $locationData['residents'][] = $location->getData();
        }
        return [
            'location' => $locationData
        ];
    }
}
