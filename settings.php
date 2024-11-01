<?php

// Merge options from database with default options
$reserveringen_settings = wp_parse_args(get_option('reserveringen_settings'), array(
    'text_tag_connecttoken' => '-',
    'text_general_baseurl' => "https://dashboard.thenexttable.com",
    'number_general_restaurantid' => -1,
    'checkbox_tag_enable' => 1,
    'checkbox_tag_enableanimation' => 1,
    'checkbox_tag_showonleft' => 0,
    'color_tag_text' => '#000000',
    'color_tag_button' => '#151515',
    'color_tag_buttontext' => '#FFFFFF',
    'number_tag_offset' => '30',
    'number_tag_minwidth' => '800',
    'color_widget_button' => '#00BECC',
    'color_widget_buttontext' => '#FFFFFF'
));
