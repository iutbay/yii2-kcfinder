/**
 * KCFinderInputWidget.
 * This is the Jquery plugin used by KCFinderInputWidget.
 * @author Kevin LEVRON <kevin.levron@gmail.com>
 */
(function($) {

	/**
	 * Constructor
	 */
	var KCFinderInputWidget = function($element, options) {
		this.options = options;
		this.$button = $element;
		this.$thumbs = $(this.options.thumbsSelector);
		
		// sortable
		this.$thumbs.sortable({
			//handle: '.header',
			containment: 'parent',
			placeholder: 'placeholder',
			connectWith: '.sortable'
		});

		// remove handler
		this.$thumbs.on('click', '.remove span', function() {
			$(this).closest('li').remove();
		});

		$element.on('click', $.proxy(function(e) {
			e.preventDefault();
			this.open();
		}, this));
	};

	/**
	 * Default options
	 */
	KCFinderInputWidget.DEFAULTS = {
		iframe: true,
		multiple: false,
		browseOptions: {},
		thumbsDir: '.thumbs'
	};

	/**
	 * Add thumb
	 * @param string url
	 */
	KCFinderInputWidget.prototype.clearThumbs = function() {
		// empty list
		this.$thumbs.text('');
	};

	/**
	 * Add thumb
	 * @param string url
	 */
	KCFinderInputWidget.prototype.addThumb = function(url) {
		// empty list
		if (!this.options.multiple) {
			this.clearThumbs();
		}

		// thumb url
		var uploadURL = this.options.uploadURL,
			thumbsUrl = uploadURL + '/' + this.options.thumbsDir,
			thumbUrl = url;
		if (url.search(thumbsUrl) == -1) {
			thumbUrl = url.replace(uploadURL, thumbsUrl);
		}
		
		// replace %
		url = url.replace('%25', '%');

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
	KCFinderInputWidget.prototype.open = function() {
		if (this.options.iframe) {
			var $iframeModal = $('#' + this.options.iframeModalId);
		}

		var kthis = this;
		window.KCFinder = {
			callBack: function(url) {
				kthis.addThumb(url);
				window.KCFinder = null;
				if ($iframeModal)
					$iframeModal.modal('hide');
			},
			callBackMultiple: function(files) {
				for (var i=0; i < files.length; i++) {
					kthis.addThumb(files[i]);
				}
				window.KCFinder = null;
				if ($iframeModal)
					$iframeModal.modal('hide');
			}
		};

		var kcfUrl = this.options.kcfUrl + '/browse.php?' + $.param(this.options.browseOptions);
		if ($iframeModal) {
			$iframeModal.find('.modal-body').html('<iframe name="kcfinder-iframe" src="' + kcfUrl + '" class="kcfinder-iframe" ' +
				'frameborder="0" width="100%" height="100%" marginwidth="0" marginheight="0" scrolling="no" />');
			$iframeModal.modal('show');
		} else {
			window.open(kcfUrl,
				'kcfinder', 'status=0, toolbar=0, location=0, menubar=0, ' +
				'directories=0, resizable=1, scrollbars=0, width=800, height=600'
			);
		}

	};

	/**
	 * JQuery plugin
	 */
	$.fn.KCFinderInputWidget = function(option) {
		return this.each(function() {
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