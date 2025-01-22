<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ORM\Query\Result;
use Sofia\Test\News\Orm\NewsTable;

\CModule::includeModule("sofia.test.news");

class NewsListComponent extends \CBitrixComponent implements Controllerable
{
    public function onPrepareComponentParams($arParams): void
    {
        $page = (int) $_GET['page'];

        if ($page < 1) {
            $page = 1;
        }

        $this->arResult['PAGE'] = $page;
        $this->arResult['PAGE_SIZE'] = $arParams['PAGE_SIZE'] ?? 10;

        $filterDate = $arParams['FILTER_DATE'];

        $date = null;
        if (!empty($filterDate)) {
            $timestamp = strtolower($filterDate);
            $date = \Bitrix\Main\Type\DateTime::createFromTimestamp($timestamp);
        }

        $this->arResult['FILTER_DATE'] = $date;

        $this->arResult['DATE_DIRECTION'] = $arParams['DATE_DIRECTION'];
    }

    public function executeComponent(): void
    {
        $this->arResult['TOTAL_PAGES'] = $this->getTotalPages();
        $this->arResult['ITEMS'] = $this->getItems();
        $this->includeComponentTemplate();
    }

    public function getItems(): array
    {
        $res = NewsTable::getList($this->getParamsQuery(['*'], $this->getPageSize(), $this->getOffset()));

        $items = [];

        while ($row = $res->fetch()) {
            $items[] = $row;
        }

        return $items;
    }

    public function getTotalPages(): int
    {
        $totalRecord = $this->getTotalRecord();
        $size = $this->getPageSize();

        return ceil($totalRecord / $size);

    }

    public function getTotalRecord(): int
    {
        return $this->query(['ID'], null, null)->getSelectedRowsCount();
    }

    private function query(array $select, ?int $limit, ?int $offset): Result
    {
        return NewsTable::getList(
            $this->getParamsQuery($select, $limit, $offset)
        );
    }

    public function getPage(): int
    {
        return $this->arResult['PAGE'];
    }

    public function getPageSize(): int
    {
        return $this->arResult['PAGE_SIZE'];
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getPageSize();
    }

    public function getParamsQuery(array $select, ?int $limit, ?int $offset): array
    {
        $params = [
            'select' => $select,
        ];

        $filter = $this->getFilter();

        if (!empty($filter)) {
            $params['filter'] = $filter;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($offset)) {
            $params['offset'] = $offset;
        }

        return $params;
    }

    public function getFilter(): array
    {
        $filter = [];

        if (!empty($date = $this->arResult['FILTER_DATE'])) {
            $dateDirection = $this->arResult['DATE_DIRECTION'];

            if ($dateDirection === 'DESC') {
                $dateDirection = '>';
            } else {
                $dateDirection = '<';
            }

            $filter[$dateDirection . 'DATE_DIRECTION'] = $date;
        }

        return $filter;
    }

    public function configureActions(): array
    {
        return [
            'delete' => [
                'prefilters' => []
            ],
            'edit' => [
                'prefilters' => []
            ],
            'create' => [
                'prefilters' => []
            ],
        ];
    }

    public function createAction(): array
    {
        $title = $this->request->get('TITLE');
        $description = $this->request->get('DESCRIPTION');
        $authorId = $this->request->get('AUTHOR_ID');

        $data = [
            'TITLE' => $title,
            'DESCRIPTION' => $description,
            'AUTHOR_ID' => $authorId,
        ];

        $result = NewsTable::add($data);

        if (!$result->isSuccess()) {
            throw new \Exception(implode(", ", $result->getErrorMessages()));
        }

        return $data;
    }

    public function editAction(): array
    {
        $title = $this->request->get('TITLE');
        $description = $this->request->get('DESCRIPTION');
        $dateCreated = $this->request->get('DATE_CREATED');
        $authorId = $this->request->get('AUTHOR_ID');
        $id = $this->request->get('ID');

        $res = NewsTable::getList([
            'select' => ['ID'],
            'filter' => ['ID' => $id]
        ])->fetch();

        if ($res === false) {
            throw new \Exception('Запись с ID=' . $id . ' не найдена.');
        }

        $timestamp = strtotime($dateCreated);

        $dateCreated = \Bitrix\Main\Type\DateTime::createFromTimestamp($timestamp);

        $data = [
            'TITLE' => $title,
            'DESCRIPTION' => $description,
            'DATE_CREATED' => $dateCreated,
            'AUTHOR_ID' => $authorId,
        ];

        $result = NewsTable::update($id, $data);

        if (!$result->isSuccess()) {
            throw new \Exception(implode(', ', $result->getErrorMessages()));
        }

        $data['DATE_CREATED'] = $dateCreated->format('Y-m-d');

        return $data;
    }

    public function deleteAction(): bool
    {
        $id = $this->request->get('id');

        $result = NewsTable::delete($id);

        if ($result->isSuccess()) {
            return true;
        }

        throw new \Exception(implode(", ", $result->getErrorMessages()));
    }
}
