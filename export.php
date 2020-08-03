<?php

include_once 'functions.php';

$urls = [];
if (isset($_POST['data']) and !empty($_POST['data'])) {
    $datas = json_decode($_POST['data'], true);
    $urls = export($datas);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #00b09b, #96c93d);
            min-height: 100vh;
        }
        .btn {
            display:inline-block;
            min-width: 300px;
            width: 80px;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="container py-2">
        <!-- For demo purpose -->
        <div class="top_banner row text-center text-white mb-2">
            <div class="col-sm-7 mx-auto">
                <h1 class="display-4">Export</h1>
                <br><br>
                <form action="/">
                    <button type="submit" class="btn btn-info" onclick="">Back</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // function deleteAllCookies() {
        //     var cookies = document.cookie.split(";");
        //
        //     for (var i = 0; i < cookies.length; i++) {
        //         var cookie = cookies[i];
        //         var eqPos = cookie.indexOf("=");
        //         var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        //         document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        //     }
        // }

        var urls = <?= json_encode($urls); ?>;

        var link = document.createElement('a');

        urls.forEach(function(url, index){
            link.href = window.location.origin + '/' + url;
            link.download = url.replace(/^.*[\\\/]/, '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })

    </script>
</body>
</html>