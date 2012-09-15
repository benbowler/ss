(function() {
	tinymce.create('tinymce.plugins.YourScoped', {
		init : function(ed, url) {
			ed.addButton('yourscoped', {
				title : 'yourscoped.scoped',
				image : url+'/scoped-tiny.png',
				onclick : function() {
					idPattern = /(^[A-Za-z0-9]*[A-Za-z0-9][A-Za-z0-9]*$)/;
					var scopedId = prompt("Scoped ID", "Enter your unique Scoped ID");
					var m = idPattern.exec(scopedId);
					if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[scoped id="'+m[1]+'"][/scoped]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Scoped Shortcode",
				author : 'Paul Arterburn',
				authorurl : 'http://goscoped.com/',
				infourl : 'http://goscoped.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('yourscoped', tinymce.plugins.YourScoped);
})();