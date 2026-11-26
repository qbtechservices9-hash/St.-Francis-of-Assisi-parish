<?php

declare(strict_types=1);

get_header();
the_post();

$video_url = (string) stf_theme_post_meta(get_the_ID(), 'video_url', '');
$audio_url = (string) stf_theme_post_meta(get_the_ID(), 'audio_url', '');
$notes_url = (string) stf_theme_post_meta(get_the_ID(), 'notes_url', '');
?>
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card p-8 md:p-10">
            <p class="section-kicker">Sermon</p>
            <h1 class="section-title mt-3"><?php the_title(); ?></h1>
            <?php if (stf_theme_post_meta(get_the_ID(), 'preacher', '') !== '') : ?><p class="section-lead mt-4">Preacher: <?php echo esc_html((string) stf_theme_post_meta(get_the_ID(), 'preacher', '')); ?></p><?php endif; ?>
            <p class="text-sm text-slate-500 mt-3"><?php echo esc_html(get_the_date('M d, Y')); ?></p>
            <div class="mt-8 prose max-w-none text-slate-600"><?php the_content(); ?></div>
            <div class="flex flex-wrap gap-3 mt-8">
                <?php if ($video_url !== '') : ?><a href="<?php echo esc_url($video_url); ?>" class="btn btn-primary" target="_blank" rel="noopener">Watch Video</a><?php endif; ?>
                <?php if ($audio_url !== '') : ?><a href="<?php echo esc_url($audio_url); ?>" class="btn btn-soft" target="_blank" rel="noopener">Listen Audio</a><?php endif; ?>
                <?php if ($notes_url !== '') : ?><a href="<?php echo esc_url($notes_url); ?>" class="pill" target="_blank" rel="noopener">Study Notes</a><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();

