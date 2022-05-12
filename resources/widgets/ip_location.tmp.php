<?php
/**
 * Сниппет определения города по IP
 *
 * Уровень блока
 * @param Block_PHP $Block Блок
 * @param Page $Page Текущая страница
 */
namespace RAAS\CMS;

use IPGeoBase;

require_once 'inc/ipgeobase.php';
$ipGeo = new IPGeoBase();
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    // $ip = $_SERVER['REMOTE_ADDR'];
    // $ip = '5.166.40.208';
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$result = $ipGeo->getRecord($ip);
if (!$result) {
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
        $result = [
            'cc' => 'RU',
            'city' => 'Сысерть',
            'region' => 'Свердловская область',
            'district' => 'Уральский федеральный округ',
        ];
    } else {
        $result = [
            'cc' => 'RU',
            'city' => 'Москва',
            'region' => 'Москва',
            'district' => 'Москва',
        ];
    }
}
echo json_encode($result);

// $citiesMType = Material_Type::importByURN('contacts');
// $sqlResult = Material::getSet(
//     array('where' => "pid = " . (int)$citiesMType->id, 'orderBy' => 'NOT priority, priority')
// );
// $cities = array();
// $cityFound = false;
// foreach ($sqlResult as $row) {
//     if (mb_strtolower($row->name) == mb_strtolower($result['city'])) {
//         echo (int)$row->id;
//         $cityFound = true;
//     }
// }
// if (!$cityFound && $sqlResult) {
//     echo (int)$sqlResult[0]->id;
// }
