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
include($_SERVER['DOCUMENT_ROOT'].'_phpetite/_config.php');
include($_SERVER['DOCUMENT_ROOT'].'_phpetite/system.php');


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