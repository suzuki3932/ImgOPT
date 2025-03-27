<?php
// imgopt-frontend.php

function imgopt_replace_images($content) {
    if (is_admin()) {
        return $content;
    }

    $show_optimized = get_option('imgopt_show_optimized');
    if ($show_optimized != 1) {
        return $content;
    }

    $upload_dir = wp_upload_dir();
    $upload_baseurl = $upload_dir['baseurl'];
    $upload_basedir = $upload_dir['basedir'];

    // サイト内部の画像のみを対象とする正規表現
    $pattern = '/<img(.*?)src="(' . preg_quote($upload_baseurl, '/') . '.*?)\.(jpg|jpeg|png)"(.*?)>/i';

    $content = preg_replace_callback($pattern, function ($matches) use ($upload_basedir) {
        $original_url = $matches[2] . '.' . $matches[3];
        $original_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $original_url);
        $webp_path = str_replace(['.jpg', 'jpeg', 'png'], '.webp', $original_path);
        if (file_exists($webp_path)) {
            $webp_url = str_replace(['.jpg', 'jpeg', 'png'], '.webp', $original_url);
            return '<img' . $matches[1] . 'src="' . esc_url($webp_url) . '"' . $matches[4] . '>';
        } else {
            return $matches[0]; // .webpファイルが存在しない場合は元の<img>タグを返す
        }
    }, $content);

    return $content;
}
add_filter('the_content', 'imgopt_replace_images');
?>