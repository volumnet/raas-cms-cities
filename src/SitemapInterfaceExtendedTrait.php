<?php
/**
 * Файл трейта городов для расширенного интерфейса sitemap.xml
 */
namespace RAAS\CMS\Cities;

use RAAS\Application;

/**
 * Трейт городов для расширенного интерфейса sitemap.xml
 */
trait SitemapInterfaceExtendedTrait
{
    public function process(
        $catalogMTypeURN = 'catalog',
        $catalogPageUrl = '/catalog/'
    ) {
        parent::process($catalogMTypeURN, $catalogPageUrl);
        $baseDir = Application::i()->baseDir;
        if (is_file($baseDir . '/sitemap.xml')) {
            rename($baseDir . '/sitemap.xml', $baseDir . '/sitemap.tmp.xml');
        }
        if (is_file($baseDir . '/sitemap.sections.xml')) {
            rename($baseDir . '/sitemap.sections.xml', $baseDir . '/sitemap.sections.tmp.xml');
        }
        if (is_file($baseDir . '/sitemap.catalog.xml')) {
            rename($baseDir . '/sitemap.catalog.xml', $baseDir . '/sitemap.catalog.tmp.xml');
        }
        if (is_file($baseDir . '/sitemap.goods.xml')) {
            rename($baseDir . '/sitemap.goods.xml', $baseDir . '/sitemap.goods.tmp.xml');
        }
    }
}
