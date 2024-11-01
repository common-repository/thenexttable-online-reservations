<?php

function reserveringen_display_menu()
{
    ?>
    <div id="reserveringen-menu" class="wrap">
        <h2><?php _e('TheNextTable Settings') ?></h2>

        <form method="post" action="options.php">
            <?php
            settings_fields('reserveringen_settingsgroup');
            do_settings_sections(__FILE__);
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function reserveringen_add_menu()
{
    add_menu_page(__('TheNextTable Settings'), __('TheNextTable'), 'manage_options', __FILE__, 'reserveringen_display_menu');
}

function reserveringen_add_settings()
{
    register_setting(
        'reserveringen_settingsgroup',
        'reserveringen_settings',
        'reserveringen_settings_sanitize'
    );


    /*
	add_settings_section('reserveringen_settings_general', __('General'),
		'reserveringen_settings_general_info', __FILE__);

	add_settings_field('text_general_baseurl', __('Base URL'),
		'reserveringen_text_callback', __FILE__,
		'reserveringen_settings_general', array('id' => 'text_general_baseurl'));
    add_settings_field('number_general_restaurantid', __('Restaurant ID'),
        'reserveringen_number_callback', __FILE__,
        'reserveringen_settings_general', array('id' => 'number_general_restaurantid', 'readonly' => ''));
	*/


    add_settings_section('reserveringen_settings_dashboard', __('Dashboard'),
        'reserveringen_settings_dashboard_info', __FILE__);

    add_settings_field('text_tag_connecttoken', __('Connect token'),
        'reserveringen_connecttoken_callback', __FILE__,
        'reserveringen_settings_dashboard', array('id' => 'text_tag_connecttoken'));


    add_settings_section('reserveringen_settings_tag', __('Floating tag'),
        'reserveringen_settings_tag_info', __FILE__);

    add_settings_field('checkbox_tag_enable', __('Enable'),
        'reserveringen_checkbox_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'checkbox_tag_enable'));

    add_settings_field('checkbox_tag_enableanimation', __('Enable Swing Animation'),
        'reserveringen_checkbox_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'checkbox_tag_enableanimation'));

    add_settings_field('checkbox_tag_showonleft', __('Left Side'),
        'reserveringen_checkbox_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'checkbox_tag_showonleft'));

    add_settings_field('color_tag_text', __('Text Color'),
        'reserveringen_colorpicker_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'color_tag_text'));

    add_settings_field('color_tag_button', __('Button Color'),
        'reserveringen_colorpicker_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'color_tag_button'));

    add_settings_field('color_tag_buttontext', __('Text Color - Button'),
        'reserveringen_colorpicker_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'color_tag_buttontext'));

    add_settings_field('number_tag_offset', __('Position'),
        'reserveringen_number_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'number_tag_offset', 'unit' => 'px'));

    add_settings_field('number_tag_minwidth', __('Minimal Window Width'),
        'reserveringen_number_callback', __FILE__,
        'reserveringen_settings_tag', array('id' => 'number_tag_minwidth', 'unit' => 'px'));


    add_settings_section('reserveringen_settings_widget', __('Widget'),
        'reserveringen_settings_widget_info', __FILE__);

    add_settings_field('color_widget_button', __('Button Color'),
        'reserveringen_colorpicker_callback', __FILE__,
        'reserveringen_settings_widget', array('id' => 'color_widget_button'));

    add_settings_field('color_widget_buttontext', __('Text Color - Button'),
        'reserveringen_colorpicker_callback', __FILE__,
        'reserveringen_settings_widget', array('id' => 'color_widget_buttontext'));
}

/* callback functions */

function reserveringen_connecttoken_callback($args)
{
    global $reserveringen_settings;

    printf('<input type="text" id="%s" name="reserveringen_settings[%1$s]" value="%s" class="regular-text"/>',
        $args['id'], $reserveringen_settings[$args['id']]);
}

function reserveringen_checkbox_callback($args)
{
    global $reserveringen_settings;

    printf('<input type="checkbox" id="%s" name="reserveringen_settings[%1$s]" value="1" %s />',
        $args['id'], checked(1 == $reserveringen_settings[$args['id']], true, false));
}

function reserveringen_colorpicker_callback($args)
{
    global $reserveringen_settings;

    printf('<input type="text" class="rs-colorpicker" id="%s" name="reserveringen_settings[%1$s]" value="%s" />',
        $args['id'], $reserveringen_settings[$args['id']]);
}

function reserveringen_number_callback($args)
{
    global $reserveringen_settings;

    printf('<input type="number" id="%s" name="reserveringen_settings[%1$s]" value="%s" /> %s',
        $args['id'], $reserveringen_settings[$args['id']], $args['unit']);
}

function reserveringen_text_callback($args)
{
    global $reserveringen_settings;

    printf('<input type="text" id="%s" name="reserveringen_settings[%1$s]" value="%s" />',
        $args['id'], $reserveringen_settings[$args['id']]);
}

/* info functions */

//function reserveringen_settings_general_info() {
//	_e("Settings for both the floating tag and the widget");
//}

function reserveringen_settings_dashboard_info()
{
    _e("Connect to the dashboard");
}

function reserveringen_settings_tag_info()
{
    _e("Settings for the floating tag");
}

function reserveringen_settings_widget_info()
{
    _e("Settings for the widget");
}

/* Sanitizer */

function reserveringen_settings_sanitize($input)
{
    global $reserveringen_settings;

    if (!is_array($input) || empty($input) || $input === false) {
        return array();
    }

    $sanitized = array();
    $valid_keys = array_keys($reserveringen_settings);

    foreach ($valid_keys as $key) {
        $prefix = explode('_', $key)[0];

        if ($prefix === 'checkbox') {
            if (isset($input[$key]) && $input[$key] == 1) {
                $sanitized[$key] = 1;
            } else {
                $sanitized[$key] = 0;
            }
        } else if ($prefix === 'color') {
            if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $input[$key])) {
                $sanitized[$key] = $input[$key];
            }
        } else if ($prefix === 'number') {
            if (is_numeric($input[$key])) {
                $sanitized[$key] = intval($input[$key]);
            }
        } else if ($prefix === 'text') {
            if ($input[$key] != '') {
                $sanitized[$key] = htmlspecialchars(strip_tags($input[$key]));
            }
        }
    }

    if (!empty($sanitized['text_tag_connecttoken'])) {
        $token = base64_decode($sanitized['text_tag_connecttoken']);
        if ($token !== FALSE && ($id_idx = strpos($token, '-ID-')) !== FALSE) {
            // search for the -ID- part
            $rid = substr($token, $id_idx + 4); // 4 == strlen('-ID-')
            if (is_numeric($rid)) {
                $sanitized['number_general_restaurantid'] = intval($rid);
            }
        }
    }

    unset($input);
    return $sanitized;
}

function reserveringen_register_settings_scripts()
{
    wp_enqueue_style('wp-color-picker');
    wp_register_script('settingspage',
        plugins_url('/js/settingspage.js', __FILE__),
        array('wp-color-picker'));
    wp_enqueue_script('settingspage');
}

if (is_admin()) {
    add_action('admin_enqueue_scripts', 'reserveringen_register_settings_scripts');
    add_action('admin_menu', 'reserveringen_add_menu');
    add_action('admin_init', 'reserveringen_add_settings');
}

?>