<?php

/**
 * Plugin Name: St. Francis Core
 * Description: Core content types, settings, and contact handling for the St. Francis parish site.
 * Version: 0.1.0
 * Author: OpenAI Codex
 */

declare(strict_types=1);

add_action('init', function (): void {
    register_post_type('ministry', [
        'labels' => ['name' => 'Ministries', 'singular_name' => 'Ministry'],
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'ministries'],
    ]);

    register_post_type('event', [
        'labels' => ['name' => 'Events', 'singular_name' => 'Event'],
        'public' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'events'],
    ]);

    register_post_type('sermon', [
        'labels' => ['name' => 'Sermons', 'singular_name' => 'Sermon'],
        'public' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'sermons'],
    ]);

    register_post_type('gallery_item', [
        'labels' => ['name' => 'Gallery', 'singular_name' => 'Gallery Item'],
        'public' => true,
        'menu_icon' => 'dashicons-format-gallery',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'gallery'],
    ]);

    register_post_type('society', [
        'labels' => ['name' => 'Societies', 'singular_name' => 'Society'],
        'public' => true,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'societies'],
    ]);

    register_post_type('friar', [
        'labels' => ['name' => 'Friars', 'singular_name' => 'Friar'],
        'public' => true,
        'menu_icon' => 'dashicons-admin-users',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'friars'],
    ]);

    register_post_type('chairperson', [
        'labels' => ['name' => 'Chairpersons', 'singular_name' => 'Chairperson'],
        'public' => true,
        'menu_icon' => 'dashicons-awards',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'chairpersons'],
    ]);

    register_post_type('service_schedule', [
        'labels' => ['name' => 'Service Schedules', 'singular_name' => 'Service Schedule'],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-clock',
        'supports' => ['title', 'page-attributes'],
    ]);

    register_post_type('daily_verse', [
        'labels' => ['name' => 'Daily Verses', 'singular_name' => 'Daily Verse'],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => ['title', 'editor', 'page-attributes'],
    ]);

    register_post_type('contact_message', [
        'labels' => ['name' => 'Contact Messages', 'singular_name' => 'Contact Message'],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-email',
        'supports' => ['title', 'editor'],
    ]);

    register_taxonomy('event_category', ['event'], [
        'labels' => ['name' => 'Event Categories', 'singular_name' => 'Event Category'],
        'public' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'event-category'],
    ]);

    register_taxonomy('gallery_category', ['gallery_item'], [
        'labels' => ['name' => 'Gallery Categories', 'singular_name' => 'Gallery Category'],
        'public' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'gallery-category'],
    ]);
});

add_action('init', function (): void {
    $meta_keys = [
        'ministry' => ['contact_email', 'contact_phone', 'meeting_time', 'is_active', 'image_url'],
        'event' => ['event_date', 'event_time', 'location', 'category', 'is_featured', 'approval_status', 'image_url'],
        'sermon' => ['preacher', 'video_url', 'audio_url', 'notes_url', 'summary', 'verses'],
        'gallery_item' => ['media_url', 'thumbnail_url', 'category', 'type', 'approval_status'],
        'service_schedule' => ['day_of_week', 'time', 'location'],
        'friar' => ['service_period'],
        'chairperson' => ['service_period'],
    ];

    foreach ($meta_keys as $post_type => $keys) {
        foreach ($keys as $key) {
            register_post_meta($post_type, $key, [
                'type' => 'string',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => function (): bool {
                    return current_user_can('edit_posts');
                },
            ]);
        }
    }
});

