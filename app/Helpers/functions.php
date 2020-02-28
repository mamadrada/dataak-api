<?php

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;


function convertNumber($x)
{
    $persianNumber = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $englishNumber = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    return str_ireplace($persianNumber, $englishNumber, $x);
}

function getRandomNumber()
{
    return rand(11111, 99999);
}

function getPassword($length = 8)
{
    $chars = 'abcdefghkmnprstuvwxyz123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}
function getCurrentDateTime()
{
    return date("Y-m-d H:i:s");
}

function addDayToTimestamp($timestamp, $day, $format = 'Y-m-d H:i:s')
{
    $date = date_create($timestamp);
    date_add($date, date_interval_create_from_date_string($day . " days"));

    return date_format($date, $format);
}
function addHourToTimestamp($timestamp, $hour, $format = 'Y-m-d H:i:s')
{
    $date = date_create($timestamp);
    date_add($date, date_interval_create_from_date_string($hour . " hours"));

    return date_format($date, $format);
}
function addMonthToTimestamp($timestamp, $month, $format = 'Y-m-d H:i:s')
{
    $date = date_create($timestamp);
    date_add($date, date_interval_create_from_date_string($month . " months"));

    return date_format($date, $format);
}
function extractDate($dateTime)
{
    $date = date_create($dateTime);

    return date_format($date, 'Y-m-d');
}
function extractTime($dateTime)
{
    $date = date_create($dateTime);

    return date_format($date, 'H:i:s');
}
function getPrettyAll($table, $primary, $field)
{
    $result = array();
    $rows = DB::table($table)->get()->toArray();
    for ($i = 0; $i < count($rows); $i++) {
        $result[$rows[$i]->$primary] = $rows[$i]->$field;
    }
    return $result;
}
function createSlug($string, $separator = '-') {
    $_transliteration = array(
        '/ä|æ|ǽ/' => 'ae','/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
        '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
        '/ç|ć|ĉ|ċ|č/' => 'c',
        '/Ð|Ď|Đ/' => 'D',
        '/ð|ď|đ/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
        '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
        '/ĝ|ğ|ġ|ģ/' => 'g',
        '/Ĥ|Ħ/' => 'H',
        '/ĥ|ħ/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
        '/Ĵ/' => 'J',
        '/ĵ/' => 'j',
        '/Ķ/' => 'K',
        '/ķ/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
        '/Ñ|Ń|Ņ|Ň/' => 'N',
        '/ñ|ń|ņ|ň|ŉ/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
        '/Ŕ|Ŗ|Ř/' => 'R',
        '/ŕ|ŗ|ř/' => 'r',
        '/Ś|Ŝ|Ş|Ș|Š/' => 'S',
        '/ś|ŝ|ş|ș|š|ſ/' => 's',
        '/Ţ|Ț|Ť|Ŧ/' => 'T',
        '/ţ|ț|ť|ŧ/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
        '/Ý|Ÿ|Ŷ/' => 'Y',
        '/ý|ÿ|ŷ/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž/' => 'Z',
        '/ź|ż|ž/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/' => 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/ƒ/' => 'f');
    $quotedReplacement = preg_quote($separator, '/');
    $merge = array('/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ','/[\s\p{Zs}]+/mu' => $separator,sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '');
    $map = $_transliteration + $merge;
    unset($_transliteration);
    return preg_replace(array_keys($map), array_values($map), $string);
}
