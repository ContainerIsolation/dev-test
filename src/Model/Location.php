<?php
namespace Totallywicked\DevTest\Model;

use Totallywicked\DevTest\Model\Resource\CharacterResource;

/**
 * Location model
 * @inheritDoc
 */
class Location extends AbstractModel
{
    /**
     * @param CharacterResource
     */
    protected $characterResource;

    /**
     * Cache for the resident character data
     * @param array[Character]
     */
    protected $residentsCache;

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
     * Return the location ID
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Return the location name
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Return the location type
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * Return the location dimension
     * @return string
     */
    public function getDimension()
    {
        return $this->data['dimension'];
    }

    /**
     * Return the location residents
     * @return array[Character]
     */
    public function getResidents()
    {
        if ($this->residentsCache === null) {
            $characterIds = $this->getIdsFromUrls($this->data['residents']);
            if (!is_array($characterIds)) {
                $this->residentsCache = [$this->characterResource->getById($characterIds)];
            } else {
                $this->residentsCache = $this->characterResource->getByIds($characterIds);
            }
        }
        return $this->residentsCache;
    }

    /**
     * Return the location url
     * @return string
     */
    public function getUrl()
    {
        return $this->data['url'];
    }

    /**
     * Return the location created date
     * @return string
     */
    public function getCreated()
    {
        return $this->data['created'];
    }
}
