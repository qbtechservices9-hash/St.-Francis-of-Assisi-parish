<?php

declare(strict_types=1);

get_header();
$site_settings = stf_theme_site_options();
?>
<section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
        <p class="section-kicker text-amber-300">Visit Us</p>
        <h1 class="text-4xl md:text-5xl font-semibold mt-4">Contact &amp; Directions</h1>
        <p class="text-lg text-white/75 mt-4">We would love to hear from you and help you plan your visit.</p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[0.9fr,1.1fr] gap-8">
            <aside class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-2xl font-semibold">Parish Office</h2>
                    <div class="space-y-4 mt-5 text-sm text-slate-700">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Phone</p>
                            <a class="text-amber-700 font-semibold" href="tel:<?php echo esc_attr($site_settings['contact_phone']); ?>"><?php echo esc_html($site_settings['contact_phone']); ?></a>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Email</p>
                            <a class="text-amber-700 font-semibold" href="mailto:<?php echo esc_attr($site_settings['contact_email']); ?>"><?php echo esc_html($site_settings['contact_email']); ?></a>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Location</p>
                            <p class="font-semibold text-[#2b1a12]"><?php echo esc_html($site_settings['church_city']); ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-soft w-full mt-6" data-directions data-destination="<?php echo esc_attr($site_settings['church_map_destination']); ?>">Use My Location</button>
                </div>
                <div class="surface-soft p-6">
                    <p class="section-kicker">Service Times</p>
                    <p class="text-slate-700 mt-3"><?php echo esc_html($site_settings['service_sunday']); ?></p>
                    <p class="text-slate-600 mt-2"><?php echo esc_html($site_settings['service_wednesday']); ?></p>
                    <p class="text-slate-600 mt-2"><?php echo esc_html($site_settings['service_friday']); ?></p>
                </div>
            </aside>
            <div class="card p-7 md:p-8">
                <p class="section-kicker">Send a Message</p>
                <h2 class="section-title mt-3">We are here to help</h2>
                <div class="mt-6">
                    <?php echo do_shortcode('[stf_contact_form]'); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
