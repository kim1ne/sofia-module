<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

global $APPLICATION;

$APPLICATION->IncludeComponent(
    'sofia.test.news:news.list',
    '.default',
    [
        'PAGE_SIZE' => 7
    ]
);

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
