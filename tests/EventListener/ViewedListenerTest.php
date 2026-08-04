<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\Event\ViewedEvent;
use App\EventListener\ViewedListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ViewedListenerTest extends TestCase
{
    public function testItLogsViewedMedia(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects(self::once())
            ->method('info')
            ->with(
                'Fiche consultée',
                [
                    'id' => 1,
                    'title' => 'Gladiator',
                    'type' => 'movie',
                ],
            );

        $listener = new ViewedListener($logger);

        $listener(
            new ViewedEvent(
                1,
                'Gladiator',
                'movie',
            ),
        );
    }
}