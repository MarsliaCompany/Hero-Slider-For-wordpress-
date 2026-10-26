<?php
/**
 * Hero Slider Plugin — Slider Groups Admin
 *
 * @copyright 2025 Mahmoud Mahsob / Marslia (https://mrslia.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ══════════════════════════════════════════════════════════════════
// دوال مساعدة للمجموعات — Group Helpers
// ══════════════════════════════════════════════════════════════════
// Map of shortcode_id => term_id stored in a single option for reliability
function hero_slider_plugin_get_group_id_map() {
    $map = get_option( 'hero_slider_group_id_map', array() );
    return is_array( $map ) ? $map : array();
}

function hero_slider_plugin_save_group_id_map( $map ) {
    update_option( 'hero_slider_group_id_map', $map, false );
}

function hero_slider_plugin_get_term_shortcode_id( $term_id ) {
    $map = hero_slider_plugin_get_group_id_map();
    foreach ( $map as $sid => $tid ) {
        if ( intval( $tid ) === intval( $term_id ) ) {
            return (string) $sid;
        }
    }
    return '';
}

function hero_slider_plugin_set_term_shortcode_id( $term_id, $shortcode_id ) {
    $map = hero_slider_plugin_get_group_id_map();
    // Remove any existing entry for this term_id
    foreach ( $map as $sid => $tid ) {
        if ( intval( $tid ) === intval( $term_id ) ) {
            unset( $map[ $sid ] );
        }
    }
    $map[ (string) $shortcode_id ] = intval( $term_id );
    hero_slider_plugin_save_group_id_map( $map );
}

function hero_slider_plugin_remove_term_shortcode_id( $term_id ) {
    $map = hero_slider_plugin_get_group_id_map();
    foreach ( $map as $sid => $tid ) {
        if ( intval( $tid ) === intval( $term_id ) ) {
            unset( $map[ $sid ] );
        }
    }
    hero_slider_plugin_save_group_id_map( $map );
}

function hero_slider_plugin_get_group_term_by_id( $shortcode_id ) {
    $shortcode_id = sanitize_text_field( $shortcode_id );
    $map = hero_slider_plugin_get_group_id_map();
    if ( ! isset( $map[ $shortcode_id ] ) ) {
        return false;
    }
    $term = get_term( intval( $map[ $shortcode_id ] ), 'hero_slider_group' );
    if ( ! $term || is_wp_error( $term ) ) {
        return false;
    }
    return $term;
}

// ──────────────────────────────────────────────────────────────────
// Per-group settings storage (uses wp_options, not term_meta)
// ──────────────────────────────────────────────────────────────────
function hero_slider_plugin_get_group_settings_map() {
    $map = get_option( 'hero_slider_group_settings_map', array() );
    return is_array( $map ) ? $map : array();
}

function hero_slider_plugin_save_group_settings_map( $map ) {
    update_option( 'hero_slider_group_settings_map', $map, false );
}

function hero_slider_plugin_get_term_settings( $term_id ) {
    $map = hero_slider_plugin_get_group_settings_map();
    $key = strval( intval( $term_id ) );
    if ( isset( $map[ $key ] ) && is_array( $map[ $key ] ) ) {
        return $map[ $key ];
    }
    return array();
}

function hero_slider_plugin_set_term_settings( $term_id, $settings ) {
    $map = hero_slider_plugin_get_group_settings_map();
    $key = strval( intval( $term_id ) );
    $map[ $key ] = is_array( $settings ) ? $settings : array();
    hero_slider_plugin_save_group_settings_map( $map );
}

function hero_slider_plugin_remove_term_settings( $term_id ) {
    $map = hero_slider_plugin_get_group_settings_map();
    $key = strval( intval( $term_id ) );
    if ( isset( $map[ $key ] ) ) {
        unset( $map[ $key ] );
        hero_slider_plugin_save_group_settings_map( $map );
    }
}

/**
 * القيم الافتراضية لنموذج إعدادات المجموعة في لوحة التحكم فقط.
 */
function hero_slider_plugin_get_group_settings_defaults() {
    return array(
        'autoplay'            => 'yes',
        'autoplay_speed'      => 5500,
        'show_dots'           => 'yes',
        'show_arrows'         => 'yes',
        'slider_height'        => 760,
        'mobile_slider_height' => 0,
        'use_custom_size'     => 'no',
        'custom_width'        => 0,
        'custom_height'       => 0,
        'transition_effect'   => 'fade',
        'transition_speed'    => 800,
        'css_easing'          => 'ease',
        'visual_effect'       => 'none',
        'overlay_disable'     => 'no',
        'overlay_alpha'       => 0.35,
        'theme_mode'          => 'dark',
        'image_size'          => 'full',
        'button_color_1'      => '#3b82f6',
        'button_color_2'      => '#1d4ed8',
        'button_color_3'      => '#2563eb',
        'button_text_color'   => '#ffffff',
        'title_color'         => '',
        'text_color'          => '',
        'effect_color_1'      => '#64b4ff',
        'effect_color_2'      => '#ffb05c',
        'effect_color_3'      => '#00c8ff',
        'content_padding_x'   => 48,
        'slide_style'         => 'classic',
        'split_bg_color'      => '#ffffff',
        'split_tint_color'    => '#2c6464',
        'split_tint_alpha'    => 0.55,
        'featured_img_mobile' => 'show',
        // RTL-friendly defaults: balanced text/image layout like reference hero
        'text_alignment'      => 'right',
        'title_alignment'     => 'right',
        'button_alignment'    => 'right',
    ) + hero_slider_portal_get_settings_defaults();
}

/**
 * إعدادات مجموعة محفوظة + قيم النموذج الافتراضية للمجموعة (بدون إعدادات عامة).
 */
