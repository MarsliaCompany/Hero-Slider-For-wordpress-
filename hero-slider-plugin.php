<?php
/**
 * Plugin Name: Hero Slider Plugin
 * Plugin URI: https://mrslia.com/
 * Description: إضافة سلايدر رئيسي متجاوب وقابل للتخصيص بالكامل، يدعم خلفيات مخصصة وتأثيرات بصرية متقدمة وأزرار لكل شريحة مع تحكم كامل عبر كود مختصر وصفحة إعدادات.
 * Version: 1.3.1
 * Author: Marsilia
 * Author URI: https://mrslia.com/
 * Text Domain: hero-slider-plugin
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright 2025–2026 Marsilia — مؤسسة مارسيليا للبرمجيات (https://mrslia.com)
 * @author    Marsilia <https://mrslia.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'HERO_SLIDER_PLUGIN_VERSION', '1.3.1' );

require_once plugin_dir_path( __FILE__ ) . 'hero-slider-groups.php';
require_once plugin_dir_path( __FILE__ ) . 'hero-slider-portal.php';

add_action( 'add_meta_boxes', 'hero_slider_plugin_add_title_html_box' );
function hero_slider_plugin_add_title_html_box() {
    add_meta_box(
        'hero-slider-title-html',
        __( 'Slide Title (HTML)', 'hero-slider-plugin' ),
        'hero_slider_plugin_title_html_box_content',
        'hero_slide',
        'normal',
        'high'
    );
}

function hero_slider_plugin_title_html_box_content( $post ) {
    $value = get_post_meta( $post->ID, '_hero_slider_title_html', true );
    wp_nonce_field( 'hero_slider_title_html_nonce', 'hero_slider_title_html_nonce' );
    ?>
    <p>
        <label for="hero-slider-title-html"><strong><?php echo esc_html__( 'Custom Title (supports HTML)', 'hero-slider-plugin' ); ?></strong></label>
        <textarea id="hero-slider-title-html" name="hero_slider_title_html" class="widefat" rows="3" placeholder="<?php echo esc_attr__( 'Example: أفضل <br> العناوين <span class=\'accent\'>الجذابة</span>', 'hero-slider-plugin' ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
        <span class="description"><?php echo esc_html__( 'Allowed: br, span, strong, em, b, i, u, small, sup, sub, mark, a (href, target, rel, title, class). Leave empty to use the regular title.', 'hero-slider-plugin' ); ?></span>
    </p>
    <?php
}

add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_title_html_meta' );
function hero_slider_plugin_save_title_html_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_title_html_nonce'] ) || ! wp_verify_nonce( $_POST['hero_slider_title_html_nonce'], 'hero_slider_title_html_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $raw = isset( $_POST['hero_slider_title_html'] ) ? wp_unslash( $_POST['hero_slider_title_html'] ) : '';
    $allowed = array(
        'br'    => array(),
        'span'  => array( 'class' => true, 'style' => true, 'title' => true, 'aria-label' => true ),
        'strong'=> array(),
        'b'     => array(),
        'em'    => array(),
        'i'     => array(),
        'u'     => array(),
        'small' => array(),
        'sup'   => array(),
        'sub'   => array(),
        'mark'  => array(),
        'a'     => array( 'href' => true, 'target' => true, 'rel' => true, 'title' => true, 'class' => true, 'aria-label' => true ),
    );
    $clean = wp_kses( $raw, $allowed );
    $trim  = trim( wp_strip_all_tags( $clean ) );
    if ( '' === $trim ) {
        delete_post_meta( $post_id, '_hero_slider_title_html' );
    } else {
        update_post_meta( $post_id, '_hero_slider_title_html', $clean );
    }
}

// Load Translation
function hero_slider_plugin_load_textdomain() {
    load_plugin_textdomain( 'hero-slider-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'hero_slider_plugin_load_textdomain', 1 );

// Explicitly load PHP-based translations if present (WordPress 6.5+ supports .l10n.php)
function hero_slider_plugin_get_l10n_file_for_locale( $locale ) {
    $domain = 'hero-slider-plugin';
    $dir    = plugin_dir_path( __FILE__ ) . 'languages/';
    $exact  = $dir . $domain . '-' . $locale . '.l10n.php';
    if ( file_exists( $exact ) ) {
        return $exact;
    }
    if ( 0 === strpos( $locale, 'ar' ) ) {
        $ar = $dir . $domain . '-ar.l10n.php';
        if ( file_exists( $ar ) ) {
            return $ar;
        }
    }
    return '';
}

function hero_slider_plugin_load_php_translations() {
    $locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
    $phpfile = hero_slider_plugin_get_l10n_file_for_locale( $locale );
    if ( $phpfile ) {
        load_textdomain( 'hero-slider-plugin', $phpfile );
    }
}
add_action( 'plugins_loaded', 'hero_slider_plugin_load_php_translations', 0 );

// Fallback: use a lightweight map for translations if .mo/.php failed to load
function hero_slider_plugin_fallback_gettext( $translation, $text, $domain ) {
    if ( 'hero-slider-plugin' !== $domain ) {
        return $translation;
    }
    static $map = null;
    if ( null === $map ) {
        $locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
        $file   = hero_slider_plugin_get_l10n_file_for_locale( $locale );
        if ( $file ) {
            $data = include $file;
            if ( is_array( $data ) && isset( $data['messages'] ) ) {
                $map = $data['messages'];
            } else {
                $map = array();
            }
        } else {
            $map = array();
        }
    }
    if ( isset( $map[ $text ] ) && is_string( $map[ $text ] ) && '' !== $map[ $text ] ) {
        return $map[ $text ];
    }
    return $translation;
}
add_filter( 'gettext', 'hero_slider_plugin_fallback_gettext', 10, 3 );

// Register Custom Post Type
function hero_slider_plugin_register_post_type() {
    $labels = array(
        'name'                  => __( 'Hero Slides', 'hero-slider-plugin' ),
        'singular_name'         => __( 'Hero Slide', 'hero-slider-plugin' ),
        'menu_name'             => __( 'Hero Slides', 'hero-slider-plugin' ),
        'name_admin_bar'        => __( 'Hero Slide', 'hero-slider-plugin' ),
        'add_new'               => __( 'Add New', 'hero-slider-plugin' ),
        'add_new_item'          => __( 'Add New Hero Slide', 'hero-slider-plugin' ),
        'edit_item'             => __( 'Edit Hero Slide', 'hero-slider-plugin' ),
        'new_item'              => __( 'New Hero Slide', 'hero-slider-plugin' ),
        'view_item'             => __( 'View Hero Slide', 'hero-slider-plugin' ),
        'search_items'          => __( 'Search Hero Slides', 'hero-slider-plugin' ),
        'not_found'             => __( 'No hero slides found.', 'hero-slider-plugin' ),
        'not_found_in_trash'    => __( 'No hero slides found in Trash.', 'hero-slider-plugin' ),
        'all_items'             => __( 'All Hero Slides', 'hero-slider-plugin' ),
        'featured_image'        => __( 'Slide Image', 'hero-slider-plugin' ),
        'set_featured_image'    => __( 'Set slide image', 'hero-slider-plugin' ),
        'remove_featured_image' => __( 'Remove slide image', 'hero-slider-plugin' ),
        'use_featured_image'    => __( 'Use as slide image', 'hero-slider-plugin' ),
    );

    $args = array(
        'label'               => __( 'Hero Slide', 'hero-slider-plugin' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'menu_position'       => 24,
        'menu_icon'           => 'dashicons-images-alt2',
        'capability_type'     => 'post',
        'hierarchical'        => false,
    );

    register_post_type( 'hero_slide', $args );
}
add_action( 'init', 'hero_slider_plugin_register_post_type', 0 );

// ══════════════════════════════════════════════════════════════════
// تسجيل تصنيف مجموعات السلايدر — Slider Group Taxonomy
// ══════════════════════════════════════════════════════════════════
function hero_slider_plugin_register_group_taxonomy() {
    $labels = array(
        'name'          => __( 'Slider Groups', 'hero-slider-plugin' ),
        'singular_name' => __( 'Slider Group', 'hero-slider-plugin' ),
        'menu_name'     => __( 'Slider Groups', 'hero-slider-plugin' ),
    );
    $args = array(
        'labels'       => $labels,
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => false,
        'show_ui'      => false,
        'show_in_menu' => false,
        'show_in_rest' => true,
    );
    register_taxonomy( 'hero_slider_group', array( 'hero_slide' ), $args );
}
add_action( 'init', 'hero_slider_plugin_register_group_taxonomy', 0 );

// Button Meta Box
function hero_slider_plugin_button_meta_box_content( $post ) {
    $button_text = get_post_meta( $post->ID, '_hero_slider_button_text', true );
    $button_url  = get_post_meta( $post->ID, '_hero_slider_button_url', true );
    $img_position = get_post_meta( $post->ID, '_hero_slider_img_position', true );
    $img_position = in_array( $img_position, array( 'floating', 'anchored' ), true ) ? $img_position : 'floating';
    wp_nonce_field( 'hero_slider_button_meta_nonce', 'hero_slider_button_meta_nonce' );
    ?>
    <p>
        <label for="hero-slider-button-text"><strong><?php echo esc_html__( 'Button Text', 'hero-slider-plugin' ); ?></strong></label>
        <input type="text" id="hero-slider-button-text" name="hero_slider_button_text" class="widefat" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php echo esc_attr__( 'Contact Us', 'hero-slider-plugin' ); ?>">
    </p>
    <p>
        <label for="hero-slider-button-url"><strong><?php echo esc_html__( 'Button URL', 'hero-slider-plugin' ); ?></strong></label>
        <input type="url" id="hero-slider-button-url" name="hero_slider_button_url" class="widefat" value="<?php echo esc_url( $button_url ); ?>" placeholder="<?php echo esc_attr( home_url( '/contact-us/' ) ); ?>">
    </p>
    <p><?php echo esc_html__( 'If left empty, the slider will use default values from the shortcode.', 'hero-slider-plugin' ); ?></p>
    <hr style="margin:16px 0;">
    <p>
        <label><strong><?php echo esc_html__( 'Image Position', 'hero-slider-plugin' ); ?></strong></label>
    </p>
    <p>
        <label style="margin-right:18px;">
            <input type="radio" name="hero_slider_img_position" value="floating" <?php checked( $img_position, 'floating' ); ?>>
            <?php echo esc_html__( 'Floating (centered)', 'hero-slider-plugin' ); ?>
        </label>
        <label>
            <input type="radio" name="hero_slider_img_position" value="anchored" <?php checked( $img_position, 'anchored' ); ?>>
            <?php echo esc_html__( 'Anchored to bottom', 'hero-slider-plugin' ); ?>
        </label>
    </p>
    <?php
}

function hero_slider_plugin_add_meta_boxes() {
    add_meta_box(
        'hero-slider-button-settings',
        __( 'Slide Button Settings', 'hero-slider-plugin' ),
        'hero_slider_plugin_button_meta_box_content',
        'hero_slide',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'hero_slider_plugin_add_meta_boxes' );

// ══════════════════════════════════════════════════════════════════
// ميتا بوكس اختيار مجموعة الشريحة — Slide Group Meta Box
// ══════════════════════════════════════════════════════════════════
add_action( 'add_meta_boxes', 'hero_slider_plugin_add_group_meta_box' );
function hero_slider_plugin_add_group_meta_box() {
    add_meta_box(
        'hero-slider-group',
        __( 'Slider Group', 'hero-slider-plugin' ),
        'hero_slider_plugin_group_meta_box_content',
        'hero_slide',
        'side',
        'default'
    );
}

function hero_slider_plugin_group_meta_box_content( $post ) {
    wp_nonce_field( 'hero_slider_group_meta_nonce', 'hero_slider_group_meta_nonce' );
    $terms = get_the_terms( $post->ID, 'hero_slider_group' );
    $current = ( $terms && ! is_wp_error( $terms ) ) ? (int) $terms[0]->term_id : 0;
    $all_groups = get_terms(
        array(
            'taxonomy'   => 'hero_slider_group',
            'hide_empty' => false,
            'orderby'    => 'term_id',
            'order'      => 'ASC',
        )
    );
    ?>
    <p>
        <label for="hero-slider-group-select"><strong><?php echo esc_html__( 'Assign to Group', 'hero-slider-plugin' ); ?> <span style="color:#b32d2e;">*</span></strong></label>
    </p>
    <?php if ( empty( $all_groups ) || is_wp_error( $all_groups ) ) : ?>
        <p class="description" style="color:#b32d2e;">
            <?php
            echo wp_kses_post(
                sprintf(
                    /* translators: %s: admin URL to create a slider group */
                    __( 'No slider groups found. <a href="%s">Create a group first</a>, then assign this slide.', 'hero-slider-plugin' ),
                    esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=add' ) )
                )
            );
            ?>
        </p>
    <?php else : ?>
        <select id="hero-slider-group-select" name="hero_slider_group" style="width:100%;" required>
            <option value="" disabled <?php selected( $current, 0 ); ?>>
                <?php echo esc_html__( '— Select a group —', 'hero-slider-plugin' ); ?>
            </option>
            <?php foreach ( $all_groups as $group ) : ?>
                <option value="<?php echo esc_attr( $group->term_id ); ?>" <?php selected( $current, $group->term_id ); ?>>
                    <?php echo esc_html( $group->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php echo esc_html__( 'You must choose a group manually. The slide will not be saved as published until a group is selected.', 'hero-slider-plugin' ); ?></p>
    <?php endif; ?>
    <?php
}

