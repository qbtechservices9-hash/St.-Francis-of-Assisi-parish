<?php

declare(strict_types=1);

get_header();
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Parish Life</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4">Gallery</h1>
        <p class="text-lg text-white/75 mt-4">Moments from worship, community life, and service.</p>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 gallery-masonry">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="gallery-item card overflow-hidden">
                    <?php if (stf_theme_image_url(get_the_ID()) !== '') : ?>
                        <img src="<?php echo esc_url(stf_theme_image_url(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-auto object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-4">
                        <h2 class="text-lg"><?php the_title(); ?></h2>
                        <p class="text-sm text-slate-600 mt-2"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 18)); ?></p>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();

