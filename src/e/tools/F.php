<?php
/**
 * Created by PhpStorm.
 * User: rakoth
 * Date: 16.04.14
 * Time: 23:41
 */

namespace Engine\tools;


class F {

    const AUTO      = 1;
    const RU_EN     = 2;
    const EN_RU     = 3;

    static function curPageURL() {
        $pageURL = 'http';
        if (self::array_get($_SERVER, "HTTPS") == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    static function mkplainarticle($name){
//        $res = strtoupper(str_replace(array(' ', ',', '/', '(', ')', '[', ']' ), '', $name));
        $res = self::simplify_string($name, true);
        $res = str_replace('#', '_s',$res);
//        $res = str_replace('&', '_and_',$res);
        return $res;
    }

    public static function escape_str($str, $force_strong=False){
        if(!$force_strong){
            $syms = array('\x00', '\n', '\r', '\\', "'", '"', '\x1a');
            $rep =array('\\x00', '\\n', '\\r', '\\\\', "\'", '\"', '\\x1a');
            return str_replace($syms,$rep,$str);
        } else {
            return preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $str);
        }
//        return mysql_real_escape_string($str);
    }

    public static function simplify_string($str, $make_slug=false)
    {
        if($make_slug){
            $r = str_replace(array(' ', '(', ')','/',',', '.', '&', '+'), array('-','-','','-','-','.','_', 'plus'), $str );
            $r = preg_replace('/[-]{2,}/s', '-', $r);
        }else{
            $r = str_replace(' ', '', $str);
        }
        $code = strtolower(self::transRu2Lat($r));
        return ($make_slug) ? urlencode($code) : $code;
    }

    public static function transRu2Lat($s) {
        $cyr = array(
            'ж',  'ч',  'щ',   'ш',  'ю', 'я', 'ё', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ы', 'ь', 'э',
            'Ж',  'Ч',  'Щ',   'Ш',  'Ю', 'Я', 'Ё', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ы', 'Ь', 'Э');
        $lat = array(
            'zh', 'ch', 'sht', 'sh', 'yu', 'ya', 'yo', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '', 'y', '', 'e',
            'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'YA', 'Yo', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', '', 'Y', '', 'E');
        return str_replace($cyr, $lat, $s);
    }

    public static function correctString ($string, $dir=NULL){
        if(!$dir) $dir = self::AUTO;
        $ru = array(
            "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
            "ф","ы","в","а","п","р","о","л","д","ж","э",
            "я","ч","с","м","и","т","ь","б","ю"
        );
        $en = array(
            "q","w","e","r","t","y","u","i","o","p","[","]",
            "a","s","d","f","g","h","j","k","l",";","'",
            "z","x","c","v","b","n","m",",","."
        );
        if(self::AUTO == $dir){
            $dir = (in_array(mb_substr($string, 0,1, 'utf-8'), $ru)) ? self::RU_EN : self::EN_RU;
        }
        return (self::RU_EN == $dir) ? str_replace($ru, $en, $string) : str_replace($en, $ru, $string);
    }

    public static function extract_numbers($str, $def=''){
        $r = preg_replace("/,/",".",$str, 1);
        $r = preg_replace("/[^-0-9\.]/","",$r);
        if(strlen($r)<1) $r = $def;
        return $r;
    }

    /**
     * gets item from array or returns $def value
     * @param array|mixed $a Array to search in
     * @param mixed $k Key to search for in array
     * @param mixed $def Value returned if $key if not present
     * @return bool
     */
    public static function array_get(&$a, $k, $def=null){
        if(!$a) return $def;
        if(is_array($k)){
            $ar = &$a;
            foreach ($k as $item) {
                try{
                    if(!isset($ar[$item])){
                        return $def;
                    } else {
                        $ar = &$ar[$item];
                    }
                } catch( \ErrorException $e){
                    var_dump($e);
                }

            }
            return $ar;
//            for($l=count($k), $ar=&$a, $i=0; $i<$l; $i++){
//                try{
//                    if(array_key_exists($k[$i], $ar)){
//                        $ar = &$ar[$k[$i]];
//                        if($i==$l-1){
//                            return $ar;
//                        }
//                    } else {
//                        return $def;
//                    }
//                } catch (ErrorException $e){
//                    var_dump($e);die;
//                }
//            }
        }else{
            return (array_key_exists($k, $a)) ? $a[$k] : $def;
        }
    }
//    public static function array_get(&$arr, $key, $def=False){
//        if(!$arr) return $def;
//        if(is_array($key)){
//            if(count($key) == 1){
//                reset($key);
//                $key = current($key);
//            }else{
//                reset($key);
//                $k = current($key);
//                $n_arr =  F::array_get($arr, $k);
//                if(is_array($n_arr)){
//                    return F::array_get($n_arr, array_slice($key, 1));
//                } if (!$n_arr) {
//                    return $def;
//                }
//            }
//        }
//        return (isset($arr[$key])) ? $arr[$key] : $def;
//    }

    static function get_random_string($length, $valid_chars=null) {
        // start with an empty random string
        $random_string = "";
        if(!$valid_chars){
            $valid_chars = 'qwertupsdfghjnbvz1234567890';
        }

        // count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = strlen($valid_chars);

        // repeat the steps until we've created a string of the right length
        for ($i = 0; $i < $length; $i++)
        {
            // pick a random number from 1 up to the number of valid chars
            $random_pick = mt_rand(1, $num_valid_chars);

            // take the random character out of the string of valid chars
            // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
            $random_char = $valid_chars[$random_pick-1];

            // add the randomly-chosen char onto the end of our string so far
            $random_string .= $random_char;
        }

        // return our finished random string
        return $random_string;
    }

    /** Алиас на  imagecreatefromX */
    static function imagecreatefromfile( $filename ) {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException('File "'.$filename.'" not found.');
        }
        switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
            case 'jpeg':
            case 'jpg': return @imagecreatefromjpeg($filename); break;
            case 'png': return @imagecreatefrompng($filename); break;
            case 'gif': return @imagecreatefromgif($filename); break;
            default:
                throw new \InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
                break;
        }
    }