function hero_slider_plugin_get_group_settings_for_term( $term_id ) {
    $term_id = (int) $term_id;
    if ( $term_id <= 0 ) {
        return hero_slider_plugin_get_group_settings_defaults();
    }

    $group_settings = hero_slider_plugin_get_term_settings( $term_id );
    if ( empty( $group_settings ) ) {
        $legacy = get_term_meta( $term_id, 'hero_slider_group_settings', true );
        if ( is_array( $legacy ) && ! empty( $legacy ) ) {
            $group_settings = $legacy;
            hero_slider_plugin_set_term_settings( $term_id, $legacy );
        }
    }
    if ( ! is_array( $group_settings ) ) {
        $group_settings = array();
    }

    return wp_parse_args( $group_settings, hero_slider_plugin_get_group_settings_defaults() );
}

/**
 * إعدادات مجموعة الشريحة (من المجموعة المعيّنة لها).
 */
function hero_slider_plugin_get_group_settings_for_slide( $post_id ) {
    $terms = get_the_terms( (int) $post_id, 'hero_slider_group' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        return hero_slider_plugin_get_group_settings_for_term( (int) $terms[0]->term_id );
    }

    return hero_slider_plugin_get_group_settings_defaults();
}

function hero_slider_plugin_get_group_settings_by_id( $shortcode_id ) {
    $term = hero_slider_plugin_get_group_term_by_id( $shortcode_id );
    if ( ! $term ) {
        return array();
    }

    return hero_slider_plugin_get_group_settings_for_term( $term->term_id );
}

// Auto-fill missing shortcode IDs for all existing groups on every admin load
add_action( 'admin_init', 'hero_slider_plugin_ensure_group_shortcode_ids' );
function hero_slider_plugin_ensure_group_shortcode_ids() {
    if ( ! taxonomy_exists( 'hero_slider_group' ) ) {
        return;
    }
    $terms = get_terms( array(
        'taxonomy'   => 'hero_slider_group',
        'hide_empty' => false,
    ) );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return;
    }
    foreach ( $terms as $term ) {
        $existing = hero_slider_plugin_get_term_shortcode_id( $term->term_id );
        if ( empty( $existing ) ) {
            $new_id = hero_slider_plugin_generate_group_shortcode_id();
            hero_slider_plugin_set_term_shortcode_id( $term->term_id, $new_id );
        }
    }
}

// Cleanup mapping when a group term is deleted
add_action( 'delete_hero_slider_group', 'hero_slider_plugin_cleanup_group_on_delete', 10, 2 );
function hero_slider_plugin_cleanup_group_on_delete( $term_id, $tt_id ) {
    hero_slider_plugin_remove_term_shortcode_id( $term_id );
    hero_slider_plugin_remove_term_settings( $term_id );
}

function hero_slider_plugin_generate_group_shortcode_id() {
    $id = strval( wp_rand( 10000, 99999 ) );
    $map = hero_slider_plugin_get_group_id_map();
    if ( isset( $map[ $id ] ) ) {
        return hero_slider_plugin_generate_group_shortcode_id();
    }
    return $id;
}

/**
 * Duplicate a slider group: new term, new shortcode ID, copied settings (no slides).
 *
 * @param int    $source_term_id Source group term ID.
 * @param string $new_name       Name for the duplicated group.
 * @return int|false New term ID on success, false on failure.
 */
function hero_slider_plugin_duplicate_group( $source_term_id, $new_name ) {
    $source_term_id = intval( $source_term_id );
    $new_name       = sanitize_text_field( $new_name );

    if ( $source_term_id <= 0 || '' === $new_name ) {
        return false;
    }

    $source_term = get_term( $source_term_id, 'hero_slider_group' );
    if ( ! $source_term || is_wp_error( $source_term ) ) {
        return false;
    }

    $result = wp_insert_term( $new_name, 'hero_slider_group' );
    if ( is_wp_error( $result ) ) {
        return false;
    }

    $new_term_id  = intval( $result['term_id'] );
    $shortcode_id = hero_slider_plugin_generate_group_shortcode_id();
    hero_slider_plugin_set_term_shortcode_id( $new_term_id, $shortcode_id );

    $settings = hero_slider_plugin_get_group_settings_for_term( $source_term_id );
    hero_slider_plugin_set_term_settings( $new_term_id, $settings );

    return $new_term_id;
}

function hero_slider_plugin_get_group_settings( $group_id_or_slug ) {
    $term = get_term_by( 'slug', sanitize_title( $group_id_or_slug ), 'hero_slider_group' );
    if ( ! $term || is_wp_error( $term ) ) {
        return hero_slider_plugin_get_group_settings_defaults();
    }

    return hero_slider_plugin_get_group_settings_for_term( $term->term_id );
}

// ══════════════════════════════════════════════════════════════════
// صفحات إدارة المجموعات — Group Admin Pages
// ══════════════════════════════════════════════════════════════════
add_action( 'admin_menu', 'hero_slider_plugin_add_groups_page' );
function hero_slider_plugin_add_groups_page() {
    add_submenu_page(
        'edit.php?post_type=hero_slide',
        __( 'Slider Groups', 'hero-slider-plugin' ),
        __( 'Slider Groups', 'hero-slider-plugin' ),
        'manage_options',
        'hero-slider-groups',
        'hero_slider_plugin_groups_list_page'
    );
}

