<?php

namespace Sofia\Test\News\Orm;

use Bitrix\Main\ORM;
use Bitrix\Main\Type\DateTime;

class NewsTable extends ORM\Data\DataManager
{
    public static function getMap(): array
    {
        return [
            'ID' => new ORM\Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            'TITLE' => new ORM\Fields\StringField('TITLE', [
                'required' => true
            ]),
            'DESCRIPTION' => new ORM\Fields\StringField('DESCRIPTION', [
                'required' => true
            ]),
            'AUTHOR_ID' => new ORM\Fields\IntegerField('AUTHOR_ID', [
                'required' => false,
            ]),
            'DATE_CREATED' => new ORM\Fields\DatetimeField('DATE_CREATE', [
                'default_value' => function()
                {
                    return new DateTime();
                }
            ]),
        ];
    }

    public static function getTableName(): string
    {
        return 'sofia_news_table';
    }
}
