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
            $locationData['residents'][] = $this->getNestedData($location);
        }
        return [
            'location' => $locationData
        ];
    }

    /**
     * Safely returns nested data
     * @param Model|null $model
     * @return array|null
     */
    public function getNestedData($model)
    {
        if ($model !== null) {
            return $model->getData();
        }
        return null;
    }
}
