<?php

declare(strict_types=1);

get_header();
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Word &amp; Reflection</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4">Sermons</h1>
        <p class="text-lg text-white/75 mt-4">Watch, listen, and reflect on recent messages.</p>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="card p-6 sermon-card">
                        <div class="sermon-thumb"><div class="sermon-play" aria-hidden="true"></div></div>
                        <div>
                            <p class="section-kicker !tracking-[0.22em]"><?php echo esc_html(get_the_date('M d, Y')); ?></p>
                            <h2 class="text-xl mt-3"><?php the_title(); ?></h2>
                            <?php if (stf_theme_post_meta(get_the_ID(), 'preacher', '') !== '') : ?><p class="text-slate-600 mt-2">Preacher: <?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'preacher', '')); ?></p><?php endif; ?>
                            <p class="text-slate-600 mt-3"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28)); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-soft mt-5">Explore Sermon</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
get_footer();

