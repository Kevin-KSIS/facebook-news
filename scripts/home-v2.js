'use strict';
var parent = $('#contents');
var html = '';

feeds.forEach(function (feed, index){
    html = '<li class="list-group-item h600">\n' +
        '                    <div class="cell media align-items-lg-center flex-column flex-lg-row">\n' +
        '                        <div class="col-md-3 avatar">\n' +
        `                            <a href="${feed.link}" target="_blank">\n` +
        `                                <img src="${feed.thumbnail}" width="120%"">\n` +
        '                            </a>\n'+
        '                        </div>\n' +
        '                        <div class="col-md-8 description">\n' +
        `                            <p class="font-weight-bold mb-0 text-danger">${feed.title}</p>\n` +
        `                            </br>\n` +
        `                            <p class="mb-0 small">${feed.description}</p>\n` +
        `                            <p class="mb-0 small">Nguồn: ${feed.source}</p>\n` +
        '                            <div class="d-flex align-items-center justify-content-between mt-1">\n' +
        '                                <ul class="list-inline small">\n' +
        `                                    <li class="text-muted list-inline-item m-0">${feed.pubDate}</li>\n` +
        '                                </ul>\n' +
        '                            </div>\n' +
        '                        </div>\n' +
        '                        <div class="col-md-1 copy">\n' +
        `                            <i class="material-icons" style="font-size: 36px">content_copy</i>\n` +
        '                        </div>\n' +
        `                        <input hidden value="${feed.hash}" class="yes" checked="false"/>\n` +
        '                    </div>\n' +
        '                </li>';

    parent.append(html);
})

function find_root(node){
    var result = {
        isCopy: false,
        node: null
    }
    while(1){
        if (node.className.includes('copy')){
            result.isCopy = true
        }

        if (node.className.includes('cell') || node.className.includes('avatar')){
            result.node = node;
            return result;
        }

        node = node.parentNode;
    }
}

//    Events
// var count_option = 0;
//
// $('').click(function(event){
//     var input_tag = event.target;
//     var itag = $('#count');
//
//     if (input_tag.checked){
//         count_option += 1;
//     }else{
//         count_option -= 1;
//     }
//     itag.text(count_option);
//
// })

// Fill color and copy description
$('.cell').click(function (event) {
    var root = find_root(event.target);
    var inputEle = root.node.getElementsByTagName('input')[0];

    if (root.isCopy){
        // Copy
        var hash = inputEle.value;
        var feed = feeds.find(item => item.hash === hash);
        var icon = root.node.getElementsByTagName('i')[0];

        icon.innerText = 'check_circle_outline';
        setTimeout(function () {
            icon.innerText = 'content_copy';
        },500);

        navigator.clipboard.writeText(feed.description + '\nNguồn: ' + feed.source).then(function() {
            console.log('Copied');
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
        });
        return;
    }else{
         // fill color
        var bgcolor = root.node.style.backgroundColor;

        if (bgcolor === ""){
            root.node.style.backgroundColor = "#a5c7b9";
            inputEle.setAttribute('checked', 'true');
        }else{
            root.node.style.backgroundColor = "";
            inputEle.setAttribute('checked', 'false');
        }
        return;
    }

    
})

// Export Images
$("#export").click(function(event) {
    event.target.setAttribute('src', '/images/loading.gif');
    event.target.setAttribute('style', 'width: 20%; height: 40px; padding: 0')
    event.target.innerHTML = "";
    event.target.outerHTML = event.target.outerHTML.replace(/button/g, "img");

    var say_yes = $('.yes')
    var hash = '';
    var feeds_chosen = [];
    
    for (var item in say_yes){
        if (isNaN(parseInt(item))){
            continue;
        }
        
        if (say_yes[item].getAttribute('checked') === 'true'){
            hash = say_yes[item].value;
            feeds_chosen.push(
                feeds.find(item => item.hash === hash)
            );
        }
    }

    feeds_chosen.forEach(function (feed) {
        $.ajax({
            url: '/index-v2.php',
            type: "post",
            data: {
                data: JSON.stringify([feed])
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

                // remove elements not choose
                $('.cell').each(function (i, element) {
                    if (element.style.backgroundColor !== 'rgb(165, 199, 185)'){
                        element.remove();
                    }
                })
            },
            error: function(e){
                console.log(e);
            }
        })
    })

});

// Get data
$('#crawl').click(function (event) {
    window.location.href = 'index.php?crawl'
});
