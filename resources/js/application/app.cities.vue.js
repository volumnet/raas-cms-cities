/**
 * Миксин для работы с городами (без доменов)
 */
export default {
    data() {
        return {
            cities: {}, // Список городов по URN
            cityURN: null, // URN текущего города
            defaultCityURN: null,
        };
    },
    async mounted() {
        this.cities = await this.api('/cities.brief.json');
        this.cityURN = await this.getCityURN();
    },
    methods: {
        /**
         * Получает URN активного города (при необходимости записывает в Cookie)
         * @return {String}
         */
        async getCityURN() {
            // Попробуем через Cookie
            let cookieCityURN = Cookie.get('city');
            if (this.cities[cookieCityURN]) {
                return cookieCityURN;
            }
            // Город по умолчанию
            if (this.defaultCityURN) {
                return this.defaultCityURN;
            }
        },
        /**
         * Получает город по IP
         * @return {Object|null}
         */
        async getCityByIP() {
            let ipLocation = await this.api('/ajax/ip-location/');
            let filteredCities = Object.values(this.cities);
            if (ipLocation.cc) {
                filteredCities = filteredCities.filter(x => (!x.country || (x.country == ipLocation.cc)));
            }
            if (ipLocation.city) {
                let cityLowerCase = ipLocation.city.toLowerCase().trim();
                filteredCities = filteredCities.filter(x => {
                    let xLowerCase = x.name.toLowerCase().trim();
                    return (xLowerCase.indexOf(cityLowerCase) != -1) || (cityLowerCase.indexOf(xLowerCase) != -1);
                });
                if (filteredCities.length == 1) {
                    return filteredCities[0];
                }
                filteredCities = filteredCities.filter(x => (x.name.toLowerCase().trim() == cityLowerCase));
                if (filteredCities.length == 1) {
                    return filteredCities[0];
                }
            }
            if (filteredCities.length && ipLocation.region) {
                let regionName = this.normalizeRegionName(ipLocation.region);
                filteredCities = filteredCities.filter(x => {
                    let xRegion = this.normalizeRegionName(x.region);
                    return (xRegion.indexOf(regionName) != -1) || (regionName.indexOf(xRegion) != -1);
                });
                if (filteredCities.length == 1) {
                    return filteredCities[0];
                }
                filteredCities = filteredCities.filter(x => (this.normalizeRegionName(x.region) == regionName));
                if (filteredCities.length == 1) {
                    return filteredCities[0];
                }
            }
            return null;
        },
        /**
         * Нормализует название региона
         * @param {String} regionName Название региона
         * @return {String}
         */
        normalizeRegionName(regionName) {
            if (/алтай/gi.test(regionName)) {
                regionName = 'Алтайский край';
            }
            if (/кабардино-балкар/gi.test(regionName)) {
                regionName = 'Кабардино-Балкария';
            }
            if (/карачаево-черкес/gi.test(regionName)) {
                regionName = 'Карачаево-Черкесия';
            }
            if (/якутия/gi.test(regionName)) {
                regionName = 'Саха (Якутия)';
            }
            if (/удмурт/gi.test(regionName)) {
                regionName = 'Удмуртия';
            }
            if (/чуваш/gi.test(regionName)) {
                regionName = 'Чувашия';
            }
            regionName = regionName.toLowerCase().trim();
            let rx = '((г(ор(од)?)?)|((а(вт(ономная)?)?)? ?(о(бл(асть)?)?))|(кр(ай)?)|(р(есп(ублика)?)?))\.?';
            let rx1 = new RegExp('^' + rx + ' ', 'gi');
            let rx2 = new RegExp(' ' + rx + '$', 'gi');
            regionName = regionName.replace(rx1, '').trim().replace(rx2, '').trim()
            return regionName;
        },
        /**
         * Устанавливает город с записью в Cookie
         * @param {String} cityURN URN активного города
         */
        setCity(city) {
            this.cityURN = city.urn;
            Cookie.set('city', this.cityURN);
        }
    },
    computed: {
        /**
         * Выбранный город
         * @return {Object}
         */
        city() {
            if (this.cityURN && this.cities[this.cityURN]) {
                return this.cities[this.cityURN];
            }
            return null;
        }
    }
}