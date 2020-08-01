<?php

include_once 'functions.php';

$urls = [];
if (isset($_COOKIE['data']) and !empty($_COOKIE['data'])) {
    $datas = json_decode($_COOKIE['data'], true);
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
                    <button type="submit" class="btn btn-info">Back</button>
                </form>
            </div>
        </div>
    </div>
    <script>
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