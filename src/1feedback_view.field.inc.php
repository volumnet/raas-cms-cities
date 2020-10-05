<?php
/**
 * Отображение поле города в просмотре сообщения обратной связи
 */
namespace RAAS\CMS\Cities;

use RAAS\Field as RAASField;
use RAAS\CMS\Material;
use RAAS\CMS\Sub_Main;

/**
 * Отображает поле города
 * @param RAASField $field Поле для отображения
 */
$_RAASForm_Control = function (RAASField $field) use (
    &$_RAASForm_Attrs,
    &$_RAASForm_Options,
    &$_RAASForm_Checkbox,
    &$_RAASForm_Control
) {
    $Item = $field->Form->Item;

    if ($Item->city_id) {
        $city = new Material($Item->city_id);
        echo '<a href="' . Sub_Main::i()->url . '&action=edit_material&id=' . (int)$Item->city_id . '">' .
                htmlspecialchars($city->name) .
             '</a>';
    }
};
