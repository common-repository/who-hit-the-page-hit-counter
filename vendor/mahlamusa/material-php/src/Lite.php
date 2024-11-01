<?php
namespace Mahlamusa\Material;

/**
 * This class is a PHP Helper class for creating material components based on 
 * Google's Material Design Lite framework available at https://getmdl.io
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Lindeni Mahlalela <himself@lindeni.co.za>
 * @package Lite
 * @link    https://github.com/mahlamusa/material-php
 */
class Lite{

    private static $instance = null;

    public static function get_instance() {
        if ( self::$instance == null ) {
            self::$instance == new self();
        }

        return self::$instance;
    }

    public function __construc() {
        if ( defined( 'ABSPATH' ) ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        }
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public static function enqueue_styles() {
        wp_register_style( 
            'mdl-admin-css', 
            'https://code.getmdl.io/1.3.0/material.indigo-pink.min.css'
        );
        wp_register_style( 
            'mdl-admin-icons', 
            'https://fonts.googleapis.com/icon?family=Material+Icons'
        );
        wp_enqueue_style( 'mdl-admin-css' );
        wp_enqueue_style( 'mdl-admin-icons' );            
    }

    /**
     * Output the styles link html tag
     */
    public static function styles( ) { ?>
        <link type="text/css" rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css" />
        <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" /><?php
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public static function enqueue_scripts() {
        wp_register_script( 
            'mdl-admin-js', 
            'https://code.getmdl.io/1.3.0/material.min.js',
            null, null, true
        );
    
        wp_enqueue_script( 'mdl-admin-js' );
    }

    /**
     * Output the script html tag
     */
    public static function scripts( ) { ?>
        <script type="text/javascript" src="https://code.getmdl.io/1.3.0/material.min.js"></script><?php
    }

    public static function header( $title, $tabs = "" ){
        $header = '<header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
            <span class="mdl-layout-title">' . __( $title , 'wp-host-panel' )  . '</span>
        </div>' . PHP_EOL;

        if ( $tabs != "" ) {
            $header .= $this->tab_bar( $tabs );
        }

        $header .= '</header>' . PHP_EOL;
    }

    public static function tab_bar( $tabs ){
        $tab_bar = '<div class="mdl-layout__tab-bar mdl-js-ripple-effect">' . PHP_EOL;
        if ( is_array( $tabs ) && count( $tabs) > 0 ) {
            foreach ( $tabs as $tab ) {
                $tab_bar .= $this->tab( $tab );
            }
        } 
        $tab_bar .= '</div>' . PHP_EOL;      

        return $tab_bar;
    }

    public static function tab( $args ){
        $args = self::parse_args( $args, array(
            'id' => '',
            'active' => false,
            'title' => 'Tab',
            'icon' => ''
        ));
        $active = $args[ 'active' ] ? 'is-active': '';

        $icon = empty( $args['icon'] ) ? '<i class="material-icons">' . $args[ 'icon' ] . '</i>': ''; 

        return '<a href="#' . $args['id'] . '" class="mdl-tabs__tab ' . $active .'">' . $args['title'] . '</a>' . PHP_EOL;
    }

    public static function tabs( $tabs, $vertical = false ){
        $vertical_class = $vertical ? 'vertical-mdl-tabs': '';
        $content = '<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect ' . $vertical_class .'">' .
        ( $vertical ? '<div class="mdl-grid mdl-grid--no-spacing">
        <div class="mdl-cell mdl-cell--3-col">': '' ) .
        '<div class="mdl-tabs__tab-bar">';
        foreach ( $tabs as $tab ) {
            $active     = isset( $tab['active'] ) && $tab['active'] ? 'is-active' : '';
            $icon       = ! empty( $tab['icon'] ) ? '<i class="material-icons">' . $tab[ 'icon' ] . '</i>': '';
            $content    .= '<a href="#' .$tab['id'] .'" class="mdl-tabs__tab ' .$active .'">' . $icon . ' ' . $tab['title'] .'</a>';
        }
        $content .= '</div><!-- /tab-bar-->'; //close tabs header
        $content .= $vertical ? '</div><div class="mdl-cell mdl-cell--9-col">': '';
        foreach ( $tabs as $tab ) {
            $active  = isset( $tab['active'] ) && $tab['active'] ? 'is-active' : '';
            $content .= '<div class="mdl-tabs__panel ' . $active . '" id="' . $tab['id'] . '">';
            $content .= $tab['content'];
            $content .= '</div>';
        }
        $content .= '</div>' . ( $vertical ? '</div></div>': '') . '<!-- /tabs-->'; //close tabs container
        return $content;
    }

    public static function section( $args ){
        $args = self::parse_args( $args, array(
            'id' => 'section',
            'active' => false,
            'content' => 'Demo Tab Content'
        ));
        $active = $args['active'] ? 'is-active' : ""; 
        
        $section = '<section class="mdl-layout__tab-panel ' . $active . '" id="scroll-tab-' . $args['id'] . '">' . PHP_EOL;
        $section .= $content . PHP_EOL;
        $section .= '</section>' . PHP_EOL;

        return $section;
    }

    public static function open( $what, $id = '', $class = '' ) {
        return '<' . $what . ' id="'. $id . '" class="' . $class . '">' . PHP_EOL;
    }

    public static function close( $what, $number = 1 ) {
        $close = '';
        for( $i = 0; $i < $number; $i++) {
            $close .= '</' . $what .'>' . PHP_EOL;
        }
        return $close;
    }

    public static function grid(){
        return '<div class="mdl-grid">';
    }

    public static function col( $size ){
        return '<div class="mdl-cell mdl-cell--' . $size .'-col">' . PHP_EOL;
    }

    public static function text_field( $args ){
        $args = self::parse_args( $args, array(
            'type' => 'text',
            'name' => '',
            'id' => '',
            'label' => 'Text Field',
            'class' => '',
            'required' => false,
            'error' => 'Required field',
            'value' => '',
        ));
        $pattern = isset( $atts['pattern'] ) ? ' pattern="' . $args['pattern'] . '"' : "";
        return '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label ' . $args['class'] . '">
            <input class="mdl-textfield__input" 
                type="' . $args['type'] . '" 
                name="' . ( $args['name'] ? $args['name'] : $args['id'] ) . '" 
                id="' . $args['id'] . '" 
                aria-required="' . $args['required'] . '"
                value="' . $args['value'] . '"' .
                $pattern . 
            '>
            <label class="mdl-textfield__label" for="' . $args['id'] . '">' . $args['label'] . '</label>
            <span class="mdl-textfield__error">' . $args['error'] . '</span>
        </div>' . PHP_EOL;
    }

    public static function textarea( $args = array() ) {
        $args = self::parse_args($args, array(
            'id' => 'textarea',
            'name' => '',
            'label' => __( 'Type here', 'qreated' ),
            'rows' => 3,
            'value' => '',
            'attributes' => array()
        ));

        return '<div class="mdl-textfield mdl-js-textfield">
            <textarea class="mdl-textfield__input" rows="' . $args['rows'] . '" id="' . $args[ 'id'] .'" name="' . ( $args[ 'name' ] ? $args['name'] : $args['id'] ). '">' . $args['value'] .'</textarea>
            <label class="mdl-textfield__label" for="' . $args['id'] . '">' . $args['label'] . '</label>
        </div>' . PHP_EOL;
    }

    public static function range( $args ) {
        $args = self::parse_args( $args, array(
            'before'        => '',
            'id'    => 'range',
            'min'   => 0,
            'max'   => 100,
            'step'  => 1,                
            'class' => '',
            'value' => 0,                
            'after'         => '',
            'attributes'    => ''
        ) );
        return $args[ 'before' ] . PHP_EOL . 
            '<input class="mdl-slider mdl-js-slider ' . $args[ 'class' ] . '"
                type="range"
                id="' . $args[ 'id' ] . '"
                min="' . $args[ 'min' ] . '"
                max="' . $args[ 'max' ] . '"
                step="' . $args[ 'step' ] . '"
                value="' . $args[ 'value' ] . '"
                tabindex="0"' .
                $args[ 'attributes' ] .
            '>' .
        $args[ 'after' ] . PHP_EOL;
    }

    public static function progress( $args ) {
        $args = self::parse_args( $args, array(
            'id'    => 'progress',
            'progress'  => 0,
            'buffer'    => 0,
            'class'     => '',
            'before'    => '<p><br />',
            'after'     => '<br /></p>'
        ) );
        return $args[ 'before' ] .
        '<div id="' . $args[ 'id' ] .'" class="mdl-progress mdl-js-progress ' . $args[ 'class' ] .'"></div>
        <script>
          document.querySelector("#' . $args[ 'id' ] .'").addEventListener( "mdl-componentupgraded", function() {
            this.MaterialProgress.setProgress(' . $args[ 'progress' ] .');
            this.MaterialProgress.setBuffer(' . $args[ 'buffer' ] .');
          });
        </script>' . 
        $args[ 'after' ] . PHP_EOL;
    }

    public static function tooltip( $args ) {
        $args = self::parse_args( $args, array(
            'for'       => 'tooltip',
            'content'   => 'Tooltip text',
            'image'     => array()
        ));
        $src    = isset( $args[ 'image'][ 'src'] ) ? $args[ 'image'][ 'src']: '';
        $width  = isset( $args[ 'image'][ 'width' ] ) ? $args[ 'image'][ 'width' ]: 20;
        $height = isset( $args[ 'image'][ 'height' ] ) ? $args[ 'image'][ 'height' ]: 20;
        $image = ! empty( $args[ 'image' ] ) ? '<img src="' . $src . '" width="' . $width . '" height="' . $height . '"> ': '';
        return '<div class="mdl-tooltip" data-mdl-for="' . $args[ 'for' ] .'">' . $image . $args[ 'content' ] .'</div>';
    }

    public static function chip( $args ) {
        $args = self::parse_args( $args, array(
            'class' => '',
            'content'   => 'Chip content',
            'icon'      => 'cancel',
            'image'     => array()
        ));
        if( ! empty( $args['image'] ) ) {
            $src    = isset( $args[ 'image' ][ 'src' ] ) ? $args[ 'image' ][ 'src' ]: '';
            $width  = isset( $args[ 'image' ][ 'width' ] ) ? $args[ 'image' ][ 'width' ]: 20;
            $height = isset( $args[ 'image' ][ 'height' ] ) ? $args[ 'image' ][ 'height' ]: 20;
            $image  = ! empty( $args['image'] ) ? '<img class="mdl-chip__contact" src="' . $src .'" width="' . $args[ 'width' ] . '" height="' .$args[ 'height' ] . '" ></img>': '';
        }            
        return '<span class="mdl-chip mdl-chip--contact mdl-chip--deletable">
            ' . $img . '
            <span class="mdl-chip__text">' .$args[ 'content' ] . '</span>
            <a href="#" id="' .$args[ 'dismiss_id' ] . ' class="mdl-chip__action"><i class="material-icons">' . $args[ 'icon' ] . '</i></a>
        </span>' . PHP_EOL;
    }

    public static function card( $args ) {
        $args = self::parse_args( $args, array(
            'id'        => 'cardid',
            'title'     => '',
            'shadow'    => '2dp',
            'class'     => '',
            'shareable' => false,
            'actions'   => array(),
            'content'   => '',
            'before'    => '',
            'after'     => '',
            
        ) );

        if ( $args[ 'shareable'] ) {
            $share = '<button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
            <i class="material-icons">share</i>
          </button>' . PHP_EOL;
        }else{
            $share = '';
        }

        $actions = '';
        if ( ! empty( $args[ 'actions'] ) ) {
            
            foreach( $args[ 'actions' ] as $action ) {
                $action = self::parse_args( $action, array(
                    'id' => 'action',
                    'link' => '#',
                    'class' => '',
                    'label' => '',
                    'icon' => '',
                    'download'  => false,
                ));
                $download = $action['download'] ? 'download': '';
                $actions .= '<a id="' .$action['id'] . '" href="' . $action[ 'link' ] . '" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect ' . $action['class'] .'" ' . $download . '>' . $action[ 'label' ] . '</a>';
            }
        }

        $title = $args['title'] != '' ? '<div class="mdl-card__title"><h2 class="mdl-card__title-text">' . $args[ 'title' ] . '</h2></div>': '';

        return $args[ 'before' ]  . PHP_EOL . 
        '<div id="' . $args[ 'id' ] . '" class="mdl-card mdl-shadow--' . $args[ 'shadow' ] . ' ' . $args[ 'class' ] . '">' .
            $title .
            '<div class="mdl-card__supporting-text">' . $args[ 'content' ] . '</div>' .
            ( !empty( $actions ) ? '<div class="mdl-card__actions mdl-card--border">' . $actions . '</div>' : '' ) .
            ( $share != '' ? '<div class="mdl-card__menu">' . $share . '</div>': '' ) .
        '</div>'  . PHP_EOL . $args[ 'after' ] . PHP_EOL;
    }

    public static function switch_button( $args ){
        $args =  self::parse_args( $args, array(
            'id'        => 'switch',
            'checked'   => 0,
            'label'     => '',
            'before'    => '', 
            'after'     => ''
        ));
        $is_checked = $args['checked'] == 1 ? 'is-checked' : "";
        $checked = $args['checked'] == 1 ? 'checked' : "";

        return $args[ 'before' ] . '<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect ' . $is_checked .'" for="'. $args['id'] . '">
            <input type="checkbox" id="'. $args['id'] . '" class="mdl-switch__input" '. $checked . '>
            <span class="mdl-switch__label">'. $args['label'] . '</span>
        </label>' . $args[ 'after' ]  . PHP_EOL;
    }

    public static function toggle( $args = array() ) {
        $args = self::parse_args( $args, array(
            'type'      => 'checkbox',
            'id'        => 'checkbox',
            'name'      => '',
            'label'     => 'Check',
            'checked'   => false,
            'class'     => '',
            'value'     => 1,
            'attributes'=> array()
        ));

        $type = $args[ 'type' ];

        $class      = $type == 'checkbox' ? 'mdl-checkbox__input': 'mdl-radio__button';
        $checked    = $args['checked'] ? 'checked': '';
        $name       = $args['name'] ? $args['name'] : $args['id'];

        $attributes = self::attributes( $args['attributes'] );

        return '<label class="mdl-'. $type .' mdl-js-'. $type .' mdl-js-ripple-effect ' . $args['class' ] . '" for="' . $args['id'] . '">
            <input type="' . $args['type'] . '" id="' . $args['id'] . '" class="' . $class .'" value="' .$args['value'] .'" name="' . $name .'" ' . $checked .' >
            <span class="mdl-'. $type .'__label">'. $args['label'] .'</span>
        </label>' . PHP_EOL;
    }

    public static function select( $args = array() ) {

        $args = self::parse_args( $args, array(
            'type'      => 'text',
            'id'        => 'select',
            'name'      => '',
            'label'     => 'Check',
            'checked'   => false,
            'label'     => 'Choose',
            'class'     => '',
            'empty_value' => null,
            'value'     => 1,
            'options'   => array()
        ));

        $options = '';
        if ( ! empty( $args[ 'options'] ) ) {
            foreach( $args[ 'options'] as $value => $label ) {
                $options .= '<li class="mdl-menu__item" data-val="' . $value . '">' . $label . '</li>' . PHP_EOL;
            }
        }

        return '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label getmdl-select getmdl-select__fix-height">
            <input type="text" value="' . $args['value'] .'" class="mdl-textfield__input" id="' . $args[ 'id' ] .'" readonly>
            <input type="hidden" value="' . $args['value'] .'" name="' . $args[ 'id' ] .'">
            <i class="mdl-icon-toggle__label material-icons">keyboard_arrow_down</i>
            <label for="' . $args[ 'id' ] .'" class="mdl-textfield__label">' . $args[ 'label' ] . '</label>
            <ul for="' . $args[ 'id' ] .'" class="mdl-menu mdl-menu--bottom-left mdl-js-menu">' .
                ( $args['empty_value'] != null ? '<li class="mdl-menu__item" data-val="">' . $args['empty_value'] . '</li>': '' ) .
                
                $options .
            '</ul>
        </div>';
    }

    public static function button( $args = array() ) {
        $args = self::parse_args( $args, array(
            'type' => 'submit',
            'label' => 'Submit',                
            'attributes' => array(
                'name' => 'button',
                'id' => 'button',
                'class' => '',
            )
        ));

        $attributes = self::attributes( $args[ 'attributes' ] );

        return '<button type="' .$args[ 'type'] . '" 
            class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" ' . $attributes .'
            >' . $args[ 'label' ] .'</button>';
    }

    public static function hidden( $args = array()) {
        $args = self::parse_args( $args, array(
            'name' => 'hidden',
            'id' => 'hidden',
            'value' => 'hidden'
        ));

        return '<input type="hidden" name="' . $args[ 'name' ] . '" value="' . $args[ 'value' ] . '" id="' . $args['id'] .'" />'; 
    }

    public static function list_view( $args ) {
        $args = wp_pars_args( $args, array(
            'before' => '',
            'after' => '',
            'class' => '',
        ));

        $list = $args['before'] . '<ul class="demo-list-three mdl-list ' . $args['class'] . '">';

        foreach( $args['items'] as $item ) {
            $list .= self::list_item( $item ) . PHP_EOL;
        }

        $list .= '</ul>' . $args['after'] . PHP_EOL;
    }

    public static function list_item( $item ) {

        $item = self::parse_args( $item, array(
            'leading'  => 'person',
            'trailing' => '',
            'title'    => 'Default item title',
            'link'     => '#',
            'subtitle' => '',
        ));

        $list_item = '<li class="mdl-list__item mdl-list__item--three-line">
        <span class="mdl-list__item-primary-content">';
        $list_item .= $item['leading']  != '' ? '<i class="material-icons mdl-list__item-avatar">' .$item['leading'] . '</i>': '';
        $list_item .= $item['title']    != '' ? '<span>' . $item['title'] .'</span>': '';
        $list_item .= $item['subtitle'] != '' ? '<span class="mdl-list__item-text-body">' . $item['subtitle'] .'</span>':'';
        $list_item .= '</span>';
        $list_item .= ( $item['trailing'] != '' ? '<span class="mdl-list__item-secondary-content">
            <a class="mdl-list__item-secondary-action" href="' . $item['link'] .'"><i class="material-icons">' . $item['trailing'] .'</i></a>
        </span>': '' );            
        $list_item .= '</li>';

        return $list_item;
    }

    

    public static function attributes( $attributes ) {
        $atts = '';
        foreach ( $attributes as $key => $value ) {
            $atts = $atts . ' ' . $key . '="' .$value . '"';
        }

        return $atts;
    }

    /**
     * Based on wp_parse_args
     *
     * @param array $args Args to parse.
     * @param string $defaults Defaults.
     */
    public static function parse_args( $args, $defaults = '' ) {
        if ( is_object( $args ) ) {
            $parsed_args = get_object_vars( $args );
        } elseif ( is_array( $args ) ) {
            $parsed_args =& $args;
        } else {
           self::parse_str( $args, $parsed_args );
        }
     
        if ( is_array( $defaults ) ) {
            return array_merge( $defaults, $parsed_args );
        }
        return $parsed_args;
    }

    /**
     * Based on wp_parse_str
     *
     * @param string $string String to parse.
     * @param string $array Array to parse.
     */
    public static function parse_str( $string, &$array ) {
        parse_str( $string, $array );     
        $array = $array;
    }
}
