<?php

function combine_books(&$v, $k, $kname) {
    $v[] = date('Y-m-d H:i:s',time());
    $v[] = date('Y-m-d H:i:s',time());
    $v = array_combine($kname, array_slice($v, 0));
}