<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

abstract class Advantage
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $points;

    /**
     * @param string $id
     * @param string $title
     * @param int $points
     *
     * @throws \InvalidArgumentException
     *   When the provided title is empty.
     *   When the provided points are zero and the advantage is a points promotion.
     */
    public function __construct($id, $title, $points)
    {
        $this->id = (string) $id;
        $this->title = (string) $title;
        $this->points = (int) $points;

        if (empty($this->title)) {
            throw new \InvalidArgumentException('Title should not be empty.');
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }
}
