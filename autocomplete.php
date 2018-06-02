<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

if (isset($_SERVER['SCRIPT_FILENAME']) && is_link($_SERVER['SCRIPT_FILENAME'])) {
    define('BP', dirname($_SERVER['SCRIPT_FILENAME']));
} else {
    define('BP', dirname(__FILE__));
}

// Configure include path
$paths = array();
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . get_include_path());

// Register autoload
spl_autoload_register(function($class) {
    $classFile = str_replace('\\', '/', $class, $count);
    if (!$count) {
        $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class)));
    }
    $classFile .= '.php';
    include $classFile;
});

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

$html = '';
$q = isset($_GET['q']) ? $_GET['q'] : '';
$found = false;

if ('' !== $q) {
    $store = isset($_GET['store']) ? $_GET['store'] : '';
    $currency = isset($_GET['currency']) ? $_GET['currency'] : '';
    $config = new Wyomind_Elasticsearch_Config($store);
    $synonyms = new Wyomind_Elasticsearch_Config($store, true);
    $strings = $synonyms->getObject();
    $searchTerm = $q;
    if (is_array($strings) && count($strings)) {
        foreach ($strings as $string) {
            if (trim($q) == trim($string['query_text']) && $store == $string['store']) {
                $q = $string['synonym_for'];
                break;
            }
        }
    }

    try {
        if (!$config->getData()) {
            throw new Exception('Could not find config for autocomplete');
        }

        $client = new Wyomind_Elasticsearch_Client($config->getClientConfig());
        $index = new Wyomind_Elasticsearch_Index($client, $client->getIndexAlias($store));
        $index->setAnalyzers($config->getAnalyzers());

        $autocomplete = new Wyomind_Elasticsearch_Autocomplete($config);
        $html = $autocomplete->search($q, $searchTerm, $currency, $index);
        $found = true;
    } catch (Exception $e) {
        $fallback = parse_url($_GET['fallback_url']);
        $current = parse_url($_SERVER['HTTP_HOST']);
        if (isset($_GET['fallback_url']) && $fallback['host'] == $current['path']) {
            $url = $_GET['fallback_url'] . '?q=' . $q;
            $html = @file_get_contents($url);
        } else {
            throw new Exception('Warning : Fallback url is not from the same domain !');
        }
    }
}

header('Fast-Autocomplete: ' . ($found ? 'HIT' : 'MISS'));

echo $html;
exit;
