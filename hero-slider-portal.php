<?php
/**
 * Hero Slider — Portal Layout Style (LVN-style hero + cards)
 *
 * @copyright 2025 Mahmoud Mahsob / Marslia (https://mrslia.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hero_slider_portal_get_default_cards() {
    return array(
        array(
            'icon_id'         => 0,
            'icon_url'        => '',
            'icon_bg'         => '#22c55e',
            'icon_radius'     => 12,
            'title'           => 'LVN for Individuals',
            'title_color'     => '#22c55e',
            'title_size'      => 18,
            'description'     => 'Track your personal assets with real-time GPS monitoring and instant alerts.',
            'desc_color'      => '#6b7280',
            'desc_size'       => 14,
            'cta_text'        => 'Enter',
            'cta_url'         => '#',
            'cta_color'       => '#22c55e',
            'cta_hover_color' => '#16a34a',
        ),
        array(
            'icon_id'         => 0,
            'icon_url'        => '',
            'icon_bg'         => '#1e3a5f',
            'icon_radius'     => 12,
            'title'           => 'LVN for Business',
            'title_color'     => '#1e3a5f',
            'title_size'      => 18,
            'description'     => 'Manage your fleet efficiently with advanced analytics and route optimization.',
            'desc_color'      => '#6b7280',
            'desc_size'       => 14,
            'cta_text'        => 'Enter',
            'cta_url'         => '#',
            'cta_color'       => '#1e3a5f',
            'cta_hover_color' => '#0f2744',
        ),
        array(
            'icon_id'         => 0,
            'icon_url'        => '',
            'icon_bg'         => '#92764a',
            'icon_radius'     => 12,
            'title'           => 'LVN for Government',
            'title_color'     => '#92764a',
            'title_size'      => 18,
            'description'     => 'Secure national infrastructure monitoring compliant with CMC standards.',
            'desc_color'      => '#6b7280',
            'desc_size'       => 14,
            'cta_text'        => 'Enter',
            'cta_url'         => '#',
            'cta_color'       => '#92764a',
            'cta_hover_color' => '#7a6240',
        ),
    );
}

function hero_slider_portal_get_settings_defaults() {
    return array(
        'portal_bg_type'            => 'radial',
        'portal_bg_color'           => '#1a1a4e',
        'portal_grad_color_1'       => '#6b21a8',
        'portal_grad_color_2'       => '#1e3a5f',
        'portal_grad_angle'         => 135,
        'portal_pattern_source'     => 'map_grid',
        'portal_pattern_custom_id'  => 0,
        'portal_pattern_custom_url' => '',
        'portal_pattern_blend'      => 'overlay',
        'portal_pattern_opacity'    => 35,
        'portal_pattern_scale'      => 100,
        'portal_pattern_color'      => '#0f172a',
        'portal_pattern_color_2'    => 'transparent',
        'portal_pattern_repeat'     => 'repeat',
        'portal_pattern_position'   => 'center center',
        'portal_pattern_animation'  => 'drift',
        'portal_pattern_anim_speed' => 45,
        'portal_title_font'         => 'Tajawal, sans-serif',
        'portal_title_size'         => 32,
        'portal_title_weight'       => 700,
        'portal_title_line_height'  => 1.35,
        'portal_title_color'        => '#ffffff',
        'portal_text_font'          => 'Tajawal, sans-serif',
        'portal_text_size'          => 18,
        'portal_text_weight'        => 400,
        'portal_text_line_height'   => 1.6,
        'portal_text_color'         => '#ffffff',
        'portal_cards_gap'          => 24,
        'portal_cards_pad_x'        => 32,
        'portal_cards_pad_bottom'   => 48,
        'portal_card_bg'            => '#ffffff',
        'portal_card_radius'        => 16,
        'portal_card_padding'       => 24,
        'portal_card_shadow'        => '0 8px 32px rgba(0,0,0,0.12)',
    );
}

function hero_slider_portal_get_empty_cards() {
    $empty = hero_slider_portal_sanitize_card( array() );
    return array( $empty, $empty, $empty );
}

function hero_slider_portal_get_group_style_map() {
    $map   = array();
    $terms = get_terms(
        array(
            'taxonomy'   => 'hero_slider_group',
            'hide_empty' => false,
        )
    );
    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return $map;
    }
    foreach ( $terms as $term ) {
        $settings              = hero_slider_plugin_get_group_settings_for_term( $term->term_id );
        $map[ $term->term_id ] = isset( $settings['slide_style'] ) ? $settings['slide_style'] : 'classic';
    }
    return $map;
}

function hero_slider_portal_slide_group_is_portal( $post_id ) {
    $settings = hero_slider_plugin_get_group_settings_for_slide( $post_id );
    return isset( $settings['slide_style'] ) && 'portal' === $settings['slide_style'];
}

function hero_slider_portal_get_pattern_presets() {
    return array(
        'map_grid' => array(
            'label'     => __( 'Map Grid', 'hero-slider-plugin' ),
            'group'     => 'basic',
            'css'       => 'repeating-linear-gradient(0deg, transparent, transparent 38px, var(--hsp-portal-pattern-color) 38px, var(--hsp-portal-pattern-color) 39px), repeating-linear-gradient(90deg, transparent, transparent 38px, var(--hsp-portal-pattern-color) 38px, var(--hsp-portal-pattern-color) 39px)',
            'size_mode' => 'percent',
        ),
        'road_map' => array(
            'label'     => __( 'Road Map', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(0deg, transparent, transparent 14px, var(--hsp-portal-pattern-color-2) 14px, var(--hsp-portal-pattern-color-2) 15px), repeating-linear-gradient(90deg, transparent, transparent 14px, var(--hsp-portal-pattern-color-2) 14px, var(--hsp-portal-pattern-color-2) 15px), repeating-linear-gradient(0deg, transparent, transparent 28px, var(--hsp-portal-pattern-color) 28px, var(--hsp-portal-pattern-color) 32px, transparent 32px, transparent 60px), repeating-linear-gradient(90deg, transparent, transparent 28px, var(--hsp-portal-pattern-color) 28px, var(--hsp-portal-pattern-color) 32px, transparent 32px, transparent 60px), repeating-linear-gradient(45deg, transparent, transparent 7px, var(--hsp-portal-pattern-color) 7px, var(--hsp-portal-pattern-color) 8px, transparent 8px, transparent 16px), radial-gradient(circle, var(--hsp-portal-pattern-color) 2px, transparent 2.5px)',
            'size_mode' => 'road_map',
        ),
        'dots' => array(
            'label'     => __( 'Dots', 'hero-slider-plugin' ),
            'group'     => 'basic',
            'css'       => 'radial-gradient(circle, var(--hsp-portal-pattern-color) 1.5px, transparent 1.5px)',
            'size_mode' => 'dots',
        ),
        'geometric_lines' => array(
            'label'     => __( 'Geometric Lines', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(45deg, transparent, transparent 20px, var(--hsp-portal-pattern-color) 20px, var(--hsp-portal-pattern-color) 21px), repeating-linear-gradient(-45deg, transparent, transparent 20px, var(--hsp-portal-pattern-color) 20px, var(--hsp-portal-pattern-color) 21px)',
            'size_mode' => 'percent',
        ),
        'diagonal_stripes' => array(
            'label'     => __( 'Diagonal Stripes', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(45deg, var(--hsp-portal-pattern-color) 0, var(--hsp-portal-pattern-color) 2px, transparent 2px, transparent 14px)',
            'size_mode' => 'percent',
        ),
        'horizontal_stripes' => array(
            'label'     => __( 'Horizontal Stripes', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(0deg, var(--hsp-portal-pattern-color) 0, var(--hsp-portal-pattern-color) 2px, transparent 2px, transparent 16px)',
            'size_mode' => 'percent',
        ),
        'vertical_stripes' => array(
            'label'     => __( 'Vertical Stripes', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(90deg, var(--hsp-portal-pattern-color) 0, var(--hsp-portal-pattern-color) 2px, transparent 2px, transparent 16px)',
            'size_mode' => 'percent',
        ),
        'checkerboard' => array(
            'label'     => __( 'Checkerboard', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'conic-gradient(var(--hsp-portal-pattern-color) 90deg, var(--hsp-portal-pattern-color-2) 0 180deg, var(--hsp-portal-pattern-color) 0 270deg, var(--hsp-portal-pattern-color-2) 0)',
            'size_mode' => 'checker',
        ),
        'diamonds' => array(
            'label'        => __( 'Diamonds', 'hero-slider-plugin' ),
            'group'        => 'geometric',
            'css'          => 'linear-gradient(45deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%, var(--hsp-portal-pattern-color-2) 75%, var(--hsp-portal-pattern-color) 75%), linear-gradient(45deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%, var(--hsp-portal-pattern-color-2) 75%, var(--hsp-portal-pattern-color) 75%)',
            'size_mode'    => 'checker',
            'bg_position'  => '0 0, 50% 50%',
        ),
        'triangles' => array(
            'label'     => __( 'Triangles', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'linear-gradient(30deg, var(--hsp-portal-pattern-color) 12%, var(--hsp-portal-pattern-color-2) 12.5%, var(--hsp-portal-pattern-color-2) 87%, var(--hsp-portal-pattern-color) 87.5%, var(--hsp-portal-pattern-color)), linear-gradient(150deg, var(--hsp-portal-pattern-color) 12%, var(--hsp-portal-pattern-color-2) 12.5%, var(--hsp-portal-pattern-color-2) 87%, var(--hsp-portal-pattern-color) 87.5%, var(--hsp-portal-pattern-color)), linear-gradient(30deg, var(--hsp-portal-pattern-color) 12%, var(--hsp-portal-pattern-color-2) 12.5%, var(--hsp-portal-pattern-color-2) 87%, var(--hsp-portal-pattern-color) 87.5%, var(--hsp-portal-pattern-color)), linear-gradient(150deg, var(--hsp-portal-pattern-color) 12%, var(--hsp-portal-pattern-color-2) 12.5%, var(--hsp-portal-pattern-color-2) 87%, var(--hsp-portal-pattern-color) 87.5%, var(--hsp-portal-pattern-color))',
            'size_mode' => 'checker',
        ),
        'zigzag' => array(
            'label'       => __( 'Zigzag', 'hero-slider-plugin' ),
            'group'       => 'geometric',
            'css'         => 'linear-gradient(135deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%), linear-gradient(225deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%), linear-gradient(315deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%), linear-gradient(45deg, var(--hsp-portal-pattern-color) 25%, var(--hsp-portal-pattern-color-2) 25%)',
            'size_mode'   => 'checker',
            'bg_position' => '0 0, 0 50%, 50% -50%, 50% 0',
        ),
        'circles' => array(
            'label'     => __( 'Circles', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'radial-gradient(circle, var(--hsp-portal-pattern-color-2) 35%, var(--hsp-portal-pattern-color) 36%, var(--hsp-portal-pattern-color) 40%, var(--hsp-portal-pattern-color-2) 41%)',
            'size_mode' => 'dots',
        ),
        'rings' => array(
            'label'     => __( 'Rings', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'radial-gradient(circle, transparent 52%, var(--hsp-portal-pattern-color) 53%, var(--hsp-portal-pattern-color) 56%, transparent 57%)',
            'size_mode' => 'dots',
        ),
        'hexagons' => array(
            'label'     => __( 'Hexagons', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'radial-gradient(circle farthest-side at 0% 50%, var(--hsp-portal-pattern-color) 23.5%, transparent 0), radial-gradient(circle farthest-side at 0% 50%, var(--hsp-portal-pattern-color) 23.5%, transparent 0), linear-gradient(var(--hsp-portal-pattern-color) 14%, var(--hsp-portal-pattern-color-2) 0, var(--hsp-portal-pattern-color-2) 85%, var(--hsp-portal-pattern-color) 0), linear-gradient(60deg, var(--hsp-portal-pattern-color-2) 23.5%, transparent 0, transparent 76.5%, var(--hsp-portal-pattern-color-2) 0), linear-gradient(-60deg, var(--hsp-portal-pattern-color-2) 23.5%, transparent 0, transparent 76.5%, var(--hsp-portal-pattern-color-2) 0)',
            'size_mode' => 'hex',
        ),
        'chevrons' => array(
            'label'     => __( 'Chevrons', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(135deg, var(--hsp-portal-pattern-color) 0, var(--hsp-portal-pattern-color) 8px, var(--hsp-portal-pattern-color-2) 8px, var(--hsp-portal-pattern-color-2) 16px)',
            'size_mode' => 'percent',
        ),
        'plus_grid' => array(
            'label'     => __( 'Plus Grid', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(0deg, transparent, transparent 18px, var(--hsp-portal-pattern-color) 18px, var(--hsp-portal-pattern-color) 20px), repeating-linear-gradient(90deg, transparent, transparent 18px, var(--hsp-portal-pattern-color) 18px, var(--hsp-portal-pattern-color) 20px)',
            'size_mode' => 'percent',
        ),
        'wave_lines' => array(
            'label'     => __( 'Wave Lines', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-radial-gradient(circle at 0 50%, transparent 0, transparent 10px, var(--hsp-portal-pattern-color) 11px, var(--hsp-portal-pattern-color) 12px, transparent 13px)',
            'size_mode' => 'percent',
        ),
        'circuit_board' => array(
            'label'     => __( 'Circuit Board', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(0deg, transparent, transparent 48px, var(--hsp-portal-pattern-color) 48px, var(--hsp-portal-pattern-color) 49px), repeating-linear-gradient(90deg, transparent, transparent 48px, var(--hsp-portal-pattern-color) 48px, var(--hsp-portal-pattern-color) 49px), radial-gradient(circle, var(--hsp-portal-pattern-color-2) 2px, transparent 2px)',
            'size_mode' => 'percent',
        ),
        'crosshatch' => array(
            'label'     => __( 'Crosshatch', 'hero-slider-plugin' ),
            'group'     => 'geometric',
            'css'       => 'repeating-linear-gradient(45deg, var(--hsp-portal-pattern-color) 0, var(--hsp-portal-pattern-color) 1px, transparent 1px, transparent 8px), repeating-linear-gradient(-45deg, var(--hsp-portal-pattern-color-2) 0, var(--hsp-portal-pattern-color-2) 1px, transparent 1px, transparent 8px)',
            'size_mode' => 'percent',
        ),
    );
}

function hero_slider_portal_get_pattern_size( $key, $scale, $presets ) {
    $scale = max( 25, intval( $scale ) );
    $mode  = isset( $presets[ $key ]['size_mode'] ) ? $presets[ $key ]['size_mode'] : 'percent';

    switch ( $mode ) {
        case 'dots':
            $px = max( 6, round( $scale / 2 ) );
            return $px . 'px ' . $px . 'px';
        case 'checker':
            $px = max( 8, round( 20 * $scale / 100 ) );
            return $px . 'px ' . $px . 'px';
        case 'hex':
            $w = max( 12, round( 28 * $scale / 100 ) );
            $h = max( 10, round( $w * 0.577 ) );
            return $w . 'px ' . $h . 'px';
        case 'road_map':
            $px = max( 36, round( 60 * $scale / 100 ) );
            return $px . 'px ' . $px . 'px';
        default:
            return $scale . '%';
    }
}

function hero_slider_portal_get_pattern_animations() {
    return array(
        'none'     => __( 'None (Static)', 'hero-slider-plugin' ),
        'drift'    => __( 'Drift (Diagonal Flow)', 'hero-slider-plugin' ),
        'scroll_h' => __( 'Scroll Horizontal', 'hero-slider-plugin' ),
        'scroll_v' => __( 'Scroll Vertical', 'hero-slider-plugin' ),
        'pulse'    => __( 'Pulse (Opacity)', 'hero-slider-plugin' ),
        'zoom'     => __( 'Zoom (Breathing)', 'hero-slider-plugin' ),
        'rotate'   => __( 'Rotate (Slow Spin)', 'hero-slider-plugin' ),
        'float'    => __( 'Float (Parallax)', 'hero-slider-plugin' ),
    );
}

function hero_slider_portal_get_pattern_anim_section_class( $opts ) {
    $p     = wp_parse_args( $opts, hero_slider_portal_get_settings_defaults() );
    $anims = array_keys( hero_slider_portal_get_pattern_animations() );
    $anim  = in_array( $p['portal_pattern_animation'], $anims, true ) ? $p['portal_pattern_animation'] : 'drift';
    if ( 'none' === $anim ) {
        return '';
    }
    return ' portal-pattern-anim-' . sanitize_html_class( $anim );
}

function hero_slider_portal_get_blend_modes() {
    return array( 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion' );
}

function hero_slider_portal_get_blend_mode_label( $mode ) {
    $labels = array(
        'normal'      => __( 'Normal', 'hero-slider-plugin' ),
        'multiply'    => __( 'Multiply', 'hero-slider-plugin' ),
        'screen'      => __( 'Screen', 'hero-slider-plugin' ),
        'overlay'     => __( 'Overlay', 'hero-slider-plugin' ),
        'darken'      => __( 'Darken', 'hero-slider-plugin' ),
        'lighten'     => __( 'Lighten', 'hero-slider-plugin' ),
        'color-dodge' => __( 'Color Dodge', 'hero-slider-plugin' ),
        'color-burn'  => __( 'Color Burn', 'hero-slider-plugin' ),
        'hard-light'  => __( 'Hard Light', 'hero-slider-plugin' ),
        'soft-light'  => __( 'Soft Light', 'hero-slider-plugin' ),
        'difference'  => __( 'Difference', 'hero-slider-plugin' ),
        'exclusion'   => __( 'Exclusion', 'hero-slider-plugin' ),
    );
    return isset( $labels[ $mode ] ) ? $labels[ $mode ] : $mode;
}

function hero_slider_portal_sanitize_card( $card ) {
    if ( ! is_array( $card ) ) {
        return array();
    }
    return array(
        'icon_id'         => isset( $card['icon_id'] ) ? absint( $card['icon_id'] ) : 0,
        'icon_url'        => isset( $card['icon_url'] ) ? esc_url_raw( $card['icon_url'] ) : '',
        'icon_bg'         => isset( $card['icon_bg'] ) ? ( sanitize_hex_color( $card['icon_bg'] ) ?: '#3b82f6' ) : '#3b82f6',
        'icon_radius'     => isset( $card['icon_radius'] ) ? max( 0, min( 50, intval( $card['icon_radius'] ) ) ) : 12,
        'title'           => isset( $card['title'] ) ? sanitize_text_field( $card['title'] ) : '',
        'title_color'     => isset( $card['title_color'] ) ? ( sanitize_hex_color( $card['title_color'] ) ?: '#111827' ) : '#111827',
        'title_size'      => isset( $card['title_size'] ) ? max( 10, min( 48, intval( $card['title_size'] ) ) ) : 18,
        'description'     => isset( $card['description'] ) ? sanitize_textarea_field( $card['description'] ) : '',
        'desc_color'      => isset( $card['desc_color'] ) ? ( sanitize_hex_color( $card['desc_color'] ) ?: '#6b7280' ) : '#6b7280',
        'desc_size'       => isset( $card['desc_size'] ) ? max( 10, min( 24, intval( $card['desc_size'] ) ) ) : 14,
        'cta_text'        => isset( $card['cta_text'] ) ? sanitize_text_field( $card['cta_text'] ) : '',
        'cta_url'         => isset( $card['cta_url'] ) ? esc_url_raw( $card['cta_url'] ) : '',
        'cta_color'       => isset( $card['cta_color'] ) ? ( sanitize_hex_color( $card['cta_color'] ) ?: '#3b82f6' ) : '#3b82f6',
        'cta_hover_color' => isset( $card['cta_hover_color'] ) ? ( sanitize_hex_color( $card['cta_hover_color'] ) ?: '#2563eb' ) : '#2563eb',
    );
}

function hero_slider_portal_sanitize_cards( $cards ) {
    if ( ! is_array( $cards ) ) {
        return array();
    }
    $clean = array();
    foreach ( $cards as $card ) {
        $clean[] = hero_slider_portal_sanitize_card( $card );
    }
    return $clean;
}

function hero_slider_portal_sanitize_settings( $input, $defaults ) {
    $clean = array();
    $valid_bg_types = array( 'solid', 'linear', 'radial' );
    $clean['portal_bg_type'] = ( isset( $input['portal_bg_type'] ) && in_array( $input['portal_bg_type'], $valid_bg_types, true ) )
        ? $input['portal_bg_type'] : $defaults['portal_bg_type'];

    $clean['portal_bg_color']     = isset( $input['portal_bg_color'] ) ? ( sanitize_hex_color( $input['portal_bg_color'] ) ?: $defaults['portal_bg_color'] ) : $defaults['portal_bg_color'];
    $clean['portal_grad_color_1'] = isset( $input['portal_grad_color_1'] ) ? ( sanitize_hex_color( $input['portal_grad_color_1'] ) ?: $defaults['portal_grad_color_1'] ) : $defaults['portal_grad_color_1'];
    $clean['portal_grad_color_2'] = isset( $input['portal_grad_color_2'] ) ? ( sanitize_hex_color( $input['portal_grad_color_2'] ) ?: $defaults['portal_grad_color_2'] ) : $defaults['portal_grad_color_2'];
    $clean['portal_grad_angle']   = isset( $input['portal_grad_angle'] ) ? max( 0, min( 360, intval( $input['portal_grad_angle'] ) ) ) : $defaults['portal_grad_angle'];

    $valid_patterns = array_keys( hero_slider_portal_get_pattern_presets() );
    $valid_patterns[] = 'custom';
    $clean['portal_pattern_source'] = ( isset( $input['portal_pattern_source'] ) && in_array( $input['portal_pattern_source'], $valid_patterns, true ) )
        ? $input['portal_pattern_source'] : $defaults['portal_pattern_source'];

    $clean['portal_pattern_custom_id']  = isset( $input['portal_pattern_custom_id'] ) ? absint( $input['portal_pattern_custom_id'] ) : 0;
    $clean['portal_pattern_custom_url']   = isset( $input['portal_pattern_custom_url'] ) ? esc_url_raw( $input['portal_pattern_custom_url'] ) : '';
    if ( $clean['portal_pattern_custom_id'] ) {
        $url = wp_get_attachment_url( $clean['portal_pattern_custom_id'] );
        if ( $url ) {
            $clean['portal_pattern_custom_url'] = $url;
        }
    }

    $valid_blends = hero_slider_portal_get_blend_modes();
    $clean['portal_pattern_blend'] = ( isset( $input['portal_pattern_blend'] ) && in_array( $input['portal_pattern_blend'], $valid_blends, true ) )
        ? $input['portal_pattern_blend'] : $defaults['portal_pattern_blend'];

    $clean['portal_pattern_opacity']  = isset( $input['portal_pattern_opacity'] ) ? max( 0, min( 100, intval( $input['portal_pattern_opacity'] ) ) ) : $defaults['portal_pattern_opacity'];
    $clean['portal_pattern_scale']    = isset( $input['portal_pattern_scale'] ) ? max( 25, min( 400, intval( $input['portal_pattern_scale'] ) ) ) : $defaults['portal_pattern_scale'];
    $clean['portal_pattern_color']    = isset( $input['portal_pattern_color'] ) ? ( sanitize_hex_color( $input['portal_pattern_color'] ) ?: $defaults['portal_pattern_color'] ) : $defaults['portal_pattern_color'];
    if ( isset( $input['portal_pattern_color_2'] ) ) {
        $raw_color_2 = sanitize_text_field( $input['portal_pattern_color_2'] );
        if ( 'transparent' === strtolower( $raw_color_2 ) ) {
            $clean['portal_pattern_color_2'] = 'transparent';
        } else {
            $clean['portal_pattern_color_2'] = sanitize_hex_color( $raw_color_2 ) ?: $defaults['portal_pattern_color_2'];
        }
    } else {
        $clean['portal_pattern_color_2'] = $defaults['portal_pattern_color_2'];
    }

    $valid_repeat = array( 'repeat', 'no-repeat', 'repeat-x', 'repeat-y', 'cover' );
    $clean['portal_pattern_repeat'] = ( isset( $input['portal_pattern_repeat'] ) && in_array( $input['portal_pattern_repeat'], $valid_repeat, true ) )
        ? $input['portal_pattern_repeat'] : $defaults['portal_pattern_repeat'];

    $clean['portal_pattern_position'] = isset( $input['portal_pattern_position'] ) ? sanitize_text_field( $input['portal_pattern_position'] ) : $defaults['portal_pattern_position'];

    $valid_anims = array_keys( hero_slider_portal_get_pattern_animations() );
    $clean['portal_pattern_animation']  = ( isset( $input['portal_pattern_animation'] ) && in_array( $input['portal_pattern_animation'], $valid_anims, true ) )
        ? $input['portal_pattern_animation'] : $defaults['portal_pattern_animation'];
    $clean['portal_pattern_anim_speed'] = isset( $input['portal_pattern_anim_speed'] ) ? max( 10, min( 120, intval( $input['portal_pattern_anim_speed'] ) ) ) : $defaults['portal_pattern_anim_speed'];

    $clean['portal_title_font']        = isset( $input['portal_title_font'] ) ? sanitize_text_field( $input['portal_title_font'] ) : $defaults['portal_title_font'];
    $clean['portal_title_size']        = isset( $input['portal_title_size'] ) ? max( 14, min( 72, intval( $input['portal_title_size'] ) ) ) : $defaults['portal_title_size'];
    $clean['portal_title_weight']      = isset( $input['portal_title_weight'] ) ? max( 100, min( 900, intval( $input['portal_title_weight'] ) ) ) : $defaults['portal_title_weight'];
    $clean['portal_title_line_height'] = isset( $input['portal_title_line_height'] ) ? max( 1, min( 3, (float) $input['portal_title_line_height'] ) ) : $defaults['portal_title_line_height'];
    $clean['portal_title_color']       = isset( $input['portal_title_color'] ) ? ( sanitize_hex_color( $input['portal_title_color'] ) ?: $defaults['portal_title_color'] ) : $defaults['portal_title_color'];

    $clean['portal_text_font']        = isset( $input['portal_text_font'] ) ? sanitize_text_field( $input['portal_text_font'] ) : $defaults['portal_text_font'];
    $clean['portal_text_size']        = isset( $input['portal_text_size'] ) ? max( 12, min( 36, intval( $input['portal_text_size'] ) ) ) : $defaults['portal_text_size'];
    $clean['portal_text_weight']      = isset( $input['portal_text_weight'] ) ? max( 100, min( 900, intval( $input['portal_text_weight'] ) ) ) : $defaults['portal_text_weight'];
    $clean['portal_text_line_height'] = isset( $input['portal_text_line_height'] ) ? max( 1, min( 3, (float) $input['portal_text_line_height'] ) ) : $defaults['portal_text_line_height'];
    $clean['portal_text_color']       = isset( $input['portal_text_color'] ) ? ( sanitize_hex_color( $input['portal_text_color'] ) ?: $defaults['portal_text_color'] ) : $defaults['portal_text_color'];

    $clean['portal_cards_gap']        = isset( $input['portal_cards_gap'] ) ? max( 0, min( 80, intval( $input['portal_cards_gap'] ) ) ) : $defaults['portal_cards_gap'];
    $clean['portal_cards_pad_x']      = isset( $input['portal_cards_pad_x'] ) ? max( 0, min( 120, intval( $input['portal_cards_pad_x'] ) ) ) : $defaults['portal_cards_pad_x'];
    $clean['portal_cards_pad_bottom'] = isset( $input['portal_cards_pad_bottom'] ) ? max( 0, min( 120, intval( $input['portal_cards_pad_bottom'] ) ) ) : $defaults['portal_cards_pad_bottom'];
    $clean['portal_card_bg']          = isset( $input['portal_card_bg'] ) ? ( sanitize_hex_color( $input['portal_card_bg'] ) ?: $defaults['portal_card_bg'] ) : $defaults['portal_card_bg'];
    $clean['portal_card_radius']      = isset( $input['portal_card_radius'] ) ? max( 0, min( 48, intval( $input['portal_card_radius'] ) ) ) : $defaults['portal_card_radius'];
    $clean['portal_card_padding']     = isset( $input['portal_card_padding'] ) ? max( 8, min( 64, intval( $input['portal_card_padding'] ) ) ) : $defaults['portal_card_padding'];
    $clean['portal_card_shadow']      = isset( $input['portal_card_shadow'] ) ? sanitize_text_field( $input['portal_card_shadow'] ) : $defaults['portal_card_shadow'];

    return $clean;
}

function hero_slider_portal_build_section_css_vars( $opts ) {
    $p = wp_parse_args( $opts, hero_slider_portal_get_settings_defaults() );

    $bg = $p['portal_bg_color'];
    if ( 'linear' === $p['portal_bg_type'] ) {
        $bg = sprintf( 'linear-gradient(%ddeg, %s 0%%, %s 100%%)', intval( $p['portal_grad_angle'] ), $p['portal_grad_color_1'], $p['portal_grad_color_2'] );
    } elseif ( 'radial' === $p['portal_bg_type'] ) {
        $bg = sprintf( 'radial-gradient(ellipse at center, %s 0%%, %s 100%%)', $p['portal_grad_color_1'], $p['portal_grad_color_2'] );
    }

    $pattern_bg = '';
    $pattern_size = '';
    if ( 'custom' === $p['portal_pattern_source'] && ! empty( $p['portal_pattern_custom_url'] ) ) {
        $pattern_bg = 'url(' . esc_url( $p['portal_pattern_custom_url'] ) . ')';
        $pattern_size = ( 'cover' === $p['portal_pattern_repeat'] ) ? 'cover' : ( intval( $p['portal_pattern_scale'] ) . '%' );
    } else {
        $presets = hero_slider_portal_get_pattern_presets();
        $key = isset( $presets[ $p['portal_pattern_source'] ] ) ? $p['portal_pattern_source'] : 'map_grid';
        $pattern_bg = $presets[ $key ]['css'];
        $pattern_size = hero_slider_portal_get_pattern_size( $key, $p['portal_pattern_scale'], $presets );
    }

    $repeat = ( 'cover' === $p['portal_pattern_repeat'] ) ? 'no-repeat' : $p['portal_pattern_repeat'];

    $pattern_position = $p['portal_pattern_position'];
    if ( 'custom' !== $p['portal_pattern_source'] ) {
        $presets_for_pos = hero_slider_portal_get_pattern_presets();
        $pos_key         = isset( $presets_for_pos[ $p['portal_pattern_source'] ] ) ? $p['portal_pattern_source'] : 'map_grid';
        if ( ! empty( $presets_for_pos[ $pos_key ]['bg_position'] ) ) {
            $pattern_position = $presets_for_pos[ $pos_key ]['bg_position'];
        }
    }

    $vars = array(
        '--hsp-portal-bg'                 => $bg,
        '--hsp-portal-pattern-bg'         => $pattern_bg,
        '--hsp-portal-pattern-size'       => $pattern_size,
        '--hsp-portal-pattern-repeat'     => $repeat,
        '--hsp-portal-pattern-position'   => $pattern_position,
        '--hsp-portal-pattern-blend'      => $p['portal_pattern_blend'],
        '--hsp-portal-pattern-opacity'    => ( intval( $p['portal_pattern_opacity'] ) / 100 ),
        '--hsp-portal-pattern-color'      => $p['portal_pattern_color'],
        '--hsp-portal-pattern-color-2'    => $p['portal_pattern_color_2'],
        '--hsp-portal-pattern-anim-duration' => max( 10, intval( $p['portal_pattern_anim_speed'] ) ) . 's',
        '--hsp-portal-title-font'         => $p['portal_title_font'],
        '--hsp-portal-title-size'         => intval( $p['portal_title_size'] ) . 'px',
        '--hsp-portal-title-weight'       => intval( $p['portal_title_weight'] ),
        '--hsp-portal-title-line-height'  => $p['portal_title_line_height'],
        '--hsp-portal-title-color'        => $p['portal_title_color'],
        '--hsp-portal-text-font'          => $p['portal_text_font'],
        '--hsp-portal-text-size'          => intval( $p['portal_text_size'] ) . 'px',
        '--hsp-portal-text-weight'        => intval( $p['portal_text_weight'] ),
        '--hsp-portal-text-line-height'   => $p['portal_text_line_height'],
        '--hsp-portal-text-color'         => $p['portal_text_color'],
        '--hsp-portal-cards-gap'          => intval( $p['portal_cards_gap'] ) . 'px',
        '--hsp-portal-cards-pad-x'        => intval( $p['portal_cards_pad_x'] ) . 'px',
        '--hsp-portal-cards-pad-bottom'   => intval( $p['portal_cards_pad_bottom'] ) . 'px',
        '--hsp-portal-card-bg'            => $p['portal_card_bg'],
        '--hsp-portal-card-radius'        => intval( $p['portal_card_radius'] ) . 'px',
        '--hsp-portal-card-padding'       => intval( $p['portal_card_padding'] ) . 'px',
        '--hsp-portal-card-shadow'        => $p['portal_card_shadow'],
    );

    $css = '';
    foreach ( $vars as $k => $v ) {
        $css .= $k . ':' . $v . ';';
    }
    return $css;
}

function hero_slider_portal_get_slide_bg_url( $post_id ) {
    $bg_id  = get_post_meta( $post_id, '_hero_slider_bg_id', true );
    $bg_url = '';
    if ( $bg_id ) {
        $bg_url = wp_get_attachment_image_url( (int) $bg_id, 'full' );
    }
    if ( ! $bg_url ) {
        $bg_url = get_post_meta( $post_id, '_hero_slider_bg_url', true );
    }
    return $bg_url ? esc_url( $bg_url ) : '';
}

function hero_slider_portal_get_slide_tint_rgba( $post_id ) {
    if ( get_post_meta( $post_id, '_hero_slider_disable_overlay', true ) === 'yes' ) {
        return '';
    }
    $tint_color = get_post_meta( $post_id, '_hero_slider_tint_color', true );
    $tint_alpha = get_post_meta( $post_id, '_hero_slider_tint_alpha', true );
    $tint_color = $tint_color ? $tint_color : '#000000';
    $tint_alpha = is_numeric( $tint_alpha ) ? max( 0, min( 1, (float) $tint_alpha ) ) : 0.35;
    $hex        = ltrim( $tint_color, '#' );
    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( strlen( $hex ) !== 6 ) {
        return '';
    }
    return sprintf(
        'rgba(%d,%d,%d,%s)',
        hexdec( substr( $hex, 0, 2 ) ),
        hexdec( substr( $hex, 2, 2 ) ),
        hexdec( substr( $hex, 4, 2 ) ),
        $tint_alpha
    );
}

function hero_slider_portal_get_cards_for_slide( $post_id ) {
    $slide_cards = get_post_meta( $post_id, '_hero_slider_portal_cards', true );
    if ( is_array( $slide_cards ) && ! empty( $slide_cards ) ) {
        return hero_slider_portal_sanitize_cards( $slide_cards );
    }
    return array();
}

function hero_slider_portal_render_slide( $post_id, $group_opts ) {
    $allowed = array(
        'br' => array(), 'span' => array( 'class' => true ), 'strong' => array(), 'em' => array(),
    );
    $title_html = get_post_meta( $post_id, '_hero_slider_title_html', true );
    $description_source = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_post_field( 'post_content', $post_id );
    $description = wp_strip_all_tags( strip_shortcodes( $description_source ) );
    $cards  = hero_slider_portal_get_cards_for_slide( $post_id );
    $bg_url = hero_slider_portal_get_slide_bg_url( $post_id );
    $tint   = $bg_url ? hero_slider_portal_get_slide_tint_rgba( $post_id ) : '';
    $slide_class = 'hero-slide-item hero-portal-slide' . ( $bg_url ? ' hero-portal-slide--has-image' : ' hero-portal-slide--has-pattern' );
    ?>
    <article class="<?php echo esc_attr( $slide_class ); ?>" data-img-mobile="show">
        <div class="hero-portal-base" aria-hidden="true"></div>
        <?php if ( $bg_url ) : ?>
            <div class="hero-portal-photo" style="background-image:url(<?php echo esc_url( $bg_url ); ?>);" aria-hidden="true"></div>
            <?php if ( $tint ) : ?>
                <div class="hero-portal-tint" style="background:<?php echo esc_attr( $tint ); ?>;" aria-hidden="true"></div>
            <?php endif; ?>
        <?php else : ?>
            <div class="hero-portal-pattern" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="hero-portal-inner">
            <div class="hero-portal-hero">
                <?php if ( $title_html ) : ?>
                    <h1 class="hero-portal-title"><?php echo wp_kses( $title_html, $allowed ); ?></h1>
                <?php else : ?>
                    <h1 class="hero-portal-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
                <?php endif; ?>
                <?php if ( $description ) : ?>
                    <p class="hero-portal-text"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $cards ) ) : ?>
                <div class="hero-portal-cards">
                    <?php foreach ( $cards as $i => $card ) :
                        $icon_url = $card['icon_url'];
                        if ( ! empty( $card['icon_id'] ) ) {
                            $att_url = wp_get_attachment_image_url( (int) $card['icon_id'], 'thumbnail' );
                            if ( $att_url ) {
                                $icon_url = $att_url;
                            }
                        }
                        $card_style = sprintf(
                            '--hsp-portal-card-title-color:%s;--hsp-portal-card-title-size:%dpx;--hsp-portal-card-desc-color:%s;--hsp-portal-card-desc-size:%dpx;--hsp-portal-card-icon-bg:%s;--hsp-portal-card-icon-radius:%dpx;--hsp-portal-card-cta-color:%s;--hsp-portal-card-cta-hover:%s;',
                            esc_attr( $card['title_color'] ),
                            intval( $card['title_size'] ),
                            esc_attr( $card['desc_color'] ),
                            intval( $card['desc_size'] ),
                            esc_attr( $card['icon_bg'] ),
                            intval( $card['icon_radius'] ),
                            esc_attr( $card['cta_color'] ),
                            esc_attr( $card['cta_hover_color'] )
                        );
                        ?>
                        <div class="hero-portal-card" style="<?php echo esc_attr( $card_style ); ?>">
                            <?php if ( $icon_url ) : ?>
                                <div class="hero-portal-card-icon">
                                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="" loading="lazy">
                                </div>
                            <?php else : ?>
                                <div class="hero-portal-card-icon hero-portal-card-icon--placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                            <div class="hero-portal-card-body">
                                <?php if ( ! empty( $card['title'] ) ) : ?>
                                    <h3 class="hero-portal-card-title"><?php echo esc_html( $card['title'] ); ?></h3>
                                <?php endif; ?>
                                <?php if ( ! empty( $card['description'] ) ) : ?>
                                    <p class="hero-portal-card-desc"><?php echo esc_html( $card['description'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $card['cta_text'] ) && ! empty( $card['cta_url'] ) ) : ?>
                                    <a class="hero-portal-card-cta" href="<?php echo esc_url( $card['cta_url'] ); ?>">
                                        <span><?php echo esc_html( $card['cta_text'] ); ?></span>
                                        <span class="hero-portal-card-cta-arrow" aria-hidden="true">&rsaquo;</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

function hero_slider_portal_render_group_fields( $settings, $field_prefix ) {
    $settings = wp_parse_args( $settings, hero_slider_portal_get_settings_defaults() );
    $fname = function( $key ) use ( $field_prefix ) {
        return esc_attr( $field_prefix . '[' . $key . ']' );
    };
    $presets = hero_slider_portal_get_pattern_presets();
    $is_portal = ( isset( $settings['slide_style'] ) && 'portal' === $settings['slide_style'] );
    ?>
    <tbody class="hsp-portal-settings" <?php echo $is_portal ? '' : 'style="display:none;"'; ?> data-portal-settings="1">
        <tr><td colspan="2"><h2 style="margin:24px 0 8px;padding-top:16px;border-top:1px solid #dcdcde;"><?php esc_html_e( 'Portal Layout — Background', 'hero-slider-plugin' ); ?></h2></td></tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Background Type', 'hero-slider-plugin' ); ?></th>
            <td>
                <select name="<?php echo $fname( 'portal_bg_type' ); ?>" class="hsp-portal-bg-type">
                    <option value="solid" <?php selected( $settings['portal_bg_type'], 'solid' ); ?>><?php esc_html_e( 'Solid Color', 'hero-slider-plugin' ); ?></option>
                    <option value="linear" <?php selected( $settings['portal_bg_type'], 'linear' ); ?>><?php esc_html_e( 'Linear Gradient', 'hero-slider-plugin' ); ?></option>
                    <option value="radial" <?php selected( $settings['portal_bg_type'], 'radial' ); ?>><?php esc_html_e( 'Radial Gradient', 'hero-slider-plugin' ); ?></option>
                </select>
            </td>
        </tr>
        <tr class="hsp-portal-solid-row">
            <th scope="row"><label><?php esc_html_e( 'Solid Color', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="text" name="<?php echo $fname( 'portal_bg_color' ); ?>" value="<?php echo esc_attr( $settings['portal_bg_color'] ); ?>" class="hsp-color-field" data-default-color="#1a1a4e"></td>
        </tr>
        <tr class="hsp-portal-grad-row">
            <th scope="row"><?php esc_html_e( 'Gradient Colors', 'hero-slider-plugin' ); ?></th>
            <td>
                <label><?php esc_html_e( 'Start', 'hero-slider-plugin' ); ?></label>
                <input type="text" name="<?php echo $fname( 'portal_grad_color_1' ); ?>" value="<?php echo esc_attr( $settings['portal_grad_color_1'] ); ?>" class="hsp-color-field" data-default-color="#6b21a8">
                <label style="margin-inline-start:12px;"><?php esc_html_e( 'End', 'hero-slider-plugin' ); ?></label>
                <input type="text" name="<?php echo $fname( 'portal_grad_color_2' ); ?>" value="<?php echo esc_attr( $settings['portal_grad_color_2'] ); ?>" class="hsp-color-field" data-default-color="#1e3a5f">
            </td>
        </tr>
        <tr class="hsp-portal-grad-row">
            <th scope="row"><label><?php esc_html_e( 'Gradient Angle (deg)', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="range" name="<?php echo $fname( 'portal_grad_angle' ); ?>" min="0" max="360" value="<?php echo esc_attr( $settings['portal_grad_angle'] ); ?>" class="hsp-portal-angle-range" oninput="this.nextElementSibling.value=this.value"><output><?php echo esc_html( $settings['portal_grad_angle'] ); ?></output></td>
        </tr>

        <tr><td colspan="2"><h2 style="margin:16px 0 8px;"><?php esc_html_e( 'Portal Layout — Pattern Overlay', 'hero-slider-plugin' ); ?></h2></td></tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Source', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select name="<?php echo $fname( 'portal_pattern_source' ); ?>" class="hsp-portal-pattern-source">
                    <optgroup label="<?php esc_attr_e( 'Basic Patterns', 'hero-slider-plugin' ); ?>">
                        <?php foreach ( $presets as $key => $preset ) : if ( isset( $preset['group'] ) && 'basic' !== $preset['group'] ) { continue; } ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['portal_pattern_source'], $key ); ?>><?php echo esc_html( $preset['label'] ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="<?php esc_attr_e( 'Geometric Shapes', 'hero-slider-plugin' ); ?>">
                        <?php foreach ( $presets as $key => $preset ) : if ( ! isset( $preset['group'] ) || 'geometric' !== $preset['group'] ) { continue; } ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['portal_pattern_source'], $key ); ?>><?php echo esc_html( $preset['label'] ); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <option value="custom" <?php selected( $settings['portal_pattern_source'], 'custom' ); ?>><?php esc_html_e( 'Custom Upload (SVG/PNG)', 'hero-slider-plugin' ); ?></option>
                </select>
                <p class="description"><?php esc_html_e( 'Choose a geometric overlay shape. Adjust background gradient colors above and pattern colors below.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr class="hsp-portal-custom-pattern-row" <?php echo 'custom' !== $settings['portal_pattern_source'] ? 'style="display:none;"' : ''; ?>>
            <th scope="row"><?php esc_html_e( 'Custom Pattern', 'hero-slider-plugin' ); ?></th>
            <td>
                <input type="hidden" name="<?php echo $fname( 'portal_pattern_custom_id' ); ?>" class="hsp-portal-pattern-id" value="<?php echo esc_attr( $settings['portal_pattern_custom_id'] ); ?>">
                <input type="text" name="<?php echo $fname( 'portal_pattern_custom_url' ); ?>" class="regular-text hsp-portal-pattern-url" value="<?php echo esc_url( $settings['portal_pattern_custom_url'] ); ?>" readonly placeholder="<?php esc_attr_e( 'No file selected', 'hero-slider-plugin' ); ?>">
                <button type="button" class="button hsp-portal-pattern-upload"><?php esc_html_e( 'Upload', 'hero-slider-plugin' ); ?></button>
                <button type="button" class="button hsp-portal-pattern-remove"><?php esc_html_e( 'Remove', 'hero-slider-plugin' ); ?></button>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Blend Mode', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select name="<?php echo $fname( 'portal_pattern_blend' ); ?>">
                    <?php foreach ( hero_slider_portal_get_blend_modes() as $mode ) : ?>
                        <option value="<?php echo esc_attr( $mode ); ?>" <?php selected( $settings['portal_pattern_blend'], $mode ); ?>><?php echo esc_html( hero_slider_portal_get_blend_mode_label( $mode ) ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Opacity (%)', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="range" name="<?php echo $fname( 'portal_pattern_opacity' ); ?>" min="0" max="100" value="<?php echo esc_attr( $settings['portal_pattern_opacity'] ); ?>" oninput="this.nextElementSibling.value=this.value+'%'"><output><?php echo esc_html( $settings['portal_pattern_opacity'] ); ?>%</output></td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Scale (%)', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="range" name="<?php echo $fname( 'portal_pattern_scale' ); ?>" min="25" max="400" value="<?php echo esc_attr( $settings['portal_pattern_scale'] ); ?>" oninput="this.nextElementSibling.value=this.value+'%'"><output><?php echo esc_html( $settings['portal_pattern_scale'] ); ?>%</output></td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Primary Color', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="text" name="<?php echo $fname( 'portal_pattern_color' ); ?>" value="<?php echo esc_attr( $settings['portal_pattern_color'] ); ?>" class="hsp-color-field" data-default-color="#0f172a"></td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Secondary Color', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="text" name="<?php echo $fname( 'portal_pattern_color_2' ); ?>" value="<?php echo esc_attr( $settings['portal_pattern_color_2'] ); ?>" class="hsp-color-field" data-default-color="transparent">
                <p class="description"><?php esc_html_e( 'Used by two-tone geometric shapes (checkerboard, diamonds, triangles, etc.).', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Repeat', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select name="<?php echo $fname( 'portal_pattern_repeat' ); ?>">
                    <option value="repeat" <?php selected( $settings['portal_pattern_repeat'], 'repeat' ); ?>><?php esc_html_e( 'Tile / Repeat', 'hero-slider-plugin' ); ?></option>
                    <option value="no-repeat" <?php selected( $settings['portal_pattern_repeat'], 'no-repeat' ); ?>><?php esc_html_e( 'No Repeat (Fixed)', 'hero-slider-plugin' ); ?></option>
                    <option value="cover" <?php selected( $settings['portal_pattern_repeat'], 'cover' ); ?>><?php esc_html_e( 'Cover', 'hero-slider-plugin' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Position', 'hero-slider-plugin' ); ?></label></th>
            <td><input type="text" name="<?php echo $fname( 'portal_pattern_position' ); ?>" value="<?php echo esc_attr( $settings['portal_pattern_position'] ); ?>" class="regular-text" placeholder="center center"></td>
        </tr>
        <tr>
            <th scope="row"><label><?php esc_html_e( 'Pattern Animation', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select name="<?php echo $fname( 'portal_pattern_animation' ); ?>">
                    <?php foreach ( hero_slider_portal_get_pattern_animations() as $key => $label ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['portal_pattern_animation'], $key ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e( 'Adds subtle motion to the geometric pattern overlay.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr class="hsp-portal-anim-speed-row" <?php echo 'none' === $settings['portal_pattern_animation'] ? 'style="display:none;"' : ''; ?>>
            <th scope="row"><label><?php esc_html_e( 'Animation Speed (seconds)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="range" name="<?php echo $fname( 'portal_pattern_anim_speed' ); ?>" min="10" max="120" value="<?php echo esc_attr( $settings['portal_pattern_anim_speed'] ); ?>" oninput="this.nextElementSibling.value=this.value+'s'">
                <output><?php echo esc_html( $settings['portal_pattern_anim_speed'] ); ?>s</output>
                <p class="description"><?php esc_html_e( 'Higher values = slower, smoother motion.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>

        <tr><td colspan="2"><h2 style="margin:16px 0 8px;"><?php esc_html_e( 'Portal Layout — Hero Text', 'hero-slider-plugin' ); ?></h2></td></tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Title Typography', 'hero-slider-plugin' ); ?></th>
            <td>
                <input type="text" name="<?php echo $fname( 'portal_title_font' ); ?>" value="<?php echo esc_attr( $settings['portal_title_font'] ); ?>" class="regular-text" placeholder="Tajawal, sans-serif">
                <input type="number" name="<?php echo $fname( 'portal_title_size' ); ?>" value="<?php echo esc_attr( $settings['portal_title_size'] ); ?>" min="14" max="72" class="small-text" title="<?php esc_attr_e( 'Font size (px)', 'hero-slider-plugin' ); ?>">
                <input type="number" name="<?php echo $fname( 'portal_title_weight' ); ?>" value="<?php echo esc_attr( $settings['portal_title_weight'] ); ?>" min="100" max="900" step="100" class="small-text" title="<?php esc_attr_e( 'Font weight', 'hero-slider-plugin' ); ?>">
                <input type="number" name="<?php echo $fname( 'portal_title_line_height' ); ?>" value="<?php echo esc_attr( $settings['portal_title_line_height'] ); ?>" min="1" max="3" step="0.05" class="small-text" title="<?php esc_attr_e( 'Line height', 'hero-slider-plugin' ); ?>">
                <input type="text" name="<?php echo $fname( 'portal_title_color' ); ?>" value="<?php echo esc_attr( $settings['portal_title_color'] ); ?>" class="hsp-color-field" data-default-color="#ffffff">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Description Typography', 'hero-slider-plugin' ); ?></th>
            <td>
                <input type="text" name="<?php echo $fname( 'portal_text_font' ); ?>" value="<?php echo esc_attr( $settings['portal_text_font'] ); ?>" class="regular-text" placeholder="Tajawal, sans-serif">
                <input type="number" name="<?php echo $fname( 'portal_text_size' ); ?>" value="<?php echo esc_attr( $settings['portal_text_size'] ); ?>" min="12" max="36" class="small-text">
                <input type="number" name="<?php echo $fname( 'portal_text_weight' ); ?>" value="<?php echo esc_attr( $settings['portal_text_weight'] ); ?>" min="100" max="900" step="100" class="small-text">
                <input type="number" name="<?php echo $fname( 'portal_text_line_height' ); ?>" value="<?php echo esc_attr( $settings['portal_text_line_height'] ); ?>" min="1" max="3" step="0.05" class="small-text">
                <input type="text" name="<?php echo $fname( 'portal_text_color' ); ?>" value="<?php echo esc_attr( $settings['portal_text_color'] ); ?>" class="hsp-color-field" data-default-color="#ffffff">
            </td>
        </tr>

        <tr><td colspan="2"><h2 style="margin:16px 0 8px;"><?php esc_html_e( 'Portal Layout — Cards Container', 'hero-slider-plugin' ); ?></h2></td></tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Container Spacing', 'hero-slider-plugin' ); ?></th>
            <td>
                <?php esc_html_e( 'Gap', 'hero-slider-plugin' ); ?> <input type="number" name="<?php echo $fname( 'portal_cards_gap' ); ?>" value="<?php echo esc_attr( $settings['portal_cards_gap'] ); ?>" min="0" max="80" class="small-text"> px
                <?php esc_html_e( 'Side padding', 'hero-slider-plugin' ); ?> <input type="number" name="<?php echo $fname( 'portal_cards_pad_x' ); ?>" value="<?php echo esc_attr( $settings['portal_cards_pad_x'] ); ?>" min="0" max="120" class="small-text"> px
                <?php esc_html_e( 'Bottom padding', 'hero-slider-plugin' ); ?> <input type="number" name="<?php echo $fname( 'portal_cards_pad_bottom' ); ?>" value="<?php echo esc_attr( $settings['portal_cards_pad_bottom'] ); ?>" min="0" max="120" class="small-text"> px
            </td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Card Design', 'hero-slider-plugin' ); ?></th>
            <td>
                <?php esc_html_e( 'Background', 'hero-slider-plugin' ); ?> <input type="text" name="<?php echo $fname( 'portal_card_bg' ); ?>" value="<?php echo esc_attr( $settings['portal_card_bg'] ); ?>" class="hsp-color-field" data-default-color="#ffffff">
                <?php esc_html_e( 'Radius', 'hero-slider-plugin' ); ?> <input type="number" name="<?php echo $fname( 'portal_card_radius' ); ?>" value="<?php echo esc_attr( $settings['portal_card_radius'] ); ?>" min="0" max="48" class="small-text"> px
                <?php esc_html_e( 'Padding', 'hero-slider-plugin' ); ?> <input type="number" name="<?php echo $fname( 'portal_card_padding' ); ?>" value="<?php echo esc_attr( $settings['portal_card_padding'] ); ?>" min="8" max="64" class="small-text"> px
                <br><label><?php esc_html_e( 'Box shadow', 'hero-slider-plugin' ); ?></label>
                <input type="text" name="<?php echo $fname( 'portal_card_shadow' ); ?>" value="<?php echo esc_attr( $settings['portal_card_shadow'] ); ?>" class="large-text" dir="ltr">
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <p class="description" style="margin:16px 0 0;"><?php esc_html_e( 'Add portal card content per slide from the slide edit screen. Card container styling is configured here.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
    </tbody>
    <?php
}

function hero_slider_portal_admin_scripts() {
    ?>
    <script>
    jQuery(function($) {
        function togglePortalRows() {
            var style = $('input[name*="[slide_style]"]:checked').val();
            var isPortal = style === 'portal';
            $('.hsp-portal-settings, .hsp-portal-settings-wrap').toggle(isPortal);
            $('.hsp-split-row').css('opacity', style === 'split' ? 1 : 0.5);
        }
        $('input[name*="[slide_style]"]').on('change', togglePortalRows);
        togglePortalRows();

        function togglePortalBgRows() {
            var t = $('.hsp-portal-bg-type').val();
            $('.hsp-portal-solid-row').toggle(t === 'solid');
            $('.hsp-portal-grad-row').toggle(t !== 'solid');
        }
        $(document).on('change', '.hsp-portal-bg-type', togglePortalBgRows);
        togglePortalBgRows();

        $(document).on('change', '.hsp-portal-pattern-source', function() {
            $('.hsp-portal-custom-pattern-row').toggle($(this).val() === 'custom');
        });

        $(document).on('change', 'select[name*="[portal_pattern_animation]"]', function() {
            $('.hsp-portal-anim-speed-row').toggle($(this).val() !== 'none');
        });

        $(document).on('click', '.hsp-portal-pattern-upload', function(e) {
            e.preventDefault();
            var $row = $(this).closest('td');
            var frame = wp.media({ title: '<?php echo esc_js( __( 'Select Pattern', 'hero-slider-plugin' ) ); ?>', button: { text: '<?php echo esc_js( __( 'Use image', 'hero-slider-plugin' ) ); ?>' }, multiple: false });
            frame.on('select', function() {
                var att = frame.state().get('selection').first().toJSON();
                $row.find('.hsp-portal-pattern-id').val(att.id);
                $row.find('.hsp-portal-pattern-url').val(att.url);
            });
            frame.open();
        });
        $(document).on('click', '.hsp-portal-pattern-remove', function() {
            var $row = $(this).closest('td');
            $row.find('.hsp-portal-pattern-id, .hsp-portal-pattern-url').val('');
        });

    });
    </script>
    <?php
}
add_action( 'admin_footer', 'hero_slider_portal_admin_scripts' );

/* ─── Portal: disable slide featured/background images in admin ─── */
add_action( 'add_meta_boxes', 'hero_slider_portal_remove_image_meta_boxes', 99 );
function hero_slider_portal_remove_image_meta_boxes() {
    global $post;
    if ( ! $post || 'hero_slide' !== $post->post_type ) {
        return;
    }
    if ( ! hero_slider_portal_slide_group_is_portal( $post->ID ) ) {
        return;
    }
    remove_meta_box( 'postimagediv', 'hero_slide', 'side' );
    remove_meta_box( 'hero-slider-mobile-img', 'hero_slide', 'side' );
}

