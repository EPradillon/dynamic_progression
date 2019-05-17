<?php
/**
 * HookManager Class | HookManager.php
 * 
 * This class make the link between the code produced in the plugin and wordpress.
 * 
 * @package CF7-Dynamic-progression
 * @subpackage CF7-Dynamic-progression Object Class
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class HookManager { 

    /**
     * This function add an action or filter hook depending on the type passed as first argument
     */
    public function addHook( string $type, string $name, object $object, $method, int $priority = 10, int $accepted_args = 1 ) {
        $type = strtolower( $type );

        if ( 'filter' === $type ) {
            $this->addFilter( $name, $object, $method, $priority, $accepted_args );
        } else if ( 'action' === $type ) {
            $this->addAction( $name, $object, $method, $priority, $accepted_args );
        } else {
            return new WP_Error( '1', 'No proper hook type defined in Dynamic-Progression plugin.' );
        }
    }

    /**
     * Add filter hook to WordPress 
     * 
     * WordPress offers filter hooks to allow 
     * plugins to modify various types of internal data at runtime.
     * 
     * @see add_filter() https://developer.wordpress.org/reference/functions/add_filter/
     */
    private function addFilter( $name, $object, $method, $priority, $accepted_args ) {
        add_filter( $name, [ $object, $method ],  $priority, $accepted_args  );
    }

    /**
     * Add action hook to WordPress
     * 
     * Actions are the hooks that the WordPress core launches at specific points during execution.
     * 
     * @see add_action() https://developer.wordpress.org/reference/functions/add_action/
     */
    public function addAction( $name, $object, $method, $priority, $accepted_args ) {

        add_action( $name, [ $object, $method ],  $priority, $accepted_args  );
    }
}