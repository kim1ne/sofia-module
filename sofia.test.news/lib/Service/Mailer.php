<?php

namespace Sofia\Test\News\Service;

class Mailer
{
    public static function send(string $email, string $subject, string $message): bool
    {
        return mail($email, $subject, $message); // Можно через битрикс API \Bitrix\Main\Mail\Event::send
    }
}
