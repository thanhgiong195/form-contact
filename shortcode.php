<?php

// 内部リンク - ブログカード化のため
function diver_run_shortcode_before($content)
{
    global $shortcode_tags;
    $orig_shortcode_tags = $shortcode_tags;  // 現在のショートコードの登録情報をすべてバックアップ
    remove_all_shortcodes();  // 現在のショートコードの登録情報を一時的にすべて削除
    add_shortcode('common_content', 'common_content');  // フィルターの前に実行するショートコードを登録
    $content = do_shortcode($content);  // 登録したショートコードの実行
    $shortcode_tags = $orig_shortcode_tags;  // バックアップしておいたショートコードの登録情報を復元
    return $content;
}
// add_filter('the_content', 'diver_run_shortcode_before', 7);

// Column shortcord
function colwrap($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="row">'.$content.'</div>';
}
add_shortcode('colwrap', 'colwrap');

// 1/2
function col2($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col2">' . $content . '</div>';
}
add_shortcode('col2', 'col2');

// 1/2 sp
function col2_sp($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col2 col2_sp">' . $content . '</div>';
}
add_shortcode('col2_sp', 'col2_sp');

// 1/3
function col3($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col3">' . $content . '</div>';
}
add_shortcode('col3', 'col3');

// 2/3
function col3_2($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col3_2">' . $content . '</div>';
}
add_shortcode('col3_2', 'col3_2');

// 1/4
function col4($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col4">' . $content . '</div>';
}
add_shortcode('col4', 'col4');

// 3/4
function col4_3($atts, $content = null)
{
    $content = do_shortcode($content);
    return '<div class="col4_3">' . $content . '</div>';
}
add_shortcode('col4_3', 'col4_3');

// clear
function columnclear()
{
    return '<div class="clearfix clearfloat"></div>';
}
add_shortcode('clear', 'columnclear');

// border
function border($atts, $content = null)
{
    extract(shortcode_atts(array(
        'color' => '#ccc',
        'height' => '2'
    ), $atts));
    return '<div class="border" style="background:'.$color.';height:'.$height.'px"></div>';
}
add_shortcode('border', 'border');

// aside
function aside($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'type' => 'normal',
    ), $atts));
    if ($type == 'normal') {
        $icon = 'fa fa-comments-o';
    } elseif ($type == 'warning') {
        $icon = 'fa fa-exclamation-triangle';
    }
    return '<div class="aside-'.$type.'"><span><i class="'.$icon.'" aria-hidden="true"></i></span>　'. $content .'</div>';
}
add_shortcode('aside', 'aside');

//button
function button($atts, $content = null)
{
    extract(shortcode_atts(array(
        'type' => '',
        'url' => '',
        'color' => '',
        'target' => ''
    ), $atts));
    preg_match("|<a href=\"(.*?)\".*?>(.*?)</a>|mis", $content, $matches);
    if ($matches) {
        $url = $matches[1];
        $content = $matches[2];
    }

    if ($target) {
        $target = 'target="'.$target.'"';
    }
    return '<div class="button '.$type.'"><a href="'.$url.'" '.$target.' style="background:'.$color.';color:#fff;" >'.$content.'</a></div>';
}
add_shortcode('btn', 'button');


// voice
function voice($atts, $content = null)
{
    extract(shortcode_atts(array(
        'type' => 'left',
        'icon' => '',
        'name' => '',
        'color' => ''
    ), $atts));
    $type = ($type == 'l') ? 'left' : $type;
    $type = ($type == 'r') ? 'right' : $type;

    return '<div class="voice clearfix '.$type.'"><div class="icon"><img src="'.$icon.'"><div class="name">'.$name.'</div></div><div class="text sc_balloon '.$type.' '.$color.'">' . $content . '</div></div>';
}
add_shortcode('voice', 'voice');


// SNS
function facebook($atts)
{
    $fb_url = get_option('diver_sns_facebook_url', get_theme_mod('facebook_profile'));
    extract(shortcode_atts(array(
        'type' => 'color',
    ), $atts));
    return '<div class="sc_facebook sc_sns '.$type.'"><a href="'.$fb_url.'"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></a></div>';
}
add_shortcode('facebook', 'facebook');

function twitter($atts)
{
    $tw_url = get_option('diver_sns_twitter_url', get_theme_mod('twitter_url'));
    extract(shortcode_atts(array(
        'type' => 'color',
    ), $atts));
    return '<div class="sc_twitter sc_sns '.$type.'"><a href="'.$tw_url.'"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></a></div>';
}
add_shortcode('twitter', 'twitter');


function instagram($atts)
{
    $insta_url = get_option('diver_sns_instagram_url', get_theme_mod('instagram_url'));
    extract(shortcode_atts(array(
        'type' => 'color',
    ), $atts));
    return '<div class="sc_instagram sc_sns '.$type.'"><a href="'.$insta_url.'"><i class="fa fa-instagram fa-fw" aria-hidden="true"></i></a></div>';
}
add_shortcode('instagram', 'instagram');

function googleplus($atts)
{
    $googleplus_url = get_theme_mod('googleplus_url');
    extract(shortcode_atts(array(
        'type' => 'color',
    ), $atts));
    return '<div class="sc_googleplus sc_sns '.$type.'"><a href="'.$googleplus_url.'"><i class="fa fa-google-plus fa-fw" aria-hidden="true"></i></a></div>';
}
add_shortcode('googleplus', 'googleplus');

// balloon
function balloon($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'type' => 'left',
    ), $atts));
    return '<div class="sc_balloon '.$type.'">'. $content .'</div>';
}
add_shortcode('balloon', 'balloon');


// badge
function badge($atts)
{
    extract(shortcode_atts(array(
        'name' => '参考',
        'color' => '#333'
    ), $atts));

    return '<span class="badge" style="background:'.$color.'">'.$name.'</span>';
}
add_shortcode('badge', 'badge');

// blockquote
function blockquote($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'url' => '',
        'title' => '',
    ), $atts));
    return '<blockquote>'. $content .'<div class="blockquote_ref"><div><a href="'.$url.'" target="_blank">'.$title.'</a></div></div></blockquote>';
}
add_shortcode('bq', 'blockquote');

// marker
function marker($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'color' => '#ffff66',
    ), $atts));
    return '<span class="sc_marker" style="background: linear-gradient(transparent 50%, '.$color.' 50%);">'. $content .'</span>';
}
add_shortcode('marker', 'marker');

// fontsize
function fontsize($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'size' => '4',
    ), $atts));
    return '<span class="fontsize '.$size.'">'. $content .'</span>';
}
add_shortcode('fontsize', 'fontsize');

// color
function color($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'color' => '#ff3333',
    ), $atts));
    return '<span class="fontcolor" style="color:'.$color.';">'. $content .'</span>';
}
add_shortcode('color', 'color');

// bgcolor
function bgcolor($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'color' => '#eee',
    ), $atts));
    return '<span class="fontbackground" style="background:'.$color.';">'. $content .'</span>';
}
add_shortcode('bgcolor', 'bgcolor');