add_action('admin_menu', function (): void {
    add_menu_page(
        'Parish Tools',
        'Parish Tools',
        'manage_options',
        'stf-tools',
        'stf_render_tools_landing',
        'dashicons-admin-generic',
        58
    );

    add_submenu_page(
        'stf-tools',
        'Seed Parish Content',
        'Seed Parish Content',
        'manage_options',
        'stf-seed',
        'stf_render_seed_page'
    );

    add_submenu_page(
        'stf-tools',
        'Import Parish CSVs',
        'Import Parish CSVs',
        'manage_options',
        'stf-import',
        'stf_render_import_page'
    );

    add_options_page(
        'Parish Settings',
        'Parish Settings',
        'manage_options',
        'stf-settings',
        'stf_render_settings_page'
    );

    add_management_page(
        'Seed Parish Content',
        'Seed Parish Content',
        'manage_options',
        'stf-seed',
        'stf_render_seed_page'
    );

    add_management_page(
        'Import Parish CSVs',
        'Import Parish CSVs',
        'manage_options',
        'stf-import',
        'stf_render_import_page'
    );
});

add_action('admin_init', function (): void {
    register_setting('stf_settings', 'stf_site_options', [
        'type' => 'array',
        'sanitize_callback' => 'stf_sanitize_site_options',
        'default' => [],
    ]);

    add_settings_section('stf_settings_main', 'Site Details', '__return_false', 'stf-settings');

    $fields = [
        'church_name' => 'Church Name',
        'church_location' => 'Church Location',
        'church_city' => 'City',
        'contact_phone' => 'Contact Phone',
        'contact_email' => 'Contact Email',
        'service_sunday' => 'Sunday Service',
        'service_wednesday' => 'Midweek Service',
        'service_friday' => 'Friday Service',
        'logo_url' => 'Logo URL',
        'social_facebook' => 'Facebook URL',
        'social_instagram' => 'Instagram URL',
        'social_youtube' => 'YouTube URL',
        'social_tiktok' => 'TikTok URL',
        'social_twitter' => 'Twitter/X URL',
        'church_map_destination' => 'Map Destination',
    ];

    foreach ($fields as $key => $label) {
        add_settings_field(
            $key,
            $label,
            'stf_render_text_field',
            'stf-settings',
            'stf_settings_main',
            ['key' => $key]
        );
    }
});

function stf_render_settings_page(): void
{
    ?>
    <div class="wrap">
        <h1>Parish Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('stf_settings');
            do_settings_sections('stf-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function stf_render_text_field(array $args): void
{
    $options = get_option('stf_site_options', []);
    $key = $args['key'] ?? '';
    $value = is_array($options) ? (string) ($options[$key] ?? '') : '';
    ?>
    <input type="text" name="stf_site_options[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
    <?php
}

function stf_sanitize_site_options(array $input): array
{
    $clean = [];
    foreach ($input as $key => $value) {
        $clean[$key] = is_string($value) ? sanitize_text_field($value) : '';
    }
    return $clean;
}

function stf_render_seed_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $notice = '';
    if (isset($_POST['stf_seed_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stf_seed_nonce'])), 'stf_seed_content')) {
        stf_seed_parish_content();
        $notice = '<div class="updated notice"><p>Seed content created. You can now review pages and posts.</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Seed Parish Content</h1>
        <?php echo $notice; ?>
        <form method="post">
            <?php wp_nonce_field('stf_seed_content', 'stf_seed_nonce'); ?>
            <p>This will create demo pages and sample posts for each custom post type.</p>
            <?php submit_button('Seed Demo Content'); ?>
        </form>
    </div>
    <?php
}

function stf_render_tools_landing(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
        <h1>Parish Tools</h1>
        <p>Use the links below to populate the demo content.</p>
        <ul>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=stf-seed')); ?>">Seed Parish Content</a></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=stf-import')); ?>">Import Parish CSVs</a></li>
        </ul>
    </div>
    <?php
}

