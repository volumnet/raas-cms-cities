<?php
/**
 * Файл трейта получения контроллера
 */
namespace RAAS\CMS\Cities;

use RAAS\Controller_Frontend;

/**
 * Трейт получения контроллера
 */
trait GetControllerTrait
{
    /**
     * Получает контроллер
     */
    public function getController()
    {
        return Controller_Frontend::i();
    }
}