// ─── قائمة المجموعات — Group List ───
function hero_slider_plugin_groups_list_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission.', 'hero-slider-plugin' ) );
    }

    $action   = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
    $group_id = isset( $_GET['group_id'] ) ? intval( $_GET['group_id'] ) : 0;

    if ( 'edit' === $action && $group_id > 0 ) {
        hero_slider_plugin_group_edit_page( $group_id );
        return;
    }
    if ( 'add' === $action ) {
        hero_slider_plugin_group_edit_page( 0 );
        return;
    }
    if ( 'duplicate' === $action && $group_id > 0 ) {
        hero_slider_plugin_group_duplicate_page( $group_id );
        return;
    }

    // Process delete
    if ( 'delete' === $action && $group_id > 0 && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'delete_group_' . $group_id ) ) {
        wp_delete_term( $group_id, 'hero_slider_group' );
        echo '<div class="updated"><p>' . esc_html__( 'Group deleted.', 'hero-slider-plugin' ) . '</p></div>';
    }

    $groups = get_terms( array(
        'taxonomy'   => 'hero_slider_group',
        'hide_empty' => false,
    ) );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Slider Groups', 'hero-slider-plugin' ); ?>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=add' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'Add New Group', 'hero-slider-plugin' ); ?></a>
        </h1>
        <p class="description"><?php echo esc_html__( 'Create separate slider groups. Each group can have its own slides, settings, and shortcode.', 'hero-slider-plugin' ); ?></p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php echo esc_html__( 'Name', 'hero-slider-plugin' ); ?></th>
                    <th><?php echo esc_html__( 'Shortcode ID', 'hero-slider-plugin' ); ?></th>
                    <th><?php echo esc_html__( 'Slides', 'hero-slider-plugin' ); ?></th>
                    <th><?php echo esc_html__( 'Shortcode', 'hero-slider-plugin' ); ?></th>
                    <th><?php echo esc_html__( 'Actions', 'hero-slider-plugin' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $groups ) || is_wp_error( $groups ) ) : ?>
                    <tr><td colspan="5"><?php echo esc_html__( 'No groups found. Create your first group above.', 'hero-slider-plugin' ); ?></td></tr>
                <?php else : ?>
                    <?php foreach ( $groups as $group ) :
                        $count_query = new WP_Query( array(
                            'post_type'      => 'hero_slide',
                            'posts_per_page' => 1,
                            'tax_query'      => array(
                                array(
                                    'taxonomy' => 'hero_slider_group',
                                    'field'    => 'term_id',
                                    'terms'    => $group->term_id,
                                ),
                            ),
                        ) );
                        $slide_count = $count_query->found_posts;
                        wp_reset_postdata();
                    ?>
                        <?php
                        $group_shortcode_id = hero_slider_plugin_get_term_shortcode_id( $group->term_id );
                        if ( empty( $group_shortcode_id ) ) {
                            $group_shortcode_id = hero_slider_plugin_generate_group_shortcode_id();
                            hero_slider_plugin_set_term_shortcode_id( $group->term_id, $group_shortcode_id );
                        }
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html( $group->name ); ?></strong></td>
                            <td><code><?php echo esc_html( $group_shortcode_id ); ?></code></td>
                            <td><?php echo intval( $slide_count ); ?></td>
                            <td><code>[hero_slider group="<?php echo esc_attr( $group_shortcode_id ); ?>"]</code></td>
                            <td>
                                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=edit&group_id=' . $group->term_id ) ); ?>"><?php echo esc_html__( 'Edit', 'hero-slider-plugin' ); ?></a> |
                                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=duplicate&group_id=' . $group->term_id ) ); ?>"><?php echo esc_html__( 'Duplicate', 'hero-slider-plugin' ); ?></a> |
                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=delete&group_id=' . $group->term_id ), 'delete_group_' . $group->term_id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this group? Slides will become ungrouped.', 'hero-slider-plugin' ) ); ?>');"><?php echo esc_html__( 'Delete', 'hero-slider-plugin' ); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// ─── استنساخ مجموعة — Duplicate Group Page ───
function hero_slider_plugin_group_duplicate_page( $group_id ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission.', 'hero-slider-plugin' ) );
    }

    $group_id = intval( $group_id );
    $group    = get_term( $group_id, 'hero_slider_group' );
    if ( ! $group || is_wp_error( $group ) ) {
        wp_die( esc_html__( 'Group not found.', 'hero-slider-plugin' ) );
    }

    $error = '';
    if ( isset( $_POST['hero_slider_duplicate_nonce'] ) && wp_verify_nonce( $_POST['hero_slider_duplicate_nonce'], 'hero_slider_duplicate_group_' . $group_id ) ) {
        $new_name = isset( $_POST['group_name'] ) ? sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) : '';
        if ( '' === $new_name ) {
            $error = __( 'Please enter a name for the new group.', 'hero-slider-plugin' );
        } else {
            $new_term_id = hero_slider_plugin_duplicate_group( $group_id, $new_name );
            if ( $new_term_id ) {
                wp_safe_redirect(
                    add_query_arg(
                        array(
                            'post_type' => 'hero_slide',
                            'page'      => 'hero-slider-groups',
                            'action'    => 'edit',
                            'group_id'  => $new_term_id,
                            'duplicated' => '1',
                        ),
                        admin_url( 'edit.php' )
                    )
                );
                exit;
            }
            $error = __( 'Could not duplicate the group. The name may already exist.', 'hero-slider-plugin' );
        }
    }

    $default_name = sprintf(
        /* translators: %s: original group name */
        __( 'Copy of %s', 'hero-slider-plugin' ),
        $group->name
    );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Duplicate Group', 'hero-slider-plugin' ); ?></h1>
        <p class="description">
            <?php
            echo esc_html(
                sprintf(
                    /* translators: %s: source group name */
                    __( 'Create a new group with the same settings as "%s". Slides are not copied.', 'hero-slider-plugin' ),
                    $group->name
                )
            );
            ?>
        </p>
        <?php if ( $error ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'hero_slider_duplicate_group_' . $group_id, 'hero_slider_duplicate_nonce' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="group-name"><?php echo esc_html__( 'New Group Name', 'hero-slider-plugin' ); ?></label></th>
                    <td>
                        <input type="text" id="group-name" name="group_name" value="<?php echo esc_attr( $default_name ); ?>" class="regular-text" required>
                        <p class="description"><?php echo esc_html__( 'All group settings (layout, colors, portal, effects, etc.) will be copied. Assign slides to the new group manually.', 'hero-slider-plugin' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Duplicate Group', 'hero-slider-plugin' ) ); ?>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups' ) ); ?>" class="button"><?php echo esc_html__( 'Cancel', 'hero-slider-plugin' ); ?></a>
        </form>
    </div>
    <?php
}

