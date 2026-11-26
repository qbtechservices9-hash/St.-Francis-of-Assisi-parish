<?php

declare(strict_types=1);

require_once get_template_directory() . '/inc/theme-helpers.php';

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'st-francis-parish'),
        'footer' => __('Footer Menu', 'st-francis-parish'),
    ]);
});

add_action('wp_enqueue_scripts', function (): void {
    wp_enqueue_style(
        'st-francis-parish-theme',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'st-francis-parish-site',
        get_template_directory_uri() . '/assets/css/site.css',
        ['st-francis-parish-theme'],
        filemtime(get_template_directory() . '/assets/css/site.css')
    );

    wp_enqueue_script(
        'st-francis-parish-site',
        get_template_directory_uri() . '/assets/js/site.js',
        [],
        filemtime(get_template_directory() . '/assets/js/site.js'),
        true
    );
});
