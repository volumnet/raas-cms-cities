<?php
/**
 * Файл трейта городов для страницы
 */
namespace RAAS\CMS\Cities;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
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
        $twig = new Environment(new ArrayLoader(['description' => $result]));
        $result = $twig->render('description', $templateData);
        return $result;
    }
}
