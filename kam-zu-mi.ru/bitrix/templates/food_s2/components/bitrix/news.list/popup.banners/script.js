function createPopup(params) {
    console.log(params);
    if (!isInTime(params.shedule)) return;
    $(document).ready(function () {
        if (!getCookie("p" + params.id)) {
            $.fancybox.open({
                src: params.id,
                type: 'inline',
                opts: {
                    afterShow: function (instance, current) { }
                }
            });
            setCookie("p" + params.id, 1, params.frequency);
        }
    });
}

function isInTime(shedule) {

    if (!shedule) return null;
    var dt = new Date();
    var sh = shedule[dt.getDay() - 1];

    if (sh == "") return false;

    var sheduleArr = sh.split(',');

    var isInInterval = false;
    for (var interval of sheduleArr) {
        var sp = interval.split('-');
        var from = sp[0].split(':');
        var to = sp[1].split(':');

        var fromInt = parseInt(from[0]) * 60 + parseInt(from[1]);
        var toInt = parseInt(to[0]) * 60 + parseInt(to[1]);
        var nowInt = dt.getHours()*60 + dt.getMinutes();        

        if (
            fromInt <= nowInt && toInt >= nowInt
        ) {
            isInInterval = true;
        }
    }

    return isInInterval;
}

function setCookie(name, value, seconds) {
    var expires = "";
    if (seconds) {
        var date = new Date();
        date.setTime(date.getTime() + (seconds * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function eraseCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


/*

$(document).ready(function () {
    const marketOpen = 11 * 60 + 0; // minutes
    const marketClosed = 22 * 60 + 45; // minutes
    var now = new Date();
    var currentTime = now.getHours() * 60 + now.getMinutes();

    if (currentTime > marketClosed || currentTime < marketOpen) {
        if (!Cookies.get('showOnlyOne')) {
            Cookies.set('showOnlyOne', '1', {
                expires: 1 / 24
            });

            $.fancybox.open({
                src: '#rm-popup',
                type: 'inline',
                opts: {
                    afterShow: function (instance, current) { }
                }
            });
        }

    } else {
        Cookies.remove('showOnlyOne');
    }

    // ПОстоянный баннер
    if (!Cookies.get('showOnlyOne_TimeOut') && <?= $staticBannerEnabled ? 1 : 0 ?>) {
        Cookies.set('showOnlyOne_TimeOut', '1', {
            expires: (1 / 24 / 60) * 5
        });

        $.fancybox.open({
            src: '#rm-popup-timeout',
            type: 'inline',
        });
    }


});

*/