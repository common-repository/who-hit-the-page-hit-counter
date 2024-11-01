<?php
/**
 * Who Hit The Page Admin Notices
 *
 * @description adds all shortcodes
 * @package Who_Hit_The_Page_Hit_Counter
 * @subpackage Includes
 *
 * @version 1.4.14.2
 * @since   1.4.14.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements admin notices.
 *
 * @version 1.4.14.2
 * @since   1.4.14.2
 */
class WHTP_Admin_Notices {
    /**
     * Registered Admin Notices
     *
     * @var array
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    protected $notices = array();

    /**
     * Construct the class
     *
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function __construct() {

    }

    /**
     * Initialize hooks
     *
     * @return void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function init_hooks() {
        add_action( 'admin_notices', array( $this, 'output_notices' ) );
    }

    /**
     * Add a new notice
     *
     * @param string $message The message to be displayed.
     * @param string $type The type of notice to display. Options: error, warning, info, success.
     * @param bool   $is_dismissable Whether the notice is dismissable or not.
     * @return array
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function add( $type, $message, $is_dismissable = true ) {
        $this->notices[] = array(
            'message'         => $message,
            'type'            => $type,
            'is_dismissable' => $is_dismissable
        );

        return $this->notices;
    }

    /**
     * Output all admin notices.
     *
     * @return  void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function output_notices() {
        foreach( $this->get_notices() as $notice ) {
            $this->{$notice['type']}( $notice['message'], $notice['is_dismissable'] );
        }
    }

    /**
     * Get all registered notices.
     *
     * @return  void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public function get_notices() {
        return apply_filters( 'whtp_admin_notices', $this->notices );
    }

    /**
     * Output error notice.
     *
     * @param string  $message The message to output.
     * @param boolean $is_dismissable Whether message is dismissable or not.
     * @return void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public static function error( $message, $is_dismissable = true ) {
        $dismissable = $is_dismissable ? 'is-dismissable' : '';
        ?>
        <div class="notice notice-error <?php echo esc_attr( $dismissable ); ?>">
            <p><?php echo wp_kses_post( $message ); ?></p>
        </div><?php
    }

    /**
     * Output warning notice.
     *
     * @param string  $message The message to output.
     * @param boolean $is_dismissable Whether message is dismissable or not.
     * @return void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public static function warning( $message, $is_dismissable = true ) {
        $dismissable = $is_dismissable ? 'is-dismissable' : '';
        ?>
        <div class="notice notice-warning <?php echo esc_attr( $dismissable ); ?>">
            <p><?php echo wp_kses_post( $message ); ?></p>
        </div>
        <?php
    }

    /**
     * Output success notice.
     *
     * @param string  $message The message to output.
     * @param boolean $is_dismissable Whether message is dismissable or not.
     * @return void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public static function success( $message, $is_dismissable = true ) {
        $dismissable = $is_dismissable ? 'is-dismissable' : '';
        ?>
        <div class="notice notice-success <?php echo esc_attr( $dismissable ); ?>">
            <p><?php echo wp_kses_post( $message ); ?></p>
        </div>
        <?php
    }

    /**
     * Output info notice.
     *
     * @param string  $message The message to output.
     * @param boolean $is_dismissable Whether message is dismissable or not.
     * @return void
     * @version 1.4.14.2
     * @since   1.4.14.2
     */
    public static function info( $message, $is_dismissable = true ) {
        $dismissable = $is_dismissable ? 'is-dismissable' : '';
        ?>
        <div class="notice notice-info <?php echo esc_attr( $dismissable ); ?>">
            <p><?php echo wp_kses_post( $message ); ?></p>
        </div>
        <?php
    }
}
