(function($) {

	$('#autocomplete').autocomplete({
	  	source: function( request, response ) {
	      $.ajax({
	        dataType : 'json',
	        type     : 'GET',
	        url      : 'wp-admin/admin-ajax.php',
	        data     : {
	           action: 'getTags',
	           term  : request.term,
	        },
	        success: function( data ) {
	           $( 'input.suggest-user' ).removeClass( 'ui-autocomplete-loading' ); 
	           response(data);
	        },
	        error: function( data ) {            	
	           $( 'input.suggest-user' ).removeClass( 'ui-autocomplete-loading' );  
	        }
	      });
	  	},
		minLength: 3,
		select: function( event, ui ) {
		    var term = ui.item.value;

		    if( '' != term && 'null' != term ){
		    	$('.tagsdiv .newtag').val(term);
		        $('.tagadd').trigger('click');	

		    }
		    
		}
	});
})(jQuery);