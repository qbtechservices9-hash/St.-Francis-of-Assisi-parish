<?php

declare(strict_types=1);

get_header();
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Serve With Us</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4">Our Ministries</h1>
        <p class="text-lg text-white/75 mt-4">Find your place to serve, grow, and build community.</p>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="card overflow-hidden">
                        <div class="relative h-52 media-frame">
                            <img src="<?php echo esc_url(stf_theme_image_url(get_the_ID(), 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080')); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#3b2314]/75 to-transparent"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl"><?php the_title(); ?></h3>
                            <p class="text-slate-600 mt-3"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 24)); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-soft mt-5 w-full">View Ministry Details</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-span-full surface-soft p-10 text-center">
                    <h3 class="text-2xl font-semibold text-[#2b1a12]">No Ministries Available</h3>
                    <p class="text-slate-600 mt-3">Ministries will appear here once they are added.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
get_footer();

