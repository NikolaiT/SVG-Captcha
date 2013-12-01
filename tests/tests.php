<?php

error_reporting(E_ALL);

$a = array("alpha" => 2433, "beta" => array(1,2, 3,4));

$b  = $a;

$b["gamma"] = array(1, 2, 34,5);
$b["beta"][0] = 777;

D($a);
D($b);

function D($a) {
    print "<pre>";
    print_r($a);
    print "</pre>";
}