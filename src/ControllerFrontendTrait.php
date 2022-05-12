<?php
/**
 * Файл трейта городов для контроллера фронтенда
 */
namespace RAAS\CMS\Cities;

use RAAS\Application;
use RAAS\CMS\Field;
use RAAS\CMS\Material;
use RAAS\CMS\Material_Type;

/**
 * Трейт городов для контроллера фронтенда
 *
 * Требует констант:
 * DEFAULT_CITY_ID - ID# города по умолчанию
 * COMPANY_ID - ID# компании
 * CITIES_MATERIAL_TYPE_URN - URN типа материалов городов
 * CITIES_DOMAIN_FIELD_URN - URN поля "домен" городов
 * CITIES_PRICE_RATIO_FIELD_URN - URN поля "наценка" городов
 * COOKIE_CITY_VAR - URN поля Cookie для городов
 */
trait ControllerFrontendTrait
{
    /**
     * Город по умолчанию
     * @var Material
     */
    public $defaultCity;

    /**
     * Город
     * @var Material
     */
    public $city;

    /**
     * Компания
     * @var Material
     */
    public $company;

    /**
     * Множитель цены
     * @var float
     */
    public $priceRatio;

    /**
     * Город определен явно
     * @var bool
     */
    public $cityDetected = false;

    /**
     * Получает город по умолчанию (для основного домена)
     * @return Material
     */
    public function getDefaultCity()
    {
        return new Material(static::DEFAULT_CITY_ID);
    }

    /**
     * Получает компанию
     * @return Material
     */
    public function getCompany()
    {
        return new Material(static::COMPANY_ID);
    }

    /**
     * Получает основной домен
     * @return string
     */
    abstract public function getMainDomain();


    /**
     * Получает тип материалов городов
     * @return Material_Type
     */
    public function getCitiesMaterialType()
    {
        return Material_Type::importByURN('cities');
    }


    /**
     * Получает множитель цены по значению поля
     * @param mixed $rawValue Значение поля
     * @return float
     */
    public function getPriceRatio($rawValue)
    {
        return (1 + ((float)$rawValue / 100.));
    }


    /**
     * Инициализирует города
     */
    public function citiesInit()
    {
        $this->defaultCity = $this->getDefaultCity();
        $this->city = $this->getCity();
        $this->company = $this->getCompany();
        $this->priceRatio = $this->getPriceRatio(
            (float)$this->city->{static::CITIES_PRICE_RATIO_FIELD_URN}
        );
    }


    public function fork()
    {
        $this->user = parent::__get('user');
        $this->citiesInit();
        return parent::fork();
    }


    public function checkStdRedirects()
    {
        if (in_array($this->requestUri, [
            '/sitemap.xml',
            '/sitemap.sections.xml',
            '/sitemap.catalog.xml',
            '/sitemap.goods.xml',
        ])) {
            $this->getSitemap($this->requestUri);
        } elseif (in_array($this->requestUri, ['/yandex.market.xml'])) {
            $this->getYML($this->requestUri);
        } else {
            return parent::checkStdRedirects();
        }
    }


    /**
     * Получает sitemap-ы с нужным доменом
     * @param string $filepath Путь к файлу
     */
    public function getSitemap($filepath)
    {
        $this->citiesInit();
        $this->city = $this->getCity();
        $realFilepath = str_replace('.xml', '.tmp.xml', $filepath);
        $text = file_get_contents(Application::i()->baseDir . $realFilepath);
        if ($currentDomain = $this->city->{static::CITIES_DOMAIN_FIELD_URN}) {
            $text = str_replace($this->getMainDomain(), $currentDomain, $text);
        }
        header('Content-Type: application/xml');
        echo $text;
        exit;
    }


    /**
     * Получает файл Яндекс.Маркет с нужным доменом
     * @param string $filepath Путь к файлу
     */
    public function getYML($filepath)
    {
        $this->citiesInit();
        $this->city = $this->getCity();
        $realFilepath = str_replace('.xml', '.tmp.xml', $filepath);
        $text = file_get_contents(Application::i()->baseDir . $realFilepath);
        if ($currentDomain = $this->city->{static::CITIES_DOMAIN_FIELD_URN}) {
            $text = str_replace($this->getMainDomain(), $currentDomain, $text);
        }
        $text = preg_replace_callback(
            '/(\\<price\\>)(.*?)(\\<\\/price\\>)/umis',
            function ($regs) {
                return $regs[1] . (ceil((float)$regs[2] * $this->priceRatio * 100) / 100) . $regs[3];
            },
            $text
        );
        header('Content-Type: application/xml');
        echo $text;
        exit;
    }


    /**
     * Получает шаблонные данные для города
     * @return array
     */
    public function getTemplateData()
    {
        $result = [
            'cityId' => $this->city->id,
            'cityName' => ' ' . $this->city->name,
            'inCity' => ' ' . $this->city->in_city,
            'cityDescription' => $this->city->description,
            'host' => $this->host,
        ];
        if ($this->city->street_address) {
            $addressItem = $this->city;
        } else {
            $addressItem = $this->company;
        }
        foreach (['city', 'street_address', 'map', 'office'] as $key) {
            $result[$key] = $addressItem->$key;
        }
        foreach ($this->city->fields as $key => $field) {
            if (!in_array($key, [
                'city',
                'street_address',
                'map',
                'office',
                'name',
                'description'
            ])) {
                $key2 = 'city'
                      . mb_strtoupper(mb_substr($key, 0, 1))
                      . mb_substr($key, 1);
                if ($val = $field->getValue()) {
                    $result[$key2] = $val;
                } elseif ($field = $this->company->fields[$key]) {
                    $result[$key2] = $field->getValue();
                } else {
                    $result[$key2] = '';
                }
            }
        }
        return $result;
    }


    /**
     * Получает город
     * @return Material
     */
    public function getCity()
    {
        $mainDomain = $this->getMainDomain();
        if ($this->host != $mainDomain) {
            $sqlQuery = "SELECT pid FROM cms_data WHERE fid = ? AND value = ?";
            $mType = Material_Type::importByURN(static::CITIES_MATERIAL_TYPE_URN);
            $field = $mType->fields[static::CITIES_DOMAIN_FIELD_URN];
            $sqlBind = [(int)$field->id, $this->host];
            $sqlResult = Material::_SQL()->getvalue([$sqlQuery, $sqlBind]);
            if ($sqlResult) {
                $this->cityDetected = true;
                if (static::COOKIE_CITY_VAR) {
                    Application::i()->setcookie(
                        static::COOKIE_CITY_VAR,
                        (int)$sqlResult
                    );
                }
                return new Material((int)$sqlResult);
            }
        }
        return $this->getDefaultCity();
    }
}
