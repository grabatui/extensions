<?php (defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED === true) or die();

/** @global CMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */

/** @var CAdminTabControl $tabControl */
$tabControl = $arResult['tab_control']
?>

<form method="POST" action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?= urlencode($arParams['MID']); ?>&lang=<?= LANGUAGE_ID; ?>">
    <?= bitrix_sessid_post(); ?>

    <?php foreach ($arResult['tabs'] as $tab): ?>
        <?php $tabControl->BeginNextTab(); ?>

        <?php foreach (array_get($tab, 'fields') as $fieldCode => $field): ?>
            <?php switch (array_get($field, 'type')):
                default: ?>
                    <tr>
                        <td>
                            <table class="adm-detail-content-table edit-table">
                                <tr>
                                    <td width="30%" class="adm-detail-content-cell-l"><?= array_get($field, 'title'); ?></td>
                                    <td width="70%" class="adm-detail-content-cell-r">
                                        <input
                                            type="text"
                                            name="<?= $fieldCode; ?>"
                                            value="<?= array_get($field, 'value', ''); ?>"
                                            max="255"
                                            size="30"
                                        />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <?php break; ?>
                <?php endswitch; ?>
        <?php endforeach; ?>

        <?php $tabControl->EndTab(); ?>
    <?php endforeach; ?>

    <?php $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="Сохранить" title="Сохранить" class="adm-btn-save" />
</form>