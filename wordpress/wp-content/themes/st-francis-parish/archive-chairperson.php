<?php

declare(strict_types=1);

get_header();
?>
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <p class="section-kicker">Leadership</p>
            <h1 class="section-title mt-3">Parish Pastoral Council Chairpersons</h1>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while (have_posts()) : the_post(); ?>
                <article class="leadership-preview-card">
                    <h2 class="text-2xl"><?php the_title(); ?></h2>
                    <?php if (stf_theme_post_meta(get_the_ID(), 'service_period', '') !== '') : ?><p class="leadership-preview-period"><?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'service_period', '')); ?></p><?php endif; ?>
                    <p class="text-slate-600 mt-3"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 20)); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
get_footer();

