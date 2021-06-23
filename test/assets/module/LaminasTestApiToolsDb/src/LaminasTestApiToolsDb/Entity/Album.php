<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Entity;

use DateTime;
use InvalidArgumentException;

class Album
{
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

    protected $artist;

    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @return static
     */
    public function setArtist(Artist $value): self
    {
        $this->artist = $value;

        return $this;
    }

    protected $album;

    /**
     * Parent Album
     *
     * @return null|Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Parent Album
     *
     * @param null|Album $album
     * @return $this
     */
    public function setAlbum($album)
    {
        if (null !== $album && ! $album instanceof Album) {
            throw new InvalidArgumentException('Invalid album argument');
        }
        $this->album = $album;
        return $this;
    }
}
