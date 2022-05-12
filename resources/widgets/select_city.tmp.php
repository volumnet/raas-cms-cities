<?php
/**
 * Виджет блока "Выбор города"
 * @param Block_Material $Block Текущий блок
 * @param Page $Page Текущая страница
 * @param array<Material>|null $Set Список материалов
 */
namespace RAAS\CMS;

use RAAS\Controller_Frontend;

$mTypeFields = $Block->Material_Type->fields;
Field::prefetch($Set, [
    $mTypeFields[Controller_Frontend::CITIES_DOMAIN_FIELD_URN],
    $mTypeFields['related'],
]);
$citiesData = [];
foreach ($Set as $item) {
    $citiesData[trim($item->id)] = [
        'id' => (int)$item->id,
        'name' => trim($item->name),
        'domains' => (array)$item->{Controller_Frontend::CITIES_DOMAIN_FIELD_URN},
        'related' => (array)$item->related,
        'active' => ($item->id == Controller_Frontend::i()->city->id),
        'default' => ($item->id == Controller_Frontend::DEFAULT_CITY_ID),
    ];
}
?>
<div class="select-city">
  <a href="#" class="select-city__title" data-bs-toggle="modal" data-bs-target="#select-city-modal">
    <span class="select-city__title-caption">
      Ваш город:
    </span>
    <span class="select-city__selected-name">
      <?php echo htmlspecialchars(Controller_Frontend::i()->city->name)?>
    </span>
  </a>
  <div data-vue-role="ip-locator" :cities="<?php echo htmlspecialchars(json_encode($citiesData))?>"></div>

  <div id="select-city-modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal select-city-modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="h5 modal-title">
            Выберите город
          </div>
          <button type="button" data-bs-dismiss="modal" aria-hidden="true" class="btn-close"></button>
        </div>
        <div class="modal-body">
          <div class="select-city-modal__list">
            <div class="select-city-modal-list">
              <?php foreach ($Set as $item) {
                  $itemDomain = $item->fields[Controller_Frontend::CITIES_DOMAIN_FIELD_URN]->getValue(); ?>
                  <div class="select-city-modal-list__item">
                    <?php if ($item->id == Controller_Frontend::i()->city->id) { ?>
                        <span class="select-city-modal-item select-city-modal-item_active">
                          <?php echo htmlspecialchars($item->name)?>
                        </span>
                    <?php } else { ?>
                        <a href="//<?php echo htmlspecialchars($itemDomain . $_SERVER['REQUEST_URI'])?>" class="select-city-modal-item">
                          <?php echo htmlspecialchars($item->name)?>
                        </a>
                    <?php } ?>
                  </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
Package::i()->requestJS('/js/select-city.js');
Package::i()->requestCSS('/css/select-city.css');

