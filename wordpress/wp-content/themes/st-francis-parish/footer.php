<?php

declare(strict_types=1);

$site_settings = stf_theme_site_options();
?>
</main>
<footer class="bg-[#1f140e] text-[#f3e7d8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-8 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-white/10 p-1">
                            <img src="<?php echo stf_theme_logo_url(); ?>" alt="<?php echo esc_attr($site_settings['church_name']); ?> logo" class="w-10 h-10 rounded-full object-cover border border-white/20" loading="lazy" />
                        </div>
                        <div>
                            <div class="font-semibold text-white"><?php echo esc_html($site_settings['church_name']); ?></div>
                            <div class="text-xs text-[#cdb8a7]"><?php echo esc_html($site_settings['church_location']); ?></div>
                        </div>
                    </div>
                    <p class="text-sm text-[#cdb8a7]">A Catholic family walking in faith, serving in love, and building hope in our community.</p>
                </div>

                <div>
                    <h3 class="font-semibold text-white mb-4">Explore</h3>
                    <ul class="space-y-2 text-sm text-[#d9c8b9]">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_page_url('about', '/about/')); ?>" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_archive_url('ministry', '/ministries/')); ?>" class="hover:text-white transition-colors">Ministries</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_archive_url('event', '/events/')); ?>" class="hover:text-white transition-colors">Events</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_archive_url('sermon', '/sermons/')); ?>" class="hover:text-white transition-colors">Sermons</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_archive_url('gallery_item', '/gallery/')); ?>" class="hover:text-white transition-colors">Gallery</a></li>
                        <li><a href="<?php echo esc_url(stf_theme_page_url('contact', '/contact/')); ?>" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-white mb-4">Worship Schedule</h3>
                    <ul class="space-y-2 text-sm text-[#d9c8b9]">
                        <li><?php echo esc_html($site_settings['service_sunday']); ?></li>
                        <li><?php echo esc_html($site_settings['service_wednesday']); ?></li>
                        <li><?php echo esc_html($site_settings['service_friday']); ?></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-white mb-4">Reach Us</h3>
                    <ul class="space-y-2 text-sm text-[#d9c8b9]">
                        <li><?php echo esc_html($site_settings['church_city']); ?></li>
                        <li><?php echo esc_html($site_settings['contact_phone']); ?></li>
                        <li><?php echo esc_html($site_settings['contact_email']); ?></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 mt-10 pt-6 text-center text-xs text-[#cdb8a7]">
                <p>&copy; <?php echo esc_html((string) gmdate('Y')); ?> <?php echo esc_html($site_settings['church_name']); ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
