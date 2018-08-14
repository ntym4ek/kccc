<?php
include 'kladr.php';

/**
 * callback autocomplete function
 */
function address_book_autocomplete_region($string) {
    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
    $api = new Kladr\Api('570f4f570a69deff1f8b4584', '');
    $query              = new Kladr\Query();
    $query->ContentName = check_plain($string);
    $query->ContentType = Kladr\ObjectType::Region;
    //$query->Limit       = 12;
    $result             = $api->QueryToArray($query);

    $regions = array();
    foreach($result as $region) {
        $region['name'] .= ' ' . $region['typeShort'] . '. ';
        $key = $region['name'] . '[' . $region['id'] .'][' . $region['zip'] . ']';
        $regions[$key] = preg_replace('!^(.*?)('.$string.')(.*?)$!iu', '$1<span class="highlight">$2</span>$3', $region['name']);
    }

    drupal_json_output($regions);
}

/**
 * callback autocomplete function
 */
function address_book_autocomplete_area($kladr, $string) {
    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
    $api = new Kladr\Api('570f4f570a69deff1f8b4584', '');

    $query              = new Kladr\Query();
    $query->ContentName = check_plain($string);
    $query->ContentType = Kladr\ObjectType::District;
    if ($kladr) {
        $query->ParentType  = Kladr\ObjectType::Region;
        $query->ParentId    = $kladr;
    }
    $query->WithParent  = TRUE;
    //$query->Limit       = 10;
    $result             = $api->QueryToArray($query);

    $areas = array();
    foreach($result as $area) {
        $area['name'] = $area['typeShort'] . '. ' . $area['name'];
        $key = $area['name'] . '[' . $area['id'] .'][' . $area['zip'] . ']';
        $areas[$key] = preg_replace('!^(.*?)('.$string.')(.*?)$!iu', '$1<span class="highlight">$2</span>$3', $area['name']);
        if (isset($area['parents']) && $area['parents']) {
            $areas[$key] .= '<span class="parents">';
            // составить хвост для населенного пункта
            foreach ($area['parents'] as $index => $parent) {
                // если область указана, не включать её в хвост
                if (!$kladr || $index) $areas[$key] .= ', ' . $parent['name'] . ' ' . $parent['type'];
            }
            $areas[$key] .= '</span>';
        }
    }

    drupal_json_output($areas);
}

/**
 * callback autocomplete function
 */
function address_book_autocomplete_city($kladr, $string) {
    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
    $api = new Kladr\Api('570f4f570a69deff1f8b4584', '');

    $query              = new Kladr\Query();
    $query->ContentName = check_plain($string);
    $query->ContentType = Kladr\ObjectType::City;
    if ($kladr) {
        $query->ParentType  = Kladr\ObjectType::Region;
        if (substr($kladr, 2, 3) != '000') $query->ParentType  = Kladr\ObjectType::District;
        $query->ParentId    = $kladr;
    }
    $query->WithParent  = TRUE;
    //$query->Limit       = 8;
    $result             = $api->QueryToArray($query);

    $cities = array();
    foreach($result as $city) {
        $city['name'] = $city['typeShort'] . '. ' . $city['name'];
        $key = $city['name'] . '[' . $city['id'] .'][' . $city['zip'] . ']';
        $cities[$key] = preg_replace('!^(.*?)('.$string.')(.*?)$!iu', '$1<span class="highlight">$2</span>$3', $city['name']);
        if (isset($city['parents']) && $city['parents']) {
            $cities[$key] .= '<span class="parents">';
            // составить хвост для населенного пункта
            foreach ($city['parents'] as $index => $parent) {
                // если область указана, не включать её в хвост
                if (!$kladr || $index) $cities[$key] .= ', ' . $parent['name'] . ' ' . $parent['type'];
            }
            $cities[$key] .= '</span>';
        }
    }

    drupal_json_output($cities);
}

/**
 * callback autocomplete function
 */
function address_book_autocomplete_street($kladr, $string) {
    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
    $api = new Kladr\Api('570f4f570a69deff1f8b4584', '');

    $query              = new Kladr\Query();
    $query->ContentName = check_plain($string);
    $query->ContentType = Kladr\ObjectType::Street;
    $query->ParentType  = Kladr\ObjectType::City;
    $query->ParentId    = $kladr;
    $query->WithParent  = false;
    //$query->Limit       = 6;
    $result             = $api->QueryToArray($query);

    $streets = array();
    foreach($result as $street) {
        $key = $street['name'] . '[' . $street['id'] .']['. $street['zip'] . ']';
        $street_name = preg_replace('!^(.*?)('.$string.')(.*?)$!iu', '$1<span class="highlight">$2</span>$3', $street['name']);
        $streets[$key] = $street['typeShort'] . '. ' . $street_name;
    }

    drupal_json_output($streets);
}

/**
 * callback autocomplete function
 */
function address_book_autocomplete_house($kladr, $string) {
    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
    $api = new Kladr\Api('570f4f570a69deff1f8b4584', '');

    $query              = new Kladr\Query();
    $query->ContentName = check_plain($string);
    $query->ContentType = Kladr\ObjectType::Building;
    $query->ParentType  = Kladr\ObjectType::Street;
    $query->ParentId    = $kladr;
    $query->WithParent  = false;
    //$query->Limit       = 11;
    $result             = $api->QueryToArray($query);

    $houses = array();
    foreach($result as $house) {
        $key = $house['name'] . '[' . $house['id'] .']['. $house['zip'] . ']';
        $house_name = preg_replace('!^(.*?)('.$string.')(.*?)$!iu', '$1<span class="highlight">$2</span>$3', $house['name']);
        $houses[$key] = $house['typeShort'] . '. ' . $house_name;
    }

    drupal_json_output($houses);
}