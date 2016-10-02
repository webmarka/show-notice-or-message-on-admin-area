
jQuery(document).ready(function($){
	//jQuery('.sna-dismiss-notice button.notice-dismiss').click(function(){
	$( '.notice.is-dismissible' ).on('click', '.notice-dismiss', function ( event ) {
			event.preventDefault();
		var nfe = $(this).parents('div:first');
		var nfn = nfe.attr('data-notice-name');
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			async: false,
			data: { action: 'sna_dismiss_notice_ajax', notice: nfn }
		});
		//console.log(nfn);
		//nfe.fadeOut();
	});
});
