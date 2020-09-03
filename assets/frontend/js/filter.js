$(document).ready(function () {
    let form = $('#filter-form');

    $('#category').change(function (e) {
        form.submit()
    });

    let checked = $('input.attributes:checkbox:checked').length;
    $('.attributes').on('click', function (e) {
        if (checked < 1) {
            form.submit()
        }

    });

    $('input.attributes:checkbox').on('click', function () {
        let value = $(this).val()
        const urlParams = new URLSearchParams(window.location.search);
        let category = urlParams.get('category');
        let attribute = getURLParam('attribute[]', decodeURI(window.location.href));
        let url = window.location.protocol + '//' + window.location.host;
        if ($(this).is(':checked')) {
            //if category is present add to url
            if (category)
                url += '?category=' + category;
            // if an attribute already exists add to url
            if (attribute) {
                url += '&attribute[]=' + attribute
            }
            // set current checked checkbox value
            url += '&attribute[]=' + value;
            // set cookie
             document.cookie = 'url=' + url;
            // refresh page with the new encoded url
            window.location.href = encodeURI(url);
        } else {
            // if an option is unchecked replace attribute value
            let replaceWhat = '&attribute[]=' + value;
            url = getCookie('url');
            url = url.replace(replaceWhat, '');
            document.cookie = 'url=' + url;
            // refresh page with the new encoded url
            window.location.href = encodeURI(url);
        }
    });


    function getURLParam(key, target) {
        var values = [];
        if (!target) target = location.href;

        key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

        var pattern = key + '=([^&#]+)';
        var o_reg = new RegExp(pattern, 'ig');
        while (true) {
            var matches = o_reg.exec(target);
            if (matches && matches[1]) {
                values.push(matches[1]);
            } else {
                break;
            }
        }

        if (!values.length) {
            return null;
        } else {
            return values.length == 1 ? values[0] : values;
        }
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
});