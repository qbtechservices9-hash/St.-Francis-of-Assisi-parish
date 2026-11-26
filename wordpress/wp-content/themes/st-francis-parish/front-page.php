<?php

declare(strict_types=1);

get_header();

$page_id = get_queried_object_id();
$hero = stf_theme_field($page_id, 'home_hero', []);
$welcome = stf_theme_field($page_id, 'home_welcome', []);
$features = stf_theme_field($page_id, 'home_features', []);
$cta = stf_theme_field($page_id, 'home_cta', []);
$site_settings = stf_theme_site_options();

$hero_defaults = [
    'title' => 'Welcome to St. Francis Of Assisi',
    'subtitle' => 'Catholic Church, Awoshie Anyaa',
    'description' => 'A community where faith, hope, and love come together. Join us as we worship and serve together.',
    'background_image' => 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
    'primary_cta_label' => 'Learn More',
    'primary_cta_url' => stf_theme_page_url('about', '/about/'),
    'secondary_cta_label' => 'Visit Us',
    'secondary_cta_url' => stf_theme_page_url('contact', '/contact/'),
];
$welcome_defaults = [
    'title' => 'Welcome Home',
    'paragraphs' => [
        'St. Francis Of Assisi Catholic Church is a vibrant community of believers dedicated to spreading the love of Christ in Awoshie Anyaa and beyond.',
        'Whether you are a longtime Catholic or exploring faith for the first time, you will find a warm welcome here.',
    ],
    'cta_label' => 'Discover Our Story',
    'cta_url' => stf_theme_page_url('about', '/about/'),
    'image' => 'https://images.unsplash.com/photo-1644292364271-59f2b61a2323?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
];
$cta_defaults = [
    'title' => 'Plan Your Visit',
    'text' => 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.',
    'location' => $site_settings['church_location'],
    'service_time' => $site_settings['service_sunday'],
    'button_label' => 'Get Directions',
    'button_url' => stf_theme_page_url('contact', '/contact/'),
];

$hero = wp_parse_args(is_array($hero) ? $hero : [], $hero_defaults);
$welcome = wp_parse_args(is_array($welcome) ? $welcome : [], $welcome_defaults);
$cta = wp_parse_args(is_array($cta) ? $cta : [], $cta_defaults);
$resolve_image = static function ($image): string {
    if (is_array($image)) {
        $image = $image['url'] ?? $image['path'] ?? $image[0] ?? '';
    }

    return is_scalar($image) ? (string) $image : '';
};
$hero_slides = array_values(array_filter(
    is_array($hero['slides'] ?? null) ? $hero['slides'] : [],
    static fn ($slide): bool => is_array($slide)
));
$hero_image = $resolve_image($hero['background_image'] ?? '');
$primary_hero_slide = $hero_slides[0] ?? [
    'title' => $hero['title'] ?? 'Welcome to our parish',
    'subtitle' => $hero['subtitle'] ?? '',
    'description' => $hero['description'] ?? '',
    'primary_cta_label' => $hero['primary_cta_label'] ?? 'Learn More',
    'primary_cta_url' => $hero['primary_cta_url'] ?? stf_theme_page_url('about', '/about/'),
    'secondary_cta_label' => $hero['secondary_cta_label'] ?? 'Visit Us',
    'secondary_cta_url' => $hero['secondary_cta_url'] ?? stf_theme_page_url('contact', '/contact/'),
    'background_image' => $hero_image,
];
$render_slides = !empty($hero_slides)
    ? array_map(
        static function (array $slide) use ($hero_image, $resolve_image): array {
            $background_image = $resolve_image($slide['background_image'] ?? $hero_image);
            $slide['background_image'] = $background_image !== '' ? $background_image : $hero_image;

            return $slide;
        },
        $hero_slides
    )
    : [$primary_hero_slide];
$render_slide_count = count($render_slides);
$features = is_array($features) && $features ? $features : [
    ['title' => 'Welcoming Community', 'text' => 'Experience the warmth of genuine fellowship and belonging.'],
    ['title' => 'Biblical Teaching', 'text' => 'Grow in your faith through sound doctrine and spiritual guidance.'],
    ['title' => 'Active Ministries', 'text' => 'Find your place to serve and make a difference in our community.'],
    ['title' => 'Engaging Events', 'text' => 'Participate in activities that strengthen faith and build community.'],
];

