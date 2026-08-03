<?php

namespace App\EventListener;

use App\Event\ViewedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class ViewedListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ViewedEvent $event): void
    {
        $this->logger->info('Fiche consultée', [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'type' => $event->getType(),
        ]);
    }
}