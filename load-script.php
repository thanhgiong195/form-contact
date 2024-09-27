<?php

function diver_customizer_enqueues()
{
    wp_enqueue_style('alpha_color_picker_css', get_template_directory_uri().'/lib/assets/colorPicker/alpha-color-picker.css', array( 'wp-color-picker' ));

    wp_enqueue_script('alpha_color_picker_js', get_template_directory_uri().'/lib/assets/colorPicker/alpha-color-picker.js', array( 'jquery', 'wp-color-picker' ), '', true);
}
add_action('customize_controls_print_footer_scripts', 'diver_customizer_enqueues');

function diver_enqueue_style()
{
    //maincss
    wp_enqueue_style('diver-main-style', get_template_directory_uri().'/css/style.min.css');

    wp_deregister_style('parent-style');

    if (strpos(get_stylesheet_directory_uri(), 'bc') !== "diver_child") {
        wp_enqueue_style('child-style', get_stylesheet_directory_uri()  . '/style.css', array(), null, 'all');
    }

    // if (get_bloginfo('version') >= "5.0.0") {
    //     wp_enqueue_style('diver-block-style', diver_minifier_file('/lib/functions/editor/gutenberg/blocks.css', '/lib/functions/editor/gutenberg/blocks.min.css', 'css'));
    // }


    // wp_enqueue_style('diver-block-style', get_template_directory_uri().'/lib/functions/editor/gutenberg/blocks.min.css',  array(), null, 'all');


    if (get_option('diver_option_firstview', '0') == '4') {
        wp_enqueue_style('YTPlayer', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/3.2.9/css/jquery.mb.YTPlayer.min.css', array(), null, 'all');
        wp_enqueue_script('ytplayer', '//cdnjs.cloudflare.com/ajax/libs/jquery.mb.YTPlayer/3.2.9/jquery.mb.YTPlayer.min.js', array(), false, true);
    }
}
add_action('wp_enqueue_scripts', 'diver_enqueue_style');


function diver_enqueue_script()
{
    // wp_deregister_script('jquery');

    // wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js', array(), '3.6.1', false);

    // $deer_inline_val = 'var ajaxurl = "'.admin_url('admin-ajax.php').'";';
    // wp_add_inline_script('jquery', $deer_inline_val, 'after');

    wp_enqueue_script('jquery');

    wp_enqueue_script('jquery-1.12.4.min', get_template_directory_uri()  . '/js/jquery-1.12.4.min.js', array(), '1.1', true);

    // lazysize
    wp_enqueue_script('unveilhooks', '//cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/plugins/unveilhooks/ls.unveilhooks.min.js', array(), false, true);
    wp_enqueue_script('lazysize', '//cdnjs.cloudflare.com/ajax/libs/lazysizes/4.1.5/lazysizes.min.js', array(), false, true);

    //slick
    wp_enqueue_script('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array('jquery'), false, true);
    // wp_enqueue_style('slickcss', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', array(), null, 'all');

    // tweenmax
    wp_enqueue_script('tweenmax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.2/TweenMax.min.js', array(), false, true);

    // lity
    wp_enqueue_script('lity', 'https://cdnjs.cloudflare.com/ajax/libs/lity/2.3.1/lity.min.js', array('jquery'), false, true);

    // tabbar
    // $is_tabbar = false;
    // for ($i = 1; $i <= get_option('diver_option_base_tabwidget', '1'); $i++) {
    //     if (is_active_sidebar('diver_tabwidget_'.$i)) {
    //         $is_tabbar = true;
    //         continue;
    //     }
    // }

    // if ($is_tabbar) {
    //     wp_enqueue_script('tabbar', get_template_directory_uri()  . '/lib/assets/tabbar/tabbar-min.js', array(), false, true);
    // }


    // prism

    // wp_enqueue_script('prism', get_template_directory_uri()  . '/lib/assets/prism/prism.js', array(), false, true);
    if (is_singular()) {
        global $post;

        $pattern = '/<code class="language-([^"]*)"/';
        if (preg_match($pattern, $post->post_content, $result)) {
            wp_enqueue_script('prism', get_template_directory_uri()  . '/lib/assets/prism/prism.js', array(), false, true);
            wp_enqueue_style('prism', get_template_directory_uri() . '/lib/assets/prism/prism.css', array(), null, 'all');
        }

        if (get_theme_mod('comment_form_style', 'none') == 'normal') {
            wp_enqueue_script('comment-reply');
        }
    }

    wp_enqueue_script('diver-main-js', get_template_directory_uri()  . '/js/diver.min.js', array('jquery','slick'), false, true);

    wp_enqueue_script('ajaxzip3-js', get_template_directory_uri()  . '/js/ajaxzip3.js', array('jquery'), '1.1', true);
    wp_enqueue_script('jquery-ui.min-js', get_template_directory_uri()  . '/js/jquery-ui.min.js', array('jquery'), '1.1', true);
    wp_enqueue_script('autoKana-js', get_template_directory_uri()  . '/js/jquery.autoKana.js', array('jquery'), '1.1', true);
    wp_enqueue_script('form-js', get_template_directory_uri()  . '/js/ozn-form.js', array('jquery'), '1.1', true);
}
add_action('wp_enqueue_scripts', 'diver_enqueue_script');


function diver_footer_styles()
{
    //fontAwesome
    wp_enqueue_style('fontAwesome4', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), null, 'all');
    // lity
    wp_enqueue_style('lity', 'https://cdnjs.cloudflare.com/ajax/libs/lity/2.3.1/lity.min.css', array(), null, 'all');
};
add_action('wp_footer', 'diver_footer_styles');

function diver_block_enqueue_scripts()
{
    wp_enqueue_style('diver-block-editor', get_template_directory_uri().'/css/editor-block.min.css', false, '1.0', 'all');
}
add_action('enqueue_block_editor_assets', 'diver_block_enqueue_scripts');

function admin_scripts($hook)
{
    // wp_deregister_script('jquery');

    // wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js', false, '3.6.0', true);

    // $deer_inline_val = 'var ajaxurl = "'.admin_url('admin-ajax.php').'";';
    // wp_add_inline_script('jquery', $deer_inline_val, 'after');

    wp_enqueue_script('jquery');

    // wp_enqueue_script('jquery');

    wp_enqueue_script('jquery-ui-sortable');

    wp_enqueue_style('diver-admin-style', get_template_directory_uri() . '/css/admin.min.css', array(), null, 'all');
    wp_enqueue_script('diver-admin-script', get_template_directory_uri() .'/js/admin.min.js', array('jquery','wp-color-picker'), false, true);

    wp_enqueue_media();
    wp_enqueue_script('mediauploader', get_template_directory_uri()  . '/lib/assets/mediaupload/mediauploader.js', array(), false, true);

    $current_screen = get_current_screen();
    if ((method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) || (function_exists('is_gutenberg_page') && is_gutenberg_page())) {
        if (get_option('diver_editor_auxiliary_gutenberg', '1')) {
            wp_enqueue_style('diver-auxiliary-style', get_template_directory_uri() .'/css/auxiliary.min.css', array(), null, 'all');
            wp_enqueue_script('diver-auxiliary-script', get_template_directory_uri() .'/js/auxiliary.min.js', array('jquery'), false, true);

            wp_enqueue_script('iconpicker', get_template_directory_uri()  . '/lib/assets/iconpicker/simple-iconpicker.js', array(), false, true);
            wp_enqueue_style('iconpicker', get_template_directory_uri()  . '/lib/assets/iconpicker/simple-iconpicker.css', array(), null, 'all');
        } elseif (get_option('diver_editor_auxiliary_blocks', '1')) {
            wp_enqueue_style('diver-auxiliary-style', get_template_directory_uri() .'/css/auxiliary-blocks.min.css', array(), null, 'all');
        }

        if (get_option('diver_editor_auxiliary_gutenberg', '1') || get_option('diver_editor_auxiliary_blocks', '1')) {
            wp_enqueue_style('fontAwesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), null, 'all');
        }
    } else {
        // wp_enqueue_script('diver-admin-script', get_template_directory_uri() .'/js/admin.min.js', array('jquery','wp-color-picker'), false, true);
        wp_enqueue_script('diver-quicktag', get_template_directory_uri() .'/lib/assets/quicktags.js', array( 'jquery', 'quicktags' ), '1.0.0', true);

        wp_enqueue_style('fontAwesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), null, 'all');
        wp_enqueue_script('iconpicker', get_template_directory_uri()  . '/lib/assets/iconpicker/simple-iconpicker.js', array(), false, true);
        wp_enqueue_style('iconpicker', get_template_directory_uri()  . '/lib/assets/iconpicker/simple-iconpicker.css', array(), null, 'all');

        add_editor_style(add_query_arg('action', 'dynamic_mce_styles', admin_url('admin-ajax.php')));

        if (get_option('diver_editor_auxiliary_classic', '1')) {
            add_editor_style(get_template_directory_uri() .'/css/auxiliary.min.css');

            wp_enqueue_script('diver-auxiliary-script', get_template_directory_uri() .'/js/auxiliary.min.js', array('jquery'), false, true);
        }
    }
}
add_action('admin_enqueue_scripts', 'admin_scripts');

add_filter('style_loader_src', 'add_file_ver_to_css_js');
add_filter('script_loader_src', 'add_file_ver_to_css_js');
if (!function_exists('add_file_ver_to_css_js')) {
    function add_file_ver_to_css_js($src)
    {
        if (strpos($src, site_url()) !== false) {
            // //Wordpressのバージョンを除去する場合
            // if ( strpos( $src, 'ver=' ) ){
            //   $src = remove_query_arg( 'ver', $src );
            // }
            //クエリーを削除したファイルURLを取得
            $removed_src = preg_replace('{\?.+$}i', '', $src);
            $resource_file = str_replace(site_url('/'), ABSPATH, $removed_src);
            $src = add_query_arg('theme', wp_theme_version(), $src);
        }
        return $src;
    }
}

if (!is_admin()) {
    function diver_remove_script_type($tag)
    {
        if (preg_match('/(diver\.min\.js|TweenMax|lity|tabbar|sticky|prism|YTPlayer)/', $tag)) {
            $tag = str_replace("type='text/javascript'", "defer ", $tag);
        } else {
            $tag = str_replace("type='text/javascript'", "", $tag);
        }

        if (is_mobile()) {
            return $tag;
        }
        return str_replace(' src', 'src', $tag);
    }
    add_filter('script_loader_tag', 'diver_remove_script_type');

    function diver_remove_style_type($tag)
    {
        $tag = preg_replace(array( "| type='.+?'s*|","| id='.+?'s*|", '| />|' ), array( ' ',' ', '>' ), $tag);
        return $tag;
    }
    add_filter('style_loader_tag', 'diver_remove_style_type');




    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
