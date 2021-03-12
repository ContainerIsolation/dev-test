<?php
namespace Totallywicked\DevTest\Model;

use Totallywicked\DevTest\Model\Resource\EpisodeResource;
use Totallywicked\DevTest\Model\Resource\LocationResource;

/**
 * Character model
 * @inheritDoc
 */
class Character extends AbstractModel
{
    /**
     * @param EpisodeResource
     */
    protected $episodeResource;

    /**
     * @param LocationResource
     */
    protected $locationResource;

    /**
     * Cache for the episode data
     * @param array[Episode]
     */
    protected $episodeCache;

    /**
     * Cache for the location data
     * @param array[Location]
     */
    protected $locationCache;

    /**
     * Cache for the origin location data
     * @param Location
     */
    protected $originLocationCache;

    /**
     * Constructor
     * 
     * @param EpisodeResource $episodeResource
     * @param LocationResource $locationResource
     * @param array $data
     */
    public function __construct(
        EpisodeResource $episodeResource,
        LocationResource $locationResource,
        array $data = []
    ) {
        $this->episodeResource = $episodeResource;
        $this->locationResource = $locationResource;
        parent::__construct($data);
    }

    /**
     * Return the character ID
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Return the character name
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Return the character status
     * @return string
     */
    public function getStatus()
    {
        return $this->data['status'];
    }

    /**
     * Return the character species
     * @return string
     */
    public function getSpecies()
    {
        return $this->data['species'];
    }

    /**
     * Return the character type
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * Return the character gender
     * @return string
     */
    public function getGender()
    {
        return $this->data['gender'];
    }

    /**
     * Return the character origin name
     * @return string
     */
    public function getOriginName()
    {
        return $this->data['origin']['name'];
    }

    /**
     * Return the character origin location
     * @return Location
     */
    public function getOrigin()
    {
        if ($this->originLocationCache === null) {
            $locationId = $this->getIdsFromUrls([$this->data['origin']['url']]);
            if (!empty($locationId)) {
                $this->originLocationCache = $this->locationResource->getById($locationId);
            }
        }
        return $this->originLocationCache;
    }

    /**
     * Return the character location name
     * @return string
     */
    public function getLocationName()
    {
        return $this->data['location']['name'];
    }

    /**
     * Return the character location
     * @return Location
     */
    public function getLocation()
    {
        if ($this->locationCache === null) {
            $locationId = $this->getIdsFromUrls([$this->data['location']['url']]);
            if (!empty($locationId)) {
                $this->locationCache = $this->locationResource->getById($locationId);
            }
        }
        return $this->locationCache;
    }

    /**
     * Return the character image
     * @return string
     */
    public function getImage()
    {
        return $this->data['image'];
    }

    /**
     * Return the character episodes
     * @return array[Episode]
     */
    public function getEpisodes()
    {
        if ($this->episodeCache === null) {
            $episodeIds = $this->getIdsFromUrls($this->data['episode']);
            if (!is_array($episodeIds)) {
                $this->episodeCache = [$this->episodeResource->getById($episodeIds)];
            } else {
                $this->episodeCache = $this->episodeResource->getByIds($episodeIds);
            }
        }
        return $this->episodeCache;
    }

    /**
     * Return the character url
     * @return string
     */
    public function getUrl()
    {
        return $this->data['url'];
    }

    /**
     * Return the character created date
     * @return string
     */
    public function getCreated()
    {
        return $this->data['created'];
    }
}
