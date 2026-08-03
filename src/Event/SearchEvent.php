<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class SearchEvent extends Event
{
    public function __construct(
        private readonly string $query,
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}