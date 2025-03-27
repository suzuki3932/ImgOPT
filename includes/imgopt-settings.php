<?php
// imgopt-settings.php

function imgopt_settings_page() {
    add_submenu_page(
        'imgopt',
        __('ImgOPT Settings', 'imgopt'),
        __('Settings', 'imgopt'),
        'manage_options',
        'imgopt-settings',
        'imgopt_settings_page_callback'
    );
}
add_action('admin_menu', 'imgopt_settings_page');

function imgopt_settings_page_callback() {
    ?>
    <div class="wrap">
        <h2><?php echo esc_html(__('ImgOPT Settings', 'imgopt')); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('imgopt_settings_group');
            do_settings_sections('imgopt-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function imgopt_register_settings() {
    register_setting(
        'imgopt_settings_group',
        'imgopt_show_optimized',
        'imgopt_sanitize_show_optimized' // サニタイズコールバック関数を指定
    );
    add_settings_section(
        'imgopt_settings_section',
        esc_html(__('Display Optimized Images', 'imgopt')),
        'imgopt_settings_section_callback',
        'imgopt-settings'
    );
    add_settings_field(
        'imgopt_show_optimized',
        esc_html(__('Show Optimized Images', 'imgopt')),
        'imgopt_show_optimized_field_callback',
        'imgopt-settings',
        'imgopt_settings_section'
    );
}
add_action('admin_init', 'imgopt_register_settings');

function imgopt_sanitize_show_optimized($input) {
    return isset($input) ? 1 : 0; // チェックボックスがチェックされている場合は1、そうでない場合は0を返す
}

function imgopt_settings_section_callback() {
    echo esc_html(__('Enable or disable optimized image display on the frontend.', 'imgopt'));
    echo '<p>' . esc_html(__('If enabled, optimized images will be displayed on the frontend.', 'imgopt')) . '</p>';
}

function imgopt_show_optimized_field_callback() {
    $option = get_option('imgopt_show_optimized', 1); // デフォルト値を1に設定
    ?>
    <input type="checkbox" name="imgopt_show_optimized" value="1" <?php checked(1, $option); ?> />
    <?php
}
?>