    static function imagetofile($image, $filename = null, $quality=null ) {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException('File "'.$filename.'" not found.');
        }
        switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
            case 'jpeg':
            case 'jpg': return imagejpeg($image, $filename, $quality); break;
            case 'png':
                $quality = $quality>9 ? floor($quality/11) : $quality;
                return imagepng($image, $filename, $quality); break;
            case 'gif': return imagegif($image, $filename); break;
            default:
                throw new \InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
                break;
        }
    }

    /**
     * Recursively delete a directory
     *
     *
     * @param string $dir Directory name
     * @param boolean $deleteRootToo Delete specified top-level directory as well
     */
    static function unlinkRecursive($dir, $deleteRootToo)
    {
        if(!$dh = @opendir($dir))
        {
            return;
        }
        while (false !== ($obj = readdir($dh)))
        {
            if($obj == '.' || $obj == '..')
            {
                continue;
            }

            if (is_dir($dir . '/' . $obj))
            {
                self::unlinkRecursive($dir.'/'.$obj, true);
            } else {
                unlink($dir . '/' . $obj);
            }
        }

        closedir($dh);

        if ($deleteRootToo)
        {
            rmdir($dir);
        }

        return;
    }

    /**
     * Возвращает строку цены, форматированную красиво и единообразно
     * @param $p - Цена
     * @param bool|false $rur - показывать ли значок валюты после числа
     * @param $delim - разделитель тысяч
     * @return string
     */
    public static function price_format($p, $rur=false, $delim=' '){
        return number_format($p, 2, '.', $delim).($rur ? ' руб.' : '');
    }

    /**
     * Отбрасывает у данного числа дробную часть, если она нулевая
     * @param float $f Количество
     * @return float
     */
    public static function amount_format($f)
    {
        return (round($f) == $f) ? round($f) : $f;
    }

    /**
     * Красиво рисуем дату. Просто чтобы в одном месте потом менять формат
     * @param null $ts - timastamp, который нужно форматировать
     * @return bool|string
     */
    public static function date_format($ts=null, $with_time=false){
//        $lc = locale_get_default();
//        setlocale(LC_TIME, "ru_RU.UTF-8");
        if(!$ts) $ts = time();
        $format = "%a %d %B %Y". ($with_time ? " %H:%M" : '');
        $r = strftime($format, $ts);
//        setlocale(LC_TIME, $lc);
        return $r;
        //return date(DATE_RSS, $ts);
    }

    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    public static function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',    1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    public static function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

    public static function serialize($data)
    {
        return json_encode($data);
    }

    public static function deserialize($data)
    {
        return json_decode($data, true);
    }

}