add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_group_meta' );
function hero_slider_plugin_save_group_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_group_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hero_slider_group_meta_nonce'], 'hero_slider_group_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $group_id = isset( $_POST['hero_slider_group'] ) ? intval( $_POST['hero_slider_group'] ) : 0;

    if ( $group_id <= 0 ) {
        wp_delete_object_term_relationships( $post_id, 'hero_slider_group' );
        set_transient( 'hsp_slide_group_error_' . $post_id, 1, MINUTE_IN_SECONDS );

        $post = get_post( $post_id );
        if ( $post && in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) ) {
            remove_action( 'save_post_hero_slide', 'hero_slider_plugin_save_group_meta' );
            wp_update_post(
                array(
                    'ID'          => $post_id,
                    'post_status' => 'draft',
                )
            );
            add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_group_meta' );
        }
        return;
    }

    wp_set_object_terms( $post_id, array( $group_id ), 'hero_slider_group', false );
    delete_transient( 'hsp_slide_group_error_' . $post_id );
}

add_action( 'admin_notices', 'hero_slider_plugin_slide_group_required_notice' );
function hero_slider_plugin_slide_group_required_notice() {
    if ( ! is_admin() ) {
        return;
    }

    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'hero_slide' !== $screen->post_type ) {
        return;
    }

    $post_id = 0;
    if ( isset( $_GET['post'] ) ) {
        $post_id = (int) $_GET['post'];
    } elseif ( isset( $_POST['post_ID'] ) ) {
        $post_id = (int) $_POST['post_ID'];
    }

    if ( $post_id <= 0 || ! get_transient( 'hsp_slide_group_error_' . $post_id ) ) {
        return;
    }

    delete_transient( 'hsp_slide_group_error_' . $post_id );
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo esc_html__( 'Please select a slider group before publishing this slide.', 'hero-slider-plugin' ); ?></p>
    </div>
    <?php
}

// ══════════════════════════════════════════════════════════════════
// إعداد الصورة البارزة على الموبايل - Mobile Featured Image Setting
// ══════════════════════════════════════════════════════════════════
add_action( 'add_meta_boxes', 'hero_slider_plugin_add_mobile_img_box' );
function hero_slider_plugin_add_mobile_img_box() {
    add_meta_box(
        'hero-slider-mobile-img',
        __( 'Featured Image — Mobile', 'hero-slider-plugin' ),
        'hero_slider_plugin_mobile_img_box_content',
        'hero_slide',
        'side',
        'high'
    );
}

