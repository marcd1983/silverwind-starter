<?php

use SilverStripe\Core\Environment;
use SilverStripe\ORM\Search\FulltextSearchable;
use SilverStripe\TinyMCE\TinyMCEConfig;

// The business's timezone, for every date and time the site shows or stores
// (store hours "Open now", ribbon schedules, LastEdited...). Set here rather
// than in php.ini so it holds on any server; override with SS_TIMEZONE.
date_default_timezone_set(Environment::getEnv('SS_TIMEZONE') ?: 'America/Chicago');

// Enables SiteTree/File MySQL FULLTEXT search and wires up $SearchForm on
// ContentController -- must run here rather than via YAML, since it also
// sets the static searchable-classes list SearchForm reads at runtime.
FulltextSearchable::enable();


$cfg = TinyMCEConfig::get('cms');

// $plugins = [
//     // content & editing
//     'advlist', 'lists', 'autolink', 'autosave', 'autoresize', 'code', 'codesample',
//     'charmap', 'directionality', 'emoticons', 'help', 'image', 'importcss',
//     'insertdatetime', 'media', 'nonbreaking', 'pagebreak', 'preview',
//     'quickbars', 'save', 'searchreplace', 'table', 'visualblocks', 'visualchars', 'wordcount',
//     'anchor', 'fullscreen',
// ];

// $cfg->enablePlugins($plugins);

// Menubar + toolbars (you can tweak order)
// $cfg->setOption('menubar', 'file edit view insert format tools table help');

// One group per row: separators ('|') split the toolbar into button groups
$cfg->setButtonsForLine(1, [
    'bold', 'italic', 'underline', 'strikethrough', '|',
    'forecolor', 'backcolor', '|',
    'bullist', 'numlist', 'outdent', 'indent', '|',
    'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|',
]);

$cfg->setButtonsForLine(2, [
    'blocks styles', 'table', 'sslink', 'unlink', 'ssmedia', 'ssembed', '|',
    'emoticons', 'pagebreak', 'nonbreaking', '|',
    'code', 'preview', 'removeformat', 'visualblocks', 'visualchars', 'searchreplace', 'fullscreen', 'help',
]);

// Quickbars (contextual toolbars on selection / at caret)
$cfg->setOption('quickbars_selection_toolbar', 'bold italic underline | forecolor backcolor | link | removeformat');
$cfg->setOption('quickbars_insert_toolbar', 'quicktable image media');

// Useful editor options
$cfg->setOption('image_caption', true);
$cfg->setOption('image_advtab', true);
$cfg->setOption('browser_spellcheck', true);
$cfg->setOption('branding', false);
$cfg->setOption('statusbar', true);
$cfg->setOption('elementpath', true);
$cfg->setOption('resize', true);

// Keep the HTML you want (allow spans/mark/classes, etc.)
$cfg->setOption('extended_valid_elements', 'div[class|style],span[class|style],mark[class|style],a[class|href|target|rel],p[class],h1[class],h2[class],h3[class],img[id|loading|src|alt|title|width|height|class],table[*],tr[*],td[*],th[*]');

// Content CSS + "Styles" menu use the silverwind theme. Its classes are
// safelisted in the theme's src/css/theme.css (editor content lives in the
// database, so Tailwind can't discover them by scanning templates).
$cfg->setContentCSS(['/_resources/themes/silverwind/dist/editor.css']);

$cfg->setOption('style_formats', [
    [
        'title' => 'Text color',
        'items' => [
            ['title' => 'Primary', 'inline' => 'span', 'classes' => 'text-primary', 'exact' => true],
            ['title' => 'Accent',  'inline' => 'span', 'classes' => 'text-accent', 'exact' => true],
            ['title' => 'Muted',   'inline' => 'span', 'classes' => 'opacity-70', 'exact' => true],
        ],
    ],
    [
        'title' => 'Highlight',
        'items' => [
            ['title' => 'Yellow', 'inline' => 'mark', 'classes' => 'bg-warning/40', 'exact' => true],
            ['title' => 'Blue',   'inline' => 'mark', 'classes' => 'bg-info/30',    'exact' => true],
        ],
    ],
    [
        'title' => 'Buttons',
        'items' => [
            ['title' => 'Primary Button',   'selector' => 'a', 'classes' => 'btn btn-primary'],
            ['title' => 'Secondary Button', 'selector' => 'a', 'classes' => 'btn btn-secondary'],
            ['title' => 'Outline Button',   'selector' => 'a', 'classes' => 'btn btn-outline'],
        ],
    ],
]);

// Optional: fixed color palette for forecolor/backcolor
$cfg->setOption('color_map', [
    '000000','Black','ffffff','White',
    'e53935','Red','fb8c00','Orange','fdd835','Yellow',
    '43a047','Green','1e88e5','Blue','8e24aa','Purple',
]);
