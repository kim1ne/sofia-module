<?php

$aMenu = array();

$aMenu[] = array(
    "parent_menu" => "global_menu_settings",
    "section" => "GENERAL",
    "sort" => 1,
    "text" => 'Страница новостей',
    "title" => 'Страница новостей',
    "url" => "sofia_test_new.php?lang=".LANGUAGE_ID,
    "more_url" => array("favorite_edit.php"),
    "icon" => "bm_adv_menu_icon",
    "page_icon" => "fav_page_icon",
);

return $aMenu;
