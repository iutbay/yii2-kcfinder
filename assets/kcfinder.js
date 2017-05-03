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
		
		// fix thumb for non image file
		if ($.inArray(thumbUrl.substr(thumbUrl.lastIndexOf(".")+1).toLowerCase(),["jpg","jpeg","png","gif"]) < 0)
		{
			thumbUrl = 	this.options.kcfUrl+"/themes/default/img/files/big/"+thumbUrl.substr(thumbUrl.lastIndexOf(".")+1)+".png";
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
			var hint = $('#' + this.options.iframeModalId+' .modal-title').attr('title');
			$iframeModal.find('.modal-body').html('<div class="row"><div class="col-sm-6">'+hint+'</div><div class="col-sm-6"><div class="form-group input-group"><input class="exturl form-control" type="text" value="" /><span class="btnurl input-group-addon">OK</span></div></div></div>'+
				'<div class="row"><div class="col-sm-12"><iframe name="kcfinder-iframe" src="' + kcfUrl + '" class="kcfinder-iframe" ' +
				'frameborder="0" width="100%" height="100%" marginwidth="0" marginheight="0" scrolling="no" /></div></div>');
			$iframeModal.modal('show');
			
			var modid = this.options.iframeModalId;
			$('#' + modid+' .btnurl').click(function(){
				var exturl = $('#' + modid+' .exturl').val();
				kthis.addThumb(exturl);
				window.KCFinder = null;
				if ($iframeModal)
					$iframeModal.modal('hide');
			});
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