function stf_seed_parish_content(): void
{
    $pages = [
        'Home' => ['template' => 'front-page.php'],
        'About' => ['template' => 'page-about.php'],
        'Contact' => ['template' => 'page-contact.php'],
    ];

    foreach ($pages as $title => $data) {
        $page = get_page_by_title($title);
        if (!$page) {
            $page_id = wp_insert_post([
                'post_type' => 'page',
                'post_title' => $title,
                'post_status' => 'publish',
            ]);
            if (!is_wp_error($page_id) && !empty($data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $data['template']);
            }
        }
    }

    $home = get_page_by_title('Home');
    $about = get_page_by_title('About');
    $contact = get_page_by_title('Contact');
    if ($home) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);
    }
    if ($about) {
        update_option('page_for_posts', $about->ID);
    }

    if ($home) {
        update_post_meta($home->ID, 'home_hero', [
            'title' => 'Welcome to St. Francis Of Assisi',
            'subtitle' => 'Catholic Church, Awoshie Anyaa',
            'description' => 'A community where faith, hope, and love come together. Join us as we worship and serve together.',
            'background_image' => 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'primary_cta_label' => 'Learn More',
            'primary_cta_url' => home_url('/about/'),
            'secondary_cta_label' => 'Visit Us',
            'secondary_cta_url' => home_url('/contact/'),
        ]);
        update_post_meta($home->ID, 'home_welcome', [
            'title' => 'Welcome Home',
            'paragraphs' => [
                'St. Francis Of Assisi Catholic Church is a vibrant community of believers dedicated to spreading the love of Christ in Awoshie Anyaa and beyond.',
                'Whether you are a longtime Catholic or exploring faith for the first time, you will find a warm welcome here.',
            ],
            'cta_label' => 'Discover Our Story',
            'cta_url' => home_url('/about/'),
            'image' => 'https://images.unsplash.com/photo-1644292364271-59f2b61a2323?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
        ]);
        update_post_meta($home->ID, 'home_features', [
            ['title' => 'Welcoming Community', 'text' => 'Experience the warmth of genuine fellowship and belonging.'],
            ['title' => 'Biblical Teaching', 'text' => 'Grow in your faith through sound doctrine and spiritual guidance.'],
            ['title' => 'Active Ministries', 'text' => 'Find your place to serve and make a difference in our community.'],
            ['title' => 'Engaging Events', 'text' => 'Participate in activities that strengthen faith and build community.'],
        ]);
        update_post_meta($home->ID, 'home_cta', [
            'title' => 'Plan Your Visit',
            'text' => 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.',
            'location' => 'Awoshie Anyaa, Accra',
            'service_time' => 'Sundays: 7:00 AM & 9:30 AM',
            'button_label' => 'Get Directions',
            'button_url' => home_url('/contact/'),
        ]);
    }

    stf_seed_post_type('ministry', [
        [
            'title' => 'Youth Ministry',
            'excerpt' => 'Empowering young people to grow in faith and community.',
            'meta' => ['contact_email' => 'youth@stfrancisawoshie.org', 'meeting_time' => 'Saturdays at 4:00 PM', 'is_active' => '1'],
        ],
        [
            'title' => 'Music Ministry',
            'excerpt' => 'Leading the congregation in worship through song.',
            'meta' => ['contact_email' => 'music@stfrancisawoshie.org', 'meeting_time' => 'Thursdays at 6:00 PM', 'is_active' => '1'],
        ],
    ]);

    stf_seed_post_type('event', [
        [
            'title' => 'Palm Sunday Mass',
            'excerpt' => 'Join us for the start of Holy Week.',
            'meta' => ['event_date' => date('Y-m-d', strtotime('+7 days')), 'event_time' => '9:30 AM', 'location' => 'Main Sanctuary', 'category' => 'Mass', 'is_featured' => '1', 'approval_status' => 'approved'],
        ],
        [
            'title' => 'Community Outreach',
            'excerpt' => 'Serve with our parish outreach team.',
            'meta' => ['event_date' => date('Y-m-d', strtotime('+14 days')), 'event_time' => '8:00 AM', 'location' => 'Parish Hall', 'category' => 'Service', 'approval_status' => 'approved'],
        ],
    ]);

    stf_seed_post_type('sermon', [
        [
            'title' => 'Hope in the Resurrection',
            'excerpt' => 'A message of faith and renewal.',
            'meta' => ['preacher' => 'Fr. Joseph Mensah', 'summary' => 'We explore the promise of new life and hope.', 'audio_url' => ''],
        ],
    ]);

    stf_seed_post_type('gallery_item', [
        [
            'title' => 'Easter Vigil',
            'excerpt' => 'Highlights from the Easter Vigil service.',
            'meta' => ['category' => 'worship', 'type' => 'image', 'approval_status' => 'approved'],
        ],
    ]);

    stf_seed_post_type('society', [
        ['title' => 'St. Anne Society', 'excerpt' => 'A society supporting women in the parish.'],
    ]);

    stf_seed_post_type('friar', [
        ['title' => 'Fr. Michael Owusu', 'excerpt' => 'Served 2014 - 2018', 'meta' => ['service_period' => '2014 - 2018']],
    ]);

    stf_seed_post_type('chairperson', [
        ['title' => 'Ama Boateng', 'excerpt' => 'Parish Council Chairperson', 'meta' => ['service_period' => '2020 - 2024']],
    ]);

    stf_seed_post_type('service_schedule', [
        ['title' => 'Sunday Mass', 'meta' => ['day_of_week' => 'Sunday', 'time' => '7:00 AM', 'location' => 'Main Sanctuary']],
        ['title' => 'Wednesday Mass', 'meta' => ['day_of_week' => 'Wednesday', 'time' => '6:00 AM', 'location' => 'Chapel']],
    ]);

    stf_seed_post_type('daily_verse', [
        ['title' => 'Daily Verse', 'excerpt' => 'Psalm 23:1', 'meta' => []],
    ]);
}

