(function($) {
	$.templates = {};

	use = function(selector, options) {
		var templated = [];
		$(selector).each(function() {
			var templateInstance = $(this).clone().removeClass("template").removeAttr("aria-template-id");

			var html = templateInstance.prop("outerHTML");

			if (options.data) {
				for(var key in options.data) {
					var value = options.data[key];

					// Simple replacement
					var simpleKey = "\\${" + key + "}";
					var regex = new RegExp(simpleKey, "g");

					html = html.replace(regex, value);
				}
			}

			templated[templated.length] = $(html)[0];
		});

		return $(templated);
	};

	$.fn.template = function(method, options) {

		if (method) {
			var settings = $.extend({}, options);

			switch(method) {
				case "use":
					return use(this, settings);
			}
		}
		else {
			// Init
			return this.each(function() {
				$.templates[$(this).attr("aria-template-id")] = $(this).clone().removeClass("template").removeAttr("aria-template-id");
			});
		}
	};
} (jQuery));