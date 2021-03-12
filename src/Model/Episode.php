<?php
namespace Totallywicked\DevTest\Model;

use Totallywicked\DevTest\Model\Resource\CharacterResource;

/**
 * Episode model
 * @inheritDoc
 */
class Episode extends AbstractModel
{
    /**
     * @param CharacterResource
     */
    protected $characterResource;

    /**
     * Cache for the characters that appeared in the episode
     * @param array[Character]
     */
    protected $charactersCache;

    /**
     * Constructor
     * 
     * @param CharacterResource $characterResource
     * @param array $data
     */
    public function __construct(CharacterResource $characterResource, array $data = []) {
        $this->characterResource = $characterResource;
        parent::__construct($data);
    }

    /**
     * Return the episode ID
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Return the episode name
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Return the episode air date
     * @return integer
     */
    public function getAirDate()
    {
        return $this->data['air_date'];
    }

    /**
     * Return the episode code
     * @return string
     */
    public function getEpisode()
    {
        return $this->data['episode'];
    }

    /**
     * Return the episode characters
     * @return array[Character]
     */
    public function getCharacters()
    {
        if ($this->charactersCache === null) {
            $characterIds = $this->getIdsFromUrls($this->data['characters']);
            if (!is_array($characterIds)) {
                $this->charactersCache = [$this->characterResource->getById($characterIds)];
            } else {
                $this->charactersCache = $this->characterResource->getByIds($characterIds);
            }
        }
        return $this->charactersCache;
    }

    /**
     * Return the episode url
     * @return string
     */
    public function getUrl()
    {
        return $this->data['url'];
    }

    /**
     * Return the episode created date
     * @return string
     */
    public function getCreated()
    {
        return $this->data['created'];
    }
}
