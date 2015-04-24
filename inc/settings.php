<?php
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Testimonials"
        add_submenu_page(
            'edit.php?post_type=testimonials', 
            'settings', 
            'Settings', 
            'manage_options', 
            'testimonials_settings', array( $this, 'create_testimonials_settings_page' ));
    }

    /**
     * Options page callback
     */
    public function create_testimonials_settings_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <?php echo screen_icon(); ?>
            <h2>Testimonials Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );   
                do_settings_sections( 'testimonials_settings' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'testimonials_settings' // Page
        );  

        add_settings_field(
            'auto_slide', // ID
            'Auto Slide', // Title 
            array( $this, 'auto_slide_callback' ), // Callback
            'testimonials_settings', // Page
            'setting_section_id' // Section           
        );  
        
        add_settings_field(
            'speed', // ID
            'Speed', // Title 
            array( $this, 'speed_callback' ), // Callback
            'testimonials_settings', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'controls', 
            'Controls', 
            array( $this, 'controls_callback' ), 
            'testimonials_settings', 
            'setting_section_id'
        );    
        add_settings_field(
            'pager', 
            'Pager', 
            array( $this, 'pager_callback' ), 
            'testimonials_settings', 
            'setting_section_id'
        );  
        add_settings_field(
            'hoverPause', 
            'Hover Pause', 
            array( $this, 'hoverPause_callback' ), 
            'testimonials_settings', 
            'setting_section_id'
        );    
        
     
        
        
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();        
        if( isset( $input['auto_slide'] ) )
            $new_input['auto_slide'] = sanitize_text_field( $input['auto_slide'] );
        else
            $new_input['auto_slide'] = 'false';

        if( isset( $input['speed'] ) )
            $new_input['speed'] = sanitize_text_field( $input['speed'] );
            
        if( isset( $input['controls'] ) )
            $new_input['controls'] = sanitize_text_field( $input['controls'] );
        else
            $new_input['controls'] = 'false';   
        
        if( isset( $input['pager'] ) )
            $new_input['pager'] = sanitize_text_field( $input['pager'] );
        else
            $new_input['pager'] = 'false';  
            
        if( isset( $input['hoverPause'] ) )
            $new_input['hoverPause'] = sanitize_text_field( $input['hoverPause'] );
        else
            $new_input['hoverPause'] = 'false';           
         

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function auto_slide_callback()
    {
     
        $html = '<input type="checkbox" id="auto_slide" name="my_option_name[auto_slide]" value="true" '.checked( $this->options['auto_slide'], 'true', false).'   />';
        $html .= '<p class="description">Animate automatically, true or false</p>';
         
        echo $html;
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function speed_callback()
    {
        
        $speed = (isset($this->options['speed']) and (!empty($this->options['speed']))) ? esc_attr( $this->options['speed']) : "500";
        $html = '<input type="text" id="speed" name="my_option_name[speed]" value="'.$speed.'"  />';
        $html .= '<p class="description">Slide transition duration (in ms) (default:500)</p>';
         
        echo $html;
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function controls_callback()
    {
        $html = '<input type="checkbox" id="controls" name="my_option_name[controls]" value="true" '.checked( $this->options['controls'], 'true', false).'   />';
        $html .= '<p class="description">If true, "Next" / "Prev" controls will be added</p>';
        
        echo $html;
    }
    
     /** 
     * Get the settings option array and print one of its values
     */
    public function pager_callback()
    {
        $html = '<input type="checkbox" id="pager" name="my_option_name[pager]" value="true" '.checked( $this->options['pager'], 'true', false).'   />';
        $html .= '<p class="description">If true, a pager will be added</p>';
        
        echo $html;
    }
    
     public function hoverPause_callback()
    {
        $html = '<input type="checkbox" id="hoverPause" name="my_option_name[hoverPause]" value="true" '.checked( $this->options['hoverPause'], 'true', false).'   />';
        $html .= '<p class="description">Start Slider on a Random Slide</p>';
        
        echo $html;
    }
     public function auto_callback($arg)
    {
        $auto = isset($this->options['auto']) ? $this->options['auto'] : "true";
        $html = '<input type="checkbox" id="auto" name="my_option_name[auto]" value="true" '.checked($auto , 'true', false).'   />';
        $html .= '<p class="description">Slides will automatically transition</p>';
        
        echo $html;
    }

    public function startslide_callback()
    {
        
        $startslide = (isset($this->options['startslide']) and (!empty($this->options['startslide']))) ? esc_attr( $this->options['startslide']) : "1";
        $html = '<input type="text" id="startslide" name="my_option_name[startslide]" value="'.$startslide.'"  />';
        $html .= '<p class="description">Slide transition duration</p>';
         
        echo $html;
    }

   

}

if( is_admin() )
    $my_settings_page = new MySettingsPage();