$service_query = stf_theme_posts('service_schedule', [
    'posts_per_page' => 3,
    'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
$event_query = stf_theme_posts('event', [
    'posts_per_page' => 4,
    'meta_query' => [
        stf_theme_approved_meta_query(),
    ],
    'orderby' => 'meta_value',
    'meta_key' => 'event_date',
    'order' => 'ASC',
]);
$ministry_query = stf_theme_posts('ministry', [
    'posts_per_page' => 6,
    'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
$latest_sermon_query = stf_theme_posts('sermon', [
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
]);
$latest_sermon = $latest_sermon_query->have_posts() ? $latest_sermon_query->posts[0] : null;
?>

<section class="relative overflow-hidden hero-3d hero-full" id="hero" data-hero-slider<?php if ($render_slide_count > 1) : ?> data-autoplay="true"<?php endif; ?>>
    <div class="absolute inset-0">
        <?php foreach ($render_slides as $index => $slide) : ?>
            <div class="hero-slide-bg <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-slide-bg aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                <img
                    src="<?php echo esc_url((string) ($slide['background_image'] ?? $hero_image)); ?>"
                    alt="<?php echo esc_attr((string) ($slide['title'] ?? $hero['title'])); ?>"
                    class="w-full h-full object-cover hero-media"
                    <?php if ($index === 0) : ?>
                        loading="eager" fetchpriority="high"
                    <?php else : ?>
                        loading="lazy"
                    <?php endif; ?>
                >
            </div>
        <?php endforeach; ?>
        <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C]/95 via-[#8C6A5D]/82 to-[#6B2C2C]/50"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),_transparent_58%)]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24 lg:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-[1.2fr,0.8fr] gap-10 items-start">
            <div class="hero-intro hero-delay-1 text-white" data-depth="0.04">
                <div class="flex flex-wrap gap-2">
                    <span class="metric-chip">Faith</span>
                    <span class="metric-chip">Community</span>
                    <span class="metric-chip">Service</span>
                </div>
                <div class="hero-slider-shell mt-5">
                    <div class="hero-slides">
                        <?php foreach ($render_slides as $index => $slide) : ?>
                            <?php
                            $primary_cta_url = !empty($slide['primary_cta_url']) ? (string) $slide['primary_cta_url'] : stf_theme_page_url('about', '/about/');
                            $secondary_cta_url = !empty($slide['secondary_cta_url']) ? (string) $slide['secondary_cta_url'] : stf_theme_page_url('contact', '/contact/');
                            ?>
                            <div class="hero-slide-panel text-white <?php echo $index === 0 ? 'is-active' : ''; ?>" data-hero-slide-panel aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                                <div class="hero-slide-copy">
                                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight"><?php echo esc_html((string) ($slide['title'] ?? 'Welcome to our parish')); ?></h1>
                                    <?php if (!empty($slide['subtitle'])) : ?>
                                        <p class="text-xl text-white/85 mt-4"><?php echo esc_html((string) $slide['subtitle']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['description'])) : ?>
                                        <p class="text-lg text-white/75 mt-6 max-w-2xl"><?php echo esc_html((string) $slide['description']); ?></p>
                                    <?php endif; ?>
                                    <div class="flex flex-col sm:flex-row gap-4 mt-8">
                                        <a href="<?php echo esc_url($primary_cta_url); ?>" class="btn btn-primary"><?php echo esc_html((string) ($slide['primary_cta_label'] ?? 'Learn More')); ?></a>
                                        <a href="<?php echo esc_url($secondary_cta_url); ?>" class="btn btn-secondary"><?php echo esc_html((string) ($slide['secondary_cta_label'] ?? 'Visit Us')); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($render_slide_count > 1) : ?>
                        <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                            <div class="flex items-center gap-3" role="tablist" aria-label="Hero slides">
                                <?php foreach ($render_slides as $index => $slide) : ?>
                                    <button
                                        type="button"
                                        class="hero-dot <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                        data-hero-dot
                                        data-index="<?php echo esc_attr((string) $index); ?>"
                                        role="tab"
                                        aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                        aria-label="<?php echo esc_attr('Show slide ' . ($index + 1)); ?>"
                                    ></button>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" class="hero-arrow" data-hero-prev aria-label="Previous hero slide">
                                    <span aria-hidden="true">&larr;</span> Previous
                                </button>
                                <button type="button" class="hero-arrow" data-hero-next aria-label="Next hero slide">
                                    Next <span aria-hidden="true">&rarr;</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero-intro hero-delay-2 hero-service-card surface-soft p-6 sm:p-7 text-[#2b1a12]" data-depth="0.08">
                <p class="section-kicker !text-amber-700">Service Times</p>
                <h2 class="text-2xl font-semibold mt-3">Join Us This Week</h2>
                <div class="mt-5 space-y-3">
                    <?php if ($service_query->have_posts()) : ?>
                        <?php foreach ($service_query->posts as $schedule) : ?>
                            <div class="stat-pair">
                                <p class="stat-pair-label"><?php echo esc_html(get_the_title($schedule)); ?></p>
                                <p class="stat-pair-value"><?php echo esc_html((string) stf_theme_post_meta($schedule->ID, 'day_of_week', 'Sunday')); ?> - <?php echo esc_html((string) stf_theme_post_meta($schedule->ID, 'time', '7:00 AM')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="stat-pair">
                            <p class="stat-pair-label">Sunday</p>
                            <p class="stat-pair-value"><?php echo esc_html($site_settings['service_sunday']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url(stf_theme_archive_url('event', '/events/')); ?>" class="link-arrow mt-5">View upcoming events <span>&rarr;</span></a>
            </div>
        </div>
    </div>
</section>

<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="section-kicker">Welcome Home</p>
                <h2 class="section-title mt-3"><?php echo esc_html((string) $welcome['title']); ?></h2>
                <?php foreach (array_slice((array) $welcome['paragraphs'], 0, 2) as $paragraph) : ?>
                    <p class="section-lead mt-4"><?php echo esc_html(is_array($paragraph) ? (string) ($paragraph['text'] ?? '') : (string) $paragraph); ?></p>
                <?php endforeach; ?>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="<?php echo esc_url((string) $welcome['cta_url']); ?>" class="btn btn-soft"><?php echo esc_html((string) $welcome['cta_label']); ?></a>
                    <a href="<?php echo esc_url(stf_theme_archive_url('ministry', '/ministries/')); ?>" class="pill">Explore Ministries</a>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -top-5 -left-5 h-20 w-20 rounded-full bg-amber-100"></div>
                <div class="relative h-[420px] rounded-3xl overflow-hidden shadow-[0_22px_50px_rgba(46,26,15,0.18)] media-frame">
                    <img src="<?php echo esc_url((string) $welcome['image']); ?>" alt="People praying" class="w-full h-full object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="section-kicker">Why Join Us</p>
            <h2 class="section-title mt-3">Why Join Our Parish?</h2>
            <p class="section-lead max-w-2xl mx-auto mt-4">We are more than just a church. We are a family united in faith and purpose.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <?php foreach (array_slice($features, 0, 4) as $feature) : ?>
                <article class="card p-6">
                    <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold"><?php echo esc_html(strtoupper(substr((string) ($feature['title'] ?? 'P'), 0, 1))); ?></div>
                    <h3 class="text-xl mt-4"><?php echo esc_html((string) ($feature['title'] ?? '')); ?></h3>
                    <p class="text-sm text-slate-600 mt-2"><?php echo esc_html((string) ($feature['text'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
            <div>
                <p class="section-kicker">Events</p>
                <h2 class="section-title mt-3">Upcoming Events</h2>
                <p class="section-lead mt-3">Join us for worship and fellowship.</p>
            </div>
            <a href="<?php echo esc_url(stf_theme_archive_url('event', '/events/')); ?>" class="btn btn-soft">View All Events</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <?php if ($event_query->have_posts()) : ?>
                <?php foreach ($event_query->posts as $event) : ?>
                    <?php $event_date = (string) stf_theme_post_meta($event->ID, 'event_date', get_the_date('Y-m-d', $event)); ?>
                    <article class="card p-6">
                        <?php if (stf_theme_image_url($event->ID) !== '') : ?>
                            <div class="media-frame mb-4"><img src="<?php echo esc_url(stf_theme_image_url($event->ID)); ?>" alt="<?php echo esc_attr(get_the_title($event)); ?>" class="w-full h-40 object-cover" loading="lazy"></div>
                        <?php endif; ?>
                        <p class="section-kicker !tracking-[0.22em]"><?php echo esc_html(wp_date('M d', strtotime($event_date))); ?></p>
                        <h3 class="text-lg mt-2"><?php echo esc_html(get_the_title($event)); ?></h3>
                        <p class="text-sm text-slate-600 mt-2"><?php echo esc_html((string) stf_theme_post_meta($event->ID, 'event_time', 'TBA')); ?></p>
                        <p class="text-sm text-slate-500 mt-1"><?php echo esc_html((string) stf_theme_post_meta($event->ID, 'location', 'Parish grounds')); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="xl:col-span-4 card p-8 text-center text-slate-600">No upcoming events have been published yet.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($latest_sermon instanceof WP_Post) : ?>
    <?php
    $preacher = (string) stf_theme_post_meta($latest_sermon->ID, 'preacher', '');
    $video_url = (string) stf_theme_post_meta($latest_sermon->ID, 'video_url', '');
    $audio_url = (string) stf_theme_post_meta($latest_sermon->ID, 'audio_url', '');
    $summary = (string) stf_theme_post_meta($latest_sermon->ID, 'summary', has_excerpt($latest_sermon) ? get_the_excerpt($latest_sermon) : '');
    ?>
    <section class="py-20 bg-[#F1EEE9]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
                <div>
                    <p class="section-kicker">Latest Sermon</p>
                    <h2 class="section-title mt-3"><?php echo esc_html(get_the_title($latest_sermon)); ?></h2>
                    <?php if ($preacher !== '') : ?><p class="section-lead mt-3">Preacher: <?php echo esc_html($preacher); ?></p><?php endif; ?>
                </div>
                <a href="<?php echo esc_url(stf_theme_archive_url('sermon', '/sermons/')); ?>" class="btn btn-soft">View All Sermons</a>
            </div>
            <div class="card p-8 md:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-[1.2fr,0.8fr] gap-8 items-start">
                    <div>
                        <p class="section-kicker">Sermon Summary</p>
                        <p class="section-lead mt-4"><?php echo esc_html($summary !== '' ? $summary : 'Dive into this week\'s message and reflect on the Word together.'); ?></p>
                        <div class="flex flex-wrap gap-3 mt-6">
                            <a href="<?php echo esc_url(get_permalink($latest_sermon)); ?>" class="btn btn-primary">Explore Sermon</a>
                            <?php if ($video_url !== '') : ?><a href="<?php echo esc_url($video_url); ?>" class="pill" target="_blank" rel="noopener">Watch Video</a><?php endif; ?>
                            <?php if ($audio_url !== '') : ?><a href="<?php echo esc_url($audio_url); ?>" class="pill" target="_blank" rel="noopener">Listen Audio</a><?php endif; ?>
                        </div>
                    </div>
                    <div class="surface-soft p-6">
                        <div class="text-sm text-slate-600">
                            <p class="font-semibold text-[#2b1a12]">Date</p>
                            <p><?php echo esc_html(get_the_date('M d, Y', $latest_sermon)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
            <div>
                <p class="section-kicker">Ministries</p>
                <h2 class="section-title mt-3">Serve With Us</h2>
                <p class="section-lead mt-3">Find your place to serve, grow, and build community.</p>
            </div>
            <a href="<?php echo esc_url(stf_theme_archive_url('ministry', '/ministries/')); ?>" class="btn btn-soft">View All Ministries</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php if ($ministry_query->have_posts()) : ?>
                <?php foreach ($ministry_query->posts as $ministry) : ?>
                    <article class="card overflow-hidden">
                        <div class="relative h-52 media-frame">
                            <img src="<?php echo esc_url(stf_theme_image_url($ministry->ID, $hero_defaults['background_image'])); ?>" alt="<?php echo esc_attr(get_the_title($ministry)); ?>" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#3b2314]/75 to-transparent"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl"><?php echo esc_html(get_the_title($ministry)); ?></h3>
                            <p class="text-slate-600 mt-3"><?php echo esc_html(has_excerpt($ministry) ? $ministry->post_excerpt : wp_trim_words(wp_strip_all_tags($ministry->post_content), 24)); ?></p>
                            <a href="<?php echo esc_url(get_permalink($ministry)); ?>" class="btn btn-soft mt-5 w-full">View Ministry Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="section-kicker text-amber-300">Plan Your Visit</p>
        <h2 class="text-3xl md:text-4xl font-semibold mt-3"><?php echo esc_html((string) $cta['title']); ?></h2>
        <p class="text-lg text-white/75 mt-6"><?php echo esc_html((string) $cta['text']); ?></p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 text-sm text-white/75">
            <div><?php echo esc_html((string) $cta['location']); ?></div>
            <div><?php echo esc_html((string) $cta['service_time']); ?></div>
        </div>
        <a href="<?php echo esc_url((string) $cta['button_url']); ?>" class="btn btn-secondary mt-8"><?php echo esc_html((string) $cta['button_label']); ?></a>
    </div>
</section>

<?php
get_footer();
