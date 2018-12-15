window.cptuiShortcodeBuilder = {};
	( function( window, $, that ) {

		// Constructor.
		that.init = function() {
			that.cache();
			that.bindEvents();
		}

		// Cache all the things.
		that.cache = function() {
			that.$c = {
				window: $( window ),
				body: $( 'body' ),
				postTypeSelect: $('#cptui_posttype'),
				shortcodeSelect: $('#cptui_shortcode'),
				shortcode: '',
				posttype: '',
				wdsShortcodeButtonModal: $('#scb-modal')
			};
		}

		// Combine all events.
		that.bindEvents = function() {

			that.$c.body.on( 'shortcode_button:open', function() {

				// Check if Site Origin's Page Builder dialog exists and if it is overriding the body overflow
				if ( $( '.so-panels-dialog' ).length || that.$c.body.css( 'overflow' ) == 'hidden' ) {
					that.$c.body.css( 'overflow', 'visible' );
				}

				that.metaShowHide( $('#none') );
				$('#shortcode_cptui').trigger('reset');
				$( '#cptui-form input[type=checkbox]' ).prop( 'checked', false );
				$( '#cptui-form input[type=radio]' ).prop( 'checked', false );
			} );

            that.metaShowHide( $('#none') );

			// show/hide meta boxes based on select otpion
			$('select[name=cptui_shortcode]').change(function() {
				$( '#cptui-form input[type=checkbox]' ).prop( 'checked', false );
				$( '#cptui-form input[type=radio]' ).prop( 'checked', false );
                var shortcodeSelect = that.$c.shortcodeSelect.val();
				that.$c.shortcode = shortcodeSelect;

                var showThese = $('#' + shortcodeSelect + '_repeat' );
                that.metaShowHide(showThese);

				if ( 'list_shortcode' === shortcodeSelect ) {
					$( '#list_shortcode_0_list_type1' ).prop( 'checked', true );
					var postTypeData = $('#list_shortcode_0_attached_post').data('search');
					postTypeData.posttype = that.$c.posttype;
					$('#list_shortcode_0_attached_post').data('search', postTypeData);
				}
				// swap post type in data-search value
				if ( 'default_shortcode' === shortcodeSelect ) {
					var postTypeData = $('#default_shortcode_0_attached_post').data('search');
					postTypeData.posttype = that.$c.posttype;
					$('#default_shortcode_0_attached_post').data('search', postTypeData);
				}

				if ( 'slider_shortcode' === shortcodeSelect ) {
					var postTypeData = $('#slider_shortcode_0_attached_post').data('search');
					postTypeData.posttype = that.$c.posttype;
					$('#slider_shortcode_0_attached_post').data('search', postTypeData);
				}

				if ( typeof cptui.taxonomy[that.$c.posttype] !== 'undefined' ) {

					$('ul.cmb2-checkbox-list').html('');
					var taxul = $('#'+ that.$c.shortcode +'_repeat ul.cmb2-checkbox-list');
					var tax = [];
                    $.each( cptui.taxonomy[that.$c.posttype], function(key, value) {

						taxul.append('<h3>' + key + '</h3>');
						$.each( cptui.taxonomy[that.$c.posttype][key], function(key, value) {
							taxul.append( $('<li><input type="checkbox" class="cmb2-option" name="' + that.$c.shortcode  + '[0][taxonomy][taxonomy-'+ value.taxonomy +'][]" id="' + that.$c.shortcode  + '_0_taxonomy_' + value.slug + '" value="' + value.slug + '"><label for="' + that.$c.shortcode  + '_0_taxonomy_' + value.slug + '">' + value.name + '</label></li>') );
						});
                    });
                }
			});

			$('.cmb2-id-post-cards-shortcode-0-card-layout').change(function() {
				if( $( '#post_cards_shortcode_0_card_layout1' ).is(":checked") || $( '#post_cards_shortcode_0_card_layout3' ).is(":checked") ) {
					$( '.cmb2-id-post-cards-shortcode-0-show-title, .cmb2-id-post-cards-shortcode-0-show-categories, .cmb2-id-post-cards-shortcode-0-show-excerpt' ).hide();
				} else {
					$( '.cmb2-id-post-cards-shortcode-0-show-title, .cmb2-id-post-cards-shortcode-0-show-categories, .cmb2-id-post-cards-shortcode-0-show-excerpt' ).show();
				}
				if( $( '#post_cards_shortcode_0_card_layout2' ).is(":checked") ) {
					$( '.cmb2-id-post-cards-shortcode-0-show-title, .cmb2-id-post-cards-shortcode-0-show-categories, .cmb2-id-post-cards-shortcode-0-show-excerpt' ).show();
				}
			});

            // Change shortcode select based on post type option
            $('select[name=cptui_posttype]').change(function() {
                that.metaShowHide( $('#none') );
                $('#cptui_shortcode option').remove();

				$('#cptui_shortcode').append( $('<option value="none">None</option>') );
				$.each( cptui.shortcodes.all, function(key, value) {
					$('#cptui_shortcode').append( $('<option value="'+ key +'">'+ value +'</option>') );
				});

				that.$c.posttype = this.value;
				var post_type = this.value;

                if ( typeof cptui.shortcodes[post_type] !== 'undefined' ) {
                    $.each( cptui.shortcodes[post_type], function(key, value) {
                         $('#cptui_shortcode')
                             .append($('<option></option>')
                             .attr('value',key)
                             .text(value));
                    });
				}

				if ( post_type === 'page' ) {
					$( '.cmb2-id-grid-shortcode-0-show-categories, .cmb2-id-post-cards-shortcode-0-show-categories' ).hide();
                    $( '.cmb2-id-grid-shortcode-0-show-categories' ).hide();
				} else {
					$( '.cmb2-id-grid-shortcode-0-show-categories, .cmb2-id-post-cards-shortcode-0-show-categories' ).show();
                    $( '.cmb2-id-grid-shortcode-0-show-categories' ).show();
				}
			});

			// Hide shortcode amount field group by default.
			$('.cmb2-id-featured-plus-shortcode-0-amount').hide();

			$('input[name="featured_plus_shortcode[0][featured_plus_type]"]').change(function() {

				var featuredPlusCheckbox = $( 'input[name="featured_plus_shortcode[0][featured_plus_type]"]:checked' ).val()

				if ( featuredPlusCheckbox === 'top' ) {
					$('.cmb2-id-featured-plus-shortcode-0-amount').show();
				} else {
					$('.cmb2-id-featured-plus-shortcode-0-amount').hide();
				}
			});
        }

        // Function to handle which items should be showing/hiding
        that.metaShowHide = function(showem) {
            var hideThese = $('#shortcode_cptui .cmb-repeatable-group').not(showem);
            showem.slideDown('slow');
            hideThese.hide();
        }



		// Engage!
		$( that.init );

	})( window, jQuery, window.cptuiShortcodeBuilder );
