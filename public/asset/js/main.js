function ajaxLoad(that, ctnEl, replace, direct) {
    if (logged !== true) {
        var string = window.location;
        var substring = "/login";
console.log(string.indexOf(substring) !== -1);
        if (string.indexOf(substring) !== -1)
            window.location.href = '/login';
    }
    if (that === undefined) {
        that = null;
    }
    if (ctnEl === undefined) {
        ctnEl = null;
    }
    if (replace === undefined) {
        replace = null;
    }
    if (direct === undefined) {
        direct = null;
    }
    if (ctnEl) {
        ctn = ctnEl;
    } else {
        ctn = $('#ajax-content');
    }
    if (that) {
        ctn.empty().append("<div class='ajax-loader' style='margin-left: 125px'></div>");
        $.get(that.attr("id"), function (data) {
            if(data.code !== 200){
                window.location.href = 'http://grc.yvon.eu/';
            }
            if (replace) {
                ctn.parent().html(data.response);
                $('a[title^=modifAdh]').remove();
            } else {
                ctn.html(data.response);
            }
        });
    } else if (direct) {
        ctn.empty().append("<div class='ajax-loader' style='margin-left: 125px'></div>");
        $.get(direct, function (data) {
            if(data.code !== 200){
                window.location.href = 'http://grc.yvon.eu/';
            }
            ctn.html(data.response);
        });
    } else {
        $.get("/", function (data) {
           /* if(data.code !== 200){
                window.location.href = 'http://grcyvon.mgel/';
            }*/
            ctn.html(data.response);
        });
    }
}

function ajaxSearchAdh(that) {
    if (that === undefined) {
        that = null;
    }
    let ctn = $('#ajax-result');
    ctn.append("<div class='ajax-loader'></div>");
    $.post(that.attr("action"), that.serialize()).done(function (data) {
        ctn.html(data.response)
    });
}

function ajaxEditAdh(that, ctn) {
    if (that === undefined) {
        that = null;
    }
    if (ctn === undefined) {
        ctn = null;
    }
    ctn.append("<div class='ajax-loader'></div>");
    $.post(that.attr("action"), that.serialize()).done(function (data) {
        ctn.html(data.response);
    });
}


function ajaxGenModal(el, that, id, url) {
    if (that === undefined) {
        that = null;
    }
    if (url === undefined) {
        url = null;
    }
    let ctn = $('body');
    let modalEl = $('#' + el + '-' + id);
    if (modalEl.length !== 1) {
        ctn.append("<div class='ajax-loader'></div>");
        $.get(that.val(), function (data) {
            ctn.append('<div class="modal" tabindex="-1" role="dialog" id="' + el + '-' + id + '">' +
                '<div class="modal-dialog modal-dialog-centered modal-xl" role="document">' +
                ' <div class="modal-content">' + data.response + '</div></div></div>');
        }).done(function () {
            $('.ajax-loader').remove();
            $('#' + el + '-' + id).modal('show');
        });
    } else {
        modalEl.modal('show');
    }
}

function setFS() {
    document.fullScreenElement && null !== document.fullScreenElement || !document.mozFullScreen && !document.webkitIsFullScreen ? document.documentElement.requestFullScreen ? document.documentElement.requestFullScreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullScreen && document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT) : document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen()
}

$(function () {
    function locationHashChanged() {
        if (location.hash === '#cool-feature') {
            console.log("You're visiting a cool feature!");
        }
    }

    window.onhashchange = locationHashChanged;
    var hash = window.location.hash;
    if (hash !== '' && logged) {
        ajaxLoad($('a[href="' + hash + '"]'));
    } else {
        ajaxLoad();
    }

    $('.sidebar-header').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
    $('#sidebar ul li').on('click', function () {
        $('#sidebar ul li.active').removeClass('active');
        that = $(this);
        if (that.not('.active')) {
            that.addClass('active');
        }
    });

    //LIENS MENUS
    $("a[href^='#']").on('click', function () {
        let that = $(this);
        if (that.attr('onclick') == null) {
            ajaxLoad(that);
            that.addClass('active');
        }

    });

    //ACTIONS MENU TOP BAR
    $('.nav-item.print').on('click', function () {
        $('#ajax-content').printThis({});
    });
    $('.nav-item.expand').on('click', function () {
        setFS();
    });
    $('.nav-item.logout').on('click', function () {
        window.location.href = '/deconnexion';
    });

    //FORCE LE BODY A RESTER FOCUS SUR LA MODAL OUVERTE
    $('*').on('hidden.bs.modal', function () {
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    })
});