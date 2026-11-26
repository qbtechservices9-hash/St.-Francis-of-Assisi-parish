<?php

declare(strict_types=1);

get_header();
the_post();

$cover = stf_theme_image_url(get_the_ID(), 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080');
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0">
        <img src="<?php echo esc_url($cover); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-[#6B2C2C]/92 via-[#8C6A5D]/78 to-[#6B2C2C]/45"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Ministry</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?><p class="text-lg text-white/80 mt-4 max-w-3xl mx-auto"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 xl:grid-cols-[2fr,1fr] gap-8">
            <article class="card p-7 md:p-8">
                <p class="section-kicker">About This Ministry</p>
                <h2 class="text-3xl font-semibold mt-3"><?php the_title(); ?></h2>
                <div class="mt-6 prose max-w-none text-slate-600 prose-headings:text-[#2b1a12]">
                    <?php the_content(); ?>
                </div>
            </article>

            <aside class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-4">Contact &amp; Schedule</h3>
                    <div class="space-y-4 text-sm text-slate-700">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Email</p>
                            <a class="text-amber-700 font-semibold" href="mailto:<?php echo esc_attr((string) stf_theme_post_meta(get_the_ID(), 'contact_email', stf_theme_option('contact_email'))); ?>"><?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'contact_email', stf_theme_option('contact_email'))); ?></a>
                        </div>
                        <?php if (stf_theme_post_meta(get_the_ID(), 'contact_phone', '') !== '') : ?>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Phone</p>
                                <a class="text-amber-700 font-semibold" href="tel:<?php echo esc_attr((string) stf_theme_post_meta(get_the_ID(), 'contact_phone', '')); ?>"><?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'contact_phone', '')); ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if (stf_theme_post_meta(get_the_ID(), 'meeting_time', '') !== '') : ?>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Meeting Time</p>
                                <p class="font-semibold text-[#2b1a12]"><?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'meeting_time', '')); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url(stf_theme_page_url('contact', '/contact/')); ?>" class="btn btn-soft w-full mt-6">Contact Parish Office</a>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php
get_footer();

