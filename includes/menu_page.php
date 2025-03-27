<?php
function imgopt_menu() {
    add_menu_page(
        'ImgOPT',
        'ImgOPT',
        'manage_options',
        'imgopt',
        'imgopt_menu_callback',
        'dashicons-format-image'
    );
}
add_action('admin_menu', 'imgopt_menu');

function imgopt_optimize($dir) {
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'];

    // ディレクトリトラバーサル防止
    $real_dir = realpath($dir);
    if (strpos($real_dir, $upload_path) !== 0) {
        return false; // 不正なディレクトリ
    }

    $files = scandir($real_dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filepath = $real_dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filepath)) {
            imgopt_optimize($filepath);
        } else {
            $filetype = wp_check_filetype($filepath);
            if (isset($filetype['ext']) && in_array(strtolower($filetype['ext']), ['jpg', 'jpeg', 'png'])) {
                $info = pathinfo($filepath);
                $output_filename = sanitize_file_name($info['filename']) . '.webp';
                $output_filepath = $real_dir . DIRECTORY_SEPARATOR . $output_filename;

                if (strtolower($filetype['ext']) === 'jpg' || strtolower($filetype['ext']) === 'jpeg') {
                    $image = @imagecreatefromjpeg($filepath);
                } else {
                    $image = @imagecreatefrompng($filepath);
                }
                if ($image) {
                    @imagewebp($image, $output_filepath, 80);
                    imagedestroy($image);
                } else {
                    
                    return false;
                }
            }
        }
    }
    return true;
}

function imgopt_menu_callback() {
    ?>
    <h1><?php echo esc_html(__('Optimize Images - ImgOPT', 'imgopt')); ?></h1>
    <p><?php echo esc_html(__('Caution: please backup wp-content/uploads.', 'imgopt')); ?></p>
    <form method="post" action="">
        <?php wp_nonce_field('imgopt_action', 'imgopt_nonce'); ?>
        <input type="hidden" value="ok" name="optimize">
        <input type="submit" class="button" value="<?php echo esc_html(__('Optimize Now', 'imgopt')); ?>">
    </form>
    <?php
    if (isset($_POST['optimize']) && $_POST['optimize'] === 'ok') {
        if (isset($_POST['imgopt_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['imgopt_nonce'])), 'imgopt_action')) { // サニタイズ
            echo esc_html(__('Optimizing images...', 'imgopt'));
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'];
            if (imgopt_optimize($upload_path)) {
                echo esc_html(__('Images optimized successfully.', 'imgopt'));
            } else {
                echo esc_html(__('Image optimization failed.', 'imgopt'));
            }
        } else {
            echo esc_html(__('Bad Request.', 'imgopt'));
        }
    }
}
?>