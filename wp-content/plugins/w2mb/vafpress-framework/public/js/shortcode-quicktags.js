(function($){

	if( typeof QTags !== 'undefined' )
	{
		var qt_cb = function(name){
			return function(){
				tinyMCE.execCommand(name + '_cmd');
			}
		}
		for (var i = 0; i < vp_w2mb_sg.length; i++) {
			QTags.addButton( vp_w2mb_sg[i].name, 'Vafpress', qt_cb(vp_w2mb_sg[i].name), '', '', vp_w2mb_sg[i].button_title, 999999 );
		}
	}

})(jQuery);