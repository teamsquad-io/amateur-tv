( function ( $, config ) {
	$( document ).ready(
		function () {
			init();
		}
	);

	function init() {
		$( '.atv-cams-list' ).each(
			function ( index, element ) {
				if ( parseInt( $( element ).attr( 'data-refresh' ) ) > 0 ) {
						setInterval(
							function () {
								refreshBlock( $( element ) );
							},
							$( element ).attr( 'data-refresh' ) * 60 * 1000
						);
				}
			}
		);
	}

	function refreshBlock( element ) {
		var attributes = JSON.parse( element.attr( 'data-attributes' ) );
		$.ajax(
			{
				url: config.url,
				method: 'POST',
				data: {
					attributes: attributes,
				},
				success: function ( data ) {
					$( element ).empty().html( data.html );
				},
			}
		);
	}
} )( jQuery, atvfconfig );
