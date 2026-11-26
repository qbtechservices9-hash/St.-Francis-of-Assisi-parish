<?php

declare(strict_types=1);

$site_settings = stf_theme_site_options();
$nav_ministries = stf_theme_nav_ministries();
$nav_links = [
    [
        'label' => 'Events',
        'url' => stf_theme_archive_url('event', '/events/'),
        'active' => is_post_type_archive('event') || is_singular('event'),
    ],
    [
        'label' => 'Sermons',
        'url' => stf_theme_archive_url('sermon', '/sermons/'),
        'active' => is_post_type_archive('sermon') || is_singular('sermon'),
    ],
    [
        'label' => 'Gallery',
        'url' => stf_theme_archive_url('gallery_item', '/gallery/'),
        'active' => is_post_type_archive('gallery_item') || is_singular('gallery_item'),
    ],
    [
        'label' => 'Contact',
        'url' => stf_theme_page_url('contact', '/contact/'),
        'active' => is_page('contact'),
    ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo stf_theme_logo_url(); ?>" type="image/jpeg">
    <?php wp_head(); ?>
</head>
<body <?php body_class('min-h-screen site-3d flex flex-col bg-[#F8F6F3] text-[#333333]'); ?> data-animate="true">
<?php wp_body_open(); ?>
<div class="site-preloader" data-preloader aria-hidden="true">
    <div class="preloader-card">
        <img src="<?php echo stf_theme_logo_url(); ?>" alt="Church logo" class="preloader-logo">
        <div class="preloader-spinner" aria-hidden="true"></div>
        <p class="preloader-text">Preparing a warm welcome...</p>
    </div>
</div>
<a href="#main-content" class="skip-link">Skip to main content</a>

<header class="sticky top-0 z-50 border-b border-[#E5DDD3]/80 bg-[#F8F6F3]/92 backdrop-blur-xl shadow-[0_10px_26px_rgba(107,44,44,0.08)]">
    <div class="hidden md:block border-b border-[#E5DDD3]/70 bg-[#F3EEE7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-[0.78rem]">
            <div class="flex items-center gap-4 text-[#8C6A5D]">
                <span><?php echo esc_html($site_settings['service_sunday']); ?></span>
            </div>
            <div class="flex items-center gap-4 text-[#6B2C2C]">
                <a href="tel:<?php echo esc_attr($site_settings['contact_phone']); ?>" class="hover:text-[#5b2a1e] transition-colors"><?php echo esc_html($site_settings['contact_phone']); ?></a>
                <a href="<?php echo esc_url(stf_theme_page_url('contact', '/contact/')); ?>" class="hover:text-[#5b2a1e] transition-colors">Visit &amp; Directions</a>
            </div>
        </div>
    </div>

    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Primary">
        <div class="flex justify-between items-center min-h-[78px] py-3">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3.5">
                <div class="rounded-full bg-white/80 p-1 shadow-[0_10px_20px_rgba(107,44,44,0.1)]">
                    <img src="<?php echo stf_theme_logo_url(); ?>" alt="<?php echo esc_attr($site_settings['church_name']); ?> logo" class="w-11 h-11 rounded-full object-cover border border-[#E5DDD3]" loading="lazy" />
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="font-semibold text-[#333333] text-[0.98rem]"><?php echo esc_html($site_settings['church_name']); ?></span>
                    <span class="text-xs text-[#8C6A5D] tracking-wide"><?php echo esc_html($site_settings['church_location']); ?></span>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-2 text-sm">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link <?php echo is_front_page() ? 'nav-link-active' : ''; ?>">Home</a>
                <a href="<?php echo esc_url(stf_theme_page_url('about', '/about/')); ?>" class="nav-link <?php echo (is_page('about') || is_post_type_archive('society') || is_post_type_archive('friar') || is_post_type_archive('chairperson')) ? 'nav-link-active' : ''; ?>">About</a>
                <div class="nav-dropdown">
                    <button type="button" class="nav-link nav-dropdown-trigger" aria-haspopup="true">Ministries <span class="nav-caret">&or;</span></button>
                    <div class="nav-dropdown-menu">
                        <?php if ($nav_ministries) : ?>
                            <?php foreach ($nav_ministries as $ministry) : ?>
                                <a href="<?php echo esc_url(get_permalink($ministry)); ?>" class="nav-dropdown-link"><?php echo esc_html(get_the_title($ministry)); ?></a>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <span class="nav-dropdown-empty">No ministries yet</span>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(stf_theme_archive_url('ministry', '/ministries/')); ?>" class="nav-dropdown-link nav-dropdown-cta">View All Ministries</a>
                    </div>
                </div>
                <?php foreach ($nav_links as $link) : ?>
                    <a href="<?php echo esc_url($link['url']); ?>" class="nav-link <?php echo $link['active'] ? 'nav-link-active' : ''; ?>"><?php echo esc_html($link['label']); ?></a>
                <?php endforeach; ?>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="<?php echo esc_url(stf_theme_page_url('contact', '/contact/')); ?>" class="btn btn-primary !px-5 !py-2.5">Join Us Sunday</a>
            </div>

            <button
                type="button"
                data-menu-toggle
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="Toggle navigation menu"
                class="menu-toggle md:hidden inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5DDD3] bg-white/70 text-[#6B2C2C] shadow-sm transition hover:bg-white"
            >
                <svg class="menu-icon-open h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg class="menu-icon-close hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        <div id="mobile-menu" data-mobile-menu aria-hidden="true" class="mobile-menu-panel md:hidden hidden py-4 border-t border-[#E5DDD3]">
            <div class="space-y-1">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors <?php echo is_front_page() ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70'; ?>">Home</a>
                <a href="<?php echo esc_url(stf_theme_page_url('about', '/about/')); ?>" class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors <?php echo is_page('about') ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70'; ?>">About</a>
                <a href="<?php echo esc_url(stf_theme_archive_url('ministry', '/ministries/')); ?>" class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors <?php echo (is_post_type_archive('ministry') || is_singular('ministry')) ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70'; ?>">Ministries</a>
                <div class="pl-6 pr-4 pb-2">
                    <div class="text-xs uppercase tracking-[0.2em] text-[#8C6A5D] mb-2">Ministries</div>
                    <?php if ($nav_ministries) : ?>
                        <?php foreach ($nav_ministries as $ministry) : ?>
                            <a href="<?php echo esc_url(get_permalink($ministry)); ?>" class="block py-2 text-sm text-[#6B2C2C] hover:text-[#333333]"><?php echo esc_html(get_the_title($ministry)); ?></a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="text-xs text-[#8C6A5D]">No ministries yet</div>
                    <?php endif; ?>
                </div>
                <?php foreach ($nav_links as $link) : ?>
                    <a href="<?php echo esc_url($link['url']); ?>" class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors <?php echo $link['active'] ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70'; ?>"><?php echo esc_html($link['label']); ?></a>
                <?php endforeach; ?>
            </div>
            <a href="<?php echo esc_url(stf_theme_page_url('contact', '/contact/')); ?>" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#6B2C2C] px-4 py-2.5 text-sm font-semibold text-white shadow">Join Us Sunday</a>
        </div>
    </nav>
</header>
<main id="main-content" class="flex-1" tabindex="-1">