// ─── تعديل/إضافة مجموعة — Group Edit Page ───
function hero_slider_plugin_group_edit_page( $group_id = 0 ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission.', 'hero-slider-plugin' ) );
    }

    $group      = null;
    $group_name = '';
    if ( $group_id > 0 ) {
        $group = get_term( $group_id, 'hero_slider_group' );
        if ( $group && ! is_wp_error( $group ) ) {
            $group_name = $group->name;
        }
    }

    $saved = false;
    if ( isset( $_POST['hero_slider_group_settings_nonce'] ) && wp_verify_nonce( $_POST['hero_slider_group_settings_nonce'], 'hero_slider_group_settings_nonce' ) ) {
        $new_name = isset( $_POST['group_name'] ) ? sanitize_text_field( $_POST['group_name'] ) : '';
        if ( $new_name ) {
            if ( $group_id > 0 && $group ) {
                wp_update_term( $group_id, 'hero_slider_group', array( 'name' => $new_name ) );
                $group_name = $new_name;
            } else {
                $result = wp_insert_term( $new_name, 'hero_slider_group' );
                if ( ! is_wp_error( $result ) ) {
                    $group_id = $result['term_id'];
                    $group    = get_term( $group_id, 'hero_slider_group' );
                    $group_name = $group->name;
                    $shortcode_id = hero_slider_plugin_generate_group_shortcode_id();
                    hero_slider_plugin_set_term_shortcode_id( $group_id, $shortcode_id );
                }
            }
        }

        if ( $group_id > 0 ) {
            $input    = isset( $_POST['hero_slider_group_settings'] ) ? (array) $_POST['hero_slider_group_settings'] : array();
            // Featured image mobile was posted outside the settings array (legacy field name).
            if ( ! isset( $input['featured_img_mobile'] ) && isset( $_POST['_hero_slider_img_mobile'] ) ) {
                $input['featured_img_mobile'] = sanitize_text_field( wp_unslash( $_POST['_hero_slider_img_mobile'] ) );
            }
            $existing = hero_slider_plugin_get_term_settings( $group_id );
            $clean    = hero_slider_plugin_sanitize_settings( $input );
            // الحقول المعطّلة (أبعاد مخصصة) لا تُرسل مع النموذج — نحافظ على القيم المحفوظة
            if ( 'no' === $clean['use_custom_size'] && is_array( $existing ) ) {
                if ( ! isset( $input['custom_width'] ) && isset( $existing['custom_width'] ) ) {
                    $clean['custom_width'] = (int) $existing['custom_width'];
                }
                if ( ! isset( $input['custom_height'] ) && isset( $existing['custom_height'] ) ) {
                    $clean['custom_height'] = (int) $existing['custom_height'];
                }
            }
            hero_slider_plugin_set_term_settings( $group_id, $clean );
            $saved = true;
        }
    }

    $settings     = $group_id > 0
        ? hero_slider_plugin_get_group_settings_for_term( $group_id )
        : hero_slider_plugin_get_group_settings_defaults();
    $page_title   = $group_id > 0 ? __( 'Edit Group', 'hero-slider-plugin' ) : __( 'Add Group', 'hero-slider-plugin' );
    $field_prefix = 'hero_slider_group_settings';
    $duplicated   = isset( $_GET['duplicated'] ) && '1' === $_GET['duplicated'];
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( $page_title ); ?></h1>
        <?php if ( $duplicated ) : ?>
            <div class="updated"><p><?php echo esc_html__( 'Group duplicated successfully. Assign slides to this group as needed.', 'hero-slider-plugin' ); ?></p></div>
        <?php elseif ( $saved ) : ?>
            <div class="updated"><p><?php echo esc_html__( 'Group settings saved.', 'hero-slider-plugin' ); ?></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'hero_slider_group_settings_nonce', 'hero_slider_group_settings_nonce' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="group-name"><?php echo esc_html__( 'Group Name', 'hero-slider-plugin' ); ?></label></th>
                    <td>
                        <input type="text" id="group-name" name="group_name" value="<?php echo esc_attr( $group_name ); ?>" class="regular-text" required>
                        <?php if ( $group_id > 0 ) :
                            $group_shortcode_id = hero_slider_plugin_get_term_shortcode_id( $group_id );
                            if ( empty( $group_shortcode_id ) ) {
                                $group_shortcode_id = hero_slider_plugin_generate_group_shortcode_id();
                                hero_slider_plugin_set_term_shortcode_id( $group_id, $group_shortcode_id );
                            }
                        ?>
                            <p class="description"><?php echo esc_html__( 'Shortcode:', 'hero-slider-plugin' ); ?> <code>[hero_slider group="<?php echo esc_attr( $group_shortcode_id ); ?>"]</code></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <hr>
            <h2><?php echo esc_html__( 'Group Settings', 'hero-slider-plugin' ); ?></h2>
            <p class="description"><?php echo esc_html__( 'These settings apply only to this group and its slides.', 'hero-slider-plugin' ); ?></p>
            <?php hero_slider_plugin_render_settings_fields( $settings, $field_prefix ); ?>
            <?php submit_button( __( 'Save Group', 'hero-slider-plugin' ) ); ?>
            <?php if ( $group_id > 0 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups&action=duplicate&group_id=' . $group_id ) ); ?>" class="button"><?php echo esc_html__( 'Duplicate Group', 'hero-slider-plugin' ); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hero_slide&page=hero-slider-groups' ) ); ?>" class="button"><?php echo esc_html__( 'Back to Groups', 'hero-slider-plugin' ); ?></a>
        </form>
    </div>
    <style>
        .hsp-effects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; max-width: 820px; }
        .hsp-effect-card { display: flex; flex-direction: column; gap: 4px; padding: 16px; border: 2px solid #e0e0e0; border-radius: 10px; background: #fff; cursor: pointer; transition: all 0.2s; position: relative; }
        .hsp-effect-card:hover { border-color: #2271b1; background: #f0f6fc; }
        .hsp-effect-card.is-active { border-color: #2271b1; background: #f0f6fc; box-shadow: 0 0 0 1px #2271b1; }
        .hsp-effect-card input[type=radio] { position: absolute; top: 12px; right: 12px; margin: 0; }
        .hsp-effect-icon { color: #2271b1; font-size: 24px; width: 24px; height: 24px; margin-bottom: 4px; }
        .hsp-effect-name { font-weight: 600; font-size: 14px; color: #1d2327; }
        .hsp-effect-desc { font-size: 12px; color: #646970; line-height: 1.5; }
    </style>
    <script>
    jQuery(function($) {
        if (typeof $.fn.wpColorPicker === 'function') {
            $('.hsp-color-field').wpColorPicker();
        }
        var prefix = '<?php echo esc_js( $field_prefix ); ?>';
        $('input[name="' + prefix + '[use_custom_size]"]').on('change', function() {
            var enabled = $('input[name="' + prefix + '[use_custom_size]"]:checked').val() === 'yes';
            var $row = $('.hsp-custom-dimensions-row');
            $row.css('opacity', enabled ? 1 : 0.5);
            $row.find('input[type=number]').prop('disabled', !enabled);
        });
        $('.hsp-effect-card input[type=radio]').on('change', function() {
            $('.hsp-effect-card').removeClass('is-active');
            $(this).closest('.hsp-effect-card').addClass('is-active');
        });
        $('input[name="' + prefix + '[slide_style]"]').on('change', function() {
            var isSplit = $('input[name="' + prefix + '[slide_style]"]:checked').val() === 'split';
            $('.hsp-split-row').css('opacity', isSplit ? 1 : 0.5);
        });
        $('input[name="' + prefix + '[use_custom_size]"]:checked').trigger('change');
        $('input[name="' + prefix + '[slide_style]"]:checked').trigger('change');
    });
    </script>
    <?php
}

// ══════════════════════════════════════════════════════════════════
// حقل الإعدادات القابلة لإعادة الاستخدام — Reusable Settings Form
// ══════════════════════════════════════════════════════════════════
function hero_slider_plugin_render_settings_fields( $settings, $field_prefix ) {
    $settings = wp_parse_args( is_array( $settings ) ? $settings : array(), hero_slider_plugin_get_group_settings_defaults() );
    $sizes    = get_intermediate_image_sizes();
    array_unshift( $sizes, 'full' );
    $sizes = array_unique( $sizes );
    $fname = function( $key ) use ( $field_prefix ) {
        return esc_attr( $field_prefix . '[' . $key . ']' );
    };
    ?>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><?php echo esc_html__( 'Navigation Controls', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <p style="margin:0 0 8px;">
                        <strong><?php echo esc_html__( 'Dots', 'hero-slider-plugin' ); ?></strong><br>
                        <label style="margin-inline-end:14px;">
                            <input type="radio" name="<?php echo $fname( 'show_dots' ); ?>" value="yes" <?php checked( $settings['show_dots'], 'yes' ); ?>>
                            <?php echo esc_html__( 'Show', 'hero-slider-plugin' ); ?>
                        </label>
                        <label>
                            <input type="radio" name="<?php echo $fname( 'show_dots' ); ?>" value="no" <?php checked( $settings['show_dots'], 'no' ); ?>>
                            <?php echo esc_html__( 'Hide', 'hero-slider-plugin' ); ?>
                        </label>
                    </p>
                    <p style="margin:0;">
                        <strong><?php echo esc_html__( 'Arrows', 'hero-slider-plugin' ); ?></strong><br>
                        <label style="margin-inline-end:14px;">
                            <input type="radio" name="<?php echo $fname( 'show_arrows' ); ?>" value="yes" <?php checked( $settings['show_arrows'], 'yes' ); ?>>
                            <?php echo esc_html__( 'Show', 'hero-slider-plugin' ); ?>
                        </label>
                        <label>
                            <input type="radio" name="<?php echo $fname( 'show_arrows' ); ?>" value="no" <?php checked( $settings['show_arrows'], 'no' ); ?>>
                            <?php echo esc_html__( 'Hide', 'hero-slider-plugin' ); ?>
                        </label>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Autoplay', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="<?php echo $fname('autoplay'); ?>" value="yes" <?php checked( $settings['autoplay'], 'yes' ); ?>> <?php echo esc_html__( 'Enabled', 'hero-slider-plugin' ); ?></label><br>
                    <label><input type="radio" name="<?php echo $fname('autoplay'); ?>" value="no" <?php checked( $settings['autoplay'], 'no' ); ?>> <?php echo esc_html__( 'Disabled', 'hero-slider-plugin' ); ?></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-autoplay-speed"><?php echo esc_html__( 'Delay Between Slides (ms)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="number" id="hsp-autoplay-speed" name="<?php echo $fname('autoplay_speed'); ?>" value="<?php echo esc_attr( $settings['autoplay_speed'] ); ?>" min="500" step="100" class="small-text">
                <p class="description"><?php echo esc_html__( 'Applies when autoplay is enabled. Example: 5500', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-transition-speed"><?php echo esc_html__( 'Transition Duration (ms)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="number" id="hsp-transition-speed" name="<?php echo $fname('transition_speed'); ?>" value="<?php echo esc_attr( $settings['transition_speed'] ); ?>" min="100" step="100" class="small-text">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-transition-effect"><?php echo esc_html__( 'Slide Transition Effect', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select id="hsp-transition-effect" name="<?php echo $fname('transition_effect'); ?>">
                    <?php
                    $transition_options = array(
                        'fade'  => __( 'Fade', 'hero-slider-plugin' ),
                        'slide' => __( 'Slide', 'hero-slider-plugin' ),
                        'zoom'  => __( 'Zoom & Fade', 'hero-slider-plugin' ),
                        'flip'  => __( 'Flip (3D)', 'hero-slider-plugin' ),
                    );
                    foreach ( $transition_options as $key => $label ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['transition_effect'], $key ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php echo esc_html__( 'Choose how slides transition. Zoom/Flip add subtle CSS animations on top of fade.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Slide Layout Style', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:inline-block;margin-inline-end:18px;">
                        <input type="radio" name="<?php echo $fname('slide_style'); ?>" value="classic" <?php checked( $settings['slide_style'], 'classic' ); ?>>
                        <strong><?php echo esc_html__( 'Classic (Full background)', 'hero-slider-plugin' ); ?></strong>
                    </label>
                    <label style="display:inline-block;">
                        <input type="radio" name="<?php echo $fname('slide_style'); ?>" value="split" <?php checked( $settings['slide_style'], 'split' ); ?>>
                        <strong><?php echo esc_html__( 'Split (Half text / half image)', 'hero-slider-plugin' ); ?></strong>
                    </label>
                    <label style="display:inline-block;">
                        <input type="radio" name="<?php echo $fname('slide_style'); ?>" value="image_centered" <?php checked( $settings['slide_style'], 'image_centered' ); ?>>
                        <strong><?php echo esc_html__( 'Image Centered (background only, text centered)', 'hero-slider-plugin' ); ?></strong>
                    </label>
                    <label style="display:block;margin-top:8px;">
                        <input type="radio" name="<?php echo $fname('slide_style'); ?>" value="portal" <?php checked( $settings['slide_style'], 'portal' ); ?>>
                        <strong><?php echo esc_html__( 'Portal (Hero + Cards — LVN style)', 'hero-slider-plugin' ); ?></strong>
                    </label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'Classic shows the image as a full background. Split places text on a colored half and the image on the other half. Image Centered hides the featured image, removes all background overlays, and displays the background image at full quality with centered text.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Text Alignment', 'hero-slider-plugin' ); ?></th>
            <td>
                <label for="hsp-text-alignment"><?php echo esc_html__( 'Text Alignment', 'hero-slider-plugin' ); ?></label>
                <select id="hsp-text-alignment" name="<?php echo $fname('text_alignment'); ?>" class="widefat">
                    <option value="left" <?php selected( $settings['text_alignment'], 'left' ); ?>><?php echo esc_html__( 'Left', 'hero-slider-plugin' ); ?></option>
                    <option value="center" <?php selected( $settings['text_alignment'], 'center' ); ?>><?php echo esc_html__( 'Center', 'hero-slider-plugin' ); ?></option>
                    <option value="right" <?php selected( $settings['text_alignment'], 'right' ); ?>><?php echo esc_html__( 'Right', 'hero-slider-plugin' ); ?></option>
                </select>
                <p class="description"><?php echo esc_html__( 'Alignment of title and description text.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Title Alignment', 'hero-slider-plugin' ); ?></th>
            <td>
                <label for="hsp-title-alignment"><?php echo esc_html__( 'Title Alignment', 'hero-slider-plugin' ); ?></label>
                <select id="hsp-title-alignment" name="<?php echo $fname('title_alignment'); ?>" class="widefat">
                    <option value="left" <?php selected( $settings['title_alignment'], 'left' ); ?>><?php echo esc_html__( 'Left', 'hero-slider-plugin' ); ?></option>
                    <option value="center" <?php selected( $settings['title_alignment'], 'center' ); ?>><?php echo esc_html__( 'Center', 'hero-slider-plugin' ); ?></option>
                    <option value="right" <?php selected( $settings['title_alignment'], 'right' ); ?>><?php echo esc_html__( 'Right', 'hero-slider-plugin' ); ?></option>
                </select>
                <p class="description"><?php echo esc_html__( 'Alignment of the slide title.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr class="hsp-split-row" <?php echo 'split' !== $settings['slide_style'] ? 'style="opacity:0.5;"' : ''; ?>>
            <th scope="row"><?php echo esc_html__( 'Split Style Colors', 'hero-slider-plugin' ); ?></th>
            <td>
                <p style="margin:0 0 10px;">
                    <label for="hsp-split-bg" style="display:inline-block;min-width:160px;"><?php echo esc_html__( 'Text Side Background', 'hero-slider-plugin' ); ?></label>
                    <input type="text" id="hsp-split-bg" name="<?php echo $fname('split_bg_color'); ?>" value="<?php echo esc_attr( $settings['split_bg_color'] ); ?>" class="hsp-color-field" data-default-color="#ffffff">
                </p>
                <p style="margin:0 0 10px;">
                    <label for="hsp-split-tint" style="display:inline-block;min-width:160px;"><?php echo esc_html__( 'Image Side Tint', 'hero-slider-plugin' ); ?></label>
                    <input type="text" id="hsp-split-tint" name="<?php echo $fname('split_tint_color'); ?>" value="<?php echo esc_attr( $settings['split_tint_color'] ); ?>" class="hsp-color-field" data-default-color="#2c6464">
                </p>
                <p style="margin:0;">
                    <label for="hsp-split-alpha" style="display:inline-block;min-width:160px;"><?php echo esc_html__( 'Image Tint Opacity (0–1)', 'hero-slider-plugin' ); ?></label>
                    <input type="number" id="hsp-split-alpha" name="<?php echo $fname('split_tint_alpha'); ?>" value="<?php echo esc_attr( $settings['split_tint_alpha'] ); ?>" min="0" max="1" step="0.05" class="small-text">
                </p>
                <p class="description"><?php echo esc_html__( 'Used only when Split style is selected. Background color fills the text half; tint overlays the image half.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-slider-height"><?php echo esc_html__( 'Default Slider Height (px)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="number" id="hsp-slider-height" name="<?php echo $fname('slider_height'); ?>" value="<?php echo esc_attr( $settings['slider_height'] ); ?>" step="any" class="small-text">
                <p class="description"><?php echo esc_html__( 'Applied to all slides in this group. Text and images scale to fit.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-mobile-slider-height"><?php echo esc_html__( 'Mobile Slider Height (px)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="number" id="hsp-mobile-slider-height" name="<?php echo $fname('mobile_slider_height'); ?>" value="<?php echo esc_attr( $settings['mobile_slider_height'] ); ?>" min="0" step="any" class="small-text">
                <p class="description"><?php echo esc_html__( 'Fixed height on screens under 768px. Leave 0 to use the same height as desktop.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-css-easing"><?php echo esc_html__( 'Animation Curve (CSS Easing)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="text" id="hsp-css-easing" name="<?php echo $fname('css_easing'); ?>" value="<?php echo esc_attr( $settings['css_easing'] ); ?>" class="regular-text" dir="ltr">
                <p class="description"><?php echo esc_html__( 'Supports ease, linear, ease-in, ease-out, ease-in-out or cubic-bezier(...) values.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-pad-x"><?php echo esc_html__( 'Text Side Padding (px)', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <input type="number" id="hsp-pad-x" name="<?php echo $fname('content_padding_x'); ?>" value="<?php echo esc_attr( $settings['content_padding_x'] ); ?>" min="0" step="1" class="small-text">
                <p class="description"><?php echo esc_html__( 'Controls the left/right padding of the slider content container.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Background Overlay (Global)', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:block;margin-bottom:6px;">
                        <input type="checkbox" name="<?php echo $fname('overlay_disable'); ?>" value="yes" <?php checked( $settings['overlay_disable'], 'yes' ); ?>>
                        <?php echo esc_html__( 'Disable overlays (show full background)', 'hero-slider-plugin' ); ?>
                    </label>
                    <label>
                        <span style="display:inline-block;min-width:180px;"> <?php echo esc_html__( 'Overlay Opacity (0–1)', 'hero-slider-plugin' ); ?> </span>
                        <input type="number" step="0.05" min="0" max="1" name="<?php echo $fname('overlay_alpha'); ?>" value="<?php echo esc_attr( $settings['overlay_alpha'] ); ?>" style="width:90px;">
                    </label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'These options apply to all slides. When disabled, both gradient and color tint are hidden. When enabled, the opacity controls the color tint strength globally.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Theme Mode', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="<?php echo $fname('theme_mode'); ?>" value="dark" <?php checked( $settings['theme_mode'], 'dark' ); ?>> <?php echo esc_html__( 'Dark', 'hero-slider-plugin' ); ?></label><br>
                    <label><input type="radio" name="<?php echo $fname('theme_mode'); ?>" value="light" <?php checked( $settings['theme_mode'], 'light' ); ?>> <?php echo esc_html__( 'Light', 'hero-slider-plugin' ); ?></label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'Switch between dark and light palettes for slide backgrounds, texts, and controls.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="hsp-image-size"><?php echo esc_html__( 'Featured Image Size', 'hero-slider-plugin' ); ?></label></th>
            <td>
                <select id="hsp-image-size" name="<?php echo $fname('image_size'); ?>">
                    <?php foreach ( $sizes as $size ) : ?>
                        <option value="<?php echo esc_attr( $size ); ?>" <?php selected( $settings['image_size'], $size ); ?>><?php echo esc_html( $size ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php echo esc_html__( 'Select the image size used in the slider (e.g. full, large, medium_large) to reduce file size when needed.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Button Colors', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:inline-block; margin-inline-end:14px;">
                        <?php echo esc_html__( 'Primary', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('button_color_1'); ?>" value="<?php echo esc_attr( $settings['button_color_1'] ); ?>">
                    </label>
                    <label style="display:inline-block; margin-inline-end:14px;">
                        <?php echo esc_html__( 'Secondary', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('button_color_2'); ?>" value="<?php echo esc_attr( $settings['button_color_2'] ); ?>">
                    </label>
                    <label style="display:inline-block;">
                        <?php echo esc_html__( 'Accent', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('button_color_3'); ?>" value="<?php echo esc_attr( $settings['button_color_3'] ); ?>">
                    </label>
                </fieldset>
                <fieldset style="margin-top:10px;">
                    <label style="display:inline-block;">
                        <?php echo esc_html__( 'Button Text Color', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('button_text_color'); ?>"
                            value="<?php echo esc_attr( $settings['button_text_color'] ); ?>"
                            data-default-color="#ffffff">
                    </label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'Controls the text and icon color inside the button. Default: white.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Text Colors', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:inline-block; margin-inline-end:20px;">
                        <?php echo esc_html__( 'Title Color', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('title_color'); ?>"
                            value="<?php echo esc_attr( $settings['title_color'] ); ?>"
                            data-default-color="#ffffff"
                            data-alpha-enabled="false">
                    </label>
                    <label style="display:inline-block;">
                        <?php echo esc_html__( 'Description Color', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('text_color'); ?>"
                            value="<?php echo esc_attr( $settings['text_color'] ); ?>"
                            data-default-color="#dee4ed"
                            data-alpha-enabled="false">
                    </label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'Default text colors for all slides. Leave empty to use theme defaults. Can be overridden per slide.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Effects Colors', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:inline-block; margin-inline-end:14px;">
                        <?php echo esc_html__( 'Primary', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('effect_color_1'); ?>" value="<?php echo esc_attr( $settings['effect_color_1'] ); ?>">
                    </label>
                    <label style="display:inline-block; margin-inline-end:14px;">
                        <?php echo esc_html__( 'Secondary', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('effect_color_2'); ?>" value="<?php echo esc_attr( $settings['effect_color_2'] ); ?>">
                    </label>
                    <label style="display:inline-block;">
                        <?php echo esc_html__( 'Accent', 'hero-slider-plugin' ); ?>
                        <input type="text" class="hsp-color-field" name="<?php echo $fname('effect_color_3'); ?>" value="<?php echo esc_attr( $settings['effect_color_3'] ); ?>">
                    </label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'These colors drive the animated canvas effects palette.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Use Custom Size Instead', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="<?php echo $fname('use_custom_size'); ?>" value="no" <?php checked( $settings['use_custom_size'], 'no' ); ?> class="hsp-custom-size-toggle"> <?php echo esc_html__( 'No', 'hero-slider-plugin' ); ?></label><br>
                    <label><input type="radio" name="<?php echo $fname('use_custom_size'); ?>" value="yes" <?php checked( $settings['use_custom_size'], 'yes' ); ?> class="hsp-custom-size-toggle"> <?php echo esc_html__( 'Yes', 'hero-slider-plugin' ); ?></label>
                </fieldset>
                <p class="description"><?php echo esc_html__( 'When enabled, the preset size above will be ignored and the dimensions below will be used.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr class="hsp-custom-dimensions-row" <?php echo 'yes' !== $settings['use_custom_size'] ? 'style="opacity:0.5;"' : ''; ?>>
            <th scope="row"><?php echo esc_html__( 'Custom Dimensions (W × H) in pixels', 'hero-slider-plugin' ); ?></th>
            <td>
                <input type="number" name="<?php echo $fname('custom_width'); ?>" value="<?php echo esc_attr( $settings['custom_width'] ); ?>" min="0" step="1" class="small-text" <?php echo 'yes' !== $settings['use_custom_size'] ? 'disabled' : ''; ?>>
                <span>&times;</span>
                <input type="number" name="<?php echo $fname('custom_height'); ?>" value="<?php echo esc_attr( $settings['custom_height'] ); ?>" min="0" step="1" class="small-text" <?php echo 'yes' !== $settings['use_custom_size'] ? 'disabled' : ''; ?>>
                <p class="description"><?php echo esc_html__( 'Leave fields at 0 to cancel the effect. Note: WordPress may not create a new size automatically and will use the closest available size.', 'hero-slider-plugin' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Visual Effect', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset class="hsp-effects-grid">
                    <?php
                    $effect_list = hero_slider_plugin_get_effects_list();
                    foreach ( $effect_list as $key => $effect ) : ?>
                        <label class="hsp-effect-card<?php echo $settings['visual_effect'] === $key ? ' is-active' : ''; ?>">
                            <input type="radio" name="<?php echo $fname('visual_effect'); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $settings['visual_effect'], $key ); ?>>
                            <span class="hsp-effect-icon dashicons <?php echo esc_attr( $effect['icon'] ); ?>"></span>
                            <span class="hsp-effect-name"><?php echo esc_html( $effect['name'] ); ?></span>
                            <span class="hsp-effect-desc"><?php echo esc_html( $effect['desc'] ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__( 'Featured Image on Mobile', 'hero-slider-plugin' ); ?></th>
            <td>
                <fieldset>
                    <label style="display:block;margin-bottom:8px">
                        <input type="radio" name="<?php echo $fname( 'featured_img_mobile' ); ?>" value="show"
                            <?php checked( $settings['featured_img_mobile'], 'show' ); ?>>
                        ✅ <?php echo esc_html__( 'Show on all screens (default)', 'hero-slider-plugin' ); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px">
                        <input type="radio" name="<?php echo $fname( 'featured_img_mobile' ); ?>" value="hide"
                            <?php checked( $settings['featured_img_mobile'], 'hide' ); ?>>
                        🚫 <?php echo esc_html__( 'Hide on mobile — under 768px', 'hero-slider-plugin' ); ?>
                    </label>
                    <label style="display:block;margin-bottom:8px">
                        <input type="radio" name="<?php echo $fname( 'featured_img_mobile' ); ?>" value="hide_xs"
                            <?php checked( $settings['featured_img_mobile'], 'hide_xs' ); ?>>
                        📵 <?php echo esc_html__( 'Hide on very small screens — under 576px', 'hero-slider-plugin' ); ?>
                    </label>
                </fieldset>
                <p class="description">
                    <?php echo esc_html__( 'Control featured image visibility on small screens for all slides in this group. You can override this per slide on the slide edit screen.', 'hero-slider-plugin' ); ?>
                </p>
            </td>
        </tr>
    </table>
    <table class="form-table hsp-portal-settings-wrap" role="presentation">
        <?php hero_slider_portal_render_group_fields( $settings, $field_prefix ); ?>
    </table>
    <?php
}
