<?php

require_once("YUICompressor.php");


class SiteBuilder {
    
    private $webfolders;

    public function __construct() {

        $this->webfolders = array( "IMAGE" => SiteConfiguration::web_folder . "/" . SiteConfiguration::image_folder . "/",
                                   "CSS"   => SiteConfiguration::web_folder . "/" . SiteConfiguration::css_folder . "/",
                                   "JS"    => SiteConfiguration::web_folder . "/" . SiteConfiguration::js_folder . "/",
                                   "PHP"   => SiteConfiguration::web_folder . "/" . SiteConfiguration::php_folder . "/",
                                   "ROOT"  => SiteConfiguration::web_folder . "/" );
    }
    
    public function buildSite() {
        
        $this->clearWebFolders();
        $this->createWebFolders();
        $this->copyStaticContent();
    }
    
    public function createStylesheet() {

        $css_files = SiteConfiguration::$stylesheets;        
        return SiteConfiguration::css_folder . "/" . $this->minify($css_files, SiteConfiguration::css_file, "CSS");
    }
    
    private function clearWebFolders() {
    
        foreach( $this->webfolders as $folder ) {
            
            if( is_dir($folder) ) {
            
                $handle = opendir($folder);
                while(false !== ($file = readdir($handle))) {
                    if($file != "." && $file != "..") {
                        unlink($folder.$file);
                    }
                }
                
                if( $folder !== SiteConfiguration::web_folder . "/" ) {
                    rmdir($folder);
                }
            }
        }
    }
    
    private function createWebFolders() {

        foreach( $this->webfolders as $folder ) {
    
            if( !is_dir($folder) ) {
                mkdir($folder);
            }
        }
    }
    
    private function copyStaticContent() {
        
        $folders = array( array("web"    => $this->webfolders["IMAGE"],
                                "develop" => "../" . SiteConfiguration::image_folder . "/"),
                          array("web"    => $this->webfolders["PHP"],
                                "develop" => "../" . SiteConfiguration::php_folder . "/"),
                          array("web"    => $this->webfolders["ROOT"],
                                "develop" => "../" . SiteConfiguration::static_folder . "/") );
        
        foreach( $folders as $folder ) {
            
            $handle = opendir($folder["develop"]);
            while(false !== ($file = readdir($handle))) {
                if(is_file($folder["develop"].$file) && $file != ".DS_Store") {
                    
                    $folder["web"] !== $this->webfolders["PHP"]  && !in_array($file, SiteConfiguration::$unversioned) ? $webfile = self::versionize($file) : $webfile = $file;
                    copy($folder["develop"].$file, $folder["web"].$webfile);
                }
            }
        }
    }
    
    public function writePage($content, $filename) {
        
        $handle = fopen( $this->webfolders["ROOT"].$filename, "w");
        fwrite($handle, self::versionizeContent($content));
        fclose($handle);
    }
    
    public function minify($files, $location, $type) {
        
        if($type !== "JS" && $type !== "CSS") throw new InvalidArgumentException;
        if(count($location) != 1 && count($location) != count($files)) throw new InvalidArgumentException;
        
        count($location) == 1 && count($files) > 1 ? $merge = true : $merge = false;
        $type == "JS" ? $devfolder = "../".SiteConfiguration::js_folder."/" : $devfolder = "../".SiteConfiguration::css_folder."/";
        $func = array( "JS" => "minifyJs", "CSS" => "minifyCss" );
        $aFiles = (array)$files;
        
        Minify_YUICompressor::$jarFile = SiteConfiguration::yui_compressor;
        Minify_YUICompressor::$tempDir = SiteConfiguration::yui_temp_dir;
        
        $path = "";
        if($merge) {
            $content = "";
            foreach($aFiles as $f) {
                $handle   = fopen($devfolder.$f, "r");
                if( !in_array($f, SiteConfiguration::$unversioned) ) {
                    $content .= self::versionizeContent( fread($handle, filesize($devfolder.$f)) );
                }
                else {
                    $content .= fread($handle, filesize($devfolder.$f));
                }
                fclose($handle);
            }
            $path = self::versionize($location);
            $content = Minify_YUICompressor::$func[$type]( $content );
            $handle = fopen( $this->webfolders[$type].$path, "w");
            fwrite($handle, $content);
            fclose($handle);
        }
        else {
            $path = array();
            for($i = 0; $i < count($aFiles); $i++) {
                
                $path[$i] = self::versionize($location[$i]);
                $handle_w = fopen( $this->webfolders[$type].$path[$i], "w");
                $handle_r = fopen($devfolder.$aFiles[$i], "r");
                $content  = Minify_YUICompressor::$func[$type]( self::versionizeContent( fread($handle_r, filesize($devfolder.$aFiles[$i])) ) );
                fwrite($handle_w, $content);
                fclose($handle_r);
                fclose($handle_w);
            }
        }
        
        return $path;
    }
    
    public function LoadBaseTemplate($docs) {
        
        $helper = new Util($docs);

        $handle = fopen(SiteConfiguration::base_template, "r");
        $base_template = Util::Obfuscate(fread($handle, filesize(SiteConfiguration::base_template)));
        $base_template = $helper->CreateLinks($base_template);
        fclose($handle);
        
        return $base_template;
    }
    
    public static function getVersion() {
        
        $handle = fopen(SiteConfiguration::version_file, "r");
        $version = fread($handle, filesize(SiteConfiguration::version_file));
        fclose($handle);
        
        return $version;
    }
    
    public static function incrVersion() {
        
        $handle = fopen(SiteConfiguration::version_file, "r+");
        $version = fread($handle, filesize(SiteConfiguration::version_file));
        
        ftruncate($handle, 0);
        rewind($handle);
        $new_version = $version + 1;
        fwrite($handle, (string)$new_version);
        
        fclose($handle);
    }
    
    public static function versionize($filename) {             
        return preg_replace("/(.*)(\..*)$/", "$1-".self::getVersion()."$2", $filename);   
    }
    
    public static function versionizeContent($content) {
        
        $pattern = SiteConfiguration::$versioned_files;
        array_walk($pattern, create_function('&$val', '$val = "/(\".*)(\.$val\")/";'));        
        $new_content = preg_replace($pattern, "$1-".self::getVersion()."$2", $content);
        
        $pattern2 = SiteConfiguration::$versioned_files;
        array_walk($pattern2, create_function('&$val', '$val = "/(\(.*)(\.$val\))/";'));
        
        $new_content = preg_replace($pattern2, "$1-".self::getVersion()."$2", $new_content);
        
        return $new_content; 
    }
}

?>