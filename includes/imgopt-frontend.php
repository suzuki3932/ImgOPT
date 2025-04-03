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

    // Match
    $pattern = '/<img(.*?)src="(' . preg_quote($upload_baseurl, '/') . '.*?)\.(jpg|jpeg|png)"(.*?)(\/?)>/i';

    $content = preg_replace_callback($pattern, function ($matches) use ($upload_basedir, $upload_baseurl) {
        $original_url = $matches[2] . '.' . $matches[3];
        $original_url_parts = wp_parse_url($original_url);

        if (!$original_url_parts) {
            return $matches[0]; // URL parse error
        }

        $original_path = str_replace($upload_baseurl, $upload_basedir, $original_url);
        $webp_path = str_replace(['.jpg', 'jpeg', 'png'], '.webp', $original_path);

        if (file_exists($webp_path)) {
            $webp_url = str_replace(['.jpg', 'jpeg', 'png'], '.webp', $original_url);
            $new_img_tag = '<img src="' . esc_url($webp_url) . '"';

            // Attributes Processing
            $attribute_string = $matches[1];
            $allowed_attributes = array_merge(
                array('http', 'https'),
                array('data-.*')
            );
            $attributes = wp_kses_hair($attribute_string, $allowed_attributes);

            foreach ($attributes as $attribute) {
                $attribute_name = $attribute['name'];
                $attribute_value = $attribute['value'];

                if ($attribute_name === 'srcset') {
                    // srcset Attribute Processing
                    $srcset_urls = explode(',', $attribute_value);
                    $new_srcset = [];
                    foreach ($srcset_urls as $srcset_url) {
                        $srcset_url = trim($srcset_url);
                        if (strpos($srcset_url, $upload_baseurl) !== false) {
                            $original_srcset_url = strtok($srcset_url, ' ');
                            $webp_srcset_url = str_replace(['.jpg', 'jpeg', 'png'], '.webp', $original_srcset_url);
                            $new_srcset[] = str_replace($original_srcset_url, esc_url($webp_srcset_url), $srcset_url);
                        } else {
                            $new_srcset[] = $srcset_url;
                        }
                    }
                    $new_img_tag .= ' srcset="' . implode(', ', $new_srcset) . '"';
                } else {
                    $new_img_tag .= ' ' . $attribute_name . '="' . esc_attr($attribute_value) . '"';
                }
            }

            $new_img_tag .= $matches[4] . $matches[5] . '>';
            return $new_img_tag;
        } else {
            return $matches[0]; // If not exist .webp file, this function is returns before
        }
    }, $content);

    return $content;
}
add_filter('the_content', 'imgopt_replace_images');