function getpost($atts)
{
    $retHTML='';
    extract(shortcode_atts(array(
        'id'     => '',
        'title' => '',
        "cat_name" => '0',
        'date' => '1',
        'description' => '1',
        'target' => ''
    ), $atts));

    $image = get_diver_thumb_img($id, apply_filters('diver_scgetpost_thumb_img_size', 'thumbnail'));
    $post = get_post($id);
    if ($post) {
        $post->post_content;

        $target_ = ($target) ? ' target="'.$target.'"' : '';

        $retHTML.= '<div class="sc_getpost">';
        $retHTML.= '<a class="clearfix" href="'.get_permalink($id).'" '.$target_.'>';
        if ($image) {
            if ($cat_name) {
                $cat = get_the_category($post->ID);
                $cat = $cat[0];
                $image .=  '<span style="background:'.get_theme_mod($cat->slug).'" class="sc_getpost_cat">'.$cat->cat_name.'</span>';
            }
            $retHTML.= '<div class="sc_getpost_thumb post-box-thumbnail__wrap">'.$image.'</div>';
        }
        if ($title) {
            $retHTML .= '<div class="title"><span class="badge">'.$title.'</span>'.esc_html(get_the_title($id)).'</div>';
        } else {
            $retHTML.= '<div class="title">'.esc_html(get_the_title($id)).'</div>';
        }
        if ($date) {
            $date_sort = apply_filters('diver_scgetpost_date_sort', get_theme_mod('post_sort', 'published'));
            $date = ($date_sort=='published') ? get_post_time('Y.n.j', null, $post->ID, true) : get_post_modified_time('Y.n.j', null, $post->ID, true);
            $retHTML.= '<div class="date">'.$date.'</div>';
        }
        $retHTML.= ($description) ? '<div class="substr">'.get_diver_excerpt($id, 150). '</div>' : '';
        $retHTML.= '</a></div>';

        wp_reset_postdata();

        return $retHTML ;
    }
}
add_shortcode('getpost', 'getpost');

// slidetoggle
function toggle($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'title' => '',
    ), $atts));
    return '<div class="sc_toggle_box"><div class="sc_toggle_title">'.$title.'</div><div class="sc_toggle_content">'. $content .'</div></div>';
}
add_shortcode('toggle', 'toggle');

// barchart
function barchart($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'width' => '',
        'color' => '',
    ), $atts));
    return '<span class="barchart" style="width:'.$width.';background:'.$color.';">'. $content .'</span>';
}
add_shortcode('barchart', 'barchart');

// star
function review_star($atts)
{
    extract(shortcode_atts(array(
        'score' => '',
        'size' => '',
    ), $atts));
    $width = '180';
    $height = '33';
    $scorewidth = '36';
    if ($size=='big') {
        $height = '51';
        $width = '280';
        $scorewidth = '56';
    } elseif ($size=='small') {
        $height = '22';
        $width = '120';
        $scorewidth = '24';
    }

    if ($score) {
        $score = ($score<=5&&$score>=0) ? $score*$scorewidth : 5 ;
    }

    return '<div class="review_star" style="background-size:'.$width.'px;height:'.$height.'px;width:'.$width.'px"><div class="star" style="width:'.$score.'px;background-size:'.$width.'px;height:'.$height.'px;"></div></div>';
}
add_shortcode('star', 'review_star');

// function do_categories($atts, $content = null) {



// }
// add_shortcode("categories", "do_categories");


//getArticle
function getArticle($atts, $content = null)
{
    extract(shortcode_atts(array(
      "num" => '5',
      "height" => 'auto',
      "category" => '',
      "cat_name" => '0',
      "date" => '0',
      "excerpt" =>  '',
      "type" =>  '',
      "orderby" => 'post_date',
      "order" => 'DESC',
      "tag" => '',
      "img" => '1',
      "author" => '0',
      "layout" => 'simple',
      "id" => '',
      "rank" => '',
      'target' => ''
    ), $atts));

    // 処理中のpost変数をoldpost変数に退避
    global $post;
    $oldpost = $post;

    $myposts = [];

    if ($id) {
        $myposts = explode(",", $id);
    } elseif ($rank) {

        if(class_exists('DiverPopularPosts')){
            $DiverPopularPosts = new DiverPopularPosts();
            if($result = $DiverPopularPosts->init($rank)) {

                $counter = 0;
                foreach($result as $post_id => $args) {

                    if(get_post_status($post_id) !== 'publish') {
                        continue;
                    }
                    $counter++;
                    if($counter > $num) {
                        break;
                    }

                    $myposts[$post_id] = $args;
                }
            }
        }

        $rank = "rank";
    } else {
        $args = array(
        'numberposts' => $num,
        'order' => $order,
        'orderby' => $orderby,
        'category' => $category,
        'tag' => $tag,
        'post_type' => apply_filters('diver_getArticle_post_type', 'post')
        );

        // 記事データ取得
        $myposts = get_posts($args);
    }

    if ($myposts) {
        $count = 0;

        $retHtml = '<ul class="sc_article wow animate__fadeInUp animate__animated '.$type.' '.$layout.' '.$rank.'" style="height:'.$height.';">';

        foreach ($myposts as $postid => $post):

            if ($id) {
                $post = get_post($post);
            } elseif ($rank) {
                $post = get_post($postid);
            }
            setup_postdata($post);

            if ($post) {
                $inner = '';

                if ($layout=="grid"||$layout=="list") {
                    $target_ = ($target) ? ' target="'.$target.'"' : '';

                    $inner .= '<a class="clearfix" href="' . get_permalink() . '" '.$target_ .'><li>';

                    if ($img) {
                        $inner .= '<figure class="post_list_thumb post-box-thumbnail__wrap">';
                        $inner .= get_diver_thumb_img($post->ID, apply_filters('diver_widget_scArticle_post_thumb', 'medium'));
                        if ($cat_name) {
                            $cat = get_the_category($post->ID);
                            $cat = $cat[0];
                            $inner.= '<span style="background:'.get_theme_mod($cat->slug).'" class="sc_article_cat">'.$cat->cat_name.'</span>';
                        }
                        $inner .= '</figure>';
                    }

                    $inner.= '<div class="meta">';

                    $inner.= '<div class="sc_article_title">' . the_title("", "", false) . '</div>';

                    if ($excerpt) {
                        $inner .= '<div class="sc_article_excerpt">';
                        $inner .= get_diver_excerpt($post->ID, get_theme_mod('post_excerpt_count', 160));
                        $inner .= '</div>';
                    }

                    if ($date) {
                        $inner .= '<div class="sc_article_date">';
                        $inner .= get_the_time(get_option('date_format'));
                        $inner .= '</div>';
                    }

                    // if($author){
                //     $author_id = get_post_field( 'post_author', $post->ID );

                //     $retHtml .= '<div class="sc_article_author">';
                //     $retHtml .= '<span class="post-author-thum">'.get_avatar($author_id, 30).'</span>';
                //     $retHtml .= "<span class='post-author-name'>".get_the_author_meta('display_name',$author_id )."</span>";
                //     $retHtml .= '</div>';
                    // }

                    $inner.= '</div>';

                    $inner.= '</li></a>';
                } else {
                    $inner .= '<li class="clearfix">';

                    if ($date) {
                        $inner .= '<div class="sc_article_date">';
                        $inner .= get_the_time(get_option('date_format'));
                        $inner.= '</div>';
                    }

                    if ($cat_name) {
                        $cat = get_the_category($post->ID);
                        $cat = $cat[0];
                        $inner.= '<a href="'.get_category_link($cat->cat_ID).'" rel="category tag" style="background:'.get_theme_mod($cat->slug).'" class="sc_article_cat">'.$cat->cat_name.'</a>';
                    }

                    $inner.= '<div class="sc_article_title">';
                    $inner.= '<a href="' . get_permalink() . '" target="'.$target.'">' . the_title("", "", false) . '</a>';

                    $inner.= '</div>';

                    $inner.= '</li>';
                }

                if ($rank) {
                    $count++;
                    if ($num <= $count) {
                        break;
                    }
                }

                $retHtml .= apply_filters('diver_sc_artice_inner', $inner, $post, $atts);
            }

        endforeach;
        wp_reset_postdata();

        $retHtml .= '</ul>';
    } else {
        // 記事がない場合↓
        $retHtml='<p>記事がありません。</p>';
    }


    // oldpost変数をpost変数に戻す
    $post = $oldpost;

    return $retHtml;
}
// 呼び出しの指定
add_shortcode("article", "getArticle");

