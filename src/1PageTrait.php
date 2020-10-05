<?php
/**
 * Файл трейта городов для страницы
 */
namespace RAAS\CMS\Cities;

use Twig_Environment;
use Twig_Loader_String;
use RAAS\Controller_Frontend;

/**
 * Трейт городов для страницы
 */
trait PageTrait
{
    use GetControllerTrait;

    public function process()
    {
        $result = parent::process();
        $templateData = $this->getController()->getTemplateData();
        $twig = new Twig_Environment(new Twig_Loader_String());
        $result = $twig->render($result, $templateData);
        return $result;
    }
}
