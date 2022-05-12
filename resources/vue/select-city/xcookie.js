const Cookie = window.Cookie;

let XCookie = {
    get(key) {
        let domainL2 = XCookie.domainL2();
        if (domainL2) {
            return Cookie.get(key);
        } else {
            return Cookie.get(key, { domain: domainL2 });
        }
    },
    set(key, value) {
        let lifetime = 14;
        if (value === null) {
            Cookie.remove(key, { path: '/'});
            Cookie.remove(key, { path: '/', domain: domainL2 });
            return null;
        }
        if (value instanceof Object) {
            value = JSON.stringify(value);
        }
        let domainL2 = XCookie.domainL2();
        if (domainL2) {
            Cookie.remove(key, { path: '/' });
            Cookie.set(
                key, 
                value, 
                { expires: lifetime, path: '/', domain: domainL2 }
            );
        } else {
            Cookie.set(key, value, { expires: lifetime, path: '/' });
        }
        return value;
    },
    domainL2() {
        let domainL2 = window.location.host.split('.');
        domainL2 = domainL2.slice(-2);
        if (domainL2.length == 2) {
            domainL2 = '.' + domainL2.join('.');
        } else {
            domainL2 = null;
        }
        return domainL2;
    }
};
export default XCookie;