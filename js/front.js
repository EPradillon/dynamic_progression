/**
 * Ensure for WordPress that jQuery runs in no conflict mode and in strict mode.
 */
(function( $ ) {
	'use strict';
	
	jQuery( document ).ready( function() {
		clearCF7HiddenInput();
		acceptanceStatus();
		$( 'input:checkbox', '.wpcf7-dynamic_acceptance' ).on( 'click', function() {acceptanceStatus();} );
	});
	
    /**
	 * ( Early 2019 ) I tryied by mysefl to fix an ( known by author) issue with the plugin Conditional-form-group.
	 * If this any conditional group is supposed to be hidden, every input's value or check/select status inside it get cleared.
	 * Occure the moment before submitting the form.
	 */
	function clearCF7HiddenInput() {
		$(':input[class="wpcf7-form-control wpcf7-submit"]').on('click', function() {
			// Check each Conditional-form-group status.
			$( 'div[data-class="wpcf7cf_group"]' ).each( function() {
				if ( $( this ).css( 'display' ) == 'none' && $( this ).hasClass( 'wpcf7cf-hidden' ) ) {
					$( 'input', this ).not( ':button, :submit, :reset' )
					.val( '' )          
					.prop( 'checked', false )
					.prop( 'selected', false );
				}
			});
		});
	}

	/**
	 * Unless checked or optional Dynamic_Acceptance disable the possibility to send the form.
	 */
	function acceptanceStatus() {
		$( '.wpcf7-dynamic_acceptance' ).each( function() {
			let $container = $( this ); 
			let $input = $( 'input:checkbox', $container ); // Target the input in the dynamic_acceptance group.

			if ( ! $container.hasClass( 'optional' ) ) { // If acceptance is mandatory
				if ( $container.hasClass( 'invert' ) && $input.is( ':checked' ) // If input is check and souldn't.
				|| ! $container.hasClass( 'invert' ) && ! $input.is( ':checked' ) ) { // Or if input isn't check and should.

					$( '.wpcf7-submit' ).prop( 'disabled', true ); // Disable submit

					$( ".wpcf7-submit" ).hover( // Add color modification for prerequirement when hovering submit
						function() {
							$( $container ).css("color", "#9b2d44");
						}, function() {
							$( $container ).css("color", "black");
						}
					);
				} else { // If everything is "in order"
					$('.wpcf7-submit').prop( 'disabled', false ); // Able submit
					$( ".wpcf7-submit" ).hover( // remove the color modification for prerequirement
						function() {
							$( $container ).css("color", "black");
						});
				}
			}
		});
	}
})(jQuery);
