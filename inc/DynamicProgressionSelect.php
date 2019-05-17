<?php
/**
 * DynamicProgressionSelect Class | DynamicProgressionSelect.php
 * 
 * This class generate additional tag in the Contact Form 7 menu : Progression select
 * 
 * The plugin tends to preserve the way Contact Form 7 produce his tag.
 * Name of the tag and associated funtctions have been change compared to regular CF7 select. 
 * 
 * @package CF7-Dynamic-progression
 * @subpackage CF7-Dynamic-progression Object Class
 * @since 1.0.0
 */

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class DynamicProgressionSelect {

	/**
     * @var object Instantiation of HookManager with the function to add action or filter the way wordpress want to.
     */
	private $hookManager;

	/**
     * @var object Instantiation of a class (from Dynamic-Progression) fetching value from the bdd 
     */
	private $cf7dp_datas;


	/**
	 * __contruct method 
	 * 
	 * @param object Instance of the class registering hook to WordPress
	 * @param object Instance of the class fetching value in the WordPress database 
	 */	
	function __construct( $hookManager, $cf7dp_datas ) {
		$this->hookManager = $hookManager;		
		$this->cf7dp_datas = $cf7dp_datas;
	}

	/**
	 * Initiate the new form tag functionalities : Progression select
	 */
	public function addProgressionSelect() {
		// add new tag to the Contact Form 7 menu
		$this->hookManager->addHook( 'action', 'wpcf7_init', $this, 'wpcf7_add_form_tag_dynamic_select', 10, 0 );
		$this->hookManager->addHook( 'filter', 'wpcf7_validate_dynamic_select', $this, 'wpcf7_select_validation_dynamic_select', 10, 2 );
		$this->hookManager->addHook( 'action', 'wpcf7_admin_init', $this, 'wpcf7_add_tag_generator_dynamic_select', 25, 0 );
	}

	/**
	 * Restituate (from the wordpress db) the last reccorded value link to the $tagname key
	 * 
	 * @param string Identification of the input (created in via CF7 plugin)
	 * 
	 * @return string Null if no value have been saved for this input located in this "post" by current user
	 * 
	 * @see dynamicProgGetResults() DynamicProgressionData.php
	 */
	private function getLastFormValue( $tagName ) {

		return $this->cf7dp_datas->dynamicProgGetResults( $tagName );
	}


	/**
	 * @see Contact Form 7 plugin
	 */
	public function wpcf7_add_form_tag_dynamic_select()
	{
		wpcf7_add_form_tag(		
			array( 'dynamic_select', 'dynamic_select*' ),
			[$this, 'wpcf7_dynamic_select_form_tag_handler'], 
			array(
				'name-attr' => true,
				'selectable-values' => true,
			)
		);
	}

	/**
	 * @see Contact Form 7 plugin
	 */
	public function wpcf7_dynamic_select_form_tag_handler( $tag ) {
		$tag = new WPCF7_FormTag( $tag );
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error )
		{
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();	
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$multiple = $tag->has_option( 'multiple' );
		//$include_blank = $tag->has_option( 'include_blank' );
		$first_as_label = $tag->has_option( 'first_as_label' );
		//Etienne
		$dynamic_value = $tag->has_option( 'dynamic_value' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );

			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}

		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}

		$values = $tag->values;
		$labels = $tag->labels;

		$default_choice = $tag->get_default_option( null, array(
			'multiple' => $multiple,
			'shifted' => $include_blank,
		) );



//Dynamic-Progression addition to the "CF7-select-input" occure here.
		if ( is_user_logged_in() )	{
			$result = $this->getLastFormValue( $tag->name );
			
			if ( $result ) {
				//add a separator with no value between the fetched data and the regular options.
				array_unshift( $labels, '---' );
				array_unshift( $values, '' );
				//The fetched data is set as the first position in the select's option.
				array_unshift( $labels, $result );
				array_unshift( $values, $result );
			}
		}		
//end Dynamic-Progression 	



		elseif ( $first_as_label )
		{
			$values[0] = '';
		}

		$html = '';
		$hangover = wpcf7_get_hangover( $tag->name );


		foreach ( $values as $key => $value ) {
			if ( $hangover ) {
				$selected = in_array( $value, (array) $hangover, true );
			} else {
				$selected = in_array( $value, (array) $default_choice, true );
			}

			$item_atts = array(
				'value' => $value,
				'selected' => $selected ? 'selected' : '',
			);


			$item_atts = wpcf7_format_atts( $item_atts );

			$label = isset( $labels[$key] ) ? $labels[$key] : $value;

			$html .= sprintf( '<option %1$s>%2$s</option>',
				$item_atts, esc_html( $label ) );
		}

		if ( $multiple ) {
			$atts['multiple'] = 'multiple';
		}

		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
			sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

		return $html;
	}

	/* Validation filter */
	// in init add_filter( 'wpcf7_validate_dynamic_select', 'wpcf7_select_validation_dynamic_select', 10, 2 );

	public function wpcf7_select_validation_dynamic_select( $result, $tag ) {
		$name = $tag->name;

		if ( isset( $_POST[$name] )
		and is_array( $_POST[$name] ) ) {
			foreach ( $_POST[$name] as $key => $value ) {
				if ( '' === $value ) {
					unset( $_POST[$name][$key] );
				}
			}
		}

		$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

		if ( $tag->is_required() and $empty ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/* Tag generator */
	// in starting init add_action( 'wpcf7_admin_init', [$this, 'wpcf7_add_tag_generator_dynamic_select'], 25, 0 );

	public function wpcf7_add_tag_generator_dynamic_select()
	{
		if ( ! class_exists( 'WPCF7_TagGenerator' ) )
		{
			return;
		}
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'dynamic_select', __( 'Progression select', 'contact-form-7' ),
			[$this, 'cf7dp_tag_generator_dynamic_select'] );
	}

	public function cf7dp_tag_generator_dynamic_select( $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		//to name the inserted box
		$type = $args['id'];
		$description = __( "Generate a form-tag for a drop-down menu. Can include the previous answer if user logged via shortcode", 'contact-form-7' );

		$desc_link = wpcf7_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7' ), __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'contact-form-7' ) ); ?></span></label><br />
		<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selections', 'contact-form-7' ) ); ?></label><br />
		<label><input type="checkbox" name="dynamic_value" class="option" /> <?php echo esc_html( __( 'Insert a dynamic value as the first option', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php

	}
}