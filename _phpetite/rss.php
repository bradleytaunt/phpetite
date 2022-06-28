<?php
    // Include custom site configurations
include ($_SERVER['DOCUMENT_ROOT'].'_phpetite/_config.php');
    include ($_SERVER['DOCUMENT_ROOT'].'_phpetite/system.php');

    echo '<?xml version="1.0" encoding="utf-8"?>
    <feed xmlns="http://www.w3.org/2005/Atom">
    <title>'.$site_title.'</title>
    <link href="'.$site_url.'/atom.xml" rel="self"/>
    <link href="'.$site_url.'"/>
    <updated>'.date("Y-m-d\TH:i:sP").'</updated>
    <id>'.$site_url.'/</id>
    <author>
      <name>'.$site_author.'</name>
      <email>'.$site_email.'</email>
    </author>'.$rss_items.'</feed>';
?>