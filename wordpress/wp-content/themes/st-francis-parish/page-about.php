<?php

declare(strict_types=1);

get_header();

$page_id = get_queried_object_id();
$hero = stf_theme_field($page_id, 'about_hero', []);
$story = stf_theme_field($page_id, 'about_story', []);
$mission = stf_theme_field($page_id, 'about_mission', []);
$vision = stf_theme_field($page_id, 'about_vision', []);
$values = stf_theme_field($page_id, 'about_values', []);
$sacraments = stf_theme_field($page_id, 'about_sacraments', []);
$saint = stf_theme_field($page_id, 'about_saint', []);

$hero = wp_parse_args(is_array($hero) ? $hero : [], [
    'title' => 'About Us',
    'subtitle' => 'Our Story, Mission, and Values',
    'background_image' => 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
]);
$story = wp_parse_args(is_array($story) ? $story : [], [
    'title' => 'Our Story',
    'paragraphs' => [
        'St. Francis Of Assisi Catholic Church was established in Awoshie Anyaa with a vision to create a spiritual home for the faithful in our community.',
        'Over the years, we have grown from a small gathering of believers to a thriving parish community.',
    ],
    'image' => 'https://images.unsplash.com/photo-1709390658353-08d14087f616?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
]);
$mission = wp_parse_args(is_array($mission) ? $mission : [], ['title' => 'Our Mission', 'text' => 'To proclaim the Gospel of Jesus Christ, celebrate the sacraments, and serve as a spiritual family.']);
$vision = wp_parse_args(is_array($vision) ? $vision : [], ['title' => 'Our Vision', 'text' => 'To be a vibrant Catholic community where every person encounters Christ and grows in discipleship.']);
$values = is_array($values) && $values ? $values : [
    ['title' => 'Faith', 'text' => 'We believe in the teachings of Jesus Christ and the Catholic Church.'],
    ['title' => 'Community', 'text' => 'We foster genuine relationships and create a welcoming environment.'],
    ['title' => 'Service', 'text' => 'We serve the poor, care for creation, and work for justice and peace.'],
];
$sacraments = wp_parse_args(is_array($sacraments) ? $sacraments : [], [
    'title' => 'Sacraments',
    'subtitle' => 'We celebrate the seven sacraments of the Catholic Church.',
    'items' => [
        ['name' => 'Baptism'],
        ['name' => 'Confirmation'],
        ['name' => 'Eucharist'],
        ['name' => 'Reconciliation'],
    ],
]);
$saint = wp_parse_args(is_array($saint) ? $saint : [], [
    'title' => 'About St. Francis Of Assisi',
    'paragraphs' => [
        'St. Francis of Assisi was an Italian Catholic friar, deacon, mystic, and preacher.',
        'His example of humility, peace, and service continues to inspire millions around the world.',
    ],
]);

