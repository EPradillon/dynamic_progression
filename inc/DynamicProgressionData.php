<?php
/**
 * DynamicProgressionData | DynamicProgressionData.php
 * 
 * Allow shortchode from this plugin to fetch datas stored by flamingo/cf7
 * 
 * @package CF7-Dynamic-progression
 * @subpackage CF7-Dynamic-progression Object Class
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DynamicProgressionData {
    /**
     * @var object Instantiation of the wpdb class in in /wp-includes/wp-db.php.
     */
    private $wordpressDb;

    /**
     * @var object Instantiation of the wp_get_current_user_object().
     */
    private $user;

    /**
     * @var string prefix add by the Database plugin (flamingo) to meta_value.
     */
    private $pluginBddPrefix;

    // /**
    //  * @var object Instantiation of the wp_get_current_post_object().
    //  */
    // private $post;

    /**
     * @param object WordPress global object variable.
     * 
     * @param object Current user object.
     */
    function __construct( $wpdb, $user ) {
        $this->wordpressDb = $wpdb;
        $this->user = $user;
        $this->pluginBddPrefix = '_field_';
        //$this->post = $post;
    }

    /**
     * Retrieve the last answered value to a given question for current user.
     * 
     * Fetch the last post ID whose content include : the user_email / the current contact Form 7 post ID / The actual Title of the post.
     * (global $post allow the identification of the post in "The Loop".)
     * 
     * The metaKey's value returned is identified by this last post ID and the param (question = metayKey ).
     * 
     * @param string value(s) passed through shortcode or via form-tag conception.
     * 
     * @return string Meta_value fetched from Wordpress BDD <= corresponding to param's meta_key.
     */
    public function dynamicProgGetResults( string $metaKey) {
        //data from the current post in <<The Loop>>.
        global $post;

        $postId = $this->wordpressDb->get_var( $this->wordpressDb->prepare(
            "SELECT * FROM {$this->wordpressDb->prefix}posts
            WHERE post_content LIKE %s AND post_content LIKE %s AND post_content LIKE %s
            ORDER BY ID DESC
            LIMIT 1 ", 
            [   '%' . $this->wordpressDb->esc_like  ($this->user->user_email)   . '%', 
                '%' . $this->wordpressDb->esc_like  ( ( string )$post->ID )     . '%', 
                '%' . $this->wordpressDb->esc_like  ( $post->title )            . '%',]
        ) );

// TODO : i think something won't work about special character or upercase.
        // The shortcode att(s) have been prefixed by the bdd plugin.
        $metaKey = $this->pluginBddPrefix . sanitize_text_field( $metaKey );
        /**
         * "SELECT meta_value
         *  FROM {$this->wordpressDb->prefix}postmeta 
         *  WHERE postId = $postId && meta_key = '$metaKey'"
         */
        if ( is_user_logged_in() ) {
            return get_post_meta( $postId, $metaKey, true); 
        } else {
            return null; 
        }
    }
}