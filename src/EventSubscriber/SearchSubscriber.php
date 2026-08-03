<?php

namespace App\EventSubscriber;

use App\Event\SearchEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SearchSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SearchEvent::class => 'onSearch',
        ];
    }

    public function onSearch(SearchEvent $event): void
    {
        $this->logger->info('Recherche effectuée', [
            'query' => $event->getQuery(),
        ]);
    }
}