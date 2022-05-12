<style lang="scss">
.ip-locator {
    display: block;
    border: 1px solid #ddd;
    padding: 1rem 1rem 1rem;
    box-shadow: .5rem .5rem 1rem rgba(black, .5);
    background: white;
    color: $body-color;
    z-index: 10;
    position: absolute;
    left: 50%;
    transform: translate(-50%, 0);
    margin-top: 5px;
    min-width: 350px;
    @include viewport-down('xs') {
        min-width: 290px;
    }
    &__close {
        @include center-alignment(24px);
        position: absolute;
        top: 1rem;
        right: 0.67rem;
        line-height: 1;
        cursor: pointer;
    }
    &__title {
        font-size: 18px;
        text-align: center;
    }
    &__city-name {
        font-weight: 500;
    }
    &__controls {
        display: flex;
        margin-top: 0.5rem;
        > * {
            flex-grow: 1;
            margin-right: 5px;
            width: 50%;
            white-space: nowrap;
            padding: .5rem 1rem !important;
            @include viewport-down('xs') {
                width: auto;
                min-width: 33.33%;
            }
            &:last-child {
                margin-right: 0;
            }
        }
    }
}
</style>

<template>
  <div class="ip-locator" v-if="active">
    <div class="ip-locator__close btn-close" @click="close()"></div>
    <div class="ip-locator__title">
      Ваш город 
      <span  class="ip-locator__city-name" v-if="foundCity">{{foundCity.name}}</span>?
    </div>
    <div class="ip-locator__controls">
      <a :href="href" class="btn btn-primary ip-locator__submit" @click="submit()">Да</a>
      <button type="button" class="btn btn-default ip-locator__submit" @click="select()">Выбрать другой</button>
    </div>
  </div>
</template>

<script>
import XCookie from './xcookie.js';

export default {
    props: {
        /**
         * Города
         * @type {Object} <pre><code>{
         *     String[] ID# города: {
         *         id: Number ID# города,
         *         name: String Название города,
         *         domains: String[] Домены города,
         *         related: String[] Связанные регионы,
         *         active: Boolean Город активен,
         *         default: Boolean Город по умолчанию,
         *     }
         * }</code></pre>
         */
        cities: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            active: false, // Окно активно
            foundCity: null, // Найденный город
            foundCityName: null, // Наименование найденного города
        };
    },
    mounted() {
        window.setTimeout(() => {
            if (!XCookie.get('locatorDisabled')) {
                $.getJSON('/ajax/ip_location/').then((result) => {
                    let city;
                    if (!result.city) {
                        return;
                    }
                    this.foundCityName = result.city;
                    if (city = this.getCityByName(result.city)) {
                        this.foundCity = city;
                    } else if (city = this.getCityByRelated(result.city)) {
                        this.foundCity = city;
                    } else if (result.region && 
                        (city = this.getCityByRelated(result.region))
                    ) {
                        this.foundCity = city;
                    } else if (result.city && this.defaultCity) {
                        this.foundCity = this.defaultCity;
                    }
                    if (this.foundCity && !this.foundCity.active) {
                        this.active = true;
                        $(document).trigger('raas.iplocator', {
                            city: this.foundCity,
                        });
                    }
                }, (error) => {
                    console.error(error)
                });
            }
        }, 10);
        $(document).on('raas.changecity', () => {
            this.close();
        })
    },
    methods: {
        /**
         * Закрывает окно
         */
        close() {
            this.active = false;
            XCookie.set('locatorDisabled', 1);
        },
        /**
         * Выбор города
         */
        select() {
            this.active = false;
            $('#select-city-modal').modal('show');
        },
        /**
         * Переходит по ссылке на найденный город
         * @return {[type]} [description]
         */
        submit() {
            this.close();
            $(document).trigger('raas.changecity');
        },
        /**
         * Определяет город по названию
         * @return {Object|null} Объект города, либо null, если не найден
         */
        getCityByName(cityName) {
            for (let city of Object.values(this.cities)) {
                if (city.name == cityName) {
                    return city;
                }
            }
            return null;
        },
        /**
         * Определяет город по связанному региону
         * @return {Object|null} Объект города, либо null, если не найден
         */
        getCityByRelated(related) {
            for (let city of Object.values(this.cities)) {
                if (city.related && city.related.length) {
                    for (let cityRelated of city.related) {
                        let rx = new RegExp(cityRelated, 'gi');
                        if (rx.test(related)) {
                            return city;
                        }
                    }
                }
            }
            return null;
        },
    },
    computed: {
        /**
         * Город по умолчанию
         * @return {Object|null} Объект города, либо null, если не найден
         */
        defaultCity() {
            for (let city of Object.values(this.cities)) {
                if (city.default) {
                    return city;
                }
            }
            return null;
        },
        /**
         * Ссылка для перехода
         * @return {String}
         */
        href() {
            if (this.foundCity && 
                this.foundCity.domains && 
                this.foundCity.domains.length
            ) {
                return '//' + this.foundCity.domains[0];
            }
            return '#';
        }
    },
}
</script>