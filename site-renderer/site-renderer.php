<?php

ob_implicit_flush();
echo "Rendering website...";

require_once("SiteConfiguration.php");
require_once("classes/Util.php");
require_once("classes/SiteBuilder.php");
require_once("classes/Document.php");
require_once("classes/NavigationItem.php");
require_once("classes/MenueItem.php");
require_once("classes/Link.php");
require_once("classes/Template.php");
require_once("classes/SiteParser.php");

echo "...";
$data      = new SiteParser;
$docs      = $data->GetDocuments();
$templates = $data->GetTemplates();

// render pages and save them in the web root
foreach( $docs as $page ) {
    echo "...";
    $templates[$page->template]->Render($page);
}

// increment version number
SiteBuilder::incrVersion();

echo "...done";