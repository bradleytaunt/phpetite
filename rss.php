<?php 
    include '_system.php';
?>
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

 <title><?php echo $site_title; ?></title>
 <link href="<?php echo $site_url; ?>/atom.xml" rel="self"/>
 <link href="<?php echo $site_url; ?>"/>
 <id><?php echo $site_url; ?></id>
 <author>
   <name><?php echo $site_author; ?></name>
   <email><?php echo $site_email; ?></email>
 </author>

 <?php echo $rss_items; ?>

</feed>