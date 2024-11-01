<?php
/*
 * Plugin Name: TheNextTable
 * Description: Setting up online reservations with TheNextTable is easy!
 * Version: 1.0.7
 * Author: TheNextTable
 * Author URI: https://www.thenexttable.com
 */

/**
 * Import $reserveringen_settings variable from database
 * and merge it with default settings. It's important that
 * this page is loaded first!
 */
include 'settings.php';

/**
 * Add action hooks for the menu in the admin panel.
 */
include 'settingspage.php';

/**
 * Class for the widget.
 */
class Reserveringen_Widget extends WP_Widget
{
    static $active = false;

    /**
     * Pass widget information to parent constructor.
     */
    function __construct()
    {
        parent::__construct(
            'thenexttable-plugin', // Base ID
            __('TheNextTable', 'thenexttable-plugin'), // Name
            array('description' => __('Setting up online reservations with TheNextTable is easy!', 'thenexttable-plugin'),) // Args
        );

        if(is_active_widget(false, false, $this->id_base)) self::$active = true;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        global $reserveringen_settings;

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];

            $w_bt = $reserveringen_settings['color_widget_button'];
            $w_bt_txt = $reserveringen_settings['color_widget_buttontext'];

            ?>

            <form method="GET" onsubmit="popupwindow(this); return false;" class="tnt-widget">
                <p>
                    <label for="datum">Datum:</label>
                    <input type="hidden" class="datumVeld" name="date" value="<?php echo date('d-m-Y') ?>"/>
                </p>

                <div class="datum"></div>
                <p><label for="aantal_personen">Aantal personen:</label></p>

                <p><input type="number" class="aantal_personen" name="aantal_personen" value="2" min="1"/></p>

                <p>
                    <button class="reserveerButton" style="background: <?php echo $w_bt ?>; color: <?php echo $w_bt_txt ?>;">BOEK NU
                    </button>
                </p>
                <p><a href="https://thenexttable.com" target="_blank"><img src="<?php echo plugins_url('images/orange-small.png', __FILE__) ?>" class="logo-img" alt="TheNextTable"/></a></p>
            </form>

            <?php
        } else {
            echo __('Error: Widget has not been configured properly.', 'text_domain');
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('New title', 'text_domain');
        //$base_url = ! empty( $instance['base_url'] ) ? $instance['base_url'] : __( 'http://example.com', 'text_domain' );
        //$restaurant_id = ! empty( $instance['restaurant_id'] ) ? $instance['restaurant_id'] : "1";
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <?php /* OLD
		<p>
		<label for="<?php echo $this->get_field_id( 'base_url' ); ?>"><?php _e( 'Base url:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'base_url' ); ?>" name="<?php echo $this->get_field_name( 'base_url' ); ?>" type="text" value="<?php echo esc_attr( $base_url ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'restaurant_id' ); ?>"><?php _e( 'Restaurant ID:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'restaurant_id' ); ?>" name="<?php echo $this->get_field_name( 'restaurant_id' ); ?>" type="text" value="<?php echo esc_attr( $restaurant_id ); ?>">
		</p>
		*/ ?>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();

        $instance['title'] = empty($new_instance['title']) ? '' : strip_tags($new_instance['title']);
//        $instance['base_url'] = empty($new_instance['base_url']) ? '' : strip_tags($new_instance['base_url']);
//        $instance['restaurant_id'] = empty($new_instance['restaurant_id']) ? '' : strip_tags($new_instance['restaurant_id']);

        return $instance;
    }

} // End of widget class.

function reserveringen_register_widget()
{
    register_widget('Reserveringen_Widget');
}

function reserveringen_register_scripts()
{
    global $reserveringen_settings;
    $active = false;

    // Javascript for tag
    if ($reserveringen_settings['checkbox_tag_enable'] == 1 && !preg_match('/msie [1-7]\./i', $_SERVER['HTTP_USER_AGENT'])) {
        $active = true;
        
        wp_register_script('tag', plugins_url('/js/tag.js', __FILE__), array('jquery'), false, true);

        // Pass the plugin URL to the script.
        wp_localize_script('tag', 'WPVARS', array('pluginURL' => plugins_url('', __FILE__), 'settings' => $reserveringen_settings));
        wp_enqueue_script('tag');
    }

    // Javascript for widget
    if(Reserveringen_Widget::$active) {
        $active = true;
        
        wp_register_script('jquery-ui-datepicker-nl', plugins_url('/js/jq-datepicker-nl.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), false, true);
        wp_enqueue_script('jquery-ui-datepicker-nl');
    }
    
    if(!$active) return;

    // General javascript
    wp_register_script('reserveringen', plugins_url('/js/reserveringen.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), false, true);

    wp_localize_script('reserveringen', 'WPVARS', array('pluginURL' => plugins_url('', __FILE__), 'settings' => $reserveringen_settings));
    wp_enqueue_script('reserveringen');

    // CSS
    wp_register_style('reserveringen', plugins_url('/styles/reserveringen.css', __FILE__));
    wp_register_style('reserveringen-fonts', add_query_arg('family', 'Dancing+Script', "http://fonts.googleapis.com/css"), array(), null);

    wp_enqueue_style('reserveringen');
    wp_enqueue_style('reserveringen-fonts');
}

add_action('widgets_init', 'reserveringen_register_widget');
add_action('wp_enqueue_scripts', 'reserveringen_register_scripts');

?>