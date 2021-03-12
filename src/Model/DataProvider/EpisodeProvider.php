<?php
namespace Totallywicked\DevTest\Model\DataProvider;

use Totallywicked\DevTest\Model\Resource\EpisodeResource;

/**
 * Requests episode data
 */
class EpisodeProvider implements DataProviderInterface
{
    /**
     * @var EpisodeResource
     */
    protected $episodeResource;

    /**
     * Constructor
     * @param EpisodeResource $episodeResource
     */
    public function __construct(EpisodeResource $episodeResource)
    {
        $this->episodeResource = $episodeResource;
    }

    /**
     * Access character id, returns character data.
     * @inheritDoc
     */
    function getData(array $data): array
    {
        $id = $data['request']->getAttribute('id');
        $episode = $this->episodeResource->getById($id);
        $episodeData = $episode->getData();
        $episodeData['characters'] = [];
        foreach ($episode->getCharacters() as $episode) {
            $episodeData['characters'][] = $this->getNestedData($episode);
        }
        return [
            'episode' => $episodeData
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
