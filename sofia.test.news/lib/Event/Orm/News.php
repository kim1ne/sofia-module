<?php

namespace Sofia\Test\News\Event\Orm;

use Bitrix\Main\ORM\Event;
use Bitrix\Main\UserTable;
use Sofia\Test\News\Service\Mailer;

class News
{
    const CACHE_TTL = 86400;

    public static function onAfterAdd(Event $event): bool
    {
        $parameters = $event->getParameters();

        return self::send(
            'Создали новость с ID=' . $parameters['id'],
            $parameters['fields']['DESCRIPTION']
        );
    }

    private static function send(string $subject, string $body): bool
    {
        $email = self::getEmailAdmin();

        return Mailer::send(
            $email,
            $subject,
            $body
        );
    }

    public static function getEmailAdmin(): string
    {
        $res = UserTable::getList([
            'filter' => [
                'ID' => 1,
            ],
            'select' => [
                'EMAIL'
            ],
            'cache' => self::CACHE_TTL
        ])->fetch();

        return $res['EMAIL'];
    }

    public static function onAfterUpdate(Event $event): bool
    {
        $parameters = $event->getParameters();

        return self::send(
            'Обновили новость с ID=' . $parameters['id'],
            $parameters['fields']['DESCRIPTION']
        );
    }
}
