<?php

namespace Sofia\Test\News\Agents;

use Sofia\Test\News\Event\Orm\News;
use Sofia\Test\News\Orm\NewsTable;
use Sofia\Test\News\Service\Mailer;

class CheckNewsNotUser
{
    public static function invoke(): string
    {
        $res = NewsTable::getList([
            'select' => ['ID'],
            'filter' => [
                'LOGIC' => 'OR',
                [
                    'AUTHOR_ID' => 0
                ],
                [
                    'AUTHOR_ID' => NULL
                ]
            ]
        ]);

        $ids = [];

        while ($row = $res->fetch()) {
            $ids[] = $row['ID'];
        }

        $method = '\\' . __METHOD__ . '();';

        if (empty($ids)) {
            return $method;
        }

        self::send(
            'Найденные записи без AUTHOR_ID: ' . implode(', ', $ids),
            'Разберитесь'
        );

        return $method;
    }

    private static function send(string $subject, string $message): void
    {
        Mailer::send(
            self::getEmailAdmin(),
            $subject,
            $message
        );
    }

    private static function getEmailAdmin(): string
    {
        return News::getEmailAdmin();
    }
}