//Diver Rank
function diver_af_ranking($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'rank' => '',
        'star' => '',
        'title' => '',
        'minihead' => '',
        'desc' => '',
        'buy_link' => '',
        'buy_txt' => '',
        'more_link' => '',
        'more_txt' => '',
        'rem' => '',
        'src' => ''
    ), $atts));

    $starheight = '14';
    $starwidth = '80';
    $scorewidth = '16';
    if ($star) {
        $score = ($star<=5&&$star>=0) ? $star*$scorewidth : 5 ;
    }


    $html = '<div class="diver_af_ranking_wrap"><div class="diver_af_ranking">';

    $html .= '<div class="rank_h '.$rank.'"><div class="rank_title">'.$title;

    if ($star!=0) {
        $html .= '<div class="review_star" style="background-size:'.$starwidth.'px;height:'.$starheight.'px;width:'.$starwidth.'px">
                        <div class="star" style="width:'.$score.'px;background-size:'.$starwidth.'px;height:'.$starheight.'px;"></div>
                </div>';
    }

    $html .= '</div></div><div class="rank_desc_wrap clearfix">';

    if (!empty($src)) {
        $html .= '<div class="rank_img"><img src="'.$src.'"></div>';
        $html .= '<div class="rank_desc">';
    } else {
        $html .= '<div class="rank_desc" style="margin:0;padding:0">';
    }


    if (!empty($minihead)) {
        $html .= '<div class="rank_minih">'.$minihead.'</div>';
    }
    if (!empty($desc)) {
        $html .= '<div class="desc">'.$desc.'</div>';
    }
    $html .= '</div></div></div>';

    if (!empty($rem)) {
        $html .= '<div class="rank_rem">'.$rem.'</div>';
    }
    $html .= '<div class="rank_btn_wrap row">';
    if (!empty($buy_link)) {
        if (!empty($more_link)) {
            $html .= '<div class="rank_buy_link"><a href="'.$buy_link.'" target="_blank">'.$buy_txt.'</a></div>';
        } else {
            $html .= '<div class="rank_buy_link" style="width:100%"><a href="'.$buy_link.'" target="_blank">'.$buy_txt.'</a></div>';
        }
    }
    if (!empty($more_link)) {
        if (!empty($buy_link)) {
            $html .= '<div class="rank_more_link"><a href="'.$more_link.'" target="_blank">'.$more_txt.'</a></div>';
        } else {
            $html .= '<div class="rank_more_link" style="width:100%"><a href="'.$more_link.'" target="_blank">'.$more_txt.'</a></div>';
        }
    }
    $html .= '</div></div>';

    return $html;
}
add_shortcode('diver_af_rank', 'diver_af_ranking');

//Diver voice
function diver_voice($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'star' => '',
        'title' => '',
        'name' => '',
        'src' => ''
    ), $atts));

    $starheight = '14';
    $starwidth = '80';
    $scorewidth = '16';
    if ($star) {
        $score = ($star<=5&&$star>=0) ? $star*$scorewidth : 5 ;
    }

    $html = '<div class="diver_voice_wrap clearfix">';

    if (!empty($src)) {
        $html .= '<img src="'.$src.'" class="diver_voice_icon">';
        $html .= '<div class="diver_voice">';
    } else {
        $html .= '<div class="diver_voice" style="width:100%">';
    }


    $html .= '<div class="diver_voice_title">'.$title;

    if ($star!=0) {
        $html .= '<div class="review_star" style="background-size:'.$starwidth.'px;height:'.$starheight.'px;width:'.$starwidth.'px">
                        <div class="star" style="width:'.$score.'px;background-size:'.$starwidth.'px;height:'.$starheight.'px;"></div>
                </div>';
    }

    $html .= '</div>';

    if (!empty($content)) {
        $html .= '<div class="diver_voice_content">'.$content.'</div>';
    }

    if (!empty($name)) {
        $html .= '<div class="diver_voice_name">'.$name.'</div>';
    }

    $html .= '</div></div>';

    return $html;
}
add_shortcode('diver_voice', 'diver_voice');

//Diver relpost
function diver_relpost($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'id' => '',
        'title' => '',
        'target' => ''
    ), $atts));

    $target_ = ($target) ? ' target="'.$target.'"' : '';

    $html = '<div class="editer_diver_kiji">';
    if (!empty($title)) {
        $html .= '<div class="editer_diver_kiji_title">'.$title.'</div>';
    }
    $html .= '<ul class="diver_rel_kiji">';

    $ids = explode(',', $id);
    foreach ($ids as $id) {
        if ($id) {
            $html.= '<li><a href="'.get_permalink($id).'" title="'.get_the_title($id).'"'.$target_.'>'.get_the_title($id).'</a></li>';
        }
    }
    $html .= '</ul></div>';


    return $html;
}
add_shortcode('diver_relpost', 'diver_relpost');

//Diver QA
function diver_qa($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'q' => '',
    ), $atts));

    $html = '<div class="diver_qa"><div class="diver_question"><div>'.$q.'</div></div><div class="diver_answer"><div>'.$content.'</div></div></div>';


    return $html;
}
add_shortcode('diver_qa', 'diver_qa');

//frame
function sc_frame($atts, $content = null)
{
    $content = do_shortcode($content);
    extract(shortcode_atts(array(
        'color' => '',
        'title' => '',
    ), $atts));

    $html = '<div class="sc_frame" style="border-color:'.$color.'">';

    if ($title) {
        $html .= '<span class="sc_frame_before" style="background:'.$color.'">'.$title.'</span>';
    }

    $html .= $content.'</div>';

    return $html;
}
add_shortcode('frame', 'sc_frame');

//gooalead
function diver_gad($atts)
{
    $html = diver_option_get_adsence();

    return $html;
}
add_shortcode('dgad', 'diver_gad');

//common
function common_content($atts)
{
    extract(shortcode_atts(array(
        'id' => '',
        'type' => ''
    ), $atts));
    if ($post = get_post($id)) {
        if ($post->post_status == 'publish') {
            $content = $post->post_content;


            
            if ($type!='text') {

                if (get_bloginfo('version') >= "5.0.0" && has_blocks($content)) {
                    // $content = filter_block_content($content);
                    $content = apply_filters( 'the_content', $content );
                } else {
                    $content = wpautop($content);
                }
            }

            return do_shortcode($content);
        }
    }
}
add_shortcode('common_content', 'common_content');

// amp content
function sw_amp($atts, $content = null)
{
    $content = do_shortcode($content);
    if (!is_amp()) {
        return;
    }
    return $content;
}
add_shortcode('sw_amp', 'sw_amp');

function sw_no_amp($atts, $content = null)
{
    $content = do_shortcode($content);
    if (is_amp()) {
        return;
    }
    return $content;
}
add_shortcode('sw_no_amp', 'sw_no_amp');

function is_pc($atts, $content = null)
{
    $content = do_shortcode($content);
    if (!is_mobile()) {
        return $content;
    }
}
add_shortcode('pc', 'is_pc');

function is_sp($atts, $content = null)
{
    $content = do_shortcode($content);
    if (is_mobile()) {
        return $content;
    }
}
add_shortcode('sp', 'is_sp');

function do_esc_html($args=array(), $content="")
{
    return htmlspecialchars($content, ENT_QUOTES, "UTF-8") ;
}
add_shortcode("esc_html", "do_esc_html") ;

