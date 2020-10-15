<?php
/**
 * Файл трейта городов для таблицы обратной связи
 */
namespace RAAS\CMS\Cities;

use RAAS\Column;
use RAAS\CMS\Material;

/**
 * Трейт городов для таблицы обратной связи
 */
trait FeedbackTableTrait
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $newColumns = [];
        foreach ($this->columns as $key => $column) {
            $newColumns[$key] = $column;
            if ($key == 'post_date') {
                $newColumns['city_id'] = new Column([
                    'caption' => 'Город',
                    'callback' => function ($row) use ($view) {
                        if ($row->city_id) {
                            $city = new Material($row->city_id);
                            return '<a href="' . $this->view->url . '&action=view&id=' . (int)$row->id . '">' .
                                      htmlspecialchars($city->name) .
                                   '</a>';
                        }
                    }
                ]);
            }
        }
        $this->columns = $newColumns;
    }
}
