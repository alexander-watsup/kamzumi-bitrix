/**
 * 
 */
;(function(window) {
	
	if (!!window.RS.TuningComponent)
		return;
	
	window.RS.TuningComponent = function(id) {
		if (!!id) {
			this.id = id;
			this.init();
		} else {
			return new RS.TuningComponent('rstuning');
		}
	};
	
	RS.TuningComponent.prototype.modTabs = false;
	RS.TuningComponent.prototype.animationDelay = 310;
	RS.TuningComponent.prototype.sidebarWidth = 415;
	RS.TuningComponent.prototype.contentWidth = 650;
	RS.TuningComponent.prototype.breakpoint = {
		top: (415 + 650),
		low: 320,
	};
	RS.TuningComponent.prototype.keyCode = {
		ESC: 27,
	};
	RS.TuningComponent.prototype.swipeData = {
		touchDown : false,
		originalPosition : null,
		el : 2,
		result: {},
	};
	RS.TuningComponent.prototype.request = {
		delay: 800,
		timeoutId: 0,
	};
	RS.TuningComponent.prototype.selectors = {
		sidebar: '.js-rstuning__sidebar',
		content: '.js-rstuning__content',
		preloader: '.js-rstuning__preloader',
		openButton: '.js-rstuning__open-button',
		closeButton: '.js-rstuning__close-button',
		mainOverlay: '.js-rstuning__main-overlay',
		contentOverlay: '.js-rstuning__content-overlay',
		toggleSidebar: '.js-rstuning__toggle-sidebar',
		defaultSettings: '.js-tuning-default-settings',
	};
	RS.TuningComponent.prototype.isMobile = {
		Android: function() {
			return navigator.userAgent.match(/Android/i);
		},
		BlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
		iOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
		Opera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
		Windows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
		any: function() {
			return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		}
	};
	
	RS.TuningComponent.prototype.init = function() {
		document.addEventListener('DOMContentLoaded', this.documentReady);
		window.addEventListener('resize', this.windowResize);
	
		return true;
	};
	
	RS.TuningComponent.prototype.documentReady = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			elements = false,
			i = 0;
		
		this_.modTabs = document.querySelector('.js-rstuning.mod-tabs') ? true : false;
	
		tuningObj.classList.add('rstuning__loaded');

		this_.setWidth();

		if (tuningObj.classList.contains('open')) {
			document.querySelector('html').classList.toggle('rstuning-enabled');
		}
	
		// main - close\open
		tuningObj.querySelector(this_.selectors.openButton).addEventListener('click', this_.closeopen);
		tuningObj.querySelector(this_.selectors.closeButton).addEventListener('click', this_.closeopen);
		document.querySelector(this_.selectors.mainOverlay).addEventListener('click', this_.closeopen);

		// main - close\open by swipe
		if (!this_.isMobile.iOS()) {
			tuningObj.addEventListener('touchstart', this_.touchStart);
			tuningObj.addEventListener('touchend', this_.touchEnd);
			tuningObj.addEventListener('touchmove', this_.touchMove);
		}

		// open sidebar
		if (this_.modTabs) {
			elements = tuningObj.querySelectorAll(this_.selectors.toggleSidebar);
			if (elements && elements.length > 0) {
				for (i = 0; i < elements.length; i++) {
					elements[i].addEventListener('click', this_.toggleSidebar);
				}
			}
			tuningObj.querySelector(this_.selectors.contentOverlay).addEventListener('click', this_.toggleSidebar);
		}

		// default settings
		tuningObj.querySelector(this_.selectors.defaultSettings).addEventListener('click', this_.restoreDefaultSettings);

		// save macros
		elements = tuningObj.querySelectorAll('[data-macros]');
		if (elements && elements.length > 0) {
			for (i = 0; i < elements.length; i++) {
				elements[i].addEventListener('change', this_.changeMacrosFields);
			}
		}

		// change reload status
		elements = tuningObj.querySelectorAll('input');
		if (elements && elements.length > 0) {
			for (i = 0; i < elements.length; i++) {
				elements[i].addEventListener('change', this_.changeField);
			}
		}
		elements = tuningObj.querySelectorAll('select');
		if (elements && elements.length > 0) {
			for (i = 0; i < elements.length; i++) {
				elements[i].addEventListener('change', this_.changeField);
			}
		}
		elements = tuningObj.querySelectorAll('textarea');
		if (elements && elements.length > 0) {
			for (i = 0; i < elements.length; i++) {
				elements[i].addEventListener('change', this_.changeField);
			}
		}

		// tabs
		elements = tuningObj.querySelectorAll('.js-rstuning-nav');
		if (elements && elements.length > 0) {
			for (i = 0; i < elements.length; i++) {
				elements[i].addEventListener('click', this_.switchTab);
			}
		}

		// form submit
		if (tuningObj.querySelector('form')) {
			tuningObj.querySelector('form').addEventListener('submit', this_.formSubmit);
		} else {
			console.warn('No form detected! Cant save settings.');
		}

		return true;
	};

	RS.TuningComponent.prototype.windowResize = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();

		this_.setWidth();

		return true;
	};

	RS.TuningComponent.prototype.closeopen = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			goOpen = (tuningObj.classList.contains('open') ? false : true);

		// main
		document.querySelector('html').classList.toggle('rstuning-enabled');

		if (goOpen) {
			BX.setCookie('RSTUNING_COOKIE_OPEN', 'Y');
			tuningObj.classList.remove('closed');
			setTimeout(function(){
				tuningObj.classList.add('open');
				window.addEventListener('keyup', this_.windowKeyUp);
			}, 10);
		} else {
			BX.setCookie('RSTUNING_COOKIE_OPEN', 'N');
			tuningObj.classList.remove('open');
			tuningObj.classList.remove('loading');
			tuningObj.classList.remove('open-sidebar');

			setTimeout(function(){
				tuningObj.classList.add('closed');

				window.removeEventListener('keyup', this_.windowKeyUp);
			}, this_.animationDelay);
		}

		// sidebar
		if (this_.modTabs) {
			tuningObj.classList.remove('open-sidebar');
			tuningObj.querySelector(this_.selectors.contentOverlay).classList.remove('open');
		}

		this_.setWidth();

		if (!!e)
			e.preventDefault();

		return true;
	};

	RS.TuningComponent.prototype.windowKeyUp = function(e) {console.log('windowKeyUp');
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();

		if (e.keyCode == this_.keyCode.ESC) {
			this_.closeopen();
		}
	}

	RS.TuningComponent.prototype.toggleSidebar = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();

		if (!this_.modTabs)
			return false;

		tuningObj.classList.toggle('open-sidebar');
		tuningObj.querySelector(this_.selectors.contentOverlay).classList.toggle('open');

		return true;
	};

	RS.TuningComponent.prototype.changeMacrosFields = function(e) {
		rsTuning.setMacros(e.target.getAttribute('data-macros'), e.target.value);
		rsTuning.generateCss();

		return true;
	};

	RS.TuningComponent.prototype.changeField = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			el = e.target;

		while ((el = el.parentElement) && !el.classList.contains('js-rs_option_info'));

		if (el && el.getAttribute('data-reload') == 'Y') {
			tuningObj.setAttribute('data-reload', 'Y');
		}

		this_.formSubmit();

		return true;
	};

	RS.TuningComponent.prototype.switchTab = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			link = e.currentTarget,
			tabId = link.getAttribute('data-tabid'),
			tab = tuningObj.querySelector('.js-rstuning__tab-content [data-tabid="' + tabId + '"]'),
			el = false;

		if (!tab || !tabId)
			return;
		
		BX.onCustomEvent(window, 'rs.tuning.tabs.before.change', [tab]);

		if (tuningObj.querySelector('.js-content-title') > 0)
			tuningObj.querySelector('.js-content-title').innerHTML = link.getAttribute('data-name');

		el = link;
		while ((el = el.parentElement) && !el.classList.contains('js-rstuning__tab-switcher'));
		if (el) {
			elements = el.querySelectorAll('a');
			if (elements && elements.length > 0) {
				for (i = 0; i < elements.length; i++) {
					elements[i].classList.remove('active');
				}
			}
		}
		link.classList.add('active');

		el = tab;
		while ((el = el.parentElement) && !el.classList.contains('js-rstuning__tab-content'));
		if (el) {
			elements = el.querySelectorAll('.js-rstuning__tab-pane');
			if (elements && elements.length > 0) {
				for (i = 0; i < elements.length; i++) {
					elements[i].classList.remove('active');
				}
			}
		}
		tab.classList.add('active');

		BX.setCookie('RSTUNING_COOKIE_TAB_ACTIVE', tabId);

		tuningObj.classList.remove('open-sidebar');
		document.querySelector(this_.selectors.contentOverlay).classList.remove('open');

		BX.onCustomEvent(window, 'rs.tuning.tabs.after.change', [tab]);

		return true;
	};

	RS.TuningComponent.prototype.formSubmit = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			form = tuningObj.querySelector('form'),
			xhr = new XMLHttpRequest(),
			data = new FormData(form);

		clearTimeout(this_.request.timeoutId);
		this_.request.timeoutId = setTimeout(function(){
			this_.loading();

			xhr.open('POST', form.getAttribute('action'), false);
			xhr.addEventListener('load', this_.requestCallback);
			xhr.send(data);

		}, this_.request.delay);

		return true;
	};

	RS.TuningComponent.prototype.requestCallback = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();

		if (e.currentTarget.status != 200) {
			console.error(e.currentTarget.status + ': ' + e.currentTarget.statusText);
		} else {
			if (tuningObj.getAttribute('data-reload') == 'Y') {
				window.location.reload();
			} else {
				setTimeout(function(){
					this_.loading();
				}, 1500);
			}
		}

		return true;
	};

	RS.TuningComponent.prototype.restoreDefaultSettings = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent(),
			form = tuningObj.querySelector('form'),
			xhr = new XMLHttpRequest(),
			postQuery = "site_id=" + tuningObj.getAttribute('data-siteid') + "&rstuning_ajax=Y&rstuning_action=restoredefaultsettings";
	
		tuningObj.setAttribute('data-reload', 'Y');

		this_.loading();

		xhr.open('POST', form.getAttribute('action'), true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.addEventListener('load', this_.requestCallback);
		xhr.send(postQuery);
	
		return true;
	};

	RS.TuningComponent.prototype.swipe = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();

		var x = 0,
			y = 0,
			dx, dy;

		if (e && e.touches[0]) {
			x = e.touches[0].pageX;
			y = e.touches[0].pageY;
		} else if (e && e.originalEvent && e.originalEvent.touches[0]) {
			x = e.originalEvent.touches[0].pageX;
			y = e.originalEvent.touches[0].pageY;
		} else if (e && e.changedTouches[0]) {
			x = e.changedTouches[0].pageX;
			y = e.changedTouches[0].pageY;
		} else {
			console.warn('TuningComponent: swipe - no coordinates');
		}
	
		if (Math.abs(this_.swipeData.originalPosition.x - x) > 80) {
			dx = (x > this_.swipeData.originalPosition.x) ? 'right' : 'left';
		}
		else {
			dx = null;
		}
	
		if (Math.abs(this_.swipeData.originalPosition.y - y) > 80) {
			dy = (y > this_.swipeData.originalPosition.y) ? 'down' : 'up';
		} else {
			dy = null;
		}
	
		this_.swipeData.result = {
			direction: {
				x: dx,
				y: dy
			},
			offset: {
				x: x - this_.swipeData.originalPosition.x,
				y: this_.swipeData.originalPosition.y - y
			}
		};
	
		return true;
	};

	RS.TuningComponent.prototype.touchStart = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();
	
		this_.swipeData.touchDown = true;
		if (e && e.touches[0]) {
			this_.swipeData.originalPosition = {
				x: e.touches[0].pageX,
				y: e.touches[0].pageY
			};
		} else if (e && e.originalEvent && e.originalEvent.touches[0]) {
			this_.swipeData.originalPosition = {
				x: e.originalEvent.touches[0].pageX,
				y: e.originalEvent.touches[0].pageY
			};
		} else if (e && e.changedTouches[0]) {
			this_.swipeData.originalPosition = {
				x: e.changedTouches[0].pageX,
				y: e.changedTouches[0].pageY
			};
		} else {
			console.error('TuningComponent: touch coordinates get error.');
		}
		
		return true;
	};

	RS.TuningComponent.prototype.touchEnd = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();
	
		this_.swipe(e);

		this_.swipeData.touchDown = false;
		this_.swipeData.originalPosition = null;
	
		if (this_.swipeData.result.direction.x == 'right' && tuningObj.classList.contains('open')) {
			this_.toggleSidebar();
		} else if (this_.swipeData.result.direction.x == 'left' && tuningObj.classList.contains('open-sidebar')) {
			this_.toggleSidebar();
		} else if (this_.swipeData.result.direction.x == 'left' && !tuningObj.classList.contains('open-sidebar')) {
			this_.closeopen();
		}
	
		return true;
	};

	RS.TuningComponent.prototype.touchMove = function(e) {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();
		
		if (!this_.swipeData.touchDown) { return; }
	};

	RS.TuningComponent.prototype.setWidth = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();
	
		if (window.innerWidth <= this_.breakpoint.top) {
			tuningObj.querySelector(this_.selectors.preloader).style.width =
				Math.min((this_.sidebarWidth + this_.contentWidth), window.innerWidth) + 'px';
			tuningObj.querySelector(this_.selectors.sidebar).style.width =
				Math.min(this_.sidebarWidth, window.innerWidth) + 'px';
			tuningObj.querySelector(this_.selectors.content).style.width =
				Math.min(this_.contentWidth, window.innerWidth) + 'px';
		} else {
			tuningObj.querySelector(this_.selectors.sidebar).style.width = '';
			tuningObj.querySelector(this_.selectors.content).style.width = '';
		}
	
		// sidebar
		if (this_.modTabs) {
			tuningObj.classList.remove('open-sidebar');
			tuningObj.querySelector(this_.selectors.contentOverlay).classList.remove('open');
		}
		
	
		return true;
	};
	
	RS.TuningComponent.prototype.loading = function() {
		var this_ = rsTuningComponent;
			tuningObj = this_.getTuningComponent();
		
		if (tuningObj.getAttribute('data-reload') == 'Y') {
			tuningObj.classList.toggle('loading');
		}
	
		return true;
	};
	
	RS.TuningComponent.prototype.getTuningComponent = function() {
		return document.getElementById(this.id);
	};
	
})(window);
