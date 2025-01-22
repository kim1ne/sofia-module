<?php

use Bitrix\Main\HttpApplication;
use \Bitrix\Main\Localization\Loc;

use Bitrix\Main\Config\Option;

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);

$request = $request->getValues();
unset($request['sessid']);
unset($request['lang']);
unset($request['id']);
unset($request['install']);
unset($request['step']);
unset($request['inst']);
foreach ($request as $key => $value) {
    Option::set($module_id, $key, $value);
}

if (!check_bitrix_sessid()) return;

global $APPLICATION;

if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => 'HTML'
    ));
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}
?>
<?php ?>
<form action="<?= $APPLICATION->GetCurPage(); ?>" name="blank-install">
    <?=bitrix_sessid_post()?>
    <input type="submit" name="" value="В список">
</form>