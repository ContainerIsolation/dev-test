<?php
namespace Totallywicked\DevTest\Model\DataProvider;

use Totallywicked\DevTest\Model\Resource\CharacterResource;

/**
 * Requests character data
 */
class CharacterProvider implements DataProviderInterface
{
    /**
     * @var CharacterResource
     */
    protected $characterResource;

    /**
     * Constructor
     * @param CharacterResource $characterResource
     */
    public function __construct(CharacterResource $characterResource)
    {
        $this->characterResource = $characterResource;
    }

    /**
     * Access character id, returns character data.
     * @inheritDoc
     */
    function getData(array $data): array
    {
        $id = $data['request']->getAttribute('id');
        $character = $this->characterResource->getById($id);
        $characterData = $character->getData();
        $characterData['origin'] = $this->getNestedData($character->getOrigin());
        $characterData['location'] = $this->getNestedData($character->getLocation());
        $characterData['episode'] = [];
        foreach ($character->getEpisodes() as $episode) {
            $characterData['episode'][] = $this->getNestedData($episode);
        }
        return [
            'character' => $characterData
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
