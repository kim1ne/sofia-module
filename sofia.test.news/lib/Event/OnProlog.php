<?php

namespace Sofia\Test\News\Event;

use Bitrix\Main\EventManager;
use Sofia\Test\News\Event\Orm\News;
use Sofia\Test\News\Orm\NewsTable;

class OnProlog
{
    public static function event(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'sofia.test',
            'News' . NewsTable::EVENT_ON_AFTER_ADD,
            [News::class, 'onAfterAdd']
        );

        $eventManager->addEventHandler(
            'sofia.test',
            'News' . NewsTable::EVENT_ON_AFTER_UPDATE,
            [News::class, 'onAfterAdd']
        );
    }
}