add_action( 'save_post_hero_slide', 'hero_slider_portal_block_slide_image_saves', 1 );
function hero_slider_portal_block_slide_image_saves( $post_id ) {
    if ( ! hero_slider_portal_slide_group_is_portal_from_request( $post_id ) ) {
        return;
    }
    if ( isset( $_POST['_thumbnail_id'] ) ) {
        unset( $_POST['_thumbnail_id'] );
    }
}

add_action( 'save_post_hero_slide', 'hero_slider_portal_purge_slide_images', 100 );
function hero_slider_portal_purge_slide_images( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( ! hero_slider_portal_slide_group_is_portal_from_request( $post_id ) ) {
        return;
    }
    delete_post_thumbnail( $post_id );
    delete_post_meta( $post_id, '_hero_slider_img_mobile' );
}

/* ─── Per-slide portal cards meta box ─── */
add_action( 'add_meta_boxes', 'hero_slider_portal_add_cards_meta_box' );
function hero_slider_portal_add_cards_meta_box() {
    add_meta_box(
        'hero-slider-portal-cards',
        __( 'Portal Cards', 'hero-slider-plugin' ),
        'hero_slider_portal_cards_meta_box_content',
        'hero_slide',
        'normal',
        'default'
    );
}

function hero_slider_portal_cards_meta_box_content( $post ) {
    $cards = get_post_meta( $post->ID, '_hero_slider_portal_cards', true );
    if ( ! is_array( $cards ) || empty( $cards ) ) {
        $cards = hero_slider_portal_get_empty_cards();
    }
    $is_portal_group = hero_slider_portal_slide_group_is_portal( $post->ID );
    wp_nonce_field( 'hero_slider_portal_cards_nonce', 'hero_slider_portal_cards_nonce' );
    ?>
    <p class="description hsp-portal-cards-desc"><?php esc_html_e( 'Configure portal cards for this slide. Use the slide title and excerpt for the hero headline and description.', 'hero-slider-plugin' ); ?></p>
    <p class="description hsp-portal-cards-disabled-notice" <?php echo $is_portal_group ? 'style="display:none;"' : ''; ?>>
        <?php esc_html_e( 'Select a slider group with Portal layout to enable card editing.', 'hero-slider-plugin' ); ?>
    </p>
    <div id="hsp-slide-portal-cards" class="hsp-slide-portal-cards-fields" <?php echo $is_portal_group ? '' : 'style="display:none;"'; ?>>
        <?php foreach ( $cards as $i => $card ) : $card = wp_parse_args( $card, hero_slider_portal_sanitize_card( array() ) ); ?>
        <div class="hsp-slide-portal-card" style="background:#f6f7f7;padding:12px;margin-bottom:10px;border-radius:8px;">
            <strong><?php echo esc_html( sprintf( __( 'Card %d', 'hero-slider-plugin' ), $i + 1 ) ); ?></strong>
            <br>
            <input type="hidden" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][icon_id]" class="hsp-slide-icon-id" value="<?php echo esc_attr( $card['icon_id'] ); ?>">
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][icon_url]" class="regular-text hsp-slide-icon-url" value="<?php echo esc_url( $card['icon_url'] ); ?>" readonly placeholder="<?php esc_attr_e( 'Icon', 'hero-slider-plugin' ); ?>">
            <button type="button" class="button hsp-slide-icon-upload"><?php esc_html_e( 'Icon', 'hero-slider-plugin' ); ?></button>
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][icon_bg]" value="<?php echo esc_attr( $card['icon_bg'] ); ?>" class="hsp-color-field" data-default-color="#3b82f6" title="<?php esc_attr_e( 'Icon box color', 'hero-slider-plugin' ); ?>">
            <input type="number" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][icon_radius]" value="<?php echo esc_attr( $card['icon_radius'] ); ?>" min="0" max="50" class="small-text" title="<?php esc_attr_e( 'Icon radius', 'hero-slider-plugin' ); ?>">
            <br>
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr( $card['title'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Card title', 'hero-slider-plugin' ); ?>">
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][title_color]" value="<?php echo esc_attr( $card['title_color'] ); ?>" class="hsp-color-field">
            <input type="number" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][title_size]" value="<?php echo esc_attr( $card['title_size'] ); ?>" min="10" max="48" class="small-text">
            <br>
            <textarea name="hero_slider_portal_cards[<?php echo (int) $i; ?>][description]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Description', 'hero-slider-plugin' ); ?>"><?php echo esc_textarea( $card['description'] ); ?></textarea>
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][desc_color]" value="<?php echo esc_attr( $card['desc_color'] ); ?>" class="hsp-color-field">
            <input type="number" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][desc_size]" value="<?php echo esc_attr( $card['desc_size'] ); ?>" min="10" max="24" class="small-text">
            <br>
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][cta_text]" value="<?php echo esc_attr( $card['cta_text'] ); ?>" class="small-text" placeholder="<?php esc_attr_e( 'CTA', 'hero-slider-plugin' ); ?>">
            <input type="url" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][cta_url]" value="<?php echo esc_url( $card['cta_url'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'URL', 'hero-slider-plugin' ); ?>">
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][cta_color]" value="<?php echo esc_attr( $card['cta_color'] ); ?>" class="hsp-color-field" title="<?php esc_attr_e( 'CTA color', 'hero-slider-plugin' ); ?>">
            <input type="text" name="hero_slider_portal_cards[<?php echo (int) $i; ?>][cta_hover_color]" value="<?php echo esc_attr( $card['cta_hover_color'] ); ?>" class="hsp-color-field" title="<?php esc_attr_e( 'CTA hover', 'hero-slider-plugin' ); ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function hero_slider_portal_slide_group_is_portal_from_request( $post_id ) {
    if ( isset( $_POST['hero_slider_group'] ) ) {
        $group_id = intval( $_POST['hero_slider_group'] );
        if ( $group_id > 0 ) {
            $settings = hero_slider_plugin_get_group_settings_for_term( $group_id );
            return isset( $settings['slide_style'] ) && 'portal' === $settings['slide_style'];
        }
        return false;
    }
    return hero_slider_portal_slide_group_is_portal( $post_id );
}

