<?php

declare(strict_types=1);

get_header();
?>
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="card p-8 mb-6">
                    <h1 class="section-title"><?php the_title(); ?></h1>
                    <div class="mt-6 prose max-w-none text-slate-600"><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="card p-8 text-center text-slate-600">No content found.</div>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
