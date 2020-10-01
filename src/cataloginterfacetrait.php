<?php
/**
 * Файл трейта городов для интерфейса каталога
 */
namespace RAAS\CMS\Cities;

use RAAS\CMS\Material;

/**
 * Трейт городов для интерфейса каталога
 */
trait CatalogInterfaceTrait
{
    use GetControllerTrait;

    public function getItemMetadata(Material $item)
    {
        $metaData = array_merge(
            (array)parent::getItemMetadata($item),
            $this->getController()->getTemplateData()
        );
        $metaData['price'] *= (float)$this->getController()->priceRatio;
        $metaData['nameLower'] = mb_strtolower(mb_substr($metaData['name'], 0, 1))
                               . mb_substr($metaData['name'], 1);
        return $metaData;
    }
}
