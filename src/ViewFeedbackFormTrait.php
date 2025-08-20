<?php
/**
 * Файл трейта городов для формы просмотра сообщения обратной связи
 */
namespace RAAS\CMS\Cities;

use RAAS\Field as RAASField;
use RAAS\CMS\Feedback;

/**
 * Трейт городов для формы просмотра сообщения обратной связи
 */
trait ViewFeedbackFormTrait
{
    protected function getPreStat(Feedback $item): array
    {
        $arr = parent::getPreStat($item);
        $result = [];
        foreach ($arr as $key => $val) {
            $result[$key] = $val;
            if ($key == 'post_date') {
                $result['city_id'] = [
                    'name' => 'city_id',
                    'caption' => 'Город',
                    'template' => __DIR__ . '/feedback_view.field.inc.php',
                ];
            }
        }
        return $result;
    }
}
