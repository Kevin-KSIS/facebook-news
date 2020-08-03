<?php

include_once 'functions.php';

$sources = [
    'https://tuoitre.vn/rss/tin-moi-nhat.rss'
];

// Get rss feeds
$feeds = [];
if (isset($_GET['crawl'])){
    $feeds = get_rss($sources);
}
?>

<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/styles/style.css">
</head>
<body>

<div class="container py-2">
    <!-- For demo purpose -->
    <div class="top_banner row text-center text-white mb-2">
        <div class="col-sm-7 mx-auto">
            <h1 class="display-4">Facebook Newsfeed</h1>
        </div>
        <div class="export">
            <button type="button" class="btn btn-info" id="crawl">Crawl</button>
            <button type="button" class="btn btn-warning" id="export">Export</button>
        </div>
    </div>
    <!-- End -->

    <div class="row">
        <div class="col-lg-1 count">
            <label>
                <input type="checkbox"  value=""/>
                <i class="fa" id="count" aria-hidden="true"></i>
            </label>
        </div>
        <div class="col-lg-8 mx-auto flx">
            <!-- List group-->
            <ul class="list-group shadow scroll sc6" id="contents">
                <!--     contents           -->
            </ul>
            <!-- End -->
        </div>
    </div>
</div>
<script>
    var feeds = <?= json_encode($feeds); ?>;
</script>
<script src="scripts/home.js"></script>
</body>
</html>