function hero_slider_plugin_mobile_img_box_content( $post ) {
    $stored          = get_post_meta( $post->ID, '_hero_slider_img_mobile', true );
    $group_settings  = hero_slider_plugin_get_group_settings_for_slide( $post->ID );
    $group_mobile    = isset( $group_settings['featured_img_mobile'] ) ? $group_settings['featured_img_mobile'] : 'show';
    $group_labels    = array(
        'show'    => __( 'Show on all screens', 'hero-slider-plugin' ),
        'hide'    => __( 'Hide on mobile — under 768px', 'hero-slider-plugin' ),
        'hide_xs' => __( 'Hide on very small screens — under 576px', 'hero-slider-plugin' ),
    );
    $group_label     = isset( $group_labels[ $group_mobile ] ) ? $group_labels[ $group_mobile ] : $group_labels['show'];
    $uses_inherit    = ( '' === $stored || ! in_array( $stored, array( 'show', 'hide', 'hide_xs' ), true ) );
    wp_nonce_field( 'hero_slider_mobile_img_nonce', 'hero_slider_mobile_img_nonce' );
    ?>
    <style>
    .hsp-mob-box { font-family: -apple-system, sans-serif; }
    .hsp-mob-opt {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 10px 12px; border: 2px solid #e0e0e0;
        border-radius: 8px; margin-bottom: 8px; cursor: pointer;
        transition: border-color .18s, background .18s;
    }
    .hsp-mob-opt:has(input:checked) { border-color: #2271b1; background: #f0f6fc; }
    .hsp-mob-opt input { margin-top: 3px; flex-shrink: 0; }
    .hsp-mob-ttl { font-weight: 600; font-size: 13px; color: #1d2327; display: block; }
    .hsp-mob-dsc { font-size: 11px; color: #646970; margin-top: 2px; line-height: 1.4; display: block; }
    .hsp-mob-prev {
        display: flex; gap: 6px; margin-top: 12px;
        padding: 10px; background: #f0f0f1; border-radius: 6px;
    }
    .hsp-mob-scr {
        flex: 1; background: #fff; border-radius: 4px;
        border: 1px solid #c3c4c7; padding: 6px 4px;
        font-size: 9px; color: #2271b1; font-weight: 600; text-align: center;
    }
    .hsp-mob-img {
        height: 26px; background: #e0e0e0; border-radius: 3px;
        margin: 4px 0; display: flex; align-items: center;
        justify-content: center; font-size: 13px; color: #555;
        transition: all .25s;
    }
    .hsp-mob-img.hsp-hid {
        background: repeating-linear-gradient(
            45deg, #f5f5f5, #f5f5f5 3px, #e0e0e0 3px, #e0e0e0 6px
        ); color: #bbb;
    }
    .hsp-mob-ln { height: 5px; background: #d0d0d0; border-radius: 2px; margin: 3px 0; }
    </style>
    <div class="hsp-mob-box">
        <p style="font-size:12px;color:#646970;margin:0 0 10px;line-height:1.5">
            <?php esc_html_e( 'Control featured image visibility by screen size.', 'hero-slider-plugin' ); ?>
        </p>

        <label class="hsp-mob-opt">
            <input type="radio" name="_hero_slider_img_mobile" value=""
                   <?php checked( $uses_inherit ); ?>>
            <div>
                <span class="hsp-mob-ttl">↩ <?php esc_html_e( 'Use group setting', 'hero-slider-plugin' ); ?></span>
                <span class="hsp-mob-dsc"><?php echo esc_html( $group_label ); ?></span>
            </div>
        </label>

        <label class="hsp-mob-opt">
            <input type="radio" name="_hero_slider_img_mobile" value="show"
                   <?php checked( ! $uses_inherit && 'show' === $stored ); ?>>
            <div>
                <span class="hsp-mob-ttl">✅ <?php esc_html_e( 'Show on all screens', 'hero-slider-plugin' ); ?></span>
                <span class="hsp-mob-dsc"><?php esc_html_e( 'Default — image visible on all devices.', 'hero-slider-plugin' ); ?></span>
            </div>
        </label>

        <label class="hsp-mob-opt">
            <input type="radio" name="_hero_slider_img_mobile" value="hide"
                   <?php checked( ! $uses_inherit && 'hide' === $stored ); ?>>
            <div>
                <span class="hsp-mob-ttl">🚫 <?php esc_html_e( 'Hide on mobile (under 768px)', 'hero-slider-plugin' ); ?></span>
                <span class="hsp-mob-dsc"><?php esc_html_e( 'Image hides on phones and small tablets; text expands to fill the screen.', 'hero-slider-plugin' ); ?></span>
            </div>
        </label>

        <label class="hsp-mob-opt">
            <input type="radio" name="_hero_slider_img_mobile" value="hide_xs"
                   <?php checked( ! $uses_inherit && 'hide_xs' === $stored ); ?>>
            <div>
                <span class="hsp-mob-ttl">📵 <?php esc_html_e( 'Hide on very small screens (under 576px)', 'hero-slider-plugin' ); ?></span>
                <span class="hsp-mob-dsc"><?php esc_html_e( 'Hides only on small phones like iPhone SE; stays visible on tablets.', 'hero-slider-plugin' ); ?></span>
            </div>
        </label>

        <div class="hsp-mob-prev">
            <div class="hsp-mob-scr">
                💻 Desktop
                <div class="hsp-mob-img" id="hsp-pv-d">🖼</div>
                <div class="hsp-mob-ln"></div>
                <div class="hsp-mob-ln" style="width:65%"></div>
            </div>
            <div class="hsp-mob-scr">
                🖥 768px
                <div class="hsp-mob-img" id="hsp-pv-t">🖼</div>
                <div class="hsp-mob-ln"></div>
                <div class="hsp-mob-ln" style="width:55%"></div>
            </div>
            <div class="hsp-mob-scr">
                📱 375px
                <div class="hsp-mob-img" id="hsp-pv-m">🖼</div>
                <div class="hsp-mob-ln"></div>
                <div class="hsp-mob-ln" style="width:45%"></div>
            </div>
        </div>
        <p style="font-size:11px;color:#999;margin:8px 0 0">
            ℹ️ <?php esc_html_e( 'Leave on group setting to apply the value from the slider group. Override only when this slide needs different behavior.', 'hero-slider-plugin' ); ?>
        </p>
    </div>
    <script>
    (function(){
        var groupMobile = <?php echo wp_json_encode( $group_mobile ); ?>;
        var radios = document.querySelectorAll('input[name="_hero_slider_img_mobile"]');
        var d = document.getElementById('hsp-pv-d');
        var t = document.getElementById('hsp-pv-t');
        var m = document.getElementById('hsp-pv-m');
        function effectiveValue(v) {
            return v ? v : groupMobile;
        }
        function upd(v) {
            v = effectiveValue(v);
            [d,t,m].forEach(function(el){ el.classList.remove('hsp-hid'); el.textContent='🖼'; });
            if(v==='hide')    { t.classList.add('hsp-hid'); t.textContent='✖'; m.classList.add('hsp-hid'); m.textContent='✖'; }
            if(v==='hide_xs') { m.classList.add('hsp-hid'); m.textContent='✖'; }
        }
        radios.forEach(function(r){ r.addEventListener('change',function(){ upd(this.value); }); if(r.checked) upd(r.value); });
    })();
    </script>
    <?php
}

// ══════════════════════════════════════════════════════════════════
// حفظ إعداد الصورة البارزة على الموبايل
// ══════════════════════════════════════════════════════════════════
add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_mobile_img_meta' );
function hero_slider_plugin_save_mobile_img_meta( $post_id ) {
    if ( function_exists( 'hero_slider_portal_slide_group_is_portal_from_request' ) && hero_slider_portal_slide_group_is_portal_from_request( $post_id ) ) {
        return;
    }

    if ( ! isset( $_POST['hero_slider_mobile_img_nonce'] ) ||
         ! wp_verify_nonce( $_POST['hero_slider_mobile_img_nonce'], 'hero_slider_mobile_img_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $allowed = array( 'show', 'hide', 'hide_xs' );
    $value   = isset( $_POST['_hero_slider_img_mobile'] )
        ? sanitize_text_field( wp_unslash( $_POST['_hero_slider_img_mobile'] ) )
        : '';
    if ( '' === $value ) {
        delete_post_meta( $post_id, '_hero_slider_img_mobile' );
        return;
    }
    if ( ! in_array( $value, $allowed, true ) ) {
        $value = 'show';
    }
    update_post_meta( $post_id, '_hero_slider_img_mobile', $value );
}

// ══════════════════════════════════════════════════════════════════
// Meta Box — ألوان نصوص الشريحة (عنوان + وصف)
// Slide Text Colors Meta Box
// ══════════════════════════════════════════════════════════════════
add_action( 'add_meta_boxes', 'hero_slider_plugin_add_text_colors_box' );
function hero_slider_plugin_add_text_colors_box() {
    add_meta_box(
        'hero-slider-text-colors',
        __( 'Text Colors', 'hero-slider-plugin' ),
        'hero_slider_plugin_text_colors_box_content',
        'hero_slide',
        'side',
        'high'
    );
}

function hero_slider_plugin_text_colors_box_content( $post ) {
    $title_color      = get_post_meta( $post->ID, '_hero_slider_title_color',      true );
    $text_color       = get_post_meta( $post->ID, '_hero_slider_text_color',       true );
    $btn_text_color   = get_post_meta( $post->ID, '_hero_slider_btn_text_color',   true );
    // Retrieve group settings (including alignment) for this slide
    wp_nonce_field( 'hero_slider_text_colors_nonce', 'hero_slider_text_colors_nonce' );
    $group_settings = hero_slider_plugin_get_group_settings_for_slide( $post->ID );
    $theme_mode     = ( isset( $group_settings['theme_mode'] ) && 'light' === $group_settings['theme_mode'] ) ? 'light' : 'dark';
    $global_title   = ! empty( $group_settings['title_color'] ) ? $group_settings['title_color'] : ( 'light' === $theme_mode ? '#0b1220' : '#ffffff' );
    $global_text    = ! empty( $group_settings['text_color'] ) ? $group_settings['text_color'] : ( 'light' === $theme_mode ? '#4b5563' : '#dee4ed' );
    $global_btn     = ! empty( $group_settings['button_text_color'] ) ? $group_settings['button_text_color'] : '#ffffff';
    // Use alignment from group settings (fallback to center)
    $text_alignment = $group_settings['text_alignment'] ?? '';
    $title_alignment = $group_settings['title_alignment'] ?? '';
    ?>
    <style>
    .hsp-tc-row { margin-bottom: 14px; }
    .hsp-tc-row label { display: block; font-weight: 600; font-size: 12px; color: #1d2327; margin-bottom: 6px; }
    .hsp-tc-hint { font-size: 11px; color: #999; margin-top: 6px; line-height: 1.4; }
    .hsp-tc-reset { font-size: 11px; color: #2271b1; cursor: pointer; text-decoration: underline; background: none; border: none; padding: 0; margin-top: 4px; display: block; }
    </style>
    <div>
        <p style="font-size:12px;color:#646970;margin:0 0 12px;line-height:1.5">
            <?php esc_html_e( 'Leave empty to use the assigned slider group colors.', 'hero-slider-plugin' ); ?>
        </p>

        <div class="hsp-tc-row">
            <label for="hsp-title-color"><?php esc_html_e( 'Title Color', 'hero-slider-plugin' ); ?></label>
            <input type="text" id="hsp-title-color" name="_hero_slider_title_color"
                   class="hsp-slide-color-field" value="<?php echo esc_attr( $title_color ); ?>"
                   data-default-color="<?php echo esc_attr( $global_title ); ?>">
            <button type="button" class="hsp-tc-reset" data-target="hsp-title-color">
                ↺ <?php esc_html_e( 'Reset', 'hero-slider-plugin' ); ?>
            </button>
        </div>

        <div class="hsp-tc-row">
            <label for="hsp-text-color"><?php esc_html_e( 'Description Color', 'hero-slider-plugin' ); ?></label>
            <input type="text" id="hsp-text-color" name="_hero_slider_text_color"
                   class="hsp-slide-color-field" value="<?php echo esc_attr( $text_color ); ?>"
                   data-default-color="<?php echo esc_attr( $global_text ); ?>">
            <button type="button" class="hsp-tc-reset" data-target="hsp-text-color">
                ↺ <?php esc_html_e( 'Reset', 'hero-slider-plugin' ); ?>
            </button>
        </div>

        <div class="hsp-tc-row">
            <label for="hsp-btn-text-color"><?php esc_html_e( 'Button Text Color', 'hero-slider-plugin' ); ?></label>
            <input type="text" id="hsp-btn-text-color" name="_hero_slider_btn_text_color"
                   class="hsp-slide-color-field" value="<?php echo esc_attr( $btn_text_color ); ?>"
                   data-default-color="<?php echo esc_attr( $global_btn ); ?>">
            <button type="button" class="hsp-tc-reset" data-target="hsp-btn-text-color">
                ↺ <?php esc_html_e( 'Reset', 'hero-slider-plugin' ); ?>
            </button>
        </div>

        <!-- Text alignment removed from slide settings; managed at group level -->

        <div class="hsp-tc-row">
            <label for="hsp-btn-alignment"><?php esc_html_e( 'Button Alignment', 'hero-slider-plugin' ); ?></label>
            <select id="hsp-btn-alignment" name="_hero_slider_button_alignment" class="widefat">
                <option value="left" <?php selected( $button_alignment, 'left' ); ?>><?php esc_html_e( 'Left', 'hero-slider-plugin' ); ?></option>
                <option value="center" <?php selected( $button_alignment, 'center' ); ?>><?php esc_html_e( 'Center', 'hero-slider-plugin' ); ?></option>
                <option value="right" <?php selected( $button_alignment, 'right' ); ?>><?php esc_html_e( 'Right', 'hero-slider-plugin' ); ?></option>
            </select>
        </div>

        <p class="hsp-tc-hint">
            ℹ️ <?php esc_html_e( 'This setting is independent for each slide.', 'hero-slider-plugin' ); ?>
        </p>
    </div>
    <script>
    jQuery(function($){
        if (typeof $.fn.wpColorPicker === 'function') {
            $('.hsp-slide-color-field').wpColorPicker();
        }
        $('.hsp-tc-reset').on('click', function(){
            var id = $(this).data('target');
            var $input = $('#' + id);
            $input.val('');
            // إعادة تعيين واجهة color picker
            var $container = $input.closest('.wp-picker-container');
            if ($container.length) {
                $container.find('.wp-color-result').css('background-color', '');
                $container.find('.wp-picker-clear').trigger('click');
            }
        });
    });
    </script>
    <?php
}

// ══════════════════════════════════════════════════════════════════
// حفظ ألوان نصوص الشريحة
// ══════════════════════════════════════════════════════════════════
add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_text_colors_meta' );
function hero_slider_plugin_save_text_colors_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_text_colors_nonce'] ) ||
         ! wp_verify_nonce( $_POST['hero_slider_text_colors_nonce'], 'hero_slider_text_colors_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $title_color    = isset( $_POST['_hero_slider_title_color'] )
        ? sanitize_hex_color( wp_unslash( $_POST['_hero_slider_title_color'] ) )
        : '';
    $text_color     = isset( $_POST['_hero_slider_text_color'] )
        ? sanitize_hex_color( wp_unslash( $_POST['_hero_slider_text_color'] ) )
        : '';
    $btn_text_color = isset( $_POST['_hero_slider_btn_text_color'] )
        ? sanitize_hex_color( wp_unslash( $_POST['_hero_slider_btn_text_color'] ) )
        : '';

    if ( $title_color ) {
        update_post_meta( $post_id, '_hero_slider_title_color', $title_color );
    } else {
        delete_post_meta( $post_id, '_hero_slider_title_color' );
    }

    if ( $text_color ) {
        update_post_meta( $post_id, '_hero_slider_text_color', $text_color );
    } else {
        delete_post_meta( $post_id, '_hero_slider_text_color' );
    }

    if ( $btn_text_color ) {
        update_post_meta( $post_id, '_hero_slider_btn_text_color', $btn_text_color );
    } else {
        delete_post_meta( $post_id, '_hero_slider_btn_text_color' );
    }
}



function hero_slider_plugin_save_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_button_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hero_slider_button_meta_nonce'], 'hero_slider_button_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $button_text = isset( $_POST['hero_slider_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['hero_slider_button_text'] ) ) : '';
    $button_url  = isset( $_POST['hero_slider_button_url'] ) ? esc_url_raw( wp_unslash( $_POST['hero_slider_button_url'] ) ) : '';

    if ( '' !== $button_text ) {
        update_post_meta( $post_id, '_hero_slider_button_text', $button_text );
    } else {
        delete_post_meta( $post_id, '_hero_slider_button_text' );
    }

    if ( '' !== $button_url ) {
        update_post_meta( $post_id, '_hero_slider_button_url', $button_url );
    } else {
        delete_post_meta( $post_id, '_hero_slider_button_url' );
    }

    $img_position = isset( $_POST['hero_slider_img_position'] ) ? sanitize_text_field( wp_unslash( $_POST['hero_slider_img_position'] ) ) : 'floating';
    $img_position = in_array( $img_position, array( 'floating', 'anchored' ), true ) ? $img_position : 'floating';
    update_post_meta( $post_id, '_hero_slider_img_position', $img_position );
    // Alignment settings are now managed at group level; per‑slide alignment meta removed.
}
add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_meta' );

function hero_slider_plugin_background_meta_box_content( $post ) {
    $bg_id      = get_post_meta( $post->ID, '_hero_slider_bg_id', true );
    $bg_url     = $bg_id ? wp_get_attachment_image_url( (int) $bg_id, 'full' ) : get_post_meta( $post->ID, '_hero_slider_bg_url', true );
    $tint_color = get_post_meta( $post->ID, '_hero_slider_tint_color', true );
    $tint_alpha = get_post_meta( $post->ID, '_hero_slider_tint_alpha', true );
    $disable_overlay = get_post_meta( $post->ID, '_hero_slider_disable_overlay', true ) === 'yes' ? 'yes' : 'no';

    $tint_color = $tint_color ? $tint_color : '#000000';
    $tint_alpha = is_numeric( $tint_alpha ) ? max( 0, min( 1, (float) $tint_alpha ) ) : 0.35;

    wp_nonce_field( 'hero_slider_background_meta_nonce', 'hero_slider_background_meta_nonce' );
    ?>
    <div class="hero-slider-bg-meta">
        <?php if ( function_exists( 'hero_slider_portal_slide_group_is_portal' ) && hero_slider_portal_slide_group_is_portal( $post->ID ) ) : ?>
            <p class="description" style="margin:0 0 12px;">
                <?php esc_html_e( 'Portal: if a background image is set it will be shown. If empty, the group geometric pattern is used instead.', 'hero-slider-plugin' ); ?>
            </p>
        <?php endif; ?>
        <p>
            <label for="hero-slider-bg-url"><strong><?php echo esc_html__( 'Background Image', 'hero-slider-plugin' ); ?></strong></label>
            <div style="margin:8px 0; display:flex; gap:8px; align-items:center;">
                <input type="hidden" id="hero-slider-bg-id" name="hero_slider_bg_id" value="<?php echo esc_attr( $bg_id ); ?>">
                <input type="url" id="hero-slider-bg-url" name="hero_slider_bg_url" class="regular-text" style="width:60%" value="<?php echo esc_url( $bg_url ); ?>" placeholder="<?php echo esc_attr__( 'https://example.com/bg.jpg', 'hero-slider-plugin' ); ?>">
                <button type="button" class="button hero-slider-bg-choose"><?php echo esc_html__( 'Choose', 'hero-slider-plugin' ); ?></button>
                <button type="button" class="button hero-slider-bg-remove"><?php echo esc_html__( 'Remove', 'hero-slider-plugin' ); ?></button>
            </div>
            <img id="hero-slider-bg-preview" src="<?php echo esc_url( $bg_url ); ?>" style="max-width:100%; height:auto; <?php echo $bg_url ? '' : 'display:none;'; ?> border:1px solid #ddd; border-radius:6px;"/>
        </p>
        <p>
            <label for="hero-slider-tint-color"><strong><?php echo esc_html__( 'Overlay Color', 'hero-slider-plugin' ); ?></strong></label><br>
            <input type="text" id="hero-slider-tint-color" name="hero_slider_tint_color" class="hero-color-field" value="<?php echo esc_attr( $tint_color ); ?>" data-default-color="#000000">
        </p>
        <p>
            <label for="hero-slider-tint-alpha"><strong><?php echo esc_html__( 'Overlay Opacity (0–1)', 'hero-slider-plugin' ); ?></strong></label><br>
            <input type="number" id="hero-slider-tint-alpha" name="hero_slider_tint_alpha" min="0" max="1" step="0.05" value="<?php echo esc_attr( $tint_alpha ); ?>" style="width:90px;"> 
        </p>
        <p>
            <label>
                <input type="checkbox" name="hero_slider_disable_overlay" value="yes" <?php checked( $disable_overlay, 'yes' ); ?>>
                <?php echo esc_html__( 'Disable overlays (show full background)', 'hero-slider-plugin' ); ?>
            </label>
            <br><span class="description"><?php echo esc_html__( 'Hides the gradient and color tint layers so the background image appears without transparency.', 'hero-slider-plugin' ); ?></span>
        </p>
    </div>
    <script>
    jQuery(function($){
        if (typeof wp !== 'undefined' && wp.media && typeof $.fn.wpColorPicker === 'function') {
            $('#hero-slider-tint-color').wpColorPicker();
        }
        function toggleTintFields(){
            var dis = $('input[name="hero_slider_disable_overlay"]').is(':checked');
            $('#hero-slider-tint-color').prop('disabled', dis);
            $('#hero-slider-tint-alpha').prop('disabled', dis);
        }
        $('input[name="hero_slider_disable_overlay"]').on('change', toggleTintFields);
        toggleTintFields();
        $('.hero-slider-bg-choose').on('click', function(e){
            e.preventDefault();
            if (!window.heroSliderBgFrame) {
                window.heroSliderBgFrame = wp.media({ title: '<?php echo esc_js( __( 'Select Background', 'hero-slider-plugin' ) ); ?>', button: { text: '<?php echo esc_js( __( 'Use image', 'hero-slider-plugin' ) ); ?>' }, multiple: false });
                window.heroSliderBgFrame.on('select', function(){
                    var att = window.heroSliderBgFrame.state().get('selection').first().toJSON();
                    $('#hero-slider-bg-id').val(att.id);
                    $('#hero-slider-bg-url').val(att.url).trigger('change');
                    $('#hero-slider-bg-preview').attr('src', att.url).show();
                });
            }
            window.heroSliderBgFrame.open();
        });
        $('.hero-slider-bg-remove').on('click', function(){
            $('#hero-slider-bg-id').val('');
            $('#hero-slider-bg-url').val('').trigger('change');
            $('#hero-slider-bg-preview').hide().attr('src','');
        });
    });
    </script>
    <?php
}

function hero_slider_plugin_add_background_box() {
    add_meta_box(
        'hero-slider-background-settings',
        __( 'Background & Overlay', 'hero-slider-plugin' ),
        'hero_slider_plugin_background_meta_box_content',
        'hero_slide',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'hero_slider_plugin_add_background_box' );

function hero_slider_plugin_save_background_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_background_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hero_slider_background_meta_nonce'], 'hero_slider_background_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $bg_id  = isset( $_POST['hero_slider_bg_id'] ) ? intval( $_POST['hero_slider_bg_id'] ) : 0;
    $bg_url = isset( $_POST['hero_slider_bg_url'] ) ? esc_url_raw( wp_unslash( $_POST['hero_slider_bg_url'] ) ) : '';
    $color  = isset( $_POST['hero_slider_tint_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['hero_slider_tint_color'] ) ) : '';
    $alpha  = isset( $_POST['hero_slider_tint_alpha'] ) ? (float) wp_unslash( $_POST['hero_slider_tint_alpha'] ) : 0.35;
    $disable = ( isset( $_POST['hero_slider_disable_overlay'] ) && 'yes' === $_POST['hero_slider_disable_overlay'] ) ? 'yes' : 'no';

    if ( $bg_id ) {
        update_post_meta( $post_id, '_hero_slider_bg_id', $bg_id );
    } else {
        delete_post_meta( $post_id, '_hero_slider_bg_id' );
    }

    if ( $bg_url ) {
        update_post_meta( $post_id, '_hero_slider_bg_url', $bg_url );
    } else {
        delete_post_meta( $post_id, '_hero_slider_bg_url' );
    }

    if ( $color ) {
        update_post_meta( $post_id, '_hero_slider_tint_color', $color );
    } else {
        delete_post_meta( $post_id, '_hero_slider_tint_color' );
    }

    $alpha = max( 0, min( 1, $alpha ) );
    update_post_meta( $post_id, '_hero_slider_tint_alpha', $alpha );
    if ( 'yes' === $disable ) {
        update_post_meta( $post_id, '_hero_slider_disable_overlay', 'yes' );
    } else {
        delete_post_meta( $post_id, '_hero_slider_disable_overlay' );
    }
}
add_action( 'save_post_hero_slide', 'hero_slider_plugin_save_background_meta' );

function hero_slider_plugin_admin_assets( $hook ) {
    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }

    // صفحة تعديل الشريحة
    if ( 'hero_slide' === $screen->id || 'hero_slide' === $screen->post_type ) {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        return;
    }

    // صفحة إعدادات مجموعات الشرائح
    if ( 'hero_slide_page_hero-slider-groups' === $screen->id ) {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }
}
add_action( 'admin_enqueue_scripts', 'hero_slider_plugin_admin_assets' );

// Instructions Page
function hero_slider_plugin_add_instructions_page() {
    add_submenu_page(
        'edit.php?post_type=hero_slide',
        __( 'Shortcode Instructions', 'hero-slider-plugin' ),
        __( 'Instructions', 'hero-slider-plugin' ),
        'edit_posts',
        'hero-slider-instructions',
        'hero_slider_plugin_instructions_page'
    );
}
add_action( 'admin_menu', 'hero_slider_plugin_add_instructions_page' );

function hero_slider_plugin_instructions_page() {
    $example_group_id   = '12345';
    $groups             = get_terms(
        array(
            'taxonomy'   => 'hero_slider_group',
            'hide_empty' => false,
            'number'     => 1,
            'orderby'    => 'term_id',
            'order'      => 'ASC',
        )
    );
    if ( ! empty( $groups ) && ! is_wp_error( $groups ) ) {
        $sid = hero_slider_plugin_get_term_shortcode_id( $groups[0]->term_id );
        if ( $sid ) {
            $example_group_id = $sid;
        }
    }
    $shortcode_basic    = '[hero_slider group="' . esc_attr( $example_group_id ) . '"]';
    $shortcode_advanced = '[hero_slider group="' . esc_attr( $example_group_id ) . '" limit="5" autoplay="yes" autoplay_speed="5500" height="760" show_dots="yes" show_arrows="yes" button_text="' . esc_attr__( 'Contact Us', 'hero-slider-plugin' ) . '" button_url="/contact-us/" stick_to_top="yes" header_offset="110" order="ASC" orderby="menu_order" text_length="28"]';
    ?>
    <style>
        .hsp-wrap { max-width: 860px; margin: 20px 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .hsp-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 12px; padding: 28px 32px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .hsp-card h2 { margin: 0 0 16px; font-size: 20px; color: #1d2327; display: flex; align-items: center; gap: 8px; }
        .hsp-card h2 .dashicons { color: #2271b1; }
        .hsp-code-block { position: relative; background: #1e1e2e; color: #cdd6f4; border-radius: 8px; padding: 18px 20px; font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace; font-size: 13px; line-height: 1.7; overflow-x: auto; direction: ltr; text-align: left; margin: 12px 0; }
        .hsp-code-inline { display: inline-block; background: #f0f0f1; color: #1d2327; padding: 2px 8px; border-radius: 4px; font-family: "SFMono-Regular", Consolas, monospace; font-size: 13px; direction: ltr; cursor: pointer; border: 1px solid #ddd; }
        .hsp-copy-btn { position: absolute; top: 8px; right: 8px; background: rgba(255,255,255,0.1); color: #cdd6f4; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 4px 12px; font-size: 12px; cursor: pointer; transition: all 0.2s; }
        .hsp-copy-btn:hover { background: rgba(255,255,255,0.2); }
        .hsp-copy-btn.copied { background: #40a02b; color: #fff; border-color: #40a02b; }
        .hsp-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; margin: 12px 0; }
        .hsp-table th { background: #f6f7f7; text-align: right; padding: 12px 16px; font-size: 13px; color: #50575e; border-bottom: 1px solid #e0e0e0; }
        .hsp-table td { padding: 11px 16px; font-size: 13px; border-bottom: 1px solid #f0f0f1; color: #2c3338; }
        .hsp-table tr:last-child td { border-bottom: none; }
        .hsp-table tr:hover td { background: #f9f9f9; }
        .hsp-table code { background: #f0f0f1; padding: 2px 6px; border-radius: 3px; font-size: 12px; direction: ltr; display: inline-block; }
        .hsp-badge { display: inline-block; background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .hsp-steps { counter-reset: step; list-style: none; padding: 0; margin: 12px 0; }
        .hsp-steps li { counter-increment: step; padding: 10px 0 10px 44px; position: relative; border-bottom: 1px solid #f0f0f1; font-size: 14px; color: #2c3338; }
        .hsp-steps li:last-child { border-bottom: none; }
        .hsp-steps li::before { content: counter(step); position: absolute; right: auto; left: 0; top: 8px; width: 30px; height: 30px; background: #2271b1; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; }
        .hsp-header { display: flex; align-items: center; gap: 14px; margin-bottom: 6px; }
        .hsp-header .dashicons { font-size: 36px; width: 36px; height: 36px; color: #2271b1; }
        .hsp-header h1 { margin: 0; font-size: 26px; color: #1d2327; }
        .hsp-header p { margin: 4px 0 0; color: #646970; font-size: 14px; }
    </style>

    <div class="wrap hsp-wrap">
        <div class="hsp-header">
            <span class="dashicons dashicons-images-alt2"></span>
            <div>
                <h1><?php echo esc_html__( 'Hero Slider — Shortcode Instructions', 'hero-slider-plugin' ); ?></h1>
                <p><?php echo esc_html__( 'Learn how to display the hero slider anywhere on your site.', 'hero-slider-plugin' ); ?></p>
            </div>
        </div>

        <!-- Quick Start -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-flag"></span> <?php echo esc_html__( 'Quick Start', 'hero-slider-plugin' ); ?></h2>
            <ol class="hsp-steps">
                <li><?php echo wp_kses_post( __( 'Go to <strong>Hero Slides → Slider Groups</strong> and create a group with your preferred settings.', 'hero-slider-plugin' ) ); ?></li>
                <li><?php echo wp_kses_post( __( 'Go to <strong>Hero Slides → Add New</strong>, assign the slide to a group, then set the title, content, and image.', 'hero-slider-plugin' ) ); ?></li>
                <li><?php echo wp_kses_post( __( 'Paste the group shortcode below into any <strong>Page</strong>, <strong>Post</strong>, or <strong>Widget</strong>.', 'hero-slider-plugin' ) ); ?></li>
            </ol>
        </div>

        <!-- Basic Shortcode -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-shortcode"></span> <?php echo esc_html__( 'Basic Shortcode', 'hero-slider-plugin' ); ?></h2>
            <p><?php echo esc_html__( 'Copy and paste this shortcode to display a slider group (group ID is required):', 'hero-slider-plugin' ); ?></p>
            <div class="hsp-code-block">
                <button type="button" class="hsp-copy-btn" data-copy="<?php echo esc_attr( $shortcode_basic ); ?>"><?php echo esc_html__( 'Copy', 'hero-slider-plugin' ); ?></button>
                <?php echo esc_html( $shortcode_basic ); ?>
            </div>
        </div>

        <!-- Advanced Shortcode -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-admin-settings"></span> <?php echo esc_html__( 'Advanced Shortcode (All Options)', 'hero-slider-plugin' ); ?></h2>
            <p><?php echo esc_html__( 'Use this version to customize every option:', 'hero-slider-plugin' ); ?></p>
            <div class="hsp-code-block">
                <button type="button" class="hsp-copy-btn" data-copy="<?php echo esc_attr( $shortcode_advanced ); ?>"><?php echo esc_html__( 'Copy', 'hero-slider-plugin' ); ?></button>
                <?php echo esc_html( $shortcode_advanced ); ?>
            </div>
        </div>

        <!-- Parameters Table -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-editor-table"></span> <?php echo esc_html__( 'Available Parameters', 'hero-slider-plugin' ); ?></h2>
            <table class="hsp-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( 'Parameter', 'hero-slider-plugin' ); ?></th>
                        <th><?php echo esc_html__( 'Default', 'hero-slider-plugin' ); ?></th>
                        <th><?php echo esc_html__( 'Description', 'hero-slider-plugin' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>group</code></td>
                        <td><span class="hsp-badge"><?php echo esc_html__( 'Required', 'hero-slider-plugin' ); ?></span></td>
                        <td><?php echo esc_html__( 'Slider group ID from Hero Slides → Slider Groups.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td><code>-1</code></td>
                        <td><?php echo esc_html__( 'Number of slides to display. Use -1 for all.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>autoplay</code></td>
                        <td><code>yes</code></td>
                        <td><?php echo esc_html__( 'Enable auto-play. Values: yes / no', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>autoplay_speed</code></td>
                        <td><code>5500</code></td>
                        <td><?php echo esc_html__( 'Auto-play interval in milliseconds.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_dots</code></td>
                        <td><code>yes</code></td>
                        <td><?php echo esc_html__( 'Show navigation dots. Values: yes / no', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_arrows</code></td>
                        <td><code>yes</code></td>
                        <td><?php echo esc_html__( 'Show navigation arrows. Values: yes / no', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>height</code></td>
                        <td><code>760</code></td>
                        <td><?php echo esc_html__( 'Slider height in pixels.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>text_length</code></td>
                        <td><code>28</code></td>
                        <td><?php echo esc_html__( 'Maximum number of words for the description.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>button_text</code></td>
                        <td><code>Contact Us</code></td>
                        <td><?php echo esc_html__( 'Default button label (can be overridden per slide).', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>button_url</code></td>
                        <td><code>/contact-us/</code></td>
                        <td><?php echo esc_html__( 'Default button link (can be overridden per slide).', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>stick_to_top</code></td>
                        <td><code>yes</code></td>
                        <td><?php echo esc_html__( 'Attach slider to the top of the page behind the header.', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>header_offset</code></td>
                        <td><code>110</code></td>
                        <td><?php echo esc_html__( 'Header height offset in pixels (used with stick_to_top).', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>order</code></td>
                        <td><code>ASC</code></td>
                        <td><?php echo esc_html__( 'Sort order. Values: ASC / DESC', 'hero-slider-plugin' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>orderby</code></td>
                        <td><code>menu_order</code></td>
                        <td><?php echo esc_html__( 'Sort field. Values: menu_order / date / title / rand', 'hero-slider-plugin' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PHP Usage -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-editor-code"></span> <?php echo esc_html__( 'Use in PHP Templates', 'hero-slider-plugin' ); ?></h2>
            <p><?php echo esc_html__( 'To display the slider directly in a theme template file:', 'hero-slider-plugin' ); ?></p>
            <div class="hsp-code-block">
                <button type="button" class="hsp-copy-btn" data-copy="<?php echo esc_attr( "<?php echo do_shortcode('[hero_slider group=\"" . $example_group_id . "\"]'); ?>" ); ?>"><?php echo esc_html__( 'Copy', 'hero-slider-plugin' ); ?></button>
                &lt;?php echo do_shortcode('[hero_slider group="<?php echo esc_attr( $example_group_id ); ?>"]'); ?&gt;
            </div>
        </div>

        <!-- Tips -->
        <div class="hsp-card">
            <h2><span class="dashicons dashicons-lightbulb"></span> <?php echo esc_html__( 'Tips', 'hero-slider-plugin' ); ?></h2>
            <ul style="margin: 8px 0; padding-right: 20px; list-style: disc;">
                <li><?php echo esc_html__( 'Use the "Order" field (Page Attributes) on each slide to control display order.', 'hero-slider-plugin' ); ?></li>
                <li><?php echo esc_html__( 'Each slide can have its own button text and URL — set them in "Slide Button Settings".', 'hero-slider-plugin' ); ?></li>
                <li><?php echo esc_html__( 'The Featured Image appears as the main visual; the Background Image fills the slide background.', 'hero-slider-plugin' ); ?></li>
                <li><?php echo esc_html__( 'If only one slide exists, the slider controls (arrows/dots) are hidden automatically.', 'hero-slider-plugin' ); ?></li>
                <li><?php echo esc_html__( 'Use the Excerpt field for a custom description; otherwise the post content is used.', 'hero-slider-plugin' ); ?></li>
            </ul>
        </div>
    </div>

    <script>
    jQuery(function($) {
        $('.hsp-copy-btn').on('click', function() {
            var btn = $(this);
            var text = btn.data('copy');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    btn.text('<?php echo esc_js( __( 'Copied!', 'hero-slider-plugin' ) ); ?>').addClass('copied');
                    setTimeout(function() { btn.text('<?php echo esc_js( __( 'Copy', 'hero-slider-plugin' ) ); ?>').removeClass('copied'); }, 2000);
                });
            } else {
                var temp = $('<textarea>').val(text).appendTo('body').select();
                document.execCommand('copy');
                temp.remove();
                btn.text('<?php echo esc_js( __( 'Copied!', 'hero-slider-plugin' ) ); ?>').addClass('copied');
                setTimeout(function() { btn.text('<?php echo esc_js( __( 'Copy', 'hero-slider-plugin' ) ); ?>').removeClass('copied'); }, 2000);
            }
        });
    });
    </script>
    <?php
}

// Settings Page
function hero_slider_plugin_get_effects_list() {
    return array(
        'plexus'        => array(
            'name' => __( 'Particles & Beams', 'hero-slider-plugin' ),
            'desc' => __( 'Floating particles with diagonal light beams — ideal for industry & technology.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-networking',
        ),
        'bokeh'         => array(
            'name' => __( 'Bokeh', 'hero-slider-plugin' ),
            'desc' => __( 'Soft glowing circles drifting slowly — ideal for photography & creative.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-format-image',
        ),
        'constellation' => array(
            'name' => __( 'Constellation', 'hero-slider-plugin' ),
            'desc' => __( 'Stars connected with lines when nearby — ideal for space & luxury.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-star-filled',
        ),
        'rain'          => array(
            'name' => __( 'Digital Rain', 'hero-slider-plugin' ),
            'desc' => __( 'Thin vertical streaks falling down — ideal for technology & digital.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-arrow-down-alt',
        ),
        'waves'         => array(
            'name' => __( 'Flowing Waves', 'hero-slider-plugin' ),
            'desc' => __( 'Smooth horizontal sine waves — ideal for medical, wellness & ocean.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-admin-site-alt3',
        ),
        'geometric'     => array(
            'name' => __( 'Geometric Shapes', 'hero-slider-plugin' ),
            'desc' => __( 'Floating rotating polygons — ideal for architecture & engineering.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-admin-customizer',
        ),
        'snow'          => array(
            'name' => __( 'Snowfall', 'hero-slider-plugin' ),
            'desc' => __( 'Gentle falling snow particles — ideal for clean & minimal designs.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-cloud',
        ),
        'fireflies'     => array(
            'name' => __( 'Fireflies', 'hero-slider-plugin' ),
            'desc' => __( 'Pulsing glowing dots with varying brightness — ideal for nature & organic.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-lightbulb',
        ),
        'network'       => array(
            'name' => __( 'Neural Network', 'hero-slider-plugin' ),
            'desc' => __( 'Glowing nodes with connecting mesh lines and light rays — ideal for technology, data & AI.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-share',
        ),
        'tourism'      => array(
            'name' => __( 'Tourism Breeze', 'hero-slider-plugin' ),
            'desc' => __( 'Gentle floating icons of sun and palm trees — perfect for travel and tourism sites.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-palmtree',
        ),
        'academy'       => array(
            'name' => __( 'Academy', 'hero-slider-plugin' ),
            'desc' => __( 'Soft knowledge nodes with gentle connecting lines and floating orbs — ideal for education, universities & e-learning.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-welcome-learn-more',
        ),
        'autumn'        => array(
            'name' => __( 'Autumn Leaves', 'hero-slider-plugin' ),
            'desc' => __( 'Colorful falling leaves gently drifting and rotating — ideal for nature, seasons & organic themes.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-palmtree',
        ),
        'exhaust'       => array(
            'name' => __( 'Exhaust Smoke', 'hero-slider-plugin' ),
            'desc' => __( 'Thick dark smoke rising and dissipating — ideal for industrial, automotive & urban themes.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-car',
        ),
        'none'          => array(
            'name' => __( 'No Effect', 'hero-slider-plugin' ),
            'desc' => __( 'Disable all animated canvas effects.', 'hero-slider-plugin' ),
            'icon' => 'dashicons-dismiss',
        ),
    );
}

function hero_slider_plugin_sanitize_settings( $input ) {
    $defaults = hero_slider_plugin_get_group_settings_defaults();
    $input    = is_array( $input ) ? $input : array();

    $clean = array();
    $clean['autoplay']         = ( isset( $input['autoplay'] ) && 'yes' === $input['autoplay'] ) ? 'yes' : 'no';
    $clean['autoplay_speed']   = isset( $input['autoplay_speed'] ) ? max( 500, intval( $input['autoplay_speed'] ) ) : $defaults['autoplay_speed'];
    $clean['show_dots']        = ( isset( $input['show_dots'] ) && 'yes' === $input['show_dots'] ) ? 'yes' : 'no';
    $clean['show_arrows']      = ( isset( $input['show_arrows'] ) && 'yes' === $input['show_arrows'] ) ? 'yes' : 'no';
    $clean['transition_speed'] = isset( $input['transition_speed'] ) ? max( 100, intval( $input['transition_speed'] ) ) : $defaults['transition_speed'];
    $valid_transitions         = array( 'fade', 'slide', 'zoom', 'flip' );
    $clean['transition_effect'] = ( isset( $input['transition_effect'] ) && in_array( $input['transition_effect'], $valid_transitions, true ) ) ? $input['transition_effect'] : $defaults['transition_effect'];
    $clean['css_easing']       = isset( $input['css_easing'] ) ? sanitize_text_field( $input['css_easing'] ) : $defaults['css_easing'];
    $clean['slider_height']    = isset( $input['slider_height'] ) && is_numeric( $input['slider_height'] )
        ? max( 0, floatval( $input['slider_height'] ) )
        : $defaults['slider_height'];
    $clean['mobile_slider_height'] = isset( $input['mobile_slider_height'] ) && is_numeric( $input['mobile_slider_height'] )
        ? max( 0, floatval( $input['mobile_slider_height'] ) )
        : $defaults['mobile_slider_height'];
    $clean['theme_mode']       = ( isset( $input['theme_mode'] ) && in_array( $input['theme_mode'], array( 'dark', 'light' ), true ) ) ? $input['theme_mode'] : $defaults['theme_mode'];
    $clean['overlay_disable']  = ( isset( $input['overlay_disable'] ) && 'yes' === $input['overlay_disable'] ) ? 'yes' : 'no';
    $alpha_in                  = isset( $input['overlay_alpha'] ) ? (float) $input['overlay_alpha'] : $defaults['overlay_alpha'];
    $clean['overlay_alpha']    = max( 0, min( 1, $alpha_in ) );
    $clean['content_padding_x'] = isset( $input['content_padding_x'] ) ? max( 0, intval( $input['content_padding_x'] ) ) : $defaults['content_padding_x'];
    $clean['button_color_1']    = isset( $input['button_color_1'] ) ? ( sanitize_hex_color( $input['button_color_1'] ) ?: $defaults['button_color_1'] ) : $defaults['button_color_1'];
    $clean['button_color_2']    = isset( $input['button_color_2'] ) ? ( sanitize_hex_color( $input['button_color_2'] ) ?: $defaults['button_color_2'] ) : $defaults['button_color_2'];
    $clean['button_color_3']    = isset( $input['button_color_3'] ) ? ( sanitize_hex_color( $input['button_color_3'] ) ?: $defaults['button_color_3'] ) : $defaults['button_color_3'];
    $clean['button_text_color'] = isset( $input['button_text_color'] ) ? ( sanitize_hex_color( $input['button_text_color'] ) ?: $defaults['button_text_color'] ) : $defaults['button_text_color'];
    $clean['effect_color_1']    = isset( $input['effect_color_1'] ) ? ( sanitize_hex_color( $input['effect_color_1'] ) ?: $defaults['effect_color_1'] ) : $defaults['effect_color_1'];
    $clean['effect_color_2']    = isset( $input['effect_color_2'] ) ? ( sanitize_hex_color( $input['effect_color_2'] ) ?: $defaults['effect_color_2'] ) : $defaults['effect_color_2'];
    $clean['effect_color_3']    = isset( $input['effect_color_3'] ) ? ( sanitize_hex_color( $input['effect_color_3'] ) ?: $defaults['effect_color_3'] ) : $defaults['effect_color_3'];
    $clean['image_size']        = isset( $input['image_size'] ) ? sanitize_text_field( $input['image_size'] ) : $defaults['image_size'];
    $clean['use_custom_size']   = ( isset( $input['use_custom_size'] ) && 'yes' === $input['use_custom_size'] ) ? 'yes' : 'no';
    $clean['custom_width']      = isset( $input['custom_width'] ) ? max( 0, intval( $input['custom_width'] ) ) : 0;
    $clean['custom_height']     = isset( $input['custom_height'] ) ? max( 0, intval( $input['custom_height'] ) ) : 0;

    $valid_effects = array_keys( hero_slider_plugin_get_effects_list() );
    $clean['visual_effect'] = ( isset( $input['visual_effect'] ) && in_array( $input['visual_effect'], $valid_effects, true ) ) ? $input['visual_effect'] : $defaults['visual_effect'];

    $allowed_mobile = array( 'show', 'hide', 'hide_xs' );
    $clean['featured_img_mobile'] = ( isset( $input['featured_img_mobile'] ) && in_array( $input['featured_img_mobile'], $allowed_mobile, true ) )
        ? $input['featured_img_mobile']
        : $defaults['featured_img_mobile'];

    $clean['title_color'] = isset( $input['title_color'] ) ? ( sanitize_hex_color( $input['title_color'] ) ?: '' ) : '';
    $clean['text_color']  = isset( $input['text_color'] ) ? ( sanitize_hex_color( $input['text_color'] ) ?: '' ) : '';
    // Alignment settings (left, center, right) for text, title, and button
    $clean['text_alignment']  = ( isset( $input['text_alignment'] ) && in_array( $input['text_alignment'], array( 'left', 'center', 'right' ), true ) ) ? $input['text_alignment'] : $defaults['text_alignment'];
    $clean['title_alignment'] = ( isset( $input['title_alignment'] ) && in_array( $input['title_alignment'], array( 'left', 'center', 'right' ), true ) ) ? $input['title_alignment'] : $defaults['title_alignment'];
    $clean['button_alignment']= ( isset( $input['button_alignment'] ) && in_array( $input['button_alignment'], array( 'left', 'center', 'right' ), true ) ) ? $input['button_alignment'] : $defaults['button_alignment'];

    $valid_styles = array( 'classic', 'split', 'image_centered', 'portal' );
    $clean['slide_style']      = ( isset( $input['slide_style'] ) && in_array( $input['slide_style'], $valid_styles, true ) ) ? $input['slide_style'] : $defaults['slide_style'];
    $clean['split_bg_color']   = isset( $input['split_bg_color'] ) ? ( sanitize_hex_color( $input['split_bg_color'] ) ?: $defaults['split_bg_color'] ) : $defaults['split_bg_color'];
    $clean['split_tint_color'] = isset( $input['split_tint_color'] ) ? ( sanitize_hex_color( $input['split_tint_color'] ) ?: $defaults['split_tint_color'] ) : $defaults['split_tint_color'];
    $tint_a_in                 = isset( $input['split_tint_alpha'] ) ? (float) $input['split_tint_alpha'] : $defaults['split_tint_alpha'];
    $clean['split_tint_alpha'] = max( 0, min( 1, $tint_a_in ) );

    $portal_defaults = hero_slider_portal_get_settings_defaults();
    $clean = array_merge( $clean, hero_slider_portal_sanitize_settings( $input, $portal_defaults ) );

    return $clean;
}

// Assets Enqueue
function hero_slider_plugin_enqueue_assets() {
    // Only enqueue if shortcode exists on page, but since we can't easily check everywhere, register them.
    wp_register_style( 'slick-carousel', content_url( '/uploads/local-fonts/slick/slick.min.css' ), array(), '1.8.1' );
    wp_register_script( 'slick-carousel', content_url( '/uploads/local-fonts/slick/slick.min.js' ), array( 'jquery' ), '1.8.1', true );
    wp_register_style( 'remixicon', content_url( '/uploads/local-fonts/remixicon/remixicon.css' ), array(), '3.5.0' );
    wp_register_script( 'hero-slider-effects', plugin_dir_url( __FILE__ ) . 'hero-slider-effects.js', array( 'jquery' ), HERO_SLIDER_PLUGIN_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'hero_slider_plugin_enqueue_assets' );

require_once plugin_dir_path( __FILE__ ) . 'hero-slider-assets.php';

/**
 * Build Slick init script (queued on slick-carousel so it always runs after the library loads).
 */
function hero_slider_plugin_build_slick_init_script( $slider_id, $slider_options ) {
    $slider_id      = sanitize_html_class( $slider_id );
    $slider_options = is_array( $slider_options ) ? $slider_options : array();

    if ( '' === $slider_id || empty( $slider_options ) ) {
        return '';
    }

    $options_json = wp_json_encode( $slider_options );
    if ( ! $options_json ) {
        return '';
    }

    $fn_id = preg_replace( '/[^a-zA-Z0-9_]/', '_', $slider_id );

    return "(function($){\n"
        . "function initHeroSlider_{$fn_id}(){\n"
        . "var \$slider = $('#" . esc_js( $slider_id ) . "');\n"
        . "if (!\$slider.length || \$slider.hasClass('slick-initialized')) { return; }\n"
        . "if (typeof $.fn.slick !== 'function') {\n"
        . "window.setTimeout(initHeroSlider_{$fn_id}, 50);\n"
        . "return;\n"
        . "}\n"
        . "var sliderOptions = " . $options_json . ";\n"
        . "function getSlideItem(\$slide) { return \$slide.find('.hero-slide-item').first(); }\n"
        . "function stackFadeSlides(slick) {\n"
        . "if (!sliderOptions.fade) { return; }\n"
        . "slick.\$slides.css({ position: 'absolute', left: 0, right: 0, top: 0, width: '100%', height: '100%', float: 'none' });\n"
        . "slick.\$slideTrack.css({ transform: 'none', width: '100%', height: '100%' });\n"
        . "}\n"
        . "function syncFadeSlides(slick, activeIndex) {\n"
        . "if (!sliderOptions.fade) { return; }\n"
        . "stackFadeSlides(slick);\n"
        . "var current = (typeof activeIndex === 'number') ? activeIndex : slick.currentSlide;\n"
        . "slick.\$slides.each(function(i){\n"
        . "var \$slide = $(this);\n"
        . "if (i === current) {\n"
        . "\$slide.css({ opacity: 1, zIndex: 2 });\n"
        . "} else {\n"
        . "\$slide.css({ opacity: 0, zIndex: 1 });\n"
        . "}\n"
        . "});\n"
        . "}\n"
        . "\$slider.on('init', function(e, slick) {\n"
        . "\$slider.css('visibility', 'visible');\n"
        . "getSlideItem($(slick.\$slides.get(slick.currentSlide))).addClass('is-enter');\n"
        . "syncFadeSlides(slick);\n"
        . "});\n"
        . "\$slider.on('beforeChange', function(e, slick, current, next) {\n"
        . "stackFadeSlides(slick);\n"
        . "slick.\$slides.find('.hero-slide-item').removeClass('is-enter is-exit');\n"
        . "getSlideItem($(slick.\$slides.get(current))).addClass('is-exit');\n"
        . "getSlideItem($(slick.\$slides.get(next))).addClass('is-enter');\n"
        . "$(slick.\$slides.get(next)).css({ opacity: 1, zIndex: 3 });\n"
        . "});\n"
        . "\$slider.on('afterChange', function(e, slick) {\n"
        . "slick.\$slides.find('.hero-slide-item').removeClass('is-exit');\n"
        . "syncFadeSlides(slick);\n"
        . "});\n"
        . "var setPosTimer = null;\n"
        . "\$slider.on('setPosition', function(e, slick) {\n"
        . "if (!sliderOptions.fade) { return; }\n"
        /* Debounce: Firefox fires setPosition on scroll/layout and causes black flashes. */
        . "if (setPosTimer) { window.clearTimeout(setPosTimer); }\n"
        . "setPosTimer = window.setTimeout(function(){\n"
        . "window.requestAnimationFrame(function(){ syncFadeSlides(slick); });\n"
        . "}, 50);\n"
        . "});\n"
        . "\$slider.slick(sliderOptions);\n"
        /* Firefox: force a paint when the slider re-enters the viewport after scroll. */
        . "if (typeof IntersectionObserver === 'function') {\n"
        . "var section = \$slider.closest('.hero-slider-section').get(0) || \$slider.get(0);\n"
        . "if (section) {\n"
        . "var wasHidden = false;\n"
        . "var io = new IntersectionObserver(function(entries){\n"
        . "entries.forEach(function(entry){\n"
        . "if (!entry.isIntersecting) { wasHidden = true; return; }\n"
        . "if (!wasHidden) { return; }\n"
        . "wasHidden = false;\n"
        . "try {\n"
        . "if (\$slider.hasClass('slick-initialized')) {\n"
        . "\$slider.slick('setPosition');\n"
        . "var slick = \$slider.slick('getSlick');\n"
        . "syncFadeSlides(slick);\n"
        . "\$slider.find('.hero-plexus').each(function(){\n"
        . "if (typeof this.__hspForceRedraw === 'function') { this.__hspForceRedraw(); }\n"
        . "});\n"
        . "}\n"
        . "} catch (err) {}\n"
        . "});\n"
        . "}, { root: null, threshold: 0.01 });\n"
        . "io.observe(section);\n"
        . "}\n"
        . "}\n"
        . "}\n"
        . "$(initHeroSlider_{$fn_id});\n"
        . "})(jQuery);";
}

// Shortcode
function hero_slider_plugin_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'limit'          => -1,
        'group'          => '',
        'autoplay'       => '',
        'autoplay_speed' => '',
        'show_dots'      => '',
        'show_arrows'    => '',
        'height'         => '',
        'text_length'    => 28,
        'button_text'    => __( 'Contact Us', 'hero-slider-plugin' ),
        'button_url'     => home_url( '/contact-us/' ),
        'stick_to_top'   => 'no',
        'header_offset'  => 110,
        'order'          => 'ASC',
        'orderby'        => 'menu_order',
        'transition_effect' => '',
    ), $atts, 'hero_slider' );

    if ( empty( $atts['group'] ) ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="hero-slider-empty">' . esc_html__( 'The group attribute is required. Example: [hero_slider group="12345"] — find the ID under Hero Slides → Slider Groups.', 'hero-slider-plugin' ) . '</div>';
        }
        return '';
    }

    $group_term = hero_slider_plugin_get_group_term_by_id( sanitize_text_field( $atts['group'] ) );
    if ( ! $group_term || is_wp_error( $group_term ) ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="hero-slider-empty">' . esc_html__( 'Slider group not found.', 'hero-slider-plugin' ) . '</div>';
        }
        return '';
    }

    $opts = hero_slider_plugin_get_group_settings_by_id( sanitize_text_field( $atts['group'] ) );
    if ( empty( $opts ) ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="hero-slider-empty">' . esc_html__( 'Slider group settings are missing.', 'hero-slider-plugin' ) . '</div>';
        }
        return '';
    }

    // Apply group settings for shortcode attributes that depend on settings
    if ( '' === $atts['autoplay'] ) {
        $atts['autoplay'] = $opts['autoplay'];
    }
    if ( '' === $atts['autoplay_speed'] ) {
        $atts['autoplay_speed'] = $opts['autoplay_speed'];
    }
    // الارتفاع دائماً من إعدادات المجموعة (إلزامي لجميع الشرائح)
    $slide_height = isset( $opts['slider_height'] ) && is_numeric( $opts['slider_height'] )
        ? max( 0, floatval( $opts['slider_height'] ) )
        : 760;
    $mobile_slide_height = isset( $opts['mobile_slider_height'] ) && is_numeric( $opts['mobile_slider_height'] ) && floatval( $opts['mobile_slider_height'] ) > 0
        ? max( 0, floatval( $opts['mobile_slider_height'] ) )
        : $slide_height;
    if ( '' === $atts['transition_effect'] ) {
        $atts['transition_effect'] = isset( $opts['transition_effect'] ) ? $opts['transition_effect'] : 'fade';
    }
    if ( '' === $atts['show_dots'] ) {
        $atts['show_dots'] = isset( $opts['show_dots'] ) ? $opts['show_dots'] : 'yes';
    }
    if ( '' === $atts['show_arrows'] ) {
        $atts['show_arrows'] = isset( $opts['show_arrows'] ) ? $opts['show_arrows'] : 'yes';
    }

    $query_args = array(
        'post_type'      => 'hero_slide',
        'posts_per_page' => intval( $atts['limit'] ),
        'post_status'    => 'publish',
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'hero_slider_group',
                'field'    => 'term_id',
                'terms'    => $group_term->term_id,
            ),
        ),
    );

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="hero-slider-empty">' . esc_html__( 'No hero slides found.', 'hero-slider-plugin' ) . '</div>';
        }
        return '';
    }

    hero_slider_plugin_mark_shortcode_used();
    hero_slider_enqueue_tajwal_font();
    hero_slider_plugin_styles();

    $slide_style_preview = isset( $opts['slide_style'] ) ? $opts['slide_style'] : 'classic';
    $is_portal_preview   = ( 'portal' === $slide_style_preview );
    $is_portal_static    = $is_portal_preview && ( $query->post_count <= 1 );

    if ( ! $is_portal_static ) {
        wp_enqueue_style( 'slick-carousel' );
        wp_enqueue_script( 'slick-carousel' );
    }
    wp_enqueue_style( 'remixicon' );

    $visual_effect = sanitize_text_field( $opts['visual_effect'] );
    if ( 'none' !== $visual_effect ) {
        wp_enqueue_script( 'hero-slider-effects' );
    }

    $slider_id = wp_unique_id( 'hero-slider-' );
    $text_length  = max( 10, intval( $atts['text_length'] ) );
    $button_text  = sanitize_text_field( $atts['button_text'] );
    $button_url   = ! empty( $atts['button_url'] ) ? esc_url( $atts['button_url'] ) : home_url( '/contact-us/' );
    $header_offset = max( 0, intval( $atts['header_offset'] ) );
    $top_attached = ( 'yes' === $atts['stick_to_top'] );
    $has_multiple = ( $query->post_count > 1 );

    ob_start();
    ?>
    <?php
    $theme_mode         = ( isset( $opts['theme_mode'] ) && 'light' === $opts['theme_mode'] ) ? 'light' : 'dark';
    $global_title_color = ! empty( $opts['title_color'] )       ? $opts['title_color']       : ( 'light' === $theme_mode ? '#0b1220'              : '#ffffff' );
    $global_text_color  = ! empty( $opts['text_color'] )        ? $opts['text_color']        : ( 'light' === $theme_mode ? 'rgba(17,24,39,0.78)'  : 'rgba(222,228,237,0.76)' );
    $global_btn_text    = ! empty( $opts['button_text_color'] ) ? $opts['button_text_color'] : '#ffffff';
    $slide_style_opt    = isset( $opts['slide_style'] ) ? $opts['slide_style'] : 'classic';
    $is_image_centered  = ( 'image_centered' === $slide_style_opt );
    $is_portal          = ( 'portal' === $slide_style_opt );
    $portal_css_vars    = $is_portal ? hero_slider_portal_build_section_css_vars( $opts ) : '';
    $split_bg_color     = isset( $opts['split_bg_color'] ) ? $opts['split_bg_color'] : '#ffffff';
    $split_tint_color   = isset( $opts['split_tint_color'] ) ? $opts['split_tint_color'] : '#2c6464';
    $split_tint_alpha   = isset( $opts['split_tint_alpha'] ) ? max( 0, min( 1, (float) $opts['split_tint_alpha'] ) ) : 0.55;
    $split_tint_hex     = ltrim( $split_tint_color, '#' );
    if ( strlen( $split_tint_hex ) === 3 ) {
        $split_tint_hex = $split_tint_hex[0] . $split_tint_hex[0] . $split_tint_hex[1] . $split_tint_hex[1] . $split_tint_hex[2] . $split_tint_hex[2];
    }
    $split_tint_r       = hexdec( substr( $split_tint_hex, 0, 2 ) );
    $split_tint_g       = hexdec( substr( $split_tint_hex, 2, 2 ) );
    $split_tint_b       = hexdec( substr( $split_tint_hex, 4, 2 ) );
    $split_tint_rgba    = sprintf( 'rgba(%d,%d,%d,%s)', $split_tint_r, $split_tint_g, $split_tint_b, $split_tint_alpha );
    ?>
    <section class="hero-slider-section hero-effect-<?php echo esc_attr( $visual_effect ); ?> theme-<?php echo esc_attr( $theme_mode ); ?> slide-style-<?php echo esc_attr( $slide_style_opt ); ?><?php echo $top_attached ? ' is-top-attached' : ''; ?><?php echo $is_portal ? ' is-portal-layout' : ''; ?><?php echo $is_portal ? esc_attr( hero_slider_portal_get_pattern_anim_section_class( $opts ) ) : ''; ?>" data-effect="<?php echo esc_attr( $visual_effect ); ?>" data-style="<?php echo esc_attr( $slide_style_opt ); ?>" style="--hero-height: <?php echo esc_attr( $slide_height ); ?>px; --hsp-mobile-height: <?php echo esc_attr( $mobile_slide_height ); ?>px; --hero-header-offset: <?php echo esc_attr( $header_offset ); ?>px; --hsp-btn1: <?php echo esc_attr( $opts['button_color_1'] ); ?>; --hsp-btn2: <?php echo esc_attr( $opts['button_color_2'] ); ?>; --hsp-btn3: <?php echo esc_attr( $opts['button_color_3'] ); ?>; --hsp-btn-text: <?php echo esc_attr( $global_btn_text ); ?>; --hsp-eff1: <?php echo esc_attr( $opts['effect_color_1'] ); ?>; --hsp-eff2: <?php echo esc_attr( $opts['effect_color_2'] ); ?>; --hsp-eff3: <?php echo esc_attr( $opts['effect_color_3'] ); ?>; --hsp-anim-speed: <?php echo esc_attr( intval( $opts['transition_speed'] ) ); ?>ms; --hsp-anim-ease: <?php echo esc_attr( $opts['css_easing'] ); ?>; --hsp-title-color: <?php echo esc_attr( $global_title_color ); ?>; --hsp-text-color: <?php echo esc_attr( $global_text_color ); ?>; --hsp-pad-x: <?php echo esc_attr( intval( $opts['content_padding_x'] ) ); ?>px; --hsp-split-bg: <?php echo esc_attr( $split_bg_color ); ?>; --hsp-split-tint: <?php echo esc_attr( $split_tint_rgba ); ?>; --hsp-split-tint-solid: <?php echo esc_attr( $split_tint_color ); ?>;<?php echo esc_attr( $portal_css_vars ); ?>">
        <div id="<?php echo esc_attr( $slider_id ); ?>" class="hero-slider<?php echo $has_multiple ? ' has-multiple-slides' : ''; ?><?php echo $is_portal_static ? ' is-static-portal' : ''; ?> te-<?php echo esc_attr( $atts['transition_effect'] ); ?>"<?php echo $is_portal_static ? ' data-static-portal="1"' : ''; ?>>
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php if ( $is_portal ) : ?>
                    <?php hero_slider_portal_render_slide( get_the_ID(), $opts ); ?>
                <?php else : ?>
                <?php
                if ( ! isset( $hero_slide_index ) ) {
                    $hero_slide_index = 0;
                }
                $is_first_slide     = ( 0 === $hero_slide_index );
                $img_size           = hero_slider_plugin_get_image_size( $opts );
                $thumb_id           = (int) get_post_thumbnail_id( get_the_ID() );
                $image_url          = hero_slider_plugin_optimized_media_url( $thumb_id, get_the_post_thumbnail_url( get_the_ID(), $img_size ), $img_size );
                $bg_id_meta         = get_post_meta( get_the_ID(), '_hero_slider_bg_id', true );
                $bg_url_meta        = get_post_meta( get_the_ID(), '_hero_slider_bg_url', true );
                if ( $bg_id_meta ) {
                    $bg_url = hero_slider_plugin_optimized_media_url( (int) $bg_id_meta, '', 'medium_large' );
                } elseif ( ! empty( $bg_url_meta ) ) {
                    $bg_url = hero_slider_plugin_prefer_webp_url( $bg_url_meta );
                } elseif ( $is_image_centered ) {
                    $bg_url = hero_slider_plugin_optimized_media_url( $thumb_id, '', $img_size );
                } else {
                    // Decorative blurred layer — use a small WebP, not the hero full-res asset.
                    $bg_url = hero_slider_plugin_optimized_media_url( $thumb_id, $image_url, 'medium_large' );
                }
                $tint_color_meta    = get_post_meta( get_the_ID(), '_hero_slider_tint_color', true );
                $tint_alpha_meta    = get_post_meta( get_the_ID(), '_hero_slider_tint_alpha', true );
                $tint_color         = $tint_color_meta ? $tint_color_meta : '#000000';
                $tint_alpha         = is_numeric( $tint_alpha_meta ) ? max( 0, min( 1, (float) $tint_alpha_meta ) ) : 0.35;
                $disable_overlay    = get_post_meta( get_the_ID(), '_hero_slider_disable_overlay', true ) === 'yes';
                if ( $is_image_centered ) {
                    $disable_overlay = true;
                } else {
                    $global_disable = ( isset( $opts['overlay_disable'] ) && 'yes' === $opts['overlay_disable'] );
                    $global_alpha   = isset( $opts['overlay_alpha'] ) ? (float) $opts['overlay_alpha'] : 0.35;
                    if ( $global_disable ) {
                        $disable_overlay = true;
                    } else {
                        $tint_alpha = max( 0, min( 1, $global_alpha ) );
                    }
                    if ( $disable_overlay && ! $bg_id_meta && ( empty( $bg_url_meta ) || $bg_url === $image_url ) ) {
                        $bg_full = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                        if ( $bg_full ) {
                            $bg_url = $bg_full;
                        }
                    }
                }
                $description_source = has_excerpt() ? get_the_excerpt() : get_post_field( 'post_content', get_the_ID() );
                $description        = wp_strip_all_tags( strip_shortcodes( $description_source ) );
                $slide_button_text  = get_post_meta( get_the_ID(), '_hero_slider_button_text', true );
                $slide_button_url   = get_post_meta( get_the_ID(), '_hero_slider_button_url', true );
                $slide_button_text  = ! empty( $slide_button_text ) ? sanitize_text_field( $slide_button_text ) : '';
                $slide_button_url   = ! empty( $slide_button_url ) ? esc_url( $slide_button_url ) : '';
                $rgba               = hero_slider_plugin_hex_to_rgba( $tint_color, $tint_alpha );
                $img_pos    = get_post_meta( get_the_ID(), '_hero_slider_img_position', true );
                $img_pos    = in_array( $img_pos, array( 'floating', 'anchored' ), true ) ? $img_pos : 'floating';
                $img_mobile = get_post_meta( get_the_ID(), '_hero_slider_img_mobile', true );
                if ( ! in_array( $img_mobile, array( 'show', 'hide', 'hide_xs' ), true ) ) {
                    $img_mobile = isset( $opts['featured_img_mobile'] ) ? $opts['featured_img_mobile'] : 'show';
                }
                // ألوان النصوص الخاصة بالشريحة
                $slide_title_color   = get_post_meta( get_the_ID(), '_hero_slider_title_color',    true );
                $slide_text_color   = get_post_meta( get_the_ID(), '_hero_slider_text_color',     true );
                $slide_btn_text_clr = get_post_meta( get_the_ID(), '_hero_slider_btn_text_color', true );
                $slide_style        = '';
                if ( $slide_title_color ) {
                    $slide_style .= '--hsp-title-color:' . esc_attr( $slide_title_color ) . ';';
                }
                if ( $slide_text_color ) {
                    $slide_style .= '--hsp-text-color:' . esc_attr( $slide_text_color ) . ';';
                }
                if ( $slide_btn_text_clr ) {
                    $slide_style .= '--hsp-btn-text:' . esc_attr( $slide_btn_text_clr ) . ';';
                }
                ?>
                <?php
                $bg_class = 'hero-slide-bg';
                $bg_inline = '';
                if ( $bg_url ) {
                    $bg_inline = 'background-image: url(' . esc_url( $bg_url ) . ');';
                    if ( ! $is_image_centered ) {
                        $bg_class .= ' hero-slide-bg--watermark';
                        $bg_inline .= ' background-repeat: no-repeat; background-position: center center; background-size: min(360px, 32vw); opacity: 0.14;';
                    } else {
                        $bg_inline .= ' background-repeat: no-repeat; background-position: center center; background-size: cover; opacity: 1;';
                    }
                }
                ?>
                <article class="hero-slide-item" data-img-mobile="<?php echo esc_attr( $img_mobile ); ?>"<?php echo $slide_style ? ' style="' . esc_attr( $slide_style ) . '"' : ''; ?>>
                    <div class="<?php echo esc_attr( $bg_class ); ?>"<?php echo $bg_inline ? ' style="' . esc_attr( $bg_inline ) . '"' : ''; ?>></div>
                    <?php if ( ! $disable_overlay ) : ?>
                        <div class="hero-slide-overlay"></div>
                        <?php if ( $tint_alpha > 0 ) : ?>
                            <div class="hero-slide-tint" style="background: <?php echo esc_attr( $rgba ); ?>;"></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ( ! $is_image_centered ) : ?>
                        <?php
                        // Blurred glow only on the first slide; reuse a small WebP so we do not download the hero twice.
                        $media_glow_url = $is_first_slide ? hero_slider_plugin_optimized_media_url( $thumb_id, $image_url, 'medium' ) : '';
                        ?>
                        <div class="hero-slide-media"<?php echo $media_glow_url ? ' style="background-image: url(' . esc_url( $media_glow_url ) . ');"' : ''; ?>></div>
                    <?php endif; ?>
                    <div class="hero-slide-effects" aria-hidden="true">
                        <canvas class="hero-plexus"></canvas>
                        <span class="hero-orb orb-1"></span>
                        <span class="hero-orb orb-2"></span>
                        <span class="hero-orb orb-3"></span>
                        <span class="hero-grid"></span>
                        <span class="hero-mesh"></span>
                        <span class="hero-wave wave-1"></span>
                        <span class="hero-wave wave-2"></span>
                    </div>
                    <div class="hero-slide-container" style="max-width:none; width:100%; margin:0; padding:0;">
                        <div class="hero-slide-layout" style="padding-left: var(--hsp-pad-x, 15px) !important; padding-right: var(--hsp-pad-x, 15px) !important;">
                            <?php
                            $group_settings = hero_slider_plugin_get_group_settings_for_slide( $post->ID );
                            $text_alignment = $group_settings['text_alignment'] ?? '';
                            $title_alignment = $group_settings['title_alignment'] ?? '';
                            $button_alignment = $group_settings['button_alignment'] ?? '';
                            $allowed_align = array( 'left', 'center', 'right' );
                            if ( ! in_array( $text_alignment, $allowed_align, true ) ) {
                                $text_alignment = is_rtl() ? 'right' : 'left';
                            }
                            if ( ! in_array( $title_alignment, $allowed_align, true ) ) {
                                $title_alignment = is_rtl() ? 'right' : 'left';
                            }
                            if ( ! in_array( $button_alignment, $allowed_align, true ) ) {
                                $button_alignment = is_rtl() ? 'right' : 'left';
                            }
                            ?>
                            <div class="hero-slide-content is-align-<?php echo esc_attr( $text_alignment ); ?>" style="padding-left: 0 !important; padding-right: 0 !important; text-align: <?php echo esc_attr( $text_alignment ); ?>;">
                                <?php
                                $allowed = array(
                                    'br'    => array(),
                                    'span'  => array( 'class' => true, 'style' => true, 'title' => true, 'aria-label' => true ),
                                    'strong'=> array(),
                                    'b'     => array(),
                                    'em'    => array(),
                                    'i'     => array(),
                                    'u'     => array(),
                                    'small' => array(),
                                    'sup'   => array(),
                                    'sub'   => array(),
                                    'mark'  => array(),
                                    'a'     => array( 'href' => true, 'target' => true, 'rel' => true, 'title' => true, 'class' => true, 'aria-label' => true ),
                                );
                                $title_html = get_post_meta( get_the_ID(), '_hero_slider_title_html', true );
                                if ( $title_html ) {
                                    $title_desktop = wp_kses( $title_html, $allowed );
                                    // Mobile: replace <br> with space so title flows naturally
                                    $title_mobile  = wp_kses( preg_replace( '#<br\s*/?>#i', ' ', $title_html ), $allowed );
                                    echo '<h1 class="hero-slide-title" style="text-align: ' . esc_attr( $title_alignment ) . ';">';
                                    echo '<span class="hero-title-desktop">' . $title_desktop . '</span>';
                                    echo '<span class="hero-title-mobile">' . $title_mobile . '</span>';
                                    echo '</h1>';
                                } else {
                                    $raw_title = get_the_title();
                                    if ( false !== strpos( $raw_title, '<' ) ) {
                                        $title_desktop = wp_kses( $raw_title, $allowed );
                                        $title_mobile  = wp_kses( preg_replace( '#<br\s*/?>#i', ' ', $raw_title ), $allowed );
                                        echo '<h1 class="hero-slide-title" style="text-align: ' . esc_attr( $title_alignment ) . ';">';
                                        echo '<span class="hero-title-desktop">' . $title_desktop . '</span>';
                                        echo '<span class="hero-title-mobile">' . $title_mobile . '</span>';
                                        echo '</h1>';
                                    } else {
                                        echo '<h1 class="hero-slide-title" style="text-align: ' . esc_attr( $title_alignment ) . ';">' . esc_html( $raw_title ) . '</h1>';
                                    }
                                }
                                ?>
                                <?php if ( ! empty( $description ) ) : ?>
                                    <div class="hero-slide-text" style="text-align: <?php echo esc_attr( $text_alignment ); ?>;"><?php echo esc_html( $description ); ?></div>
                                <?php endif; ?>
                                <?php if ( ! empty( $slide_button_text ) && ! empty( $slide_button_url ) ) : ?>
                                    <div class="hero-slide-actions is-align-<?php echo esc_attr( $button_alignment ); ?>" style="text-align: <?php echo esc_attr( $button_alignment ); ?>;">
                                        <a class="hero-slide-button" href="<?php echo esc_url( $slide_button_url ); ?>">
                                            <span><?php echo esc_html( $slide_button_text ); ?></span>
                                            <span class="hero-slide-button-icon ri-arrow-left-up-line"></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="hero-slide-visual hero-img-<?php echo esc_attr( $img_pos ); ?>"<?php echo $is_image_centered ? ' aria-hidden="true"' : ''; ?>>
                                <span class="hero-visual-glow glow-1"></span>
                                <span class="hero-visual-glow glow-2"></span>
                                <span class="hero-visual-ring ring-1"></span>
                                <span class="hero-visual-ring ring-2"></span>
                                <?php if ( $image_url && ! $is_image_centered ) : ?>
                                    <img class="hero-slide-machine" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="720" height="720" decoding="async"<?php echo $is_first_slide ? ' fetchpriority="high" loading="eager"' : ' loading="lazy" fetchpriority="low"'; ?>>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
                <?php $hero_slide_index++; ?>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </section>
    <?php

    if ( $has_multiple && ! $is_portal_static ) {
        $use_fade = ( 'slide' !== $atts['transition_effect'] );
        $slider_options = array(
            // Slick fade + rtl breaks slide positioning (slides render outside viewport).
            'rtl'          => $use_fade ? false : is_rtl(),
            'slidesToShow' => 1,
            'slidesToScroll' => 1,
            'fade'         => $use_fade,
            'speed'        => intval( $opts['transition_speed'] ),
            'autoplay'     => ( 'yes' === $atts['autoplay'] ),
            'autoplaySpeed'=> intval( $atts['autoplay_speed'] ),
            'dots'         => ( 'yes' === $atts['show_dots'] ),
            'arrows'       => ( 'yes' === $atts['show_arrows'] ),
            'infinite'     => true,
            'pauseOnHover' => false,
            'pauseOnFocus' => false,
            'adaptiveHeight' => false,
            'cssEase'      => sanitize_text_field( $opts['css_easing'] ),
            'prevArrow'    => '<button type="button" class="hero-arrow hero-prev" aria-label="Previous slide"><span class="ri-arrow-left-line"></span></button>',
            'nextArrow'    => '<button type="button" class="hero-arrow hero-next" aria-label="Next slide"><span class="ri-arrow-right-line"></span></button>',
        );

        $init_js = hero_slider_plugin_build_slick_init_script( $slider_id, $slider_options );
        if ( $init_js ) {
            wp_add_inline_script( 'slick-carousel', $init_js, 'after' );
        }
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'hero_slider', 'hero_slider_plugin_shortcode' );


/**
 * Prefer an existing sibling .webp file for a media URL (e.g. file.png.webp).
 *
 * @param string $url Absolute media URL.
 * @return string
 */
function hero_slider_plugin_prefer_webp_url( $url ) {
    if ( ! is_string( $url ) || $url === '' || false !== stripos( $url, '.webp' ) ) {
        return $url;
    }
    $uploads = wp_get_upload_dir();
    if ( empty( $uploads['baseurl'] ) || empty( $uploads['basedir'] ) ) {
        return $url;
    }
    if ( 0 !== strpos( $url, $uploads['baseurl'] ) ) {
        return $url;
    }
    $rel  = substr( $url, strlen( $uploads['baseurl'] ) );
    $path = $uploads['basedir'] . $rel;
    if ( is_file( $path . '.webp' ) ) {
        return $url . '.webp';
    }
    return $url;
}

/**
 * Resolve a display URL for hero media: sized + WebP when available.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $fallback_url  Fallback URL.
 * @param string $size          Registered image size.
 * @return string
 */
function hero_slider_plugin_optimized_media_url( $attachment_id, $fallback_url = '', $size = 'large' ) {
    $url = '';
    if ( $attachment_id ) {
        // Prefer large/1536 over full to avoid multi-MB originals on mobile.
        foreach ( array( $size, '1536x1536', 'large', '1024x1024', 'full' ) as $try ) {
            $candidate = wp_get_attachment_image_url( (int) $attachment_id, $try );
            if ( $candidate ) {
                $url = $candidate;
                if ( 'full' !== $try ) {
                    break;
                }
            }
        }
    }
    if ( ! $url ) {
        $url = $fallback_url;
    }
    return hero_slider_plugin_prefer_webp_url( $url );
}

function hero_slider_plugin_get_image_size( $settings = null ) {
    $opts = is_array( $settings ) ? $settings : hero_slider_plugin_get_group_settings_defaults();
    if ( 'yes' === $opts['use_custom_size'] && ( $opts['custom_width'] > 0 || $opts['custom_height'] > 0 ) ) {
        return array( intval( $opts['custom_width'] ), intval( $opts['custom_height'] ) );
    }
    return ! empty( $opts['image_size'] ) && 'full' !== $opts['image_size'] ? $opts['image_size'] : 'large';
}

add_filter( 'manage_edit-hero_slide_columns', 'hero_slider_plugin_add_order_column' );
function hero_slider_plugin_add_order_column( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        if ( 'date' === $key ) {
            $new['menu_order'] = __( 'Order', 'hero-slider-plugin' );
        }
        $new[ $key ] = $label;
    }
    if ( ! isset( $new['menu_order'] ) ) {
        $new['menu_order'] = __( 'Order', 'hero-slider-plugin' );
    }
    return $new;
}

add_action( 'manage_hero_slide_posts_custom_column', 'hero_slider_plugin_render_order_column', 10, 2 );
function hero_slider_plugin_render_order_column( $column, $post_id ) {
    if ( 'menu_order' === $column ) {
        echo intval( get_post_field( 'menu_order', $post_id ) );
    }
}

add_filter( 'manage_edit-hero_slide_sortable_columns', 'hero_slider_plugin_sortable_order_column' );
function hero_slider_plugin_sortable_order_column( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

add_action( 'pre_get_posts', 'hero_slider_plugin_admin_default_order' );
function hero_slider_plugin_admin_default_order( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    $post_type = $query->get( 'post_type' );
    if ( 'hero_slide' !== $post_type ) {
        return;
    }
    if ( isset( $_GET['orderby'] ) ) {
        return;
    }
    $query->set( 'orderby', 'menu_order' );
    $query->set( 'order', 'ASC' );
}

add_action( 'admin_enqueue_scripts', 'hero_slider_plugin_enable_reorder' );
function hero_slider_plugin_enable_reorder( $hook ) {
    if ( 'edit.php' !== $hook ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen || 'hero_slide' !== $screen->post_type ) {
        return;
    }
    wp_enqueue_script( 'jquery-ui-sortable' );
    add_action( 'admin_print_footer_scripts', 'hero_slider_plugin_print_reorder_script' );
}

function hero_slider_plugin_print_reorder_script() {
    $screen = get_current_screen();
    if ( ! $screen || 'hero_slide' !== $screen->post_type ) {
        return;
    }
    $nonce = wp_create_nonce( 'hsp_update_order' );
    ?>
    <script>
    jQuery(function($){
        var $tbody = $('#the-list');
        if(!$tbody.length) return;
        $tbody.sortable({
            items: 'tr:not(.no-items)',
            axis: 'y',
            cursor: 'move',
            handle: '.column-title',
            update: function(){
                var order = [];
                $tbody.find('tr').each(function(i, el){
                    var id = $(el).attr('id');
                    if(!id) return;
                    id = id.replace('post-','');
                    order.push(id);
                });
                $.post(ajaxurl, {
                    action: 'hsp_update_order',
                    nonce: '<?php echo esc_js( $nonce ); ?>',
                    order: order
                });
            }
        });
    });
    </script>
    <?php
}

add_action( 'wp_ajax_hsp_update_order', 'hero_slider_plugin_ajax_update_order' );
function hero_slider_plugin_ajax_update_order() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'forbidden' );
    }
    check_ajax_referer( 'hsp_update_order', 'nonce' );
    $order = isset( $_POST['order'] ) ? (array) $_POST['order'] : array();
    if ( empty( $order ) ) {
        wp_send_json_error( 'bad_request' );
    }
    $order = array_map( 'intval', $order );
    foreach ( $order as $menu_order => $post_id ) {
        if ( 'hero_slide' !== get_post_type( $post_id ) ) {
            continue;
        }
        wp_update_post( array( 'ID' => $post_id, 'menu_order' => $menu_order ) );
    }
    wp_send_json_success();
}

function hero_slider_plugin_hex_to_rgba( $hex, $alpha = 1 ) {
    $hex = sanitize_hex_color( $hex );
    if ( ! $hex ) {
        $hex = '#000000';
    }
    $hex = ltrim( $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $r = hexdec( str_repeat( substr( $hex, 0, 1 ), 2 ) );
        $g = hexdec( str_repeat( substr( $hex, 1, 1 ), 2 ) );
        $b = hexdec( str_repeat( substr( $hex, 2, 1 ), 2 ) );
    } else {
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
    }
    $alpha = max( 0, min( 1, (float) $alpha ) );
    return sprintf( 'rgba(%d, %d, %d, %.3f)', $r, $g, $b, $alpha );
}
