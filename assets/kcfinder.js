/**
 * KCFinderInputWidget.
 * This is the Jquery plugin used by KCFinderInputWidget.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
(function($) {

	// KCFinderInputWidget constructor
	var KCFinderInputWidget = function($element, options){
		this.options = options;
		this.$button = $element;
		this.$thumbs = $(this.options.thumbsSelector);

		$element.on('click', $.proxy(function(e){
			e.preventDefault();
			this.open();
		}, this));
	};

	// KCFinderInputWidget default options
	KCFinderInputWidget.DEFAULTS = {
		multiple: false,
		browseOptions: {},
		thumbsDir: '.thumbs'
	};

	/**
	 * Add thumb
	 * @param string url
	 */
	KCFinderInputWidget.prototype.addThumb = function(url) {
		// empty list
		if (!this.options.multiple) {
			this.$thumbs.text('');
		}

		// thumb url
		var uploadURL = this.options.uploadURL,
		    thumbsUrl = uploadURL+'/'+this.options.thumbsDir,
		    thumbUrl = url;
		if (url.search(thumbsUrl)==-1) {
			thumbUrl = url.replace(uploadURL, thumbsUrl);
		}

		// add thumb
		var tpl = this.options.thumbTemplate;
		var thumb = tpl
			.replace('{thumbSrc}', thumbUrl)
			.replace('{inputName}', this.options.inputName)
			.replace('{inputValue}', url);
		this.$thumbs.append(thumb);
	};

	/**
	 * Open KCFinderInputWidget
	 */
	KCFinderInputWidget.prototype.open = function(){
		var kthis = this;
		window.KCFinder = {
			callBack: function(url) {
                console.log('callBack : '+url);
				kthis.addThumb(url);
				window.KCFinder = null;
			},
			callBackMultiple: function(files) {
                console.log('callBackMultiple : '+files);
				window.KCFinder = null;
                for (var i; i < files.length; i++) {
					kthis.addThumb(files[i]);
                }
			}
		};

		window.open(kcfAssetPath+'/browse.php?'+$.param(this.options.browseOptions),
			'kcfinder', 'status=0, toolbar=0, location=0, menubar=0, ' +
			'directories=0, resizable=1, scrollbars=0, width=800, height=600'
		);
	};

	/**
	 * JQuery plugin
	 */
	$.fn.KCFinderInputWidget = function(option) {
		return this.each(function () {
			var $this = $(this);
			var data = $this.data('KCFinderInputWidget');
			var options = $.extend({}, KCFinderInputWidget.DEFAULTS, $this.data(), typeof option == 'object' && option);

			if (!data)
				$this.data('KCFinderInputWidget', (data = new KCFinderInputWidget($this, options)));
			if (typeof option == 'string')
				data[option]();
		});
	};
	
}(jQuery));