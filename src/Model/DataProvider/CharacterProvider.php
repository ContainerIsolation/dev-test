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
        $characterData['origin'] = $character->getOrigin()->getData();
        $characterData['location'] = $character->getLocation()->getData();
        $characterData['episode'] = [];
        foreach ($character->getEpisodes() as $episode) {
            $characterData['episode'][] = $episode->getData();
        }
        return [
            'character' => $characterData
        ];
    }
}