function stf_seed_post_type(string $post_type, array $items): void
{
    $existing = get_posts(['post_type' => $post_type, 'posts_per_page' => 1, 'post_status' => 'publish']);
    if (!empty($existing)) {
        return;
    }

    foreach ($items as $item) {
        $post_id = wp_insert_post([
            'post_type' => $post_type,
            'post_status' => 'publish',
            'post_title' => $item['title'] ?? ucfirst($post_type),
            'post_excerpt' => $item['excerpt'] ?? '',
            'post_content' => $item['content'] ?? '',
        ]);

        if (is_wp_error($post_id)) {
            continue;
        }

        $meta = $item['meta'] ?? [];
        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }
}

function stf_render_import_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $notice = '';
    if (isset($_POST['stf_import_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stf_import_nonce'])), 'stf_import_csv')) {
        $summary = stf_import_csv_seed_files();
        $notice = '<div class="updated notice"><p>CSV import complete. ' . esc_html($summary) . '</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Import Parish CSVs</h1>
        <?php echo $notice; ?>
        <form method="post">
            <?php wp_nonce_field('stf_import_csv', 'stf_import_nonce'); ?>
            <p>This will import sample CSVs from the local seed folder.</p>
            <?php submit_button('Import CSVs'); ?>
        </form>
    </div>
    <?php
}

