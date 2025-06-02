<?php
/**
 * Plugin Name: PK News Highlights
 * Description: Allows admin to add news headlines and display them using a shortcode.
 * Version: 1.1
 * Author: Kumar Ankit
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Register Custom Post Type
function pk_news_register_post_type() {
    register_post_type('pk_news', array(
        'labels' => array(
            'name' => 'PK News Highlights',
            'singular_name' => 'PK News Highlight',
            'add_new' => 'Add PK News Highlight',
            'add_new_item' => 'Add New PK News Highlight',
            'edit_item' => 'Edit PK News Highlight',
            'new_item' => 'New PK News Highlight',
            'view_item' => 'View PK News Highlight',
            'search_items' => 'Search PK News Highlights',
            'not_found' => 'No news highlights found',
            'not_found_in_trash' => 'No news highlights found in trash',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array('title'),
        'show_in_rest' => true,
        'menu_position' => 5
    ));
}
add_action('init', 'pk_news_register_post_type');

// Add Meta Boxes
function pk_news_add_meta_boxes() {
    add_meta_box('pk_news_meta', 'News Summary & Link', 'pk_news_meta_box_callback', 'pk_news');
}
add_action('add_meta_boxes', 'pk_news_add_meta_boxes');

function pk_news_meta_box_callback($post) {
    $summary = get_post_meta($post->ID, '_pk_news_summary', true);
    $link = get_post_meta($post->ID, '_pk_news_link', true);
    ?>
    <p>
        <label>Summary:</label><br>
        <textarea name="pk_news_summary" rows="4" style="width:100%;"><?php echo esc_textarea($summary); ?></textarea>
    </p>
    <p>
        <label>Link:</label><br>
        <input type="url" name="pk_news_link" style="width:100%;" value="<?php echo esc_url($link); ?>">
    </p>
    <?php
}

// Save Meta Box Data
function pk_news_save_meta($post_id) {
    if (isset($_POST['pk_news_summary'])) {
        update_post_meta($post_id, '_pk_news_summary', sanitize_text_field($_POST['pk_news_summary']));
    }
    if (isset($_POST['pk_news_link'])) {
        update_post_meta($post_id, '_pk_news_link', esc_url_raw($_POST['pk_news_link']));
    }
}
add_action('save_post', 'pk_news_save_meta');

// Shortcode to Display News Highlights
// Shortcode to Display News Highlights
// Shortcode to Display News Highlights
function pk_news_highlights_shortcode($atts) {
    $args = array(
        'post_type' => 'pk_news',
        'posts_per_page' => -1,
    );
    $news_query = new WP_Query($args);
    $output = '<div class="pk-news-highlights">';
    if ($news_query->have_posts()) {
        $output .= '<ul style="list-style-type: none; padding: 0;">';
        while ($news_query->have_posts()) {
            $news_query->the_post();
            $summary = get_post_meta(get_the_ID(), '_pk_news_summary', true);
            $link = get_post_meta(get_the_ID(), '_pk_news_link', true);

            $output .= '<li style="margin-bottom: 20px;">';
            $output .= '<div style="font-size: 26px; line-height: 1.5; font-weight: 700; font-family: Noto Sans, sans-serif; color: black; margin-bottom: 20px;">' . esc_html(get_the_title()) . '</div>';
            $output .= '<div style="font-size: 18px; line-height: normal; font-weight: 700; font-family: Noto Sans, sans-serif; color: #444;">' . esc_html($summary) . '</div>';
            if ($link) {
                $output .= '<div><a href="' . esc_url($link) . '" style="color: #0073aa; text-decoration: underline;">Read more</a></div>';
            }
            $output .= '</li><hr>';
        }
        $output .= '</ul>';
    } else {
        $output .= '<p>No news highlights found.</p>';
    }
    $output .= '</div>';
    wp_reset_postdata();
    return $output;
}


add_shortcode('pk_news_highlights', 'pk_news_highlights_shortcode');

// Admin Settings Page
function pk_news_admin_menu() {
    add_options_page('PK News Settings', 'PK News Settings', 'manage_options', 'pk-news-settings', 'pk_news_settings_page');
}
add_action('admin_menu', 'pk_news_admin_menu');

function pk_news_settings_page() {
    ?>
    <div class="wrap">
        <h1>PK News Highlights - Settings</h1>
        <p>Use shortcode <code>[pk_news_highlights]</code> to display the latest news headlines.</p>
        <p>News items can be managed under <strong>PK News Highlights</strong> in the admin sidebar.</p>
    </div>
    <?php
}

// Admin notice on the News Highlights screen
function pk_news_admin_notice_in_list() {
    global $pagenow, $post_type;
    if ($pagenow == 'edit.php' && $post_type == 'pk_news') {
        echo '<div class="notice notice-info is-dismissible">
            <p><strong>PK News Highlights:</strong> Hello प्रभात खबर HR Team! <br><br> To show headlines on the front-end, use the shortcode <code>[pk_news_highlights]</code> inside any page, post, or widget.</p>
        </div>';
    }
}
add_action('admin_notices', 'pk_news_admin_notice_in_list');

// Show shortcode notice below the title and below the Publish box in editor
function pk_news_shortcode_editor_notice() {
    global $post;
    if (get_post_type($post) === 'pk_news') {
        echo '<div class="notice notice-info inline"><p>Hello प्रभात खबर HR Team! <br><br><strong>Shortcode:</strong> Use <code>[pk_news_highlights]</code> on any page, post, or widget to display all news highlights.</p></div>';
    }
}
add_action('edit_form_top', 'pk_news_shortcode_editor_notice');
add_action('post_submitbox_misc_actions', 'pk_news_shortcode_editor_notice');

