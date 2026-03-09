class Sillove_loadscripts {
	constructor(e) {
		this.triggerEvents = e, this.eventOptions = {
			passive: !0
		}, this.userEventListener = this.triggerListener.bind(this), this.lazy_trigger, this.lazy_scripts_load_fired = 0, this.scripts_load_fired = 0, this.scripts_load_fire = 0, this.Sillove_scripts = {
			normal: [],
			async: [],
			defer: [],
			lazy: []
		}, this.allJQueries = []
	}
	user_events_add(e) {
		this.triggerEvents.forEach((t => window.addEventListener(t, e.userEventListener, e.eventOptions)))
	}
	user_events_remove(e) {
		this.triggerEvents.forEach((t => window.removeEventListener(t, e.userEventListener, e.eventOptions)))
	}
	triggerListener() {
		this.user_events_remove(this), this.lazy_scripts_load_fired = 1, "loading" === document.readyState ? (document.addEventListener("DOMContentLoaded",this.load_resources.bind(this)), (!this.scripts_load_fire ? document.addEventListener("DOMContentLoaded", this.load_resources.bind(this)) : "")) : ((!this.scripts_load_fire ? this.load_resources() : ""))
	}
	async load_resources() {
		if (this.scripts_load_fired) {
			return;
		}
		this.scripts_load_fired = !0, this.hold_event_listeners(), this.exe_document_write(), this.register_scripts(), (typeof (Sillove_events_on_start_js) == "function" ? Sillove_events_on_start_js() : ""), this.preload_scripts(this.Sillove_scripts.normal), this.preload_scripts(this.Sillove_scripts.defer), this.preload_scripts(this.Sillove_scripts.async), await this.load_scripts(this.Sillove_scripts.normal), await this.load_scripts(this.Sillove_scripts.defer), await this.load_scripts(this.Sillove_scripts.async), await this.execute_domcontentloaded(), await this.execute_window_load(), window.dispatchEvent(new Event("Sillove-scripts-loaded")), (typeof (Sillove_events_on_end_js) == "function" ? Sillove_events_on_end_js() : "");
		this.lazy_trigger = setInterval(this.Sillove_trigger_lazy_script, 500, this);
	}
	register_scripts() {
		document.querySelectorAll("script[type=lazyload_int]").forEach((e => {
			e.hasAttribute("data-src") ? e.hasAttribute("async") && !1 !== e.async ? this.Sillove_scripts.async.push(e) : e.hasAttribute("defer") && !1 !== e.defer || "module" === e.getAttribute("data-Sillove-type") ? this.Sillove_scripts.defer.push(e) : this.Sillove_scripts.normal.push(e) : this.Sillove_scripts.normal.push(e)
		}))
		document.querySelectorAll("script[type=lazyload_ext]").forEach((e => {
			this.Sillove_scripts.lazy.push(e)
		}))
	}
	async execute_script(e) {
		return await this.repaint_frame(), new Promise((t => {
			const n = document.createElement("script");
			let r;
			[...e.attributes].forEach((e => {
				let t = e.nodeName;
				"type" !== t && "data-src" !== t && ("data-Sillove-type" === t && (t = "type", r = e.nodeValue), n.setAttribute(t, e.nodeValue))
			})), e.hasAttribute("data-src") ? (n.setAttribute("src", e.getAttribute("data-src")), n.addEventListener("load", t), n.addEventListener("error", t)) : (n.text = e.text, t()), e.parentNode !== null ? e.parentNode.replaceChild(n, e) : e;
		}))
	}
	async load_scripts(e) {
		const t = e.shift();
		return t ? (await this.execute_script(t), this.load_scripts(e)) : Promise.resolve()
	}
	preload_scripts(resource) {
		var e = document.createDocumentFragment();
		[...resource].forEach((t => {
			const n = t.getAttribute("data-src");
			if (n) {
				const t = document.createElement("link");
				t.href = n, t.rel = "preload", t.as = "script", e.appendChild(t)
			}
		})), document.head.appendChild(e)
	}
	hold_event_listeners() {
		let e = {};

		function t(t, n) {
			! function (t) {
				function n(n) {
					return e[t].eventsToRewrite.indexOf(n) >= 0 ? "Sillove-" + n : n
				}
				e[t] || (e[t] = {
					originalFunctions: {
						add: t.addEventListener,
						remove: t.removeEventListener
					},
					eventsToRewrite: []
				}, t.addEventListener = function () {
					arguments[0] = n(arguments[0]), e[t].originalFunctions.add.apply(t, arguments)
				}, t.removeEventListener = function () {
					arguments[0] = n(arguments[0]), e[t].originalFunctions.remove.apply(t, arguments)
				})
			}(t), e[t].eventsToRewrite.push(n)
		}

		function n(e, t) {
			let n = e[t];
			Object.defineProperty(e, t, {
				get: () => n || function () { },
				set(r) {
					e["Sillove" + t] = n = r
				}
			});
		}
		t(document, "DOMContentLoaded");
		t(window, "DOMContentLoaded");
		t(window, "load");
		t(window, "pageshow");
		t(document, "readystatechange");
		n(document, "onreadystatechange");
		n(window, "onload");
		n(window, "onpageshow");
	}
	hold_jquery(e) {
		let t = window.jQuery;
		Object.defineProperty(window, "jQuery", {
			get: () => t,
			set(n) {
				if (n && n.fn && !e.allJQueries.includes(n)) {
					n.fn.ready = n.fn.init.prototype.ready = function (t) {
						if (typeof t != "undefined") {
							e.scripts_load_fired ? e.domReadyFired ? t.bind(document)(n) : document.addEventListener("Sillove-DOMContentLoaded", () => t.bind(document)(n)) : t.bind(document)(n)
							return n(document);
						}
					};
					const t = n.fn.on;
					n.fn.on = n.fn.init.prototype.on = function () {
						if ("ready" == arguments[0]) {
							if (this[0] === document) {
								arguments[1].bind(document)(n);
							} else {
								return t.apply(this, arguments), this
							}
						}
						if (this[0] === window) {
							function e(e) {
								return e.split(" ").map(e => "load" === e || 0 === e.indexOf("load.") ? "Sillove-jquery-load" : e).join(" ")
							}
							"string" == typeof arguments[0] || arguments[0] instanceof String ? arguments[0] = e(arguments[0]) : "object" == typeof arguments[0] && Object.keys(arguments[0]).forEach(t => {
								Object.assign(arguments[0], {
									[e(t)]: arguments[0][t]
								})[t]
							})
						}
						return t.apply(this, arguments), this
					}, e.allJQueries.push(n)
				}
				t = n
			}
		})
	}
	async execute_domcontentloaded() {
		this.domReadyFired = !0, await this.repaint_frame(), document.dispatchEvent(new Event("Sillove-DOMContentLoaded")), await this.repaint_frame(), window.dispatchEvent(new Event("Sillove-DOMContentLoaded")), await this.repaint_frame(), document.dispatchEvent(new Event("Sillove-readystatechange")), await this.repaint_frame(), document.Silloveonreadystatechange && document.Silloveonreadystatechange()
	}
	async execute_window_load() {
		await this.repaint_frame(), setTimeout(function () { window.dispatchEvent(new Event("Sillove-load")) }, 100), await this.repaint_frame(), window.Silloveonload && window.Silloveonload(), await this.repaint_frame(), this.allJQueries.forEach((e => e(window).trigger("Sillove-jquery-load"))), window.dispatchEvent(new Event("Sillove-pageshow")), await this.repaint_frame(), window.Silloveonpageshow && window.Silloveonpageshow()
	}
	exe_document_write() {
		const e = new Map;
		document.write = document.writeln = function (t) {
			const n = document.currentScript,
				r = document.createRange(),
				i = n.parentElement;
			let o = e.get(n);
			void 0 === o && (o = n.nextSibling, e.set(n, o));
			const a = document.createDocumentFragment();
			r.setStart(a, 0), a.appendChild(r.createContextualFragment(t)), i.insertBefore(a, o)
		}
	}
	async repaint_frame() {
		return new Promise((e => requestAnimationFrame(e)))
	}
	static execute() {
		const e = new Sillove_loadscripts(["keydown", "mousemove", "touchmove", "touchstart", "touchend", "wheel", "scroll"]);
		e.user_events_add(e)
		if (!e.excluded_js) {
			e.hold_jquery(e);
		}
	}
}
// setTimeout(function(){Sillove_loadscripts.execute();},1000);

var handleScroll;
setTimeout(function () {
	Sillove_loadscripts.execute();
}, 1e3),
	(window.onload = function () {
		console.log("Page has fully loaded");
	}),
	document.addEventListener("DOMContentLoaded", function () {
		console.log("DOM content has loaded");
		var base_url = window.location.origin;
		window.checkout = window.checkout || {};
		window.checkout.shoppingCartUrl= base_url + "/checkout/cart/";
		window.checkout.checkoutUrl=base_url + "/checkout/";
		window.checkout.updateItemQtyUrl=base_url + "/checkout/sidebar/updateItemQty/";
		window.checkout.removeItemUrl=base_url + "/checkout/sidebar/removeItem/";
		window.checkout. imageTemplate="Magento_Catalog/product/image_with_borders";
		console.log(window.checkout);
			setTimeout(function () {
				var e = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
				window.scrollTo(0, e + 1), window.dispatchEvent(new Event("scroll")), window.addEventListener("scroll", handleScroll);
			}, 5e3);
	});