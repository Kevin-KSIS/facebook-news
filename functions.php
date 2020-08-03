<?php
require __DIR__.'/vendor/autoload.php';
use GDText\Box;
use GDText\Color;
include_once __DIR__."/vendor/simple_html_dom.php";

function __transform_publish_date($item){

    $available = ['pubDate'];
    foreach ($available as $date){
        $temp = $item -> $date -> __toString();
        if ($temp){
            return date("D, d-m-Y H:i:s", strtotime($item -> $date));
        }
    }
    return "Now";
}

function __extract_link_in_description($desc){
    preg_match("/<img src=\"([^\"]*)\"/",$desc,$rs);
    if (isset($rs[1]))
        return $rs[1];
    return "";
}

function get_rss($sources = []){
    $results = Array();

    foreach ($sources as $source){
        $feeds = @simplexml_load_file($source);

        if(!empty($feeds)){
            foreach ($feeds->channel->item as $item) {
                $result = Array();
                $result['hash'] = uniqid();
                $result['source'] = $feeds -> channel -> copyright -> __toString();
                $result['title'] = $item -> title -> __toString();
                $result['link'] = $item -> link -> __toString();
                $result['description'] = strip_tags($item -> description -> __toString());
                $result['pubDate'] = __transform_publish_date($item);
                $result['thumbnail'] = __extract_link_in_description($item -> description -> __toString());

                array_push($results, $result);
            }
        }
    }

    return $results;
}

function __check_origin_image($links){
    $base_size = 0;
    $rs = '';
    foreach ($links as $i => $link){
        list($width, $height, $type, $attr) = getimagesize($link);
        $size = $width * $height;
        if ($size > $base_size){
            $base_size = $size;
            $rs = $link;
        }
    }
    return $rs;
}

function __crawling_img($url){
    $options = array(
        'http'=>array(
            'method'=>"GET",
            'header'=>
                "Accept-language: en\r\n" .
                "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
        )
    );

    $context = stream_context_create($options);
    $html = file_get_contents($url, false, $context);

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);

    $xpath = new DOMXPath($doc);
    $nodelist = $xpath->query("//img");

    $reviews = [];
    for ($i=0; $i<$nodelist -> count(); $i++){
        $node = $nodelist->item($i); // gets the 1st image
        $value = $node->attributes->getNamedItem('src')->nodeValue;
        $reviews[] = $value;
    }

    return __check_origin_image($reviews);
}

function __crop_image($url){
    if (strpos($url, '.png')){
        $image = imagecreatefrompng($url);
    }else if (strpos($url, '.jpg') || strpos($url, '.jpeg')){
        $image = imagecreatefromjpeg($url);
    }else{
        throw new Exception("Extension not supported");
    }
    $filename = 'export_dir/cropped_temp.jpg';

    $thumb_width = $GLOBALS['width'];
    $thumb_height = $GLOBALS['height'];

    $width = imagesx($image);
    $height = imagesy($image);

    $original = $width / $height;
    $thumb = $thumb_width / $thumb_height;

    if ( $original >= $thumb )
    {
        // crop width
        $new_height = $thumb_height;
        $new_width = $width / ($height / $thumb_height);
    }else
    {
        // crop height
        $new_width = $thumb_width;
        $new_height = $height / ($width / $thumb_width);
    }

    $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
    // Resize and crop
    imagecopyresampled($thumb,
        $image,
        0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
        0 - ($new_height - $thumb_height) / 2, // Center the image vertically
        0, 0,
        $new_width, $new_height,
        $width, $height);
    imagejpeg($thumb, $filename, 100);

    return $filename;
}

function __merge_frame($frame, $temp){
    $image_info = getimagesize($temp);
    if ($image_info['mime'] === 'image/png'){
        $dest = imagecreatefrompng($temp);
    }else if ($image_info['mime'] === 'image/jpeg'){
        $dest = imagecreatefromjpeg($temp);
    }else{
        throw new Exception("Extension not supported");
    }
    $src = imagecreatefrompng($frame);

    imagealphablending($dest, true);
    imagesavealpha($dest, true);
    imagecopy($dest, $src, 0, 0, 0, 0, $GLOBALS['width'], $GLOBALS['height']);

    return $dest;
}

function __add_text($img, $text){
    $factor = round((imagesx($img) / imagesy($img)), 0, PHP_ROUND_HALF_DOWN);

    $textbox = new Box($img);
    $textbox->setFontFace('fonts/Rowdies-Bold.ttf');
    $textbox->setFontColor(new Color(223, 167, 30));
    $textbox->setBox(
        70, // distance from left edge
        ($GLOBALS['height'] / 2) + (50 * $factor), // distance from top edge
        $GLOBALS['width'] - 150, // textbox width
        500  // textbox height
    );

    $textbox->setFontSize(50 * $factor );

    $textbox->setTextAlign('center', 'center');
    $textbox->draw($text );

//    header('Content-Type: image/png');
//    imagepng($img);

    return $img;
}

function export($datas){
    $GLOBALS['width'] = 900;
    $GLOBALS['height'] = 900;

    $dest_dir = 'export_dir/' . date("Y/m/d/");
    if (! is_dir($dest_dir))
        mkdir($dest_dir, 0777, true);

    $results = [];
    foreach ($datas as $data){
        $frame = __DIR__ . '/images/frame-900x900.png';
        $img_src = __crawling_img($data['link']);
        $temp = __crop_image($img_src);
        $mix_img = __merge_frame($frame, $temp);
        $mix_text_img = __add_text($mix_img, $data['title']);
        $filename = $dest_dir . $data['hash'] .'.png';
        imagepng($mix_text_img, $filename);
        $results[] = $filename;
    }
//    setcookie("data", "", time(), "/");
    return $results;
}