function do_author_profile($atts)
{
    extract(shortcode_atts(array(
        'id' => '',
        'link' => '記事一覧を見る',
    ), $atts));

    if (is_singular('post') && in_the_loop() && !$id) {
        $id = get_the_author_meta('ID');
    }

    $id = get_the_author_meta('ID', $id);

    if ($id) {
        $facebook = get_the_author_meta('facebook', $id);
        $twitter = get_the_author_meta('twitter', $id);
        $instagram = get_the_author_meta('instagram', $id);
        $youtube = get_the_author_meta('youtube', $id);


        $html = "<div class='sc_post_author_user post_author_user clearfix vcard author'>";
        $html .= "<div class='post_thum'>";
        $html .= get_avatar($id, 100);
        $html .= "<ul class='profile_sns'>";

        if ($facebook) {
            $html .= "<li><a class='facebook' href='".$facebook."'><i class='fa fa-facebook'></i></a></li>";
        }
        if ($twitter) {
            $html .= "<li><a class='twitter' href='".$twitter."'><i class='fa fa-twitter'></i></a></li>";
        }
        if ($instagram) {
            $html .= "<li><a class='instagram' href='".$instagram."'><i class='fa fa-instagram'></i></a></li>";
        }

        $html .= "</ul></div>";

        $html .= "<div class='post_author_user_meta'>";

        $html .= "<div class='post-author fn'><a href='".get_author_posts_url($id)."'>".get_the_author_meta('display_name', $id)."</a></div>";

        $html .= "<div class='post-description'>".get_the_author_meta('description', $id)."</div>";

        $html .= "</div>";

        $html .= "<div class='post-author-more'><a href='".get_author_posts_url($id)."'>".$link."</a></div>";

        $html .= "</div>";

        return $html;
    }
}
add_shortcode('author_profile', 'do_author_profile');

function do_author_list($atts)
{
    extract(shortcode_atts(array(
        'order' => 'DESC',
        'orderby' => 'name',
        'exclude' => '',
        'role' => '',
        'number' => ''
    ), $atts));

    $args = array(
        'exclude' => $exclude,
        'order' => $order,
        'orderby' => $orderby,
        'role' => $role,
        'number' => $number
    );

    $users = get_users($args);
    $html = '<div class="user_list">';
    foreach ($users as $user) {
        $html .= '<a class="user" href="'.get_author_posts_url($user->ID).'">';

        $html .= '<div class="user_avator">'.get_avatar($user->ID, '150').'</div>';
        $html .= '<div class="user_name">'.$user->display_name.'</div>';

        $html .= '</a>';
    }
    $html .= '</div>';

    return $html;
}
add_shortcode('author_list', 'do_author_list');


if (!function_exists('diver_shortcode_empty_paragraph_fix')) {
    function diver_shortcode_empty_paragraph_fix($content)
    {
        $array = array(
            '<p>[' => '[',
            ']</p>' => ']',
            ']<br />' => ']'
        );

        $content = strtr($content, $array);
        return $content;
    }
}
add_filter('the_content', 'diver_shortcode_empty_paragraph_fix');

if (!function_exists('diver_audio_shortcode_custom')) {
    function diver_audio_shortcode_custom($html, $atts, $audio, $post_id, $library)
    {
        $default_types = wp_get_audio_extensions();

        $fileurl = '';

        foreach ($default_types as $fallback) {
            if (! empty($atts[ $fallback ])) {
                if (empty($fileurl)) {
                    $fileurl = $atts[ $fallback ];
                }
            }
        }

        $html = '<p><audio src="'.$fileurl.'" controls="controls"></audio></p>';

        return $html;
    }
}
add_filter('wp_audio_shortcode', 'diver_audio_shortcode_custom', 5, 10);


// editor format ========================================


// function diver_shortcord_editor_format( $return_value ) {
//     $content = get_post_field('post_content', get_the_ID());

//     return do_shortcode( $content );
// }
// add_filter( 'format_for_editor', 'diver_shortcord_editor_format' );


// function diver_shortcord_editor_format( $return_value ) {

//   $reflect_items = array('[getpost]');

//   foreach( $reflect_items as $item ) {
//     $shortcode[] = do_shortcode( $item );
//   }

