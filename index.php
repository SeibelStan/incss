<?php

function allSpace($data) {
    return preg_replace('/\s+/', ' ', $data);
}

function cssWrap($data) {
    $data = preg_replace('/\s+/', '', $data);
    $data = preg_replace('/;/', ";\n", $data);
    $data = preg_replace('/^/m', "\t", $data);
    return $data;
}

function keyTag($data) {
    return preg_replace('/\W/', '', $data);
}

$pck = @$_GET['pck'] ?: 'home';

$stl = json_decode(file_get_contents('stl/' . $pck . '.json'));
$tpl = file_get_contents('tpl/' . $pck . '.php');

$rsl = $tpl;
foreach($stl as $key => $val) {
    switch($key[0]) {
        case '#': {
            $rsl = preg_replace('/id="' . keyTag($key) . '"/', 'style="' . allSpace($val) . '"', $rsl);
            break;
        }
        case '.': {
            $rsl = preg_replace('/class="' . keyTag($key) . '"/', 'style="' . allSpace($val) . '"', $rsl);
            break;
        }
        case '{': {
            $rsl = preg_replace('/' . $key . '/', $val, $rsl);
            break;
        }
        default: {
            $rsl = preg_replace('/<' . keyTag($key) . '/', '<' . keyTag($key) . ' style="' . allSpace($val) . '"', $rsl);
        }
    }
}

$rsl = preg_replace('/style="(.+?)" style="/', 'style="$1 ', $rsl);

if(@$_GET['dbg']) {
    header('Content-type: text/plain');
    echo json_encode($stl, 386);
    echo "\n\n";
    echo $tpl;
    echo "\n\n";
}

if(@$_GET['css']) {
    echo "<style>\n";
    foreach($stl as $key => $val) {
        if($key[0] != '{') {
            echo $key . " {\n";
            echo cssWrap($val);
            echo "}\n\n";
        }
    }
    echo "</style>\n\n";
}

if(file_exists('mdw/' . $pck . '.php')) {
    include('mdw/' . $pck . '.php');
}

echo $rsl;