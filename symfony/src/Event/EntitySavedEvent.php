<?php


namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\Movie;

class EntitySavedEvent extends Event
{
    private $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function getMovie()
    {
        return $this->movie;
    }
}