//   return str_replace( $reflect_items, $shortcode, $return_value );
// }
// add_filter( 'format_for_editor', 'diver_shortcord_editor_format' );
function save_contact_form()
{
    global $wpdb;

    $wpdb->insert('wp8_contact_data',  array(
        "name_kanji" => $_POST["customer_name"],
        "name_kana" => $_POST["customer_kana"],
        "phone" => $_POST["tel"],
        "email" => $_POST["email"],
        "content" => $_POST["content"],
        "content_option" => $_POST["content_option"],
        "content_etc" => $_POST["content-etc"],
        "class_room" => $_POST["class_room"],
        "class_room_etc" => $_POST["class_room-etc"],
        "gakkou" => $_POST["gakkou"],
    ));
    $dataCreate = $wpdb->insert_id;

    if ($dataCreate) {
        $selectData = "SELECT * FROM wp8_contact_setting";
        $result = $wpdb->get_results($selectData, 'ARRAY_A');

        $headers = array('Content-Type: text/html; charset=UTF-8');
        //send email user
        $emailTitle = "【ENGLISH-X】お問合せありがとうございます。";

        $emailMessage = $result[0]["email_content"];
        
        $emailMessage = str_replace("{{customer_name}}", $_POST["customer_name"], $emailMessage);
        $emailMessage = str_replace("{{customer_kana}}", $_POST["customer_kana"], $emailMessage);
        $emailMessage = str_replace("{{email}}", $_POST["email"], $emailMessage);
        $emailMessage = str_replace("{{tel}}", $_POST["tel"], $emailMessage);
        $emailMessage = str_replace("{{content}}", $_POST["content"], $emailMessage);
        $emailMessage = str_replace("{{content-etc}}", $_POST["content-etc"], $emailMessage);
        $emailMessage = str_replace("{{class_room}}", $_POST["class_room"], $emailMessage);
        $emailMessage = str_replace("{{class_room-etc}}", $_POST["class_room-etc"], $emailMessage);
        $emailMessage = str_replace("{{gakkou}}", $_POST["gakkou"], $emailMessage);

        // $emailMessage = '-----------------------------------------------<br/>';
        // $emailMessage .= 'このメールは自動送信されています。<br/>';
        // $emailMessage .= '本メールにお心当たりのない方は、「info@englishx.jp」までご連絡のうえ、<br/>';
        // $emailMessage .= '速やかに本メールを破棄していただきますようお願いいたします。<br/>';
        // $emailMessage .= '-----------------------------------------------<br/><br/>';

        // $emailMessage .= $_POST["customer_name"] . '様<br/><br/>';
        // $emailMessage .= 'この度はお問い合わせをいただき、誠にありがとうございます。ENGLISH-Xです。<br/>';
        // $emailMessage .= '以下の内容にてお問い合わせを承りました。<br/><br/>';
        // $emailMessage .= '・生徒のお名前：' . $_POST["customer_name"] . '<br/>';
        // $emailMessage .= '・生徒のお名前（カナ）：' . $_POST["customer_kana"] . '<br/>';
        // $emailMessage .= '・連絡先アドレス：' . $_POST["email"] . '<br/>';
        // $emailMessage .= '・連絡先電話番号：' . $_POST["tel"] . '<br/>';

        // $emailMessage .= '・お問合せ内容：' . $_POST["content"] . '<br/>';
        // if (!empty($_POST["content_option"])) {
        //     $emailMessage .= '・春期講習希望：' . $_POST["content_option"] . '<br/>';
        // }
        // if (!empty($_POST["content-etc"])) {
        //     $emailMessage .= '・具体的なお問い合わせ内容：' . $_POST["content-etc"] . '<br/>';
        // }

        // $emailMessage .= '・生徒の学年：' . $_POST["class_room"] . '<br/>';
        // if (!empty($_POST["class_room-etc"])) {
        //     $emailMessage .= '・生徒の学年をご記入ください：' . $_POST["class_room-etc"] . '<br/>';
        // }

        // $emailMessage .= '・生徒の学校：' . $_POST["gakkou"] . '<br/><br/>';

        // $emailMessage .= '弊社でお問い合わせ内容を確認後、通常1～3営業日以内にお電話もしくは"info@englishx.jp"から確認メールをお送りし、そちらへの返信をもってご予約を確定させていただきます。<br/>';
        // $emailMessage .= 'ご希望日に間違いございませんこと、ご確認ください。もし入力間違いなどございます場合は、上記メールアドレスもしくはお電話（03-6417-4406）にてご連絡ください。<br/><br/>';

        // $emailMessage .= '※初回無料体験授業ですが、目安として、塾のご説明が15分、体験授業が60分の内容となります<br/>';
        // $emailMessage .= '※中学生以下のお客様につきましては、説明のお時間は、保護者様ご同伴にてお願いしております（体験授業は生徒さまご本人のみで問題ございません）<br/>';
        // $emailMessage .= '※入力にエラーなどございます場合、当塾より、改めてご連絡いたします<br/>';
        // $emailMessage .= '※混雑状況により、ご連絡に3日以上かかる場合がございます。ご容赦ください<br/><br/>';

        // $emailMessage .= '---<br/>';
        // $emailMessage .= '【体験授業ご希望の場合】<br/>';
        // $emailMessage .= '以下初回無料体験授業の流れ、注意事項、以下の通りになります。<br/>';
        // $emailMessage .= 'ご確認のほど、よろしくお願いいたします。<br/><br/>';

        // $emailMessage .= '当日、お越しいただく場所は以下の通りです。<br/>';
        // $emailMessage .= '〒141-0021<br/>';
        // $emailMessage .= '東京品川区上大崎2-13-20 高砂ビル白金 204号室<br/>';
        // $emailMessage .= 'https://goo.gl/maps/jnt4uAfUure5jhyT8<br/><br/>';

        // $emailMessage .= '目黒駅からの詳しいアクセスは以下の動画を参照ください。<br/>';
        // $emailMessage .= 'https://www.youtube.com/watch?v=1jWilctC3vs<br/><br/>';

        // $emailMessage .= '10分を過ぎて、ご参加が確認できない場合、キャンセル扱いとさせていただきます。<br/>';
        // $emailMessage .= 'また、無断にてキャンセルされた場合、次回以降のご予約などをお受けできない場合ございますので、ご了承ください<br/><br/>';

        // $emailMessage .= 'ご不明点、ご希望などございましたら、お気軽にご連絡ください。<br/>';
        // $emailMessage .= 'どうぞよろしくお願いいたします。 <br/>';
        // $emailMessage .= '---<br/>';
        // $emailMessage .= 'このメールは、目黒の難関大学・高校受験対策英語塾でNO.1！【ENGLISH-X】(https://englishx.jp) のお問い合わせフォームから送信されました。<br/>';

        wp_mail($_POST["email"], $emailTitle, $emailMessage, $headers);

        //send email admin
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $emailTitleAdmin = "【無料体験】ENGLISH-X " . $_POST["customer_name"] . "様";

        $emailMessageAdmin = '・申込番号：' . $dataCreate . '<br/>';
        $emailMessageAdmin .= '・生徒のお名前：' . $_POST["customer_name"] . '<br/>';
        $emailMessageAdmin .= '・生徒のお名前（カナ）：' . $_POST["customer_kana"] . '<br/>';
        $emailMessageAdmin .= '・連絡先アドレス：' . $_POST["email"] . '<br/>';
        $emailMessageAdmin .= '・連絡先電話番号：' . $_POST["tel"] . '<br/>';
        $emailMessageAdmin .= '・お問合せ内容：' . $_POST["content"] . $_POST["content-etc"] . '<br/>';
        $emailMessageAdmin .= '・生徒の学年：' . $_POST["class_room"] . $_POST["class_room-etc"] . '<br/>';
        $emailMessageAdmin .= '・生徒の学校：' . $_POST["gakkou"] . '<br/>';

        $emailMessageAdmin .= '---------------------------------------<br/>';
        $emailMessageAdmin .= '送信者情報<br/>';
        $emailMessageAdmin .= '・ブラウザー：' . $_SERVER['HTTP_USER_AGENT'] . '<br/>';
        $emailMessageAdmin .= '・IPアドレス：' . $ip . '<br/>';
        $emailMessageAdmin .= '・HOST：' . $_SERVER['HTTP_HOST'] . '<br/>';
        $emailMessageAdmin .= '---------------------------------------<br/>';

        wp_mail('info@englishx.jp', $emailTitleAdmin, $emailMessageAdmin, $headers);

        $response['contact_id'] = $dataCreate;
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
    }

    //check_ajax_referer('jsforwp_events');
    $response = json_encode($response);
    echo $response;

    exit;
}
add_action('wp_ajax_save_contact_form', 'save_contact_form');
add_action('wp_ajax_nopriv_save_contact_form', 'save_contact_form');

