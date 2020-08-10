<?php

$DEBUG = false;

if ($DEBUG){
    error_reporting(1);
}else{
    error_reporting(0);
}

include_once 'functions.php';

$sources = [
    'https://tuoitre.vn/rss/tin-moi-nhat.rss'
];

// Get rss feeds
$feeds = [];
if (isset($_GET['crawl'])){
    $feeds = get_rss($sources);
}

$urls = [];
if (isset($_POST['data']) and !empty($_POST['data'])) {
    $datas = json_decode($_POST['data'], true);
    $urls = export($datas);
    die(json_encode($urls));
}
?>

<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/styles/style-v2.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

    </style>
</head>

<body>
    <div class="container-fluid">

        <!-- Title  -->
        <div class="row text-right">
            <div class="col-md-8 text-white">
                <h1>News feed Generation</h1>
            </div>
            <div class="col-md-4 button-title">
                <button type="button" class="btn btn-info" id="crawl">Crawl</button>
                <button type="button" class="btn btn-warning" id="export">Export</button>
            </div>
        </div>

        <!-- Contents-->
        <div class="row">
            <div class="col-lg-8 mx-auto flx">
                <ul class="list-group shadow scroll sc6" id="contents">
                    <!--     news contents           -->

                    <!--                    end-->
                </ul>
            </div>
        </div>

    </div>
    <script> var feeds = <?= json_encode($feeds); ?>; </script>
    <script src="scripts/home-v2.js"></script>
</body>
</html>
