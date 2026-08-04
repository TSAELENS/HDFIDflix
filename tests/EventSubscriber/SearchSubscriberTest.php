<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Event\SearchEvent;
use App\EventSubscriber\SearchSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SearchSubscriberTest extends TestCase
{
    public function testItSubscribesToSearchEvent(): void
    {
        self::assertSame(
            [
                SearchEvent::class => 'onSearch',
            ],
            SearchSubscriber::getSubscribedEvents(),
        );
    }

    public function testItLogsSearchQuery(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects(self::once())
            ->method('info')
            ->with(
                'Recherche effectuée',
                [
                    'query' => 'gladiator',
                ],
            );

        $subscriber = new SearchSubscriber($logger);

        $subscriber->onSearch(
            new SearchEvent('gladiator'),
        );
    }
}