$history_archives = [
    ['title' => 'Groups & Societies', 'description' => 'Browse the societies and groups that have shaped parish life.', 'count' => wp_count_posts('society')->publish ?? 0, 'href' => stf_theme_archive_url('society', '/societies/'), 'cta' => 'View societies'],
    ['title' => 'Friars Who Served', 'description' => 'See the friars who have served the church from the beginning to date.', 'count' => wp_count_posts('friar')->publish ?? 0, 'href' => stf_theme_archive_url('friar', '/friars/'), 'cta' => 'View friars'],
    ['title' => 'Parish Pastoral Council Chairpersons', 'description' => 'Follow the timeline of chairpersons who have led the parish council.', 'count' => wp_count_posts('chairperson')->publish ?? 0, 'href' => stf_theme_archive_url('chairperson', '/chairpersons/'), 'cta' => 'View chairpersons'],
];
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0">
        <img src="<?php echo esc_url((string) $hero['background_image']); ?>" alt="Church Interior" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-[#6B2C2C]/92 via-[#8C6A5D]/78 to-[#6B2C2C]/46"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Our Story</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4"><?php echo esc_html((string) $hero['title']); ?></h1>
        <p class="text-lg text-white/70 mt-4"><?php echo esc_html((string) $hero['subtitle']); ?></p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="section-kicker">Our Parish</p>
                <h2 class="section-title mt-3"><?php echo esc_html((string) $story['title']); ?></h2>
                <?php foreach ((array) $story['paragraphs'] as $paragraph) : ?>
                    <p class="section-lead mt-4"><?php echo esc_html(is_array($paragraph) ? (string) ($paragraph['text'] ?? '') : (string) $paragraph); ?></p>
                <?php endforeach; ?>
            </div>
            <div class="relative">
                <div class="absolute -top-6 -left-6 h-24 w-24 rounded-full bg-amber-100"></div>
                <div class="relative h-[460px] rounded-3xl overflow-hidden shadow-[0_24px_52px_rgba(45,25,14,0.2)] media-frame">
                    <img src="<?php echo esc_url((string) $story['image']); ?>" alt="Our Story" class="w-full h-full object-cover" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] px-6 py-10 md:px-10 md:py-12 text-white shadow-[0_24px_52px_rgba(45,25,14,0.22)]">
            <div class="max-w-3xl">
                <p class="section-kicker text-amber-300">Church History</p>
                <h2 class="text-3xl md:text-4xl font-semibold mt-3">Explore the people and societies behind our parish story</h2>
                <p class="text-white/75 text-lg mt-4">Continue into dedicated archive pages covering church societies, friars who served, and Parish Pastoral Council chairpersons.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-10">
                <?php foreach ($history_archives as $archive) : ?>
                    <a href="<?php echo esc_url($archive['href']); ?>" class="glass block rounded-[1.75rem] border border-white/15 p-6 transition duration-300 hover:-translate-y-1 hover:border-amber-300/60">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Archive</p>
                                <h3 class="text-2xl font-semibold mt-3"><?php echo esc_html($archive['title']); ?></h3>
                            </div>
                            <span class="inline-flex min-w-16 items-center justify-center rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-amber-100"><?php echo esc_html((string) $archive['count']); ?> entries</span>
                        </div>
                        <p class="text-white/75 mt-4"><?php echo esc_html($archive['description']); ?></p>
                        <span class="inline-flex items-center gap-2 text-amber-200 font-semibold mt-6"><?php echo esc_html($archive['cta']); ?><span aria-hidden="true">&rarr;</span></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card p-8">
                <h3 class="text-2xl mb-4"><?php echo esc_html((string) $mission['title']); ?></h3>
                <p class="text-slate-600 text-lg"><?php echo esc_html((string) $mission['text']); ?></p>
            </div>
            <div class="card p-8">
                <h3 class="text-2xl mb-4"><?php echo esc_html((string) $vision['title']); ?></h3>
                <p class="text-slate-600 text-lg"><?php echo esc_html((string) $vision['text']); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($values as $value) : ?>
                <div class="card p-6 text-center">
                    <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold"><?php echo esc_html(strtoupper(substr((string) ($value['title'] ?? 'V'), 0, 1))); ?></div>
                    <h3 class="text-xl mb-3"><?php echo esc_html((string) ($value['title'] ?? '')); ?></h3>
                    <p class="text-slate-600"><?php echo esc_html((string) ($value['text'] ?? '')); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="section-kicker text-amber-300">Sacraments</p>
            <h2 class="text-3xl md:text-4xl font-semibold mt-3"><?php echo esc_html((string) $sacraments['title']); ?></h2>
            <p class="text-white/70 text-lg max-w-2xl mx-auto mt-4"><?php echo esc_html((string) $sacraments['subtitle']); ?></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <?php foreach ((array) ($sacraments['items'] ?? []) as $sacrament) : ?>
                <div class="glass p-6">
                    <h3 class="text-lg mb-2"><?php echo esc_html(is_array($sacrament) ? (string) ($sacrament['name'] ?? '') : (string) $sacrament); ?></h3>
                    <p class="text-white/70 text-sm leading-6 mt-2"><?php echo esc_html(is_array($sacrament) ? (string) ($sacrament['process'] ?? 'Contact the parish office for the full process and requirements.') : 'Contact the parish office for the full process and requirements.'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card p-8">
            <div class="text-center mb-8">
                <p class="section-kicker">Our Patron</p>
                <h2 class="section-title mt-3"><?php echo esc_html((string) $saint['title']); ?></h2>
            </div>
            <?php foreach ((array) $saint['paragraphs'] as $paragraph) : ?>
                <p class="text-slate-600 text-lg mb-4"><?php echo esc_html(is_array($paragraph) ? (string) ($paragraph['text'] ?? '') : (string) $paragraph); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
get_footer();

