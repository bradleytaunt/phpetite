<?php

// !!!
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// DO NOT TOUCH anything below here unless you know what you're doing. 
// Most basic use cases won't need to change anything here.
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!!


// Dependencies
include('dependencies/Parsedown.php');
include('dependencies/ParsedownExtra.php');
include('dependencies/ParsedownExtraPlugin.php');

// Set variable blank defaults
$toc = '';
$posts = '';
$pages = '';
$pages_footer = '';

function create_slug($string){
  $string = strtolower($string);
  $string = strip_tags($string);
  $string = stripslashes($string);
  $string = html_entity_decode($string);
  $string = str_replace('\'', '', $string);
  $string = trim(preg_replace('/[^a-z0-9]+/', '-', $string), '-');
  return $string;
}

$files = [];
foreach (new DirectoryIterator(__DIR__.'/content/') as $file) {
  if ( $file->getType() == 'file' && strpos($file->getFilename(),'.md') ) {
    $files[] = $file->getFilename();
  }
}
rsort($files);

foreach ($files as $file) {

  $filename_no_ext = substr($file, 0, strrpos($file, "."));    
  $file_path = __DIR__.'/content/'.$file;
  $file = fopen($file_path, 'r');
  $post_title = trim(fgets($file),'#');
  $post_slug = create_slug($filename_no_ext.$post_title);
  fclose($file);
    
  $parsedown = new ParsedownExtraPlugin();
  // Allow single line breaks
  $parsedown->setBreaksEnabled(true);
  // Add image dimensions, lazy loading and figures
  $parsedown->imageAttributes = ['width', 'height'];
  $parsedown->imageAttributes = ['loading' => 'lazy'];
  $parsedown->figuresEnabled = true;
  // Remove the id and #links on footnotes
  $parsedown->footnoteLinkAttributes = function() {return ['href' => '#'];};
  $parsedown->footnoteReferenceAttributes = function() {return ['id' => null];};
  $parsedown->footnoteBackLinkAttributes = function() {return ['href' => '#'];};
  $parsedown->footnoteBackReferenceAttributes = function() {return ['id' => null];};

  $toc .= '<li><a href="#'.$post_slug.'"><span>'.$post_title.'</span></a> <time datetime="'.substr($filename_no_ext, 0, 10).'">'.substr($filename_no_ext, 0, 10).'</time></li>';
  $posts .= '<section tabindex="0" role="document" aria-label="'.$post_title.'" id="'.$post_slug.'">'.$parsedown->text(file_get_contents($file_path)).'</section>';
  
}

$files_pages = [];
foreach (new DirectoryIterator(__DIR__.'/content/_pages/') as $file_page) {
  if ( $file_page->getType() == 'file' && strpos($file_page->getFilename(),'.md') ) {
    $files_pages[] = $file_page->getFilename();
  }
}
rsort($files_pages);

foreach ($files_pages as $file_page) {

  $filename_no_ext_page = substr($file_page, 0, strrpos($file_page, "."));    
  $file_path_page = __DIR__.'/content/_pages/'.$file_page;
  $file_page = fopen($file_path_page, 'r');
  $page_title = trim(fgets($file_page),'# ');
  $page_slug = create_slug($filename_no_ext_page);
  fclose($file_page);
    
  $parsedown = new ParsedownExtraPlugin();
  // Allow single line breaks
  $parsedown->setBreaksEnabled(true);
  // Add image dimensions, lazy loading and figures
  $parsedown->imageAttributes = ['width', 'height'];
  $parsedown->imageAttributes = ['loading' => 'lazy'];
  $parsedown->figuresEnabled = true;
  // Remove the id and #links on footnotes
  $parsedown->footnoteLinkAttributes = function() {return ['href' => '#'];};
  $parsedown->footnoteReferenceAttributes = function() {return ['id' => null];};
  $parsedown->footnoteBackLinkAttributes = function() {return ['href' => '#'];};
  $parsedown->footnoteBackReferenceAttributes = function() {return ['id' => null];};

  $pages .= '<section tabindex="0" role="document" aria-label="'.$page_title.'" id="'.$page_slug.'">'.$parsedown->text(file_get_contents($file_path_page)).'</section>';
  $pages_footer .='<a class="'.$page_slug.'" href="#'.$page_slug.'">'.$page_title.'</a><span class="divider">/</span>';
  
}

?>