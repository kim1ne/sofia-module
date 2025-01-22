<?php

use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) return;

global $APPLICATION;

?>

<form action="<?= $APPLICATION->GetCurPage(); ?>" name="blank-install">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="hidden" name="id" value="sofia.test.news">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">
    <input type="submit" name="inst" value="<?=Loc::getMessage('MOD_INSTALL_BUTTON')?>">
</form>
