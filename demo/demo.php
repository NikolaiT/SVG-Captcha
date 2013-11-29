<?php

include_once '../SVGCaptcha.php';

$req = $_REQUEST["svgcaptcha_difficulty"];

if (isset($req) && !empty($req)) {
    $obj = null; 
    switch ($req) {
        case "easy":
            $obj = SVGCaptcha::getInstance(4, $width = 300, $height = 130, $difficulty = SVGCaptcha::EASY);
            $c = $obj->getSVGCaptcha();
            echo $c[1];
            break;
        case "medium":
            $obj = SVGCaptcha::getInstance(4, $width = 300, $height = 130, $difficulty = SVGCaptcha::MEDIUM);
            $c = $obj->getSVGCaptcha();
            echo $c[1];
            break;
        case "hard":
            $obj = SVGCaptcha::getInstance(4, $width = 300, $height = 130, $difficulty = SVGCaptcha::HARD);
            $c = $obj->getSVGCaptcha();
            echo $c[1];
            break;
        default:
            break;
    }
}

