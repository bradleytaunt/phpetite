<?php
// PHPetite v.1.0 (Based off the excellent work of Portable PHP)
// Render each of the Markdown files from a folder in a <section>, with date-and-title as #id.

// !!!
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// DO NOT TOUCH anything below here unless you know what you're doing.
// Most basic use cases won't need to change anything here.
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!!

// Include custom site configurations
include('_config.php');

// Dependencies
include('dependencies/Parsedown.php');
include('dependencies/ParsedownExtra.php');
include('dependencies/ParsedownExtraPlugin.php');

// Set variable blank defaults
$toc = '';
$posts = '';
$pages = '';
$pages_footer = '';
$rss_items = '';
$site_info = '';

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
foreach (new DirectoryIterator(__DIR__.'/../content/') as $file) {
  if ( $file->getType() == 'file' && strpos($file->getFilename(),'.md') ) {
    $files[] = $file->getFilename();
  }
}
rsort($files);

foreach ($files as $file) {

  $filename_no_ext = substr($file, 0, strrpos($file, "."));
  $file_path = __DIR__.'/../content/'.$file;
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
  $posts .= '<section tabindex="0" role="document" aria-label="'.$post_title.'" id="'.$post_slug.'"><time class="posted-on" datetime="'.substr($filename_no_ext, 0, 10).'">'.substr($filename_no_ext, 0, 10).'</time>'.$parsedown->text(file_get_contents($file_path)).'</section>';

  $rss_items .= '
  <entry>
    <title>'.trim($post_title, " \t\n\r").'</title>
    <link href="'.$site_url.'#'.$post_slug.'"/>
    <updated>'.substr($filename_no_ext, 0, 10).'T00:00:00+00:00</updated>
    <id>'.$site_url.'/#'.$post_slug.'</id>
    <content type="html">'.htmlspecialchars($parsedown->text(file_get_contents($file_path)), ENT_XML1, 'UTF-8').'</content>
  </entry>
  ';

}

$files_pages = [];
foreach (new DirectoryIterator(__DIR__.'/../content/_pages/') as $file_page) {
  if ( $file_page->getType() == 'file' && strpos($file_page->getFilename(),'.md') ) {
    $files_pages[] = $file_page->getFilename();
  }
}
rsort($files_pages);

// Footer page link arrays
$pages_footer_array = [];

foreach ($files_pages as $file_page) {

  $filename_no_ext_page = substr($file_page, 0, strrpos($file_page, "."));
  $file_path_page = __DIR__.'/../content/_pages/'.$file_page;
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

  if ($page_slug != 'home-content') {
    $pages .= '<section tabindex="0" role="document" aria-label="'.$page_title.'" id="'.$page_slug.'">'.$parsedown->text(file_get_contents($file_path_page)).'</section>';
  }
  $pages_footer .='<a class="'.$page_slug.'" href="#'.$page_slug.'">'.trim($page_title, " \t\n\r").'</a><span class="divider">/</span>';
  $pages_footer_array[] = $pages_footer;

}
sort($pages_footer_array);

// $site_info takes all page content from /content/_pages/home-content.md
$site_info = '<div class="site-info">' . $parsedown->text(file_get_contents('content/_pages/home-content.md')) .'</div>';

$cssFiles = array(
  "style.css"
);

$updated_date = date("F j, Y");
$base_64_favicon = base64_encode(file_get_contents($site_icon));

$buffer = "";
foreach ($cssFiles as $cssFile) {
  $buffer .= file_get_contents($cssFile);
}
$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
$buffer = str_replace(': ', ':', $buffer);
$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

// Decide whether or not to display the site info on homepage
if ($site_info != '') {
  $show_site_info = $site_info . '<hr>';
} else {
  $show_site_info = '';
}

$html = <<<EOD
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>$site_title</title>
  <meta name="description" content="$site_desc">
  <meta name="color-scheme" content="dark light">
  <link rel="icon" href="data:image/png;base64,$base_64_favicon">
  <!-- og tags -->
  <meta property="og:title" content="$site_title">
  <meta property="og:description" content="$site_desc">
  <!-- other -->
  <meta name="twitter:card" content="summary">
  <style>$buffer</style>
</head>
<body>
  <header>
    <h1>
      <a href="#home">$site_title</a>
    </h1>
  </header>
  <main>
    $posts
    $pages
    <section tabindex="0" role="document" aria-label="Home" id="home">
      $show_site_info
      <nav>
        <ul class="toc">
          $toc
        </ul>
      </nav>
    </section>
  </main>
  <footer>
    <small>Last updated on $updated_date</small>
    <div class="footer-links">
        <small>$pages_footer</small>
    </div>
  </footer>
</body>
</html>
EOD;

if ($images_to_base64 == true) {
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
  libxml_clear_errors();
  $post_images = $dom->getElementsByTagName('img');
  foreach ($post_images as $image) {
    $src = $image->getAttribute('src');
    $type = pathinfo($src, PATHINFO_EXTENSION);
    $data = file_get_contents($src);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $image->setAttribute("src", $base64); 
  }

  $html = $dom->saveHTML();
  echo $html;
} else {
  echo $html;
}

?>