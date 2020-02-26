$('body').css('overflow','hidden');
$(function () {
    size = $('.navbar').height() + 30 + 40 + $('.pageTitle').height() + 33 + 40;
    $('#msgList,#msgCtn').css({
        'height': 'calc(100vh - ' + size + 'px)'
    });
    $('.mailRow').click(function () {
        $('#msgCtn').removeAttr('hidden');
        that = $(this);
        ctn = $('#msgCtn');
        title = that.find('h6').html();
        auth = that.find('small').html();
        authLink = that.find('.assureLink').html();
        text = that.find('div:hidden').html();
        action = that.find('div.row:hidden').html();
        id = ctn.attr('id');
        ctn.find('#msgTitle').html(title);
        ctn.find('#msgAuthor').html(auth);
        ctn.find('#msgAuthor').append(authLink);
        ctn.find('#msgContent').html(text);
        ctn.find('#msgAction').html(action);
    });
});