add_shortcode('form-contact-custom', 'form_contact_custom');
function form_contact_custom($atts)
{
    global $wpdb;
    $selectData = "SELECT * FROM wp8_contact_setting";
    $result = $wpdb->get_results($selectData, 'ARRAY_A');
    $inquryVal = $result[0]["inquiry"];
    $thanksContent = $result[0]["thanks_content"];

    // foreach $inqury to list option, $inqury data from textarea
    $inquryVal = explode("\n", $inquryVal);
    $inqury_option = '';
    foreach ($inquryVal as $opt) {
        $inqury_option .= '<option value="'.$opt.'">'.$opt.'</option>';
    }

    $string = '<style>.contact-form-inner .td, .contact-form-inner .th{border-right: 1px solid #a6a6b0; border-bottom: 1px solid #a6a6b0; box-sizing: border-box;}.contact-form-errors::before, .contact-form-recaptcha-error::before, .contact-form-warning::before{content: "▲";}.contact-form-inner{width: 100%; margin-bottom: 20px; border-top: 1px solid #a6a6b0; box-sizing: border-box;}::-webkit-input-placeholder{line-height: 20px; font-size: 16px;}:-ms-input-placeholder{line-height: 20px; font-size: 16px;}::placeholder{line-height: 20px; font-size: 16px;}.contact-form-inner dl, .contact-form-inner dl dd{margin-bottom: 10px; padding: 0;}.contact-form-inner .th{overflow: hidden; background-color: #eee; border-left: 1px solid #a6a6b0; padding: 10px 18px; vertical-align: middle; text-align: left; min-width: 33%; font-size: 16px;}.contact-form-inner .td{background-color: #fff; padding: 10px 10px 10px 20px;}@media (min-width: 641px){.contact-form-inner{display: table;}.contact-form-inner .tr{display: table-row;}.contact-form-inner .td, .contact-form-inner .th{display: table-cell;}.contact-form-inner .th{width: 330px;}}.contact-form-inner dl dt{padding: 8px 0; display: flex; align-items: center; font-weight: 400;}.contact-form-inner dl dt .contact-label{float: none;}@media (min-width: 941px){.contact-form-inner dl dt{clear: left; float: left; width: 25%; font-weight: 400;}.contact-form-inner dl dd{margin-left: calc(25%); margin-bottom: 10px; border-bottom: none;}.contact-form-inner dl dt.w-200{width: 225px;}.contact-form-inner dl dd.wc-200{margin-left: 0px;}.contact-form-inner dl dd.wc-225{margin-left: 225px;}}@media (max-width: 640px){.contact-form-inner .td{border-left: 1px solid #a6a6b0; padding-top: 20px; padding-bottom: 20px;}.contact-form-inner dl dt{float: none; width: auto; padding: 5px 0;}.contact-form-inner dl dd{margin-left: 0; padding: 0;}}.contact-form-confirm .contact-form-inner dl dt{padding: 0;}.contact-form-inner input[type="radio"], .contact-form-inner input[type="checkbox"]{-webkit-transform: scale(1.5); transform: scale(1.5); margin-right: 3px;}.contact-form-inner label{cursor: pointer;}.contact-form-inner p{margin: 10px 0;}.contact-check, .contact-input{box-sizing: border-box; margin-right: 5px; width: calc(100% - 30px); vertical-align: middle;}.contact-check{display: inline-block; border: 2px solid #fff;}.contact-check.horizontal label{display: inline-block; padding: 10px 20px 10px 5px; font-size: 16px;}.contact-check.vertical label{display: block; padding: 10px 0;}.contact-input{display: inline-block !important; border: 2px solid #999 !important; padding: 10px 8px !important; width: 100% !important; font-size: 16px !important; color: #000 !important;}.contact-input.w-50{width: 50%;}.contact-input.w-30{width: 30%;}@media (max-width: 480px){.contact-input.sp-30{width: 30%;}}@media (min-width: 481px) and (max-width: 640px){.contact-input.tb-50{width: 50%;}.contact-input.tb-30{width: 30%;}}.contact-label{display: inline-block; color: #fff; font-weight: 400; letter-spacing: 5px; padding: 2px 5px 2px 10px; margin-left: 8px; -moz-border-radius: 4px; -webkit-border-radius: 4px; -o-border-radius: 4px; border-radius: 4px; line-height: 20px; font-size: 13px; margin-top: 2px;}@media (min-width: 641px){.contact-input.pc-50{width: 50%;}.contact-input.pc-30{width: 30%;}.contact-label{float: right;}}.contact-label.optional{background-color: #5b7ff4;}.contact-label.required{background-color: #fe6c63;}.contact-file-block{padding: 15px 0; color: #666; font-size: 1.2rem; line-height: 1.4;}.contact-file-block .fileinput-button{display: inline-block; vertical-align: middle; margin-right: 10px;}.contact-file-block .fileinput-button button{font-size: 1.5rem;}.contact-file-block .contactform-uploaded-files{display: inline-block; vertical-align: middle;}.contact-file-block .contactform-uploaded-thumbnail{margin-right: 10px;}.contact-form-errors, .contact-form-recaptcha-error, .contact-form-warning{display: block; padding: 5px 10px; margin: 8px 0 10px; font-size: 14px; font-weight: 700; box-sizing: border-box;}.contact-file-block .contactform-uploaded-filename, .contact-file-block .contactform-uploaded-thumbnail img{vertical-align: middle;}.contact-input:focus{border-color: #66afe9; box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);}.contact-form-valid{border-color: #7bd177 !important; background-color: #fff !important;}.contact-form-invalid{border-color: red !important; background-color: #ffe6e6 !important; box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(255, 0, 0, 0.6);}.contact-form-warning{color: #1b7704 !important; background-color: #d4fdc9 !important;}.contact-form-errors, .contact-form-recaptcha-error{color: red; background-color: #fcc;}.contact-notice{color: #666; font-size: 13px; line-height: 1.4; margin-top: 5px;}.contact-form-icon.icon-ok::before{color: #7bd177;}.contact-form-icon.icon-caution::before{color: #e70012;}.contact-form-buttons{padding: 15px 20px; margin: 20px auto 40px; overflow: hidden; text-align: center;}@media (max-width: 640px){.contact-form-buttons span{display: block;}}.contact-btn{display: inline-block; text-decoration: none; font-family: "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif; border: 1px solid #666; border-radius: 8px;}.contact-btn.back{background-image: -moz-linear-gradient(90deg, #bababa 0, rgba(186, 186, 186, 0.5) 100%); background-image: -webkit-linear-gradient(90deg, #bababa 0, rgba(186, 186, 186, 0.5) 100%); background-image: -ms-linear-gradient(90deg, #bababa 0, rgba(186, 186, 186, 0.5) 100%); color: #3b3b3b; font-size: 13px; padding: 5px 20px; float: left; background-color: transparent;}.contact-btn.back.contact-form-sending{display: none;}.box-content-after-validate{width: 300px; background-color: #2bb94c; display: block; margin: auto; padding: 15px 20px !important; border-radius: 8px; color: white; font-size: 18px; line-height: 1.6; font-weight: 700;}.contact-btn.submit{color: #fff; font-weight: 600; padding: 20px 30px; font-size: 20px; border: none; background-color: #2bb94c;}.contact-btn.submit.contact-form-disabled{background-color: #999; border-color: #888; background-image: none;}@media (max-width: 640px){.contact-btn.back, .contact-btn.submit{float: none;}.contact-btn.back{margin: 20px 0;}.contact-form-confirm .contact-btn.submit{margin-bottom: 30px;}}.contact-form-suggest-wrapper{position: relative;}.contact-form-suggest{position: absolute; background-color: #fff; border: 1px solid #c9c9c6; width: calc(100% - 30px); z-index: 1000; box-sizing: border-box; cursor: pointer;}.contact-form-suggest div{display: block; overflow: hidden; white-space: nowrap; line-height: 2; font-size: 16px; padding-left: 15px; box-sizing: border-box;}.contact-form-suggest div.select{color: #efefef; background-color: #36f;}.contact-form-suggest div.over{background-color: #9cf;}.contact-form-stepbar{position: relative; list-style: none; margin: 0 0 1em; padding-left: 0 !important; padding: 0; text-align: center; width: 100%; overflow: hidden;}.contact-form-stepbar li{font-size: 12px; color: #c9c9c6; position: relative; display: inline-block; float: left; line-height: 40px; padding: 0 40px 0 20px; background-color: #e9e8ea; box-sizing: border-box; font-weight: bold; font-size: 16px;}.contact-form-stepbar li:after, .contact-form-stepbar li:before{position: absolute; left: -15px; display: block; content: ""; background-color: #e9e8ea; border-left: 4px solid #fff; width: 20px; height: 20px;}.contact-form-stepbar li:after{top: 0; -webkit-transform: skew(30deg); -moz-transform: skew(30deg); -ms-transform: skew(30deg); -o-transform: skew(30deg); transform: skew(30deg);}.contact-form-stepbar li:before{bottom: 0; -webkit-transform: skew(-30deg); -moz-transform: skew(-30deg); -ms-transform: skew(-30deg); -o-transform: skew(-30deg); transform: skew(-30deg);}.contact-form-stepbar li:first-child{border-left-radius: 4px;}.contact-form-stepbar li:first-child:after, .contact-form-stepbar li:first-child:before{content: none;}.contact-form-stepbar li:last-child{border-right-radius: 4px;}.contact-form-stepbar li.current{color: #fff; background-color: #16184d;}.contact-form-stepbar li.current:after, .contact-form-stepbar li.current:before{background-color: #16184d;}.contact-form-stepbar.step2 li{width: 50%;}.contact-form-stepbar.step3 li{width: 33.333%;}.contact-form-stepbar.step4 li{width: 25%;}.contact-form-stepbar.step5 li{width: 20%;}.contact-form-container ol li, .contact-form-container ul li{line-height: 1.6;}q:after, q:before{content: "";}table{border-collapse: collapse; border-spacing: 0;}sub{vertical-align: text-bottom;}@media (min-width: 641px){.pc-hide{display: none;}}.hide{display: none;}img{max-width: 100%; height: auto;}@media (min-width: 941px){.container{max-width: 940px; margin: 0 auto;}}.contact-form-container{padding: 30px 0;}.contact-form-container ul{margin-bottom: 20px; margin-left: 20px; list-style: disc;}.contact-form-container ul li ul{margin-top: 10px;}.contact-form-container ol{margin-bottom: 20px; list-style: decimal;}.contact-form-lead{margin-bottom: 20px;}.privacy-wrapper{max-width: 750px; margin: 0 auto 30px;}.privacy-wrapper h5{font-size: 16px; color: #222;}.privacy-wrapper .privacy-inner{border: 1px solid #c9c9c6; height: 150px; overflow: auto; padding: 20px; font-size: 13px;}form .privacy-inner p{font-size: 12px; line-height: 1.5;}@media (max-width: 480px){.sp-hide{display: none;}}@media (min-width: 481px) and (max-width: 640px){.tb-hide{display: none;}}button#SubmitForm:disabled{opacity: 0.5;}.form-input-data{margin: 30px 0;}</style>';
    $string .= '<script type="application/javascript">ContactForm={},ContactForm.page_role="form",ContactForm.page_data=[],ContactForm.vsetting={show_icon:!0,shift_scroll_position:-50},ContactForm.forms={customer_name:{label:"お名前",validates:["required"],error_messages:[],validate_condition:[]},customer_kana:{label:"フリガナ",validates:["required","kanaOnly"],error_messages:[],validate_condition:[]},tel:{label:"電話番号",validates:["required","tel"],error_messages:[],validate_condition:[]},email:{label:"お問い合わせ詳細",validates:["required","email_detail"],mobile_mail_warning:"携帯電話のアドレスが入力されました。ozonenotes.jp ドメインからのメールを受信できるように設定お願いします。",error_messages:[],validate_condition:[]},content:{label:"お問合せ内容",validates:[],error_messages:[],validate_condition:[]},class_room:{label:"興味のある商品",validates:[],error_messages:[],validate_condition:[]},gakkou:{label:"生徒の学校",validates:[],error_messages:[],validate_condition:[]}},ContactForm.reCAPTCHA=!1,ContactForm.init_msg=[],ContactForm.unload_message="このページから移動してよろしいですか？入力内容は保存されていない可能性があります。",ContactForm.utilities={setSessionData:function(e,t){var a=this;$.each(e,(function(e,n){if("upload_files"===t[e].type)n&&$.each(n,(function(){a.setUploadedFile(e,this)}));else{var o=$(`[name="${e}"]`);a.setValue(o,n)}}))},setInitMessage:function(e){var t=this;$.each(e,(function(e,a){$.isArray(a)&&(e+="[]");var n=$(`[name="${e}"]`);if(n.length>0)switch(n.attr("type")){case"radio":case"checkbox":0===n.filter("checked").length&&t.setValue(n,a);break;default:""===n.val()&&t.setValue(n,a)}}))},setValue:function(e,t){!e.attr("type")||t instanceof Array?e.val(t):e.val([t])},setSendmailButtonEvent:function(e){e.on("click",(function(){var e=$(this);if(e.hasClass("contact-form-disabled"))return!1;window.ContactForm.submitLabel=e.text();var t="送信中です…お待ちください。";e.data("message")&&(t=e.data("message")),e.text(t),e.addClass("contact-form-disabled disabled"),$(".contact-form-nav").addClass("contact-form-sending")}))},clearSendingButtonStyle:function(e){if(!e.hasClass("contact-form-disabled"))return!1;e.text(window.ContactForm.submitLabel),e.removeClass("contact-form-disabled disabled"),$(".contact-form-nav").removeClass("contact-form-sending")},getFormValues:function(e,t){$.isArray(e)||(e=[e]);var a={};return $.each(e,(function(){var e=this,n=$(`[name="${e}"]`),o=n.val(),i=window.ContactForm.forms[e],r=!1;if(i&&(r="upload_files"===i.type),r){var l=ContactForm.utilities.updatedFileElementName(e);o=$("#"+l).find("input").length>0?"check_ok":""}else"radio"===n.attr("type")?o=n.filter(":checked").val():"checkbox"===n.attr("type")?(o=[],n.filter(":checked").each((function(){o.push($(this).val())}))):"INPUT"===n.prop("nodeName")&&(o=ContactForm.utilities.toHalfWidth(o),-1!==$.inArray("kanaOnly",i.validates)&&(o=ContactForm.utilities.hankana2zenkana(o)),!1!==i.to_half&&n.val(o));t&&(e=e.replace("[]","")),a[e]=o})),a},getFormNameByElement:function(e){var t=e.attr("name");return void 0===t&&(t=e.data("formname")),t},getFormElementByName:function(e){return $(`[name="${e}"]`)},getEnteredValue:function(e){switch("string"==typeof e&&(e=this.getFormElementByName(e)),e.attr("type")){case"radio":case"checkbox":return e.filter(":visible:checked").val();default:return e.val()}},toHalfWidth:function(e){return e.replace(/[！-～]/g,(function(e){return String.fromCharCode(e.charCodeAt(0)-65248)}))},hankana2zenkana:function(e){var t={"ｶﾞ":"ガ","ｷﾞ":"ギ","ｸﾞ":"グ","ｹﾞ":"ゲ","ｺﾞ":"ゴ","ｻﾞ":"ザ","ｼﾞ":"ジ","ｽﾞ":"ズ","ｾﾞ":"ゼ","ｿﾞ":"ゾ","ﾀﾞ":"ダ","ﾁﾞ":"ヂ","ﾂﾞ":"ヅ","ﾃﾞ":"デ","ﾄﾞ":"ド","ﾊﾞ":"バ","ﾋﾞ":"ビ","ﾌﾞ":"ブ","ﾍﾞ":"ベ","ﾎﾞ":"ボ","ﾊﾟ":"パ","ﾋﾟ":"ピ","ﾌﾟ":"プ","ﾍﾟ":"ペ","ﾎﾟ":"ポ","ｳﾞ":"ヴ","ﾜﾞ":"ヷ","ｦﾞ":"ヺ","ｱ":"ア","ｲ":"イ","ｳ":"ウ","ｴ":"エ","ｵ":"オ","ｶ":"カ","ｷ":"キ","ｸ":"ク","ｹ":"ケ","ｺ":"コ","ｻ":"サ","ｼ":"シ","ｽ":"ス","ｾ":"セ","ｿ":"ソ","ﾀ":"タ","ﾁ":"チ","ﾂ":"ツ","ﾃ":"テ","ﾄ":"ト","ﾅ":"ナ","ﾆ":"ニ","ﾇ":"ヌ","ﾈ":"ネ","ﾉ":"ノ","ﾊ":"ハ","ﾋ":"ヒ","ﾌ":"フ","ﾍ":"ヘ","ﾎ":"ホ","ﾏ":"マ","ﾐ":"ミ","ﾑ":"ム","ﾒ":"メ","ﾓ":"モ","ﾔ":"ヤ","ﾕ":"ユ","ﾖ":"ヨ","ﾗ":"ラ","ﾘ":"リ","ﾙ":"ル","ﾚ":"レ","ﾛ":"ロ","ﾜ":"ワ","ｦ":"ヲ","ﾝ":"ン","ｧ":"ァ","ｨ":"ィ","ｩ":"ゥ","ｪ":"ェ","ｫ":"ォ","ｯ":"ッ","ｬ":"ャ","ｭ":"ュ","ｮ":"ョ","｡":"。","､":"、","ｰ":"ー","｢":"「","｣":"」","･":"・"},a=new RegExp("("+Object.keys(t).join("|")+")","g");return e.replace(a,(function(e){return t[e]})).replace(/ﾞ/g,"゛").replace(/ﾟ/g,"゜")},disableEnterKeySubmit:function(){$("input").on("keydown",(function(e){return!(e.which&&13===e.which||e.keyCode&&13===e.keyCode)}))},addUploadFileElement:function(e,t,a,n,o){},updatedFileElementName:function(e){return(e=e.replace("[]",""))+"_files"},uploadButtonElementName:function(e){return"upload-"+(e=e.replace("[]",""))+"-button"},setUploadedFile:function(e,t){var a=this,n=encodeURIComponent(t),o=ContactForm.furl+"?file="+n;$.ajax({type:"get",url:o}).done((function(t){t=$.parseJSON(t),a.addUploadFileElement($("#"+a.updatedFileElementName(e)),e,t.file.thumbnailUrl,t.file.name,t.file.deleteUrl)}))},objectKeys:function(e){return $.map(e,(function(e,t){return t}))}};</script>';
    $string .= '<div class="container form-contact" style="text-align: left"> <div class="page-header"><h1>お申込みと事前面談の日程調整</h1></div><div class="contact-form-stepbar-wrapper"> <ol class="contact-form-stepbar step3 sp-hide"> <li class="current">1.内容の入力</li><li>2.内容確認</li><li>3.送信完了</li></ol> <ol class="contact-form-stepbar step3 tb-hide pc-hide"> <li class="current">入力</li><li>確認</li><li>完了</li></ol> <div class="text-note-confirm hide" style="margin-bottom: 20px"> <p style="margin-bottom: 10px; font-size: 20px; font-weight: 700">ご入力内容の確認</p><p>下記の申込内容に間違いがなければ、下の「申込完了、日程調整画面に移動」ボタンをクリックして、日程調整にお進みください。※申込内容に過ちがある場合は「戻る」ボタンをクリックしてご修正願います。※「申込完了、日程調整画面に移動」ボタンをクリック頂いた時点で申込完了となり、記載いただいたメールアドレスにご記載内容がメールで配信されます。</p></div></div><form action="#" method="post" class="form-input-data" id="FormDataContact"> <div class="contact-form-inner"> <div class="tr" data-contactform-area="customer_name"> <div class="th">お名前<span class="contact-label required">必須</span></div><div class="td"> <input name="customer_name" class="contact-input" id="customer_name" data-autoruby="customer_name" placeholder="例）山田 太郎" autocomplete="name"/> </div></div><div class="tr" data-contactform-area="customer_kana"> <div class="th">フリガナ<span class="contact-label required">必須</span></div><div class="td"> <input name="customer_kana" class="contact-input" id="customer_kana" data-autoruby-katakana="customer_name" placeholder="例）ヤマダ タロウ" autocomplete=""/> </div></div><div class="tr" data-contactform-area="tel"> <div class="th">電話番号<span class="contact-label required">必須</span></div><div class="td"> <input type="tel" name="tel" class="contact-input" style="ime-mode: inactive" placeholder="例）03-1234-5678" autocomplete="tel-national"/> <p class="contact-notice">日中にご連絡の取りやすい番号をご記入ください。</p></div></div><div class="tr" data-contactform-area="email"> <div class="th">メールアドレス<span class="contact-label required">必須</span></div><div class="td"> <div class="contact-form-suggest-wrapper"> <input name="email" style="ime-mode: inactive" class="contact-input" placeholder="例）example@englishx.jp"/> </div></div></div><div class="tr" data-contactform-area="content"> <div class="th">お問合せ内容</div><div class="td"> <select name="content" class="contact-input pc-50 tb-50" id="SelectContent"> <option value="">選択してください</option>'.$inqury_option.'</select> <div id="ContentOption" class="hide"> <p>希望のタームを選択してください</p><dl> <dt class="w-200" style="padding: 0"></dt> <div class="td" style="display: block; border: none; padding-left: 5px"> <div class="ozn-check horizontal" style="display: flex; flex-direction: column"> <label> <input type="checkbox" name="contentoption" value="第1ターム(3/6日〜3/12日)"/> 第1ターム(3/6日〜3/12日) </label> <label> <input type="checkbox" name="contentoption" value="第2ターム(3/13日〜3/19日)"/> 第2ターム(3/13日〜3/19日) </label> <label> <input type="checkbox" name="contentoption" value="第3ターム(3/20日〜3/26日)"/> 第3ターム(3/20日〜3/26日) </label> <label> <input type="checkbox" name="contentoption" value="第4ターム(3/27日〜4/2日)"/> 第4ターム(3/27日〜4/2日) </label> </div></div></dl> </div><div id="ContentEtc" class="hide"> <p>具体的なお問い合わせ内容</p><dl> <dt class="w-200" style="padding: 0"></dt> <dd class="wc-200"> <input name="content-etc" class="contact-input" placeholder="例）一度気軽に相談してみたい。など"/> </dd> </dl> </div></div></div><div class="tr" data-contactform-area="class_room"> <div class="th">生徒の学年</div><div class="td"> <select name="class_room" class="contact-input pc-50 tb-50" id="SelectClassroom"> <option value="">選択してください</option> <option value="中学1年生">中学1年生</option> <option value="中学2年生">中学2年生</option> <option value="中学3年生">中学3年生</option> <option value="高校1年生">高校1年生</option> <option value="高校2年生">高校2年生</option> <option value="高校3年生">高校3年生</option> <option value="浪人生">浪人生</option> <option value="その他">その他</option> </select> <div id="ClassroomEtc" class="hide"> <dl> <dt class="w-200">生徒の学年をご記入ください</dt> <dd class="wc-200"><input name="class_room-etc" class="contact-input" placeholder="例）小学6年生"/></dd> </dl> </div></div></div><div class="tr" data-contactform-area="gakkou"> <div class="th">生徒の学校</div><div class="td"> <p class="contact-notice"> *個別指導となり、生徒さまの英語力やご希望に合わせ、最適の講師をアサインし、最大限効果のあるレッスンを展開しております。<br/>以下、詳細にご記載いただけるほど、効果的なレッスンをご提供可能ですので、ぜひご記入ください </p><div class="contact-form-suggest-wrapper"> <input name="gakkou" style="ime-mode: inactive" class="contact-input" placeholder="例）私立目黒高等学校"/> </div></div></div></div><div class="privacy-wrapper"> <h5>個人情報の取り扱いについて</h5> <div class="privacy-inner">';
    $string .= $result[0]["policy"];
    $string .= '</div><h5 class="text-center" style="text-align: center"> 上記　個人情報保護方針にご同意いただきましたら、入力内容の確認へお進みください。 </h5> </div><div class="contact-form-buttons text-center" id="GroupButton"> <p class="box-content-after-validate text-center">必須項目を全て入力すると<br/>確認ボタンが表示されます。</p><input type="hidden" name="submit_data"/><button type="button" id="NextPageConfirm" class="contact-btn contact-form-nav submit text-center hide" > 入力内容の確認に進む → </button> </div></form> <div class="form-confirm hide"> <div class="contact-form-inner"> <div class="tr" data-contactform-area="customer_name"> <div class="th">お名前<span class="contact-label required">必須</span></div><div class="td"><p id="customer_name_value"></p></div></div><div class="tr" data-contactform-area="customer_kana"> <div class="th">フリガナ<span class="contact-label required">必須</span></div><div class="td"><p id="customer_kana_value"></p></div></div><div class="tr" data-contactform-area="tel"> <div class="th">電話番号<span class="contact-label required">必須</span></div><div class="td"><p id="tel_value"></p></div></div><div class="tr" data-contactform-area="email"> <div class="th">メールアドレス<span class="contact-label required">必須</span></div><div class="td"> <div class="contact-form-suggest-wrapper"><p id="email_value"></p></div></div></div><div class="tr" data-contactform-area="content"> <div class="th">お問合せ内容</div><div class="td"> <div class="contact-form-suggest-wrapper"> <p id="content_value"></p><p id="content-etc_value"></p><p id="contentoption_value"></p></div></div></div><div class="tr" data-contactform-area="class_room"> <div class="th">生徒の学年</div><div class="td"> <div class="contact-form-suggest-wrapper"> <p id="class_room_value"></p><p id="class_room-etc_value"></p></div></div></div><div class="tr" data-contactform-area="gakkou"> <div class="th">生徒の学校</div><div class="td"> <div class="contact-form-suggest-wrapper"><p id="gakkou_value"></p></div></div></div></div><div class="contact-form-buttons" style="border: 1px solid #a6a6b0"> <button type="button" id="BackForm" class="contact-btn contact-form-nav back">← 戻って書き直す</button ><button type="submit" id="SubmitForm" class="contact-btn contact-form-nav submit" style="float: right">申込完了<br/>日程調整画面に移動</button> </div></div><div class="form-thanks hide"> <div class="contact-form-inner" style="border-top: none"> <h5 style="padding: 0; border: none; font-size: 20px; margin-bottom: 10px; margin-top: 10px; color: #000"> 送信完了しました </h5>'.$thanksContent.'</div></div></div>';
    return $string;
}
