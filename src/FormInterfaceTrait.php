<?php
/**
 * Файл трейта городов для интерфейса формы
 */
namespace RAAS\CMS\Cities;

use SOME\SOME;

/**
 * Трейт городов для интерфейса формы
 */
trait FormInterfaceTrait
{
    use GetControllerTrait;

    public function processUserData(SOME $object, array $server = [])
    {
        parent::processUserData($object, $server);
        $object->city_id = (int)$this->getController()->city->id;
    }
}
