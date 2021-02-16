/* JS for custom fields */
( function() {

    var fieldsData = {};
    
    var bbFormOpen = false;

    const manageSelectPostFields = function( mutationsList, observer ) {
        for( const mutation of mutationsList ) {
            if ( mutation.type === 'childList' ) {
                let bbFormOpenNow = document.querySelector( 'form.fl-builder-module-settings' );
                
                // If form has been opened...
                if ( ! bbFormOpen && bbFormOpenNow ) {
                    
                    bbFormOpen = true;
                    
                    // Initialise all the select post fields.
                    var selectPostfields = document.querySelectorAll( '.st-bb-select-post-field' );
                    selectPostfields.forEach( function( selectPostfield ) {
                        
                        // Get and store the post type field name.
                        let postTypeFieldName = selectPostfield.dataset.posttypefield;
                        fieldsData[ selectPostfield.getAttribute( 'name' ) ] = {
                            'postTypeFieldName' : postTypeFieldName,
                            'currentPostType' : null,
                            'optionData' : {}
                        }

                        // Set a listener on field.
                        selectPostfield.addEventListener( 'focus', function() {
                            setFieldOptions( this );
                        } );

                    } );
                }
                
            }
        }
    }
    
    // Create a MutationObserver to watch for introduction of fields to the DOM.
    observer = new MutationObserver( manageSelectPostFields );

    const body = document.querySelector( 'body' );
    observer.observe( body, {
        childList: true,
        subtree: true
    } );

    // Set the field options.
    function setFieldOptions( field ) {
        
        // Get the post type if available, defaulting to post otherwise.
        let postType = 'post',
            fieldName = field.getAttribute( 'name' );
            fieldData = fieldsData[ fieldName ];

        // Get post type.
        let postTypeField = document.querySelector( 'select[name="' + fieldData.postTypeFieldName + '"]' );
        if ( postTypeField ) {
            postType = postTypeField.value;
        }

        // Update if post type has changed.
        if ( postType != fieldData.currentPostType ) {

            fieldData.currentPostType = postType;

            // Get the options data either from already stored or using ajax.
            let options = fieldData.optionData[ postType ];
            if ( 'undefined' == typeof options ) {

                let request = new XMLHttpRequest();
                request.open( 'POST', stBbCustomFields.ajaxurl );
                request.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
                let data = 'action=st_bb_fetch_post_options&post_type=' + postType + '&nonce=' + stBbCustomFields.nonce;
                request.send( data );

                request.onreadystatechange = function() {
                    if( this.readyState === 4 && this.status === 200 ) {
                        let response = this.responseText;
                        if ( response ) {
                            options = JSON.parse( response );
                            setFieldOptionsHtml( field, options );
                            fieldsData[ fieldName ].optionData[ postType ] = options;
                        }
                    }
                }

                

            } else {
                setFieldOptionsHtml( field, options );
            }

        }

    }

    // Set the select options html.
    function setFieldOptionsHtml( field, options ) {
        let optionsHtml = '';
        if ( 'object' == typeof options ) {
            for ( const index in options ) {
                for ( const postID in options[ index ] ) {
                    let newOption = '<option value="' + postID + '">' + options[ index ][ postID ] + '</option>';
                    optionsHtml += newOption;
                }
                
            }
        }

        if ( optionsHtml ) {
            field.innerHTML = optionsHtml;
        }
    }   

} )();