<?php

use Bitrix\Main\Application;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Sofia\Test\News\Agents\CheckNewsNotUser;
use Sofia\Test\News\Event\OnProlog;
use Sofia\Test\News\Orm\NewsTable;

Class sofia_test_news extends CModule
{
    public $MODULE_ID;
    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_GROUP_RIGHTS = 'Y';

    function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');
        $this->MESS_PREFIX = mb_strtoupper(get_class($this));
        $this->MODULE_ID = str_replace('_', '.', get_class($this));
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage('MODULE_NETI_BLANK_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_NETI_BLANK_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('PARTNER_NAME');
        $this->PARTNER_URI = 'https://php.i-neti.ru';

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $step = intval($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile('', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/step1.php");
        }
        if ($step == 2) {
            RegisterModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallEvents();
            $APPLICATION->IncludeAdminFile('Завершение установки', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/step2.php");
        }
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT, $step;
        $step = intval($step);

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile('', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/unstep1.php");
        }
        if ($step == 2) {
            $context = \Bitrix\Main\Application::getInstance()->getContext();
            $request = $context->getRequest();
            if ($request->getValues()['savedata'] !== 'Y') {
                $this->UnInstallDB();
            }
            UnRegisterModule($this->MODULE_ID);
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $APPLICATION->IncludeAdminFile('Удаление завершено', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/unstep2.php");
        }


        return true;
    }

    function InstallEvents()
    {
        RegisterModuleDependences(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            OnProlog::class,
            'event',
            "100"
        );

        \CAgent::AddAgent(
            '\\' . CheckNewsNotUser::class . 'invoke();',
            'sofia.test.news',
            "Y",
            86400
        );

        return false;
    }

    function InstallDB()
    {
        $connection = Application::getConnection();

        $entity = NewsTable::getEntity();

        if (!$connection->isTableExists($entity->getDBTableName())) {
            $sql = $entity->compileDbTableStructureDump();
            $connection->queryExecute(implode('; ', $sql));
        }

        return true;
    }

    function UnInstallDB(): bool
    {
        $connection = Application::getConnection();
        $connection->dropTable(NewsTable::getTableName());
        return true;
    }

    function UnInstallEvents()
    {
        UnRegisterModuleDependences(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            OnProlog::class,
            'event',
            "100"
        );

        $res = \CAgent::GetList(arFilter: [
            'MODULE_ID' => $this->MODULE_ID,
        ]);

        while($row  = $res->GetNext()) {
            \CAgent::Delete($row['ID']);
        }

        return true;
    }

    function InstallFiles()
    {
        $menuFile = $this->menuFile();
        copy(
            __DIR__ . '/../admin/' . $menuFile,
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $menuFile
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/local/modules/' . $this->MODULE_ID . '/install/components',
            $_SERVER['DOCUMENT_ROOT'] . '/local/components',
            true,
            true
        );

        return true;
    }

    private function menuFile(): string
    {
        return 'sofia_test_new.php';
    }

    function UnInstallFiles()
    {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->menuFile());

        DeleteDirFilesEx(
            '/local/components/sofia.test.news'
        );

        return true;
    }
}
