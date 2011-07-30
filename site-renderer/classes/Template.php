<?php

class Template {

    public $id;
    public $navigation_position;
    public $head_image_small;
    public $head_image_large;
    public $menue;
  
    private $navigation;
    private $num_nav_items;
    private $stylesheet;
    private $base_template;
    
    public function setNavigation($navigation) {
        $this->navigation    = $navigation;
        $this->num_nav_items = count($navigation);
    }
    
    public function setStylesheet($stylesheet) {
        $this->stylesheet = $stylesheet;
    }
    
    public function setBaseTemplate($base_template) {
        $this->base_template = $base_template;
    }
    
    public function __construct() { }
    
    public function getInstance() {
        
        $inst = new self();
        $inst->setStylesheet($this->stylesheet);
        $inst->setBaseTemplate($this->base_template);
        
        return $inst;
    }
    
    public function Render($document) {
        
        $pattern = SiteConfiguration::$placeholder;
        array_walk($pattern, create_function('&$val', '$val = "/$val/";')); 
        
        // create main navigation and content
        $content = ""; $head_image_small = ""; $head_image_large = "";
        $image_folder = SiteConfiguration::image_folder . "/";
        !empty($document->img_small) ? $head_image_small = $document->img_small : $head_image_small = $this->head_image_small;
        !empty($document->img_large) ? $head_image_large = $document->img_large : $head_image_large = $this->head_image_large;
        
         foreach( $this->navigation as $item ) {
            
            SiteConfiguration::use_absolute_uri ? $href = $item->protocol."://".SiteConfiguration::server_name."/".$item->href : $href = $item->href;
            
            if($item->position == $this->navigation_position) {
                $nav_class = sprintf(SiteConfiguration::nav_active_class_format, $item->position);
                $content  .= sprintf( SiteConfiguration::nonseparated_link_format, $nav_class, $href, $item->link_text );
                $content  .= sprintf( SiteConfiguration::inner_content, $image_folder.$head_image_small, $image_folder.$head_image_large, $this->CreateMenue(), $document->content );
            }
            else if ($item->position == $this->num_nav_items) {
                $nav_class = sprintf(SiteConfiguration::nav_class_format, $item->position);
                $content  .= sprintf( SiteConfiguration::nonseparated_link_format, $nav_class, $href, $item->link_text );
            }
            else {
                $nav_class = sprintf(SiteConfiguration::nav_class_format, $item->position);
                $content  .= sprintf( SiteConfiguration::separated_link_format, $nav_class, $href, $item->link_text );
            }
        }
        
        // calculate footer width
        $width = $this->CalculateFooterWidth();
        
        // include javascript
        if(!empty($document->scripts)) {
                        
            $builder = new SiteBuilder;
            $scripts = sprintf(SiteConfiguration::js_file_format, $document->id);
            //SiteConfiguration::use_absolute_uri ? $script_path = $document->protocol."://".SiteConfiguration::server_name."/".SiteConfiguration::js_folder . "/" . $scripts : $script_path = SiteConfiguration::js_folder . "/" . $scripts;
            $script_path = SiteConfiguration::js_folder . "/" . $builder->minify($document->scripts, $scripts, "JS");            
            $scripts = sprintf(SiteConfiguration::script_format, $script_path);
        }
        
        // create page
        $replacement = array( $document->description, $document->title, $content, $width->left, $width->right, $scripts, $this->stylesheet);
        
        // write page to web directory
        $builder = new SiteBuilder;
        $builder->writePage( preg_replace($pattern, $replacement, $this->base_template), $document->path );
    }
    
    private function CalculateFooterWidth() {
        
        $width = new stdClass();
        $width->left  = SiteConfiguration::small_header_width + ($this->navigation_position - 1) * SiteConfiguration::link_separation_width + $this->navigation_position * SiteConfiguration::link_width - SiteConfiguration::horizontal_padding_left;
        $width->right = SiteConfiguration::outer_content_width - $width->left - SiteConfiguration::horizontal_padding_right - SiteConfiguration::horizontal_padding_left - SiteConfiguration::bottom_border;
        
        return $width;
    }
    
    private function CreateMenue() {
                                
        $outer_menue_format = "<ul class=\"" . SiteConfiguration::outer_menue_class . "\">%s</ul>";
        $inner_menue_format = "<ul class=\"" . SiteConfiguration::inner_menue_class . "\">%s</ul>";
        $outer_link_format  = "<li>%s</li>";
        $inner_link_format  = "<li><a href=\"%s\">%s</a></li>";
        
        $outer_links = "";
        foreach( $this->menue as $item ) {
            
            if( !empty($item->heading) ) {
                $outer_links .= sprintf( $outer_link_format, $item->heading );
                
                $inner_links = "";
                foreach( $item->links as $link ) {
                    SiteConfiguration::use_absolute_uri ? $href = $link->protocol."://".SiteConfiguration::server_name."/".$link->href : $href = $link->href;
                    $inner_links .= sprintf( $inner_link_format, $href, $link->text );
                }
                $inner_menue  = sprintf( $inner_menue_format, $inner_links );
                $outer_links .= sprintf( $outer_link_format, $inner_menue );
                $outer_links .= sprintf( $outer_link_format, "&nbsp;" );
            }
        }
        
        !empty($outer_links) ? $menue = sprintf( $outer_menue_format, $outer_links ) : $menue = "";
        return $menue;
    }
}

?>