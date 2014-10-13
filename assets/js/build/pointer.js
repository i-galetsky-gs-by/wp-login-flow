jQuery(function ( $ ) {
	$( '#menu-users' ).pointer(
		{
			content: '<h3>' + wplf_pointer.h3 + '</h3><p>' + wplf_pointer.p + '</p>',
			position: {
				edge: 'left',
				align: 'center'
			},
			close: function () {
				$.post(
					ajaxurl, {
						pointer: 'wplf_activate_pointer',
						action: 'dismiss-wp-pointer'
					}
				);
			}
		}
	).pointer( 'open' );
});