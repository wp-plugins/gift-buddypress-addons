<div class="wrap">
    <h2>WP Plugin Template</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('gift_buddypress_template-group'); ?>
        <?php @do_settings_fields('gift_buddypress_template-group'); ?>

        <?php do_settings_sections('gift_buddypress_template'); ?>

        <?php @submit_button(); ?>
    </form>
</div>