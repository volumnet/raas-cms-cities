<?php
/**
 * Команда установки модуля "Города"
 */
namespace RAAS\CMS\Cities;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use RAAS\Application;
use RAAS\Command;
use RAAS\CMS\Feedback;
use RAAS\CMS\Field;
use RAAS\CMS\Material_Type;

/**
 * Команда установки модуля "Города"
 */
class InstallCommand extends Command
{
    /**
     * Обработка команды
     * @param string $materialTypeURN URN типа материалов "Города"
     */
    public function process($materialTypeURN = 'cities')
    {
        $materialType = Material_Type::importByURN($materialTypeURN);
        if (!$materialType->id) {
            $materialType = $this->createMaterialType();
        }
        $materialTypeId = (int)$materialType->id;
        if (!$materialType->fields['in_city']) {
            $inCityField = new Field([
                'classname' => Material_Type::class,
                'pid' => $materialTypeId,
                'datatype' => 'text',
                'urn' => 'in_city',
                'name' => 'В городе',
            ]);
            $inCityField->commit();
        }
        if (!$materialType->fields['domain']) {
            $domainField = new Field([
                'classname' => Material_Type::class,
                'pid' => $materialTypeId,
                'datatype' => 'text',
                'urn' => 'domain',
                'name' => 'Домен',
            ]);
            $domainField->commit();
        }
        try {
            $sqlQuery = "ALTER TABLE " . Feedback::_tablename()
                      . " ADD city_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'City ID#'";
            Feedback::_SQL()->query($sqlQuery);
        } catch (Exception $e) {
        }
        if (class_exists('RAAS\CMS\Shop\Module')) {
            if (!$materialType->fields['price_ratio']) {
                $domainField = new Field([
                    'classname' => Material_Type::class,
                    'pid' => $materialTypeId,
                    'datatype' => 'number',
                    'urn' => 'price_ratio',
                    'name' => 'Наценка, %%',
                ]);
                $domainField->commit();
                try {
                    $sqlQuery = "ALTER TABLE " . \RAAS\CMS\Shop\Order::_tablename()
                              . " ADD city_id INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'City ID#'";
                    Feedback::_SQL()->query($sqlQuery);
                } catch (Exception $e) {
                }
            }
        }
        $examplesDir = __DIR__ . '/../examples';
        $incDir = Application::i()->baseDir . '/inc';
        $glob = glob($examplesDir . '/*.example');
        foreach ($glob as $filename) {
            $text = file_get_contents($filename);
            preg_match('/namespace (.*?);/umis', $text, $regs);
            $isShop = false;
            if (!stristr($regs[1], 'Shop') || class_exists('RAAS\CMS\Shop\Module')) {
                $isShop = stristr($regs[1], 'Shop');
                $basename = str_replace('.example', '', basename($filename));
                $destDir = $incDir . '/raas';
                if ($isShop && file_exists($destDir . '/shop')) {
                    $destDir .= '/shop';
                };
                $dest = $destDir . '/' . $basename;
                if ($this->customFileExists($basename, $incDir)) {
                    $logMessage = 'File ' . $basename
                                . ' already exists. You have to resolve it manually';
                } else {
                    if (!file_exists($destDir)) {
                        mkdir($destDir, 0755, true);
                    }
                    copy($filename, $dest);
                    $logMessage = $filename . ' -> ' . $dest;
                }
                $this->controller->doLog($logMessage);
            }
        }
    }


    /**
     * Создает тип материалов "Города"
     * @param string $materialTypeURN URN типа материалов "Города"
     * @return Material_Type
     */
    public function createMaterialType($materialTypeURN = 'cities')
    {
        $companyMaterialType = Material_Type::importByURN('company');
        $companyMaterialTypeId = (int)$companyMaterialType->id;
        $materialType = new Material_Type([
            'pid' => $companyMaterialTypeId,
            'urn' => $materialTypeURN,
            'name' => 'Города',
            'global_type' => 1,
        ]);
        $materialType->commit();
        return $materialType;
    }


    /**
     * Проверяет, существует ли переопределенный файл
     * @param string $filename Имя файла без пути
     * @param string $startDir Стартовая директория для проверки
     * @return bool
     */
    public function customFileExists($filename, $startDir)
    {
        $directory = new RecursiveDirectoryIterator($startDir);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $fileEntry) {
            if ($fileEntry->isFile()) {
                if ($fileEntry->getFilename() == $filename) {
                    return true;
                }
            }
        }
        return false;
    }
}
