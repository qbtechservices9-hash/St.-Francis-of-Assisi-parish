<?php

declare(strict_types=1);

function stf_theme_site_options(): array
{
    $defaults = [
        'church_name' => 'St. Francis Of Assisi Catholic Church',
        'church_location' => 'Awoshie Anyaa',
        'church_city' => 'Accra, Ghana',
        'contact_phone' => '+233 XX XXX XXXX',
        'contact_email' => 'info@stfrancisawoshie.org',
        'service_sunday' => 'Sunday Mass: 7:00 AM & 9:30 AM',
        'service_wednesday' => 'Monday, Wednesday & Friday Mass: 6:00 AM',
        'service_friday' => 'Sunday Adoration: 5:00 PM',
        'social_facebook' => '#',
        'social_instagram' => '#',
        'social_youtube' => '#',
        'social_tiktok' => '#',
        'social_twitter' => '#',
        'logo_url' => '',
        'church_map_destination' => 'St. Francis Of Assisi Catholic Church, Awoshie Anyaa, Accra',
    ];

    $saved = get_option('stf_site_options', []);

    return wp_parse_args(is_array($saved) ? $saved : [], $defaults);
}

function stf_theme_option(string $key, mixed $default = null): mixed
{
    $options = stf_theme_site_options();

    return $options[$key] ?? $default;
}

function stf_theme_logo_url(): string
{
    $custom = (string) stf_theme_option('logo_url', '');

    if ($custom !== '') {
        return esc_url($custom);
    }

    return esc_url(get_template_directory_uri() . '/assets/images/logo.jpg');
}

function stf_theme_page_id(string|int|null $reference = null): int
{
    if (is_numeric($reference)) {
        return (int) $reference;
    }

    if (is_string($reference) && $reference !== '') {
        $page = get_page_by_path($reference);

        return $page instanceof WP_Post ? (int) $page->ID : 0;
    }

    return get_queried_object_id();
}

function stf_theme_field(string|int|null $reference, string $field, mixed $default = null): mixed
{
    $page_id = stf_theme_page_id($reference);

    if ($page_id && function_exists('get_field')) {
        $value = get_field($field, $page_id);
        if ($value !== null && $value !== false && $value !== '') {
            return $value;
        }
    }

    if ($page_id) {
        $meta = get_post_meta($page_id, $field, true);
        if ($meta !== '' && $meta !== null) {
            return $meta;
        }
    }

    return $default;
}

function stf_theme_archive_url(string $post_type, string $fallback): string
{
    $archive = get_post_type_archive_link($post_type);

    if (is_string($archive) && $archive !== '') {
        return $archive;
    }

    return home_url($fallback);
}

function stf_theme_page_url(string $slug, string $fallback = '/'): string
{
    $page = get_page_by_path(trim($slug, '/'));

    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }

    return home_url($fallback);
}

function stf_theme_post_meta(int $post_id, string $key, mixed $default = ''): mixed
{
    $value = get_post_meta($post_id, $key, true);

    if ($value === '' || $value === null) {
        return $default;
    }

    return $value;
}

function stf_theme_image_url(int $post_id, string $fallback = ''): string
{
    if (has_post_thumbnail($post_id)) {
        return (string) get_the_post_thumbnail_url($post_id, 'full');
    }

    foreach (['image_url', 'thumbnail_url', 'media_url'] as $key) {
        $value = (string) stf_theme_post_meta($post_id, $key, '');
        if ($value !== '') {
            return $value;
        }
    }

    return $fallback;
}

function stf_theme_approved_meta_query(string $key = 'approval_status'): array
{
    return [
        'relation' => 'OR',
        [
            'key' => $key,
            'compare' => 'NOT EXISTS',
        ],
        [
            'key' => $key,
            'value' => 'approved',
            'compare' => '=',
        ],
    ];
}

function stf_theme_posts(string $post_type, array $args = []): WP_Query
{
    $defaults = [
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'ignore_sticky_posts' => true,
    ];

    return new WP_Query(wp_parse_args($args, $defaults));
}

function stf_theme_nav_ministries(int $limit = 8): array
{
    $query = stf_theme_posts('ministry', [
        'posts_per_page' => $limit,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'meta_query' => [
            [
                'key' => 'is_active',
                'value' => ['1', 1, true, 'true', 'yes'],
                'compare' => 'IN',
            ],
        ],
    ]);

    return $query->posts;
}

function stf_theme_event_category_choices(): array
{
    $terms = get_terms([
        'taxonomy' => 'event_category',
        'hide_empty' => true,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return ['Mass', 'Prayer', 'Fellowship', 'Formation', 'Music', 'Service', 'Sacraments', 'Special'];
    }

    return wp_list_pluck($terms, 'name');
}

function stf_theme_gallery_category_choices(): array
{
    $terms = get_terms([
        'taxonomy' => 'gallery_category',
        'hide_empty' => true,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
        return ['worship', 'events', 'sacraments', 'youth', 'outreach', 'church'];
    }

    return wp_list_pluck($terms, 'name');
}
