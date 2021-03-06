# PHPetite

PHPetite (/p/h/pəˈtēt/) is a single file, static blog generated from PHP. Based off the very minimal and awesome <a target="_blank" href="https://github.com/cadars/portable-php">portable-php</a>.

## Key Features

- Entire blog is rendered in a single HTML file
- Inline, compressed CSS
- All images converted into base64 encoding
- Minimal requirements / no heavy build tools

## Core Principles

The basic idea behind PHPetite is to keep the concept and workflow as simple as possible. Therefore, this project will try it's best to avoid bloat and feature creep. More elaborate and feature-rich blogging platforms should be used if your needs are not met with PHPetite.

## Requirements

1. `PHP 7.3` or higher
2. If using Linux, you will require the following packages in order to convert your images to base64 encoding:
    - PHP XML -> `sudo apt-get install php-xml`
    - PHP mbstring -> `sudo apt-get install php-mbstring`

That's really it!

## General Usage

You can find most basic explanations and details on working with the project at [phpetite.org](https://phpetite.org) if you prefer.

### Generating the Blog

Get [PHPetite](https://github.com/bradleytaunt/phpetite "PHPetite at Github") in order to convert a collection of Markdown files into a single HTML file with inline CSS.

1. Write posts in `/content`
2. (Optional) include any images under the `/content/img/` directory
3. From the command-line run:

```.bash
bash build.sh
```

This will generate both the single file HTML page, along with an `atom.xml` file for the use of an optional RSS feed.

These two files are output into the `_site` directory.

---

### Structuring Blog Posts

Blog posts should be placed into the `/content` directory and be named based only on their post date. See an example here:

```.markdown
2048-01-01.md
```

PHPetite will create a `target` by appending the page title inside the article to the file's date name. So a markdown file with the following content:

```.markdown
# Bladerunner Rocks

Bladerunner is amazing because blah blah blah...
```

will render out the `target` link as:

```.markdown
example.com/#2048-01-01-bladerunner-rocks
```

---

### Adding Custom Pages

To add your own custom pages, simply create a Markdown file under the `content/_pages` directory. PHPetite will take it from there!

#### Some Cavets

Any page you create will be automatically added to the `footer` navigation section. If you wish to hide individual pages from showing in the `footer`, do so via CSS:

```.css
footer a.slug-name-of-your-page {
    display: none;
}
```

If you want to remove the `footer` navigation altogether, add the following to your `style.css` file:

```.css
footer .footer-links {
    display: none;
}
```

---

## TO DO

- Proper accessibility audit
- Allow custom fonts to be set as Base64 strings
- ~~Set images as inline Base64 strings~~
- ~~Basic RSS feed~~
- ~~Automatically add new pages to footer nav~~
- ~~Compress inline CSS~~