add_action( 'save_post_hero_slide', 'hero_slider_portal_save_cards_meta' );
function hero_slider_portal_save_cards_meta( $post_id ) {
    if ( ! isset( $_POST['hero_slider_portal_cards_nonce'] ) || ! wp_verify_nonce( $_POST['hero_slider_portal_cards_nonce'], 'hero_slider_portal_cards_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    delete_post_meta( $post_id, '_hero_slider_portal_use_custom' );
    if ( ! hero_slider_portal_slide_group_is_portal_from_request( $post_id ) ) {
        return;
    }
    if ( isset( $_POST['hero_slider_portal_cards'] ) && is_array( $_POST['hero_slider_portal_cards'] ) ) {
        update_post_meta( $post_id, '_hero_slider_portal_cards', hero_slider_portal_sanitize_cards( wp_unslash( $_POST['hero_slider_portal_cards'] ) ) );
    }
}

add_action( 'admin_footer', 'hero_slider_portal_slide_admin_scripts' );
function hero_slider_portal_slide_admin_scripts() {
    if ( ! function_exists( 'get_current_screen' ) ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen || 'hero_slide' !== $screen->post_type || ! in_array( $screen->base, array( 'post', 'post-new' ), true ) ) {
        return;
    }
    $group_styles = hero_slider_portal_get_group_style_map();
    ?>
    <script>
    jQuery(function($) {
        var hspGroupStyles = <?php echo wp_json_encode( $group_styles ); ?>;

        function toggleSlidePortalCards() {
            var groupId = parseInt($('#hero-slider-group-select').val(), 10) || 0;
            var isPortal = hspGroupStyles[groupId] === 'portal';
            var $box = $('#hero-slider-portal-cards');
            $box.toggle(isPortal);
            $('.hsp-slide-portal-cards-fields').toggle(isPortal);
            $('.hsp-portal-cards-disabled-notice').toggle(!isPortal);
            $('.hsp-portal-cards-desc').toggle(isPortal);

            $('#postimagediv, #hero-slider-mobile-img').toggle(!isPortal);
            $('#hero-slider-background-settings').toggle(true);
            $('#hsp-portal-images-notice').toggle(isPortal);
            if (isPortal) {
                $('#postimagediv input, #postimagediv button, #postimagediv a').prop('disabled', true);
                $('#hero-slider-mobile-img input').prop('disabled', true);
                $('#hero-slider-background-settings input, #hero-slider-background-settings button, #hero-slider-background-settings select').prop('disabled', false);
            } else {
                $('#postimagediv input, #postimagediv button, #postimagediv a').prop('disabled', false);
                $('#hero-slider-mobile-img input').prop('disabled', false);
            }
        }

        if (!$('#hsp-portal-images-notice').length) {
            $('#hero-slider-group-select').closest('td, .inside').first().append(
                '<p id="hsp-portal-images-notice" class="description" style="display:none;margin-top:10px;"><?php echo esc_js( __( 'Portal layout: upload a background image per slide, or leave empty to use geometric patterns from group settings.', 'hero-slider-plugin' ) ); ?></p>'
            );
        }

        $('#hero-slider-group-select').on('change', toggleSlidePortalCards);
        toggleSlidePortalCards();

        $(document).on('click', '.hsp-slide-icon-upload', function(e) {
            e.preventDefault();
            var $box = $(this).closest('.hsp-slide-portal-card');
            var frame = wp.media({
                title: '<?php echo esc_js( __( 'Select Icon', 'hero-slider-plugin' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use image', 'hero-slider-plugin' ) ); ?>' },
                multiple: false
            });
            frame.on('select', function() {
                var att = frame.state().get('selection').first().toJSON();
                $box.find('.hsp-slide-icon-id').val(att.id);
                $box.find('.hsp-slide-icon-url').val(att.url);
            });
            frame.open();
        });
    });
    </script>
    <?php
}
