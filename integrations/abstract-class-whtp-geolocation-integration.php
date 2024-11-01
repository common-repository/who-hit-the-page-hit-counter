<?php
/**
 * Contains the abastract integration class.
 *
 * @author Lindeni Mahlalela
 * @package Who_Hit_The_Page_Hit_Counter/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Geolocation integration class.
 *
 * @version 1.4.11
 * @since   1.4.11
 */
abstract class WHTP_Geolocation_Integration {

    /**
     * Integration ID
     *
     * @var string
     * @version 1.4.11
     * @since   1.4.11
     */
    public $id;

    /**
     * Database file path
     *
     * @var string
     * @version 1.4.11
     * @since   1.4.11
     */
    public $database;

    /**
     * The url to the remote api server
     *
     * @var string
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public $api_url;

    /**
     * Construct the class
     *
     * @param string $id The optional id of the integration.
     * @version 1.4.11
     * @since   1.4.11
     */
    public function __construct( $id = 'ipinfo' ) {
        $this->id = $id;
        $this->fields = $this->get_settings_fields();
    }

    /**
     * Get the API Key
     *
     * @return string
     * @version 1.4.11
     * @since   1.4.11
     */
    public function get_api_key() {
        return get_option( "whtp_{ $this->id }_api_key" );
    }

    /**
     * Save the API key
     *
     * @param string $key The api key to be saved
     * @return void
     * @version 1.4.11
     * @since   1.4.11
     */
    public function save_api_key( $key ) {
        update_option( "whtp_{ $this->id }_api_key", sanitize_text_field( $key ) );
    }

    /**
     * Get the list of settings fields for the integration
     *
     * @return  array
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function get_settings_fields() {
        return array(
            "whtp_{$this->id}_api_key" => array(
                'label' => __( 'API Key', 'whtp' ),
                'value' => get_option( "whtp_{$this->id}_api_key" ),
            )
        );
    }

    public function output_settings_fields() {
        foreach ( $this->fields as $id => $attributes ) {
            $attributes['id'] = $id;
            switch ( $attributes['type'] ) {
                case 'text':
                case 'number':
                case 'email':
                case 'password':
                    echo Input( $attributes );
                    break;
                case 'textarea':
                    echo Text_Area( $attributes );
                    break;
                case 'select':
                    echo Select( $attributes );
                    break;
                case 'switch':
                    echo SwitchButton( $attributes );
                    break;
                case 'button':
                case 'submit':                
                    echo Button( $attributes );
                    break;
                case 'hidden':
                    echo Hidden( $attributes );
                default:
                    do_action( 'whtp_output_field_' . $attributes['type'], $attributes );
                    break;
            }
        }
    }
}