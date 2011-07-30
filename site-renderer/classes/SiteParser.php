<?php

class SiteParser {
    
    private $docs;
    
    public function GetDocuments() {
                
        $xml_path = SiteConfiguration::xml_folder . "/" . SiteConfiguration::site_xml;
        
        $xml  = new SimpleXMLElement($xml_path, null, true);
        $docs = array();
        foreach( $xml->children() as $node ) {
            $temp              = new Document();
            $temp->id          = trim( (string)$node["id"] );
            $temp->path        = trim( (string)$node["path"] );
            $temp->template    = trim( (string)$node->template["id"] );
            $temp->title       = trim( (string)$node->title );
            $temp->description = trim( (string)$node->description );
            $temp->img_small   = SiteBuilder::versionize( trim( (string)$node->head_image["small"] ) );
            $temp->img_large   = SiteBuilder::versionize( trim( (string)$node->head_image["large"] ) );
            
            trim( (string)$node["ssl"] ) == "on" ? $temp->protocol = "https" : $temp->protocol = "http";
            
            foreach($node->children() as $subnode) {
                if($subnode->getName() == "script") {
                    $src = trim($subnode["src"]);
                    if(!empty($src)) {
                        array_push($temp->scripts, $src);
                    }
                }
            }
            
            $file = SiteConfiguration::content_folder . "/" . (string)$node["content"];
            if(file_exists($file) && @filesize($file) > 0) {
                $handle        = fopen($file, "r");
                $temp->content = fread($handle, filesize($file));
                fclose($handle);
            }
            else {
                $temp->content = "";
            }
            
            $docs[$temp->id] = $temp;
        }
       
        // Replace links in content and obfuscate private information
        $helper = new Util($docs);
        foreach( $docs as $doc ) {
            $doc->content = $helper->CreateLinks($doc->content);
            $doc->content = Util::Obfuscate($doc->content);
        }
        
        $this->docs = $docs;
        return $docs;
    }
    
    public function GetTemplates() {
        
        $xml_path = SiteConfiguration::xml_folder . "/" . SiteConfiguration::template_xml;
        $xml      = new SimpleXMLElement($xml_path, null, true);
        
        $builder = new SiteBuilder;
        $builder->buildSite();
        
        $template = new Template();
        $template->setBaseTemplate($builder->LoadBaseTemplate($this->docs));
        $template->setStylesheet($builder->createStylesheet());
        
        $navigation = array();
        $templates  = array();
        foreach( $xml->children() as $node ) {
            $temp                           = $template->getInstance();
            $temp->id                       = trim( (string)$node["id"] );
            $temp->navigation_position      = trim( (string)$node->navigation["position"] );
            $temp->head_image_small         = trim( (string)$node->head_image["small"] );
            $temp->head_image_large         = trim( (string)$node->head_image["large"] );
            
            $startdoc       = trim( (string)$node->navigation["startdocument"] );
            $nav            = new NavigationItem;
            $nav->protocol  = $this->docs[$startdoc]->protocol;
            $nav->href      = $this->docs[$startdoc]->path;
            $nav->position  = trim( (string)$node->navigation["position"] );
            $nav->link_text = trim( (string)$node->navigation["linktext"] );
            array_push($navigation, $nav);
            
            $temp->menue = array();
            foreach( $node->menue->children() as $item ) {
                $temp2          = new MenueItem();
                $temp2->heading = trim( (string)$item["heading"] );
                $temp2->links   = array();
                
                foreach( $item->children() as $link) {
                    $temp3       = new Link();
                    $temp3->text = trim( (string)$link );
                    
                    foreach( $this->docs as $page ) {
                        if($page->id == (string)$link["document"]) {
                            $temp3->href     = $page->path;
                            $temp3->protocol = $page->protocol;
                        }
                    }
                    
                    array_push($temp2->links, $temp3);
                }
                
                array_push($temp->menue, $temp2);
            }
            
            $templates[$temp->id] = $temp;
        }
        
        // setup navigation
        foreach($templates as $item) {
            $item->setNavigation($navigation);
        }
  
        return $templates;
    }
}

?>