function stf_import_csv_seed_files(): string
{
    $seed_dir = dirname(__FILE__, 3) . '/seed';
    $map = [
        'ministries.csv' => ['post_type' => 'ministry', 'meta' => ['contact_email', 'contact_phone', 'meeting_time', 'is_active', 'image_url'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'events.csv' => ['post_type' => 'event', 'meta' => ['event_date', 'event_time', 'location', 'category', 'is_featured', 'approval_status', 'image_url'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'sermons.csv' => ['post_type' => 'sermon', 'meta' => ['preacher', 'summary', 'video_url', 'audio_url', 'notes_url', 'verses'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'gallery_items.csv' => ['post_type' => 'gallery_item', 'meta' => ['category', 'type', 'approval_status', 'media_url', 'thumbnail_url'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'societies.csv' => ['post_type' => 'society', 'meta' => [], 'title' => 'title', 'excerpt' => 'excerpt'],
        'friars.csv' => ['post_type' => 'friar', 'meta' => ['service_period'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'chairpersons.csv' => ['post_type' => 'chairperson', 'meta' => ['service_period'], 'title' => 'title', 'excerpt' => 'excerpt'],
        'service_schedules.csv' => ['post_type' => 'service_schedule', 'meta' => ['day_of_week', 'time', 'location'], 'title' => 'title', 'excerpt' => null],
        'daily_verses.csv' => ['post_type' => 'daily_verse', 'meta' => ['reference', 'text', 'verse_date', 'is_published'], 'title' => 'title', 'excerpt' => null],
    ];

    $imported = 0;
    foreach ($map as $file => $config) {
        $path = $seed_dir . '/' . $file;
        if (!file_exists($path)) {
            continue;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            continue;
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            continue;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            if (!$data) {
                continue;
            }

            $post_id = wp_insert_post([
                'post_type' => $config['post_type'],
                'post_status' => 'publish',
                'post_title' => $data[$config['title']] ?? ucfirst($config['post_type']),
                'post_excerpt' => $config['excerpt'] ? ($data[$config['excerpt']] ?? '') : '',
            ]);

            if (is_wp_error($post_id)) {
                continue;
            }

            foreach ($config['meta'] as $meta_key) {
                if (isset($data[$meta_key]) && $data[$meta_key] !== '') {
                    update_post_meta($post_id, $meta_key, $data[$meta_key]);
                }
            }
            $imported++;
        }
        fclose($handle);
    }

    return sprintf('%d rows imported.', $imported);
}

add_shortcode('stf_contact_form', function (): string {
    $status = isset($_GET['stf_contact']) ? sanitize_text_field(wp_unslash($_GET['stf_contact'])) : '';
    $notice = $status === 'success' ? '<div class="card p-4 mb-6 text-green-700 bg-green-50 border border-green-200">Thank you for your message! We will get back to you soon.</div>' : '';

    $action = esc_url(admin_url('admin-post.php'));
    $nonce = wp_create_nonce('stf_contact_form');

    return $notice . '
    <form method="post" action="' . $action . '" class="space-y-4">
        <input type="hidden" name="action" value="stf_contact">
        <input type="hidden" name="stf_contact_nonce" value="' . esc_attr($nonce) . '">
        <input class="input" type="text" name="name" placeholder="Full Name" required>
        <input class="input" type="email" name="email" placeholder="Email Address" required>
        <input class="input" type="text" name="phone" placeholder="Phone (optional)">
        <input class="input" type="text" name="subject" placeholder="Subject" required>
        <textarea class="input" name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit" class="btn btn-primary w-full">Send Message</button>
    </form>';
});

add_action('admin_post_nopriv_stf_contact', 'stf_handle_contact_form');
add_action('admin_post_stf_contact', 'stf_handle_contact_form');

function stf_handle_contact_form(): void
{
    if (!isset($_POST['stf_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stf_contact_nonce'])), 'stf_contact_form')) {
        wp_safe_redirect(wp_get_referer() ?: home_url('/'));
        exit;
    }

    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $phone = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
    $subject = sanitize_text_field(wp_unslash($_POST['subject'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        wp_safe_redirect(wp_get_referer() ?: home_url('/'));
        exit;
    }

    wp_insert_post([
        'post_type' => 'contact_message',
        'post_status' => 'private',
        'post_title' => $subject,
        'post_content' => $message,
        'meta_input' => [
            'sender_name' => $name,
            'sender_email' => $email,
            'sender_phone' => $phone,
        ],
    ]);

    $redirect = wp_get_referer() ?: home_url('/');
    $redirect = add_query_arg('stf_contact', 'success', $redirect);
    wp_safe_redirect($redirect);
    exit;
}
