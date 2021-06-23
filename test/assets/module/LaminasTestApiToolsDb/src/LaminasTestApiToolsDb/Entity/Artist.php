<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LaminasTestApiToolsDb\Entity\Album;

class Artist
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->album = new ArrayCollection();
    }

    protected $id;

    public function getId()
    {
        return $this->id;
    }

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return static
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    protected $createdAt;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return static
     */
    public function setCreatedAt(DateTime $value): self
    {
        $this->createdAt = $value;

        return $this;
    }

    protected $album;

    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Add album
     *
     * @param Album $album
     * @return $this
     * @throws Exception
     */
    public function addAlbum($album)
    {
        if ($album instanceof Album) {
            $this->album[] = $album;
        } elseif ($album instanceof ArrayCollection) {
            foreach ($album as $a) {
                if (! $a instanceof Album) {
                    throw new Exception('Invalid type in addAlbum');
                }
                $this->album->add($a);
            }
        }

        return $this;
    }

    /**
     * Remove album
     *
     * @param Album $album
     *
     * @throws Exception
     *
     * @return void
     */
    public function removeAlbum($album): void
    {
        if ($album instanceof Album) {
            $this->album[] = $album;
        } elseif ($album instanceof ArrayCollection) {
            foreach ($album as $a) {
                if (! $a instanceof Album) {
                    throw new Exception('Invalid type remove addAlbum');
                }
                $this->album->removeElement($a);
            }
        }
    }
}
