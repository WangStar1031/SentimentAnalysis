<?php

class Utility {

    function text($text) {
        $text = preg_replace("/(^|[\n ])([\w]*?)([\w]*?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2 <a class=\"btn btn-xs\" target=\"_blank\" href=\"$3\" ><i class=\"fa fa-external-link\" aria-hidden=\"true\"></i> vView link</a>", $text);
        $text = preg_replace("/(^|[\n ])([\w]*?)((www)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a class=\"btn btn-xs\" target=\"_blank\" href=\"http://$3\" ><i class=\"fa fa-external-link\" aria-hidden=\"true\"></i> view link</a></a>", $text);
        $text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a target=\"_blank\" class=\"btn btn-xs\" href=\"mailto:$2@$3\">$2@$3 3333</a>", $text);

        /*
          $pattern = "/(http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
          $text= preg_replace($pattern, "<a href='http://\\0' target='_blank' class='btn btn-xs' >\\0 333</a>", $text);
          $pattern = "/(https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
          $text= preg_replace($pattern, "<a href='https://\\0' target='_blank' class='btn btn-xs' >\\0 333</a>", $text);
         */
        /*

          $str = str_replace("http://www","www",$text);
          $str = str_replace("https://www","www",$str);

          $attrs = '';
          foreach ($attributes as $attribute => $value)
          {
          $attrs .= " {$attribute}=\"{$value}\"";
          }

          //$str = ' ' . $str;
          $str = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i','$0 111****<a target=\'_blank\' class=\'btn btn-xs\' href="$2"'.$attrs.'>$2</a>',$str);
          $str = preg_replace('`([^"=\'>])((www).[^\s<]+[^\s<\.)])`i','$0 222****<a target=\'_blank\' class=\'btn btn-xs\' href="http://$2"'.$attrs.'>$2</a>',$str);
          $str = substr($str, 1);
          return $str;


         */
        return($text);
    }

    /*     * ***********************************
     * Funzione conversione data americana > Italiana
     * *********************************** */

    function dataConvertItalian($data) {
        $aaaa = substr($data, 0, 4);
        $mm = substr($data, 5, 2);
        $gg = substr($data, 8, 2);
        $ore = substr($data, 11, 2);
        $min = substr($data, 14, 2);


        return $gg . '-' . $mm . '-' . $aaaa;
    }

    function dataConvert($data, $viwOre) {

        $aaaa = substr($data, 0, 4);
        $mm = substr($data, 5, 2);
        $gg = substr($data, 8, 2);
        $ore = substr($data, 11, 2);
        $min = substr($data, 14, 2);

        /*
          $l = 1;
          if (isset($_REQUEST['l']))
          $l = $_REQUEST['l'];
          else if (isset($_SESSION['l']))
          $l = $_SESSION['l'];
         */

        if ($viwOre == 1) {

            if ($aaaa . '' . $mm . '' . $gg == date('Ymd')) {
                return "Oggi alle $ore:$min";
            } else if ($aaaa . '' . $mm . '' . $gg == (date('Ymd') + 1)) {
                return "Domani $ore:$min";
            } else {
                return $gg . '-' . $mm . '-' . $aaaa . ' ' . $ore . ':' . $min;
            }
        } else {
            if ($aaaa . '' . $mm . '' . $gg == date('Ymd')) {
                return "Oggi";
            } else if ($aaaa . '' . $mm . '' . $gg == (date('Ymd') + 1)) {
                return "Domani";
            } else {
                return $gg . '-' . $mm . '-' . $aaaa;
            }
        }
    }

    static function dataConvertInsert($data, $viwOre, $separatore) {
        $aaaa = substr($data, 0, 4);
        $mm = substr($data, 5, 2);
        $gg = substr($data, 8, 2);
        $ore = substr($data, 11, 2);
        $min = substr($data, 14, 2);

        $data = $gg . $separatore . $mm . $separatore . $aaaa;
        if ($viwOre <> '0')
            $data = $data . ' ' . $ore . ':' . $min . ':00';

        return $data;
    }

    function json_decode_nice($json, $assoc = TRUE) {

        $json = str_replace("\n", " ", $json);
        $json = str_replace("\r", " ", $json);
        /* $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
          $json = preg_replace('/(,)\s*}$/','}',$json);
         */
        $json = htmlentities($json);

// return json_decode($json,$assoc);
        return $json;
    }

    function MaxOf($Array, $Campo) {
        $max = 0;
        foreach ($Array as $obj) {
            if ($obj[$Campo] > $max) {
                $max = $obj[$Campo];
            }
        }
        return $max;
    }

    function MinOf($Array, $Campo) {
        $max = 0;
        foreach ($Array as $obj) {
            if ($obj[$Campo] < $max) {
                $max = $obj[$Campo];
            }
        }
        return $max;
    }

    function GroupBy($arr, $Campo) {
        $result = array();
        $RowIndex = 0;
        foreach ($arr as $data) {
            $id = $data[$Campo];

            if (!isset($result[$id])) {
                $result[$id] = $RowIndex++;
            }
        }
        return $result;
    }

    function ColorTable($Value) {

        $Value = round($Value, 1, PHP_ROUND_HALF_UP);

        if ($Value <= -1.00) {
            return '#ff0000';
        }
        if ($Value == -0.90)
            return '#ff0000';
        if ($Value == -0.80)
            return '#ff0000';
        if ($Value == -0.70)
            return '#ff6e00';
        if ($Value == -0.60)
            return '#ff6e00';
        if ($Value == -0.50)
            return '#ff6e00';
        if ($Value == -0.40)
            return '#ff6e00';
        if ($Value == -0.30)
            return '#ffcd00';
        if ($Value == -0.20)
            return '#ffcd00';
        if ($Value == -0.10)
            return '#ffff00';
        if ($Value == 0.00)
            return '#ffff00';
        if ($Value == 0.10)
            return '#ffff00';
        if ($Value == 0.20)
            return '#beff00';
        if ($Value == 0.30)
            return '#beff00';
        if ($Value == 0.40)
            return '#53ff00';
        if ($Value == 0.50)
            return '#53ff00';
        if ($Value == 0.60)
            return '#53ff00';
        if ($Value == 0.70)
            return '#53ff00';
        if ($Value == 0.80)
            return '#00ff00';
        if ($Value == 0.90)
            return '#00ff00';
        if ($Value >= 1.00)
            return '#00ff00';

        return '#00ff00';
    }

    function GetLanguageList($str, $rowsLingue) {
        $List = explode(",", $str);
        $RetVal = "";
        foreach ($List as $l) {
            try {
                $s = intval(array_search($l, array_column($rowsLingue, 'idCodiceLingua')));
                $RetVal .= $rowsLingue[$s]["LIngua"] . " ";
            } catch (Exception $e) {
                
            }
        }
        return trim($RetVal);
    }
    
    function GetLanguageShortList($str, $rowsLingue) {
        $List = explode(",", $str);
        $RetVal = "";
        foreach ($List as $l) {
            try {
                $s = intval(array_search($l, array_column($rowsLingue, 'idCodiceLingua')));
                $RetVal .= "'" . $rowsLingue[$s]["CodiceLingua"] . "',";
            } catch (Exception $e) {
                
            }
        }
        return trim($RetVal, ",");
    }

}

?>