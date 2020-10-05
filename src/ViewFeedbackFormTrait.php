<?php
/**
 * Файл трейта городов для формы просмотра сообщения обратной связи
 */
namespace RAAS\CMS\Cities;

use RAAS\Field as RAASField;

/**
 * Трейт городов для формы просмотра сообщения обратной связи
 */
trait ViewFeedbackFormTrait
{
    protected function getDetails()
    {
        $arr = parent::getDetails();
        $newArr = [];
        foreach ($arr as $key => $val) {
            $newArr[$key] = $val;
            if ($key == 'post_date') {
                $newArr['city_id'] = $this->getFeedbackField([
                    'name' => 'city_id',
                    'caption' => 'Город',
                    'template' => __DIR__ . '/feedback_view.field.inc.php',
                ]);
            }
        }
        return $newArr;
    }
}
