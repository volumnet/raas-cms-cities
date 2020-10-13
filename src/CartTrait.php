<?php
/**
 * Файл трейта городов для корзины
 */
namespace RAAS\CMS\Cities;

use RAAS\Controller_Frontend;

/**
 * Трейт городов для корзины
 */
trait CartTrait
{
    use GetControllerTrait;

    public function __get($var)
    {
        $val = parent::__get($var);
        $ratio = $this->getController()->priceRatio;
        switch ($var) {
            case 'items':
                foreach ($val as $row) {
                    $row->realprice *= $ratio;
                }
                break;
            case 'sum':
                $val *= $ratio;
                break;
        }
        return $val;
    }
}
