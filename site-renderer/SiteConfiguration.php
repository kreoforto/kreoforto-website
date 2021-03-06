<?php

class SiteConfiguration {

    const server_name              = "www.kreoforto.de";

    public static $stylesheets     = array( "colour.css", "structure.css" );

    const version_file             = "VERSION.txt";
    const conf_file                = "sr.conf";
    
    public static $versioned_files = array("gif", "jpg", "jpeg", "pdf", "png");
    public static $unversioned     = array("robots.txt", "sitemap.xml", "favicon.ico", "jquery-1.6.min.js");

    const link_width               = 19;
    const link_separation_width    = 1;
    const small_header_width       = 262;
    const horizontal_padding_right = 25;
    const horizontal_padding_left  = 6;
    const outer_content_width      = 984;
    const bottom_border            = 2;
    
    const image_folder             = "images";
    const css_folder               = "css";
    const js_folder                = "js";
    const php_folder               = "php";
    const static_folder            = "static";
    
    const content_folder           = "../content";
    const xml_folder               = "data";
    //const web_folder               = "../page";
    const base_template            = "base-template.html";
    
    const site_xml                 = "site.xml";
    const template_xml             = "layout.xml";
    
    const outer_menue_class        = "outer_nav";
    const inner_menue_class        = "inner_nav";
    
    const css_file                 = "style.css";
    const js_file_format           = "kreoforto-%s.js";
    
    public static $placeholder     = array( "%DESCRIPTION%", "%TITLE%", "%CONTENT%", "%LEFT_WIDTH%", "%RIGHT_WIDTH%", "%SCRIPTS%", "%CSS%", "%MOD_DATE%" );
    
    const script_format            = "<script type=\"text/javascript\" src=\"%s\"></script>";
    
    const nonseparated_link_format = "<div class=\"link_bg\"><div class=\"link %s\"><a href=\"%s\" target=\"_self\"><span>%s</span></a></div></div>";
    const separated_link_format    = "<div class=\"link_bg link_separate\"><div class=\"link %s\"><a href=\"%s\" target=\"_self\"><span>%s</span></a></div></div>";
    
    const nav_class_format         = "navigation-%s";
    const nav_active_class_format  = "navigation-%s-active";
    
    const inner_content            = "<div id=\"inner_content\">
                                        <div id=\"subheader\">
                                            <div id=\"subheader_small\"><img src=\"%s\" alt=\"KREOforto - Agentur für Werbung und Webdesign\"></div>
                                            <div id=\"subheader_large\"><img src=\"%s\" alt=\"KREOforto - Agentur für Print- und Onlinemarketing\"></div>
                                        <!-- end subheader --></div>
                                        <div id=\"content\">
                                            <div id=\"menue\">%s<!-- end menue --></div>
                                            <div id=\"text\">%s<!-- end text --></div>
                                            <div class=\"clear\"></div> 
                                        <!-- end content --></div>
                                      <!-- end inner_content --></div>";    
}

?>
