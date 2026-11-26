<?php

declare(strict_types=1);

get_header();
$current_category = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : 'all';
$category_list = array_merge(['all'], stf_theme_event_category_choices());
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Calendar</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4">Events &amp; Worship</h1>
        <p class="text-lg text-white/75 mt-4">Join us for worship, fellowship, and service.</p>
    </div>
</section>

<section class="sticky-filter-bar py-6 bg-white/90 border-b sticky z-40 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="surface-soft p-3">
            <div class="flex flex-wrap gap-2 justify-center">
                <?php foreach ($category_list as $cat) : ?>
                    <a href="<?php echo esc_url(add_query_arg('category', $cat, stf_theme_archive_url('event', '/events/'))); ?>" class="pill <?php echo $current_category === $cat ? 'bg-[#6B2C2C] text-white border-[#6B2C2C]' : ''; ?>"><?php echo esc_html(ucfirst($cat)); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 event-scroll">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $event_category = (string) stf_theme_post_meta(get_the_ID(), 'category', '');
                    if ($current_category !== 'all' && strcasecmp($event_category, $current_category) !== 0) {
                        continue;
                    }
                    ?>
                    <article class="card overflow-hidden">
                        <?php if (stf_theme_image_url(get_the_ID()) !== '') : ?>
                            <div class="media-frame">
                                <img src="<?php echo esc_url(stf_theme_image_url(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-52 object-cover" loading="lazy">
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 rounded-2xl bg-amber-100 px-4 py-3 text-center text-amber-700 min-w-[76px]">
                                    <div class="text-xs uppercase tracking-widest"><?php echo esc_html(get_the_date('M')); ?></div>
                                    <div class="text-2xl font-semibold leading-none mt-1"><?php echo esc_html(get_the_date('d')); ?></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <h3 class="text-xl"><?php the_title(); ?></h3>
                                        <?php if ($event_category !== '') : ?><span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600"><?php echo esc_html($event_category); ?></span><?php endif; ?>
                                    </div>
                                    <p class="text-slate-600 mt-3"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28)); ?></p>
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                        <div class="rounded-xl bg-white px-3 py-2 text-slate-600 border border-[#eadcc9]"><span class="font-semibold text-[#2b1a12]">Time:</span> <?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'event_time', 'TBA')); ?></div>
                                        <div class="rounded-xl bg-white px-3 py-2 text-slate-600 border border-[#eadcc9]"><span class="font-semibold text-[#2b1a12]">Location:</span> <?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'location', 'Parish grounds')); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-span-full surface-soft p-10 text-center">
                    <h3 class="text-2xl font-semibold text-[#2b1a12]">No Events Found</h3>
                    <p class="text-slate-600 mt-3">No events are currently available in this category. Try another category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
get_footer();

