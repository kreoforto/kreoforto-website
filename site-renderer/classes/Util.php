<?php

class Util {

    private $documents;
    
    public function __construct($docs) {
        $this->documents = $docs;
    }

    public function CreateLinks($content) {
        
        $matches = array();
        if( preg_match_all("/###LINKTO:([\d]+)###/", $content, $matches) ) {
    
            $unique = array_unique($matches[1]);
            foreach( $unique as $match ) {
                foreach( $this->documents as $item ) {
                    if($match == $item->id) {
                        $content = preg_replace("/###LINKTO:".$match."###/", $item->path, $content);
                        break;
                    }
                }
            }
        }
        
        return $content;
    }
    
    public static function Obfuscate($content) {
        
        $chars = array("/A/", "/B/", "/C/", "/D/", "/E/", "/F/", "/G/", "/H/", "/I/", "/J/", "/K/", "/L/", "/M/", "/N/", "/O/", "/P/", "/Q/", "/R/", "/S/", "/T/", "/U/", "/V/", "/W/", "/X/", "/Y/", "/Z/",
                       "/a/", "/b/", "/c/", "/d/", "/e/", "/f/", "/g/", "/h/", "/i/", "/j/", "/k/", "/l/", "/m/", "/n/", "/o/", "/p/", "/q/", "/r/", "/s/", "/t/", "/u/", "/v/", "/w/", "/x/", "/y/", "/z/",
                       "/\./", "/-/", "/_/", "/@/", "/\//", "/ /");
                
        $code  = array("&#65;", "&#66;", "&#67;", "&#68;", "&#69;", "&#70;", "&#71;", "&#72;", "&#73;", "&#74;", "&#75;", "&#76;", "&#77;", "&#78;", "&#79;", "&#80;", "&#81;", "&#82;", "&#83;", "&#84;", "&#85;", "&#86;", "&#87;", "&#88;", "&#89;", "&#90;",
                       "&#97;", "&#98;", "&#99;", "&#100;", "&#101;", "&#102;", "&#103;", "&#104;", "&#105;", "&#106;", "&#107;", "&#108;", "&#109;", "&#110;", "&#111;", "&#112;", "&#113;", "&#114;", "&#115;", "&#116;", "&#117;", "&#118;", "&#119;", "&#120;", "&#121;", "&#122;",
                       "&#46;", "&#45;", "&#95;", "&#64;", "&#47;", "&#32;");
        
        $ncode = array("&#48;", "&#49;", "&#50;", "&#51;", "&#52;", "&#53;", "&#54;", "&#55;", "&#56;", "&#57;");
        $ntemp = array("%0%;", "%1%", "%2%", "%3%", "%4%", "%5%", "%6%", "%7%", "%8%", "%9%");
        $nums  = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        
        $marker = array("&#64;", "&#46;", "&#47;", "&#32;");
        
        
        $matches = array();
        if( preg_match_all("/###OBFUSCATE:([^#]*?)###/", $content, $matches) ) {
            foreach( $matches[1] as $match) {
                
                $obfuscated = str_replace($nums, $ntemp, $match);
                $obfuscated = preg_replace($chars, $code, $obfuscated);
                foreach( $marker as $item ) {
                    $obfuscated = str_replace($item, sprintf("<span>%s</span>", $item), $obfuscated);
                }
                $obfuscated = str_replace($ntemp, $ncode, $obfuscated);
                
                $content = preg_replace("/###OBFUSCATE:".preg_quote($match, "/")."###/", $obfuscated, $content, 1); 
            }
        }
        
        return $content;
    }
    
    public static function readConfig($paramName) {
        
        $filename = SiteConfiguration::conf_file;
        
        $fid  = fopen($filename, 'r');
        $conf = fread($fid, filesize($filename));
        fclose($fid);
        
        $matches = array();
        preg_match("/{$paramName}[\s]*=[\s]*(.+)/m", $conf, $matches);
        
        return is_numeric($matches[1]) ? (boolean)$matches[1] : $matches[1];        
    }
}

?>