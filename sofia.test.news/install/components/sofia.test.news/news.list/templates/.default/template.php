<?php

use Sofia\Test\News\Orm\NewsTable;

echo '<h1>Новости София!</h1>';

\CModule::IncludeModule("sofia.test.news");

$totalPages = (int) $arResult['TOTAL_PAGES'];
$page = (int) $arResult['PAGE'];

?>
<table id="sofia" border="1">
    <thead>
    <tr>
        <th>TITLE</th>
        <th>DESCRIPTION</th>
        <th>DATE_CREATED</th>
        <th>AUTHOR</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($arResult['ITEMS'] as $data): ?>
        <tr data-id-row="<?=$data['ID'] ?>">
            <td data-field="TITLE"><?=$data['TITLE'] ?></td>
            <td data-field="DESCRIPTION"><?=$data['DESCRIPTION'] ?></td>
            <td data-field="DATE_CREATED"><?=$data['DATE_CREATED']->format('Y-m-d') ?></td>
            <td data-field="AUTHOR_ID"><?=$data['AUTHOR_ID'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="menu_sofia"></div>
<?php if ($totalPages > 1): ?>
<div id="sofia-pagination">
    <?php for ($i = 0; $i < $totalPages; ++$i) {
        $pageBlock = $i + 1;

        if ($pageBlock === $page) {
            echo '<span>' . $pageBlock . '</span>';
        } else {
            echo '<a href="?page=' . $pageBlock . '">' . $pageBlock . '</a>';
        }
    } ?>
</div>
<?php endif; ?>

<button onclick="create()">Создать</button>
