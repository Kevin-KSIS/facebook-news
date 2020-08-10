'use strict';
var parent = $('#contents');
var html = '';

feeds.forEach(function (feed, index){
    html = '<li class="list-group-item h600">\n' +
        '                    <div class="media align-items-lg-center flex-column flex-lg-row p-3">\n' +
        '                        <div class="col-lg media-body order-2 order-lg-1">\n' +
        `                            <p class="font-weight-bold mb-0 text-danger">${feed.title}</p>\n` +
        `                            </br>\n` +
        `                            <p class="mb-0 small">${feed.description}</p>\n` +
        `                            <p class="mb-0 small">Nguồn: ${feed.source}</p>\n` +
        '                            <div class="d-flex align-items-center justify-content-between mt-1">\n' +
        '                                <ul class="list-inline small">\n' +
        `                                    <li class="text-muted list-inline-item m-0">${feed.pubDate}</li>\n` +
        '                                </ul>\n' +
        `                                <h6 class="font-weight-bold my-2"><a href="${feed.link}" target="_blank">Open</a></h6>\n` +
        '                            </div>\n' +
        '                        </div>\n' +
        `                        <img src="${feed.thumbnail}" alt="Generic placeholder image" width="200" class="ml-lg-5 order-1 order-lg-2">\n` +
        '                        <div class="order-sm-3 form-check">\n' +
        '                            <input value="${feed.hash}" type="checkbox" class="form-check-input yes" name="yes" id="materialUnchecked">\n'+
        '                        </div>\n'+
        '                    </div>\n' +
        '                </li>';

    parent.append(html);
})

//    Events
var count_option = 0;

$('.yes').click(function(event){
    var input_tag = event.target;
    var itag = $('#count');

    if (input_tag.checked){
        count_option += 1;
    }else{
        count_option -= 1;
    }
    itag.text(count_option);

})

$("#export").click(function(event) {
    var say_yes = $('.yes')
    var hash = null;
    var feeds_chosen = [];

    for (var item in say_yes){
        if (say_yes[item].checked){
            hash = say_yes[item].value
            feeds_chosen.push(
                feeds.find(item => item.hash === hash)
            );
        }
    }
    $.ajax({
        url: '/index.php',
        type: "post",
        data: {
            data: JSON.stringify(feeds_chosen)
        },
        success: function(urls, status){
            var link = document.createElement('a');

            JSON.parse(urls).forEach(function(url, index){
                link.href = window.location.origin + '/' + url;
                link.download = url.replace(/^.*[\\\/]/, '');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
        },
        error: function(e){
            console.log(e);
        }
    })
});

$('#crawl').click(function (event) {
    window.location.href = 'index.php?crawl'
});