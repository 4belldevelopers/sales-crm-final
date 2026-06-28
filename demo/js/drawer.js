
var KTDrawer = function(e, t) {
    var n = this,
        i = document.getElementsByTagName("BODY")[0];
    if (null != e) {
        var r = {
                overlay: !0,
                direction: "end",
                baseClass: "drawer",
                overlayClass: "drawer-overlay"
            },
            o = function() {
                n.options = KTUtil.deepExtend({}, r, t), n.uid = KTUtil.getUniqueId("drawer"), n.element = e, n.overlayElement = null, n.name = n.element.getAttribute("data-kt-drawer-name"), n.shown = !1, n.lastWidth, n.toggleElement = null, n.element.setAttribute("data-kt-drawer", "true"), a(), d(), KTUtil.data(n.element).set("drawer", n)
            },
            a = function() {
                var e = f("toggle"),
                    t = f("close");
                null !== e && e.length > 0 && KTUtil.on(i, e, "click", (function(e) {
                    e.preventDefault(), n.toggleElement = this, l()
                })), null !== t && t.length > 0 && KTUtil.on(i, t, "click", (function(e) {
                    e.preventDefault(), n.closeElement = this, s()
                }))
            },
            l = function() {
                !1 !== KTEventHandler.trigger(n.element, "kt.drawer.toggle", n) && (!0 === n.shown ? s() : u(), KTEventHandler.trigger(n.element, "kt.drawer.toggled", n))
            },
            s = function() {
                !1 !== KTEventHandler.trigger(n.element, "kt.drawer.hide", n) && (n.shown = !1, m(), i.removeAttribute("data-kt-drawer-" + n.name, "on"), i.removeAttribute("data-kt-drawer"), KTUtil.removeClass(n.element, n.options.baseClass + "-on"), null !== n.toggleElement && KTUtil.removeClass(n.toggleElement, "active"), KTEventHandler.trigger(n.element, "kt.drawer.after.hidden", n))
            },
            u = function() {
                !1 !== KTEventHandler.trigger(n.element, "kt.drawer.show", n) && (n.shown = !0, c(), i.setAttribute("data-kt-drawer-" + n.name, "on"), i.setAttribute("data-kt-drawer", "on"), KTUtil.addClass(n.element, n.options.baseClass + "-on"), null !== n.toggleElement && KTUtil.addClass(n.toggleElement, "active"), KTEventHandler.trigger(n.element, "kt.drawer.shown", n))
            },
            d = function() {
                var e = p(),
                    t = f("direction");
                !0 === KTUtil.hasClass(n.element, n.options.baseClass + "-on") && "on" === String(i.getAttribute("data-kt-drawer-" + n.name + "-")) ? n.shown = !0 : n.shown = !1, !0 === f("activate") ? (KTUtil.addClass(n.element, n.options.baseClass), KTUtil.addClass(n.element, n.options.baseClass + "-" + t), KTUtil.css(n.element, "width", e, !0), n.lastWidth = e) : (KTUtil.css(n.element, "width", ""), KTUtil.removeClass(n.element, n.options.baseClass), KTUtil.removeClass(n.element, n.options.baseClass + "-" + t), s())
            },
            c = function() {
                !0 === f("overlay") && (n.overlayElement = document.createElement("DIV"), KTUtil.css(n.overlayElement, "z-index", KTUtil.css(n.element, "z-index") - 1), i.append(n.overlayElement), KTUtil.addClass(n.overlayElement, f("overlay-class")), KTUtil.addEvent(n.overlayElement, "click", (function(e) {
                    e.preventDefault(), s()
                })))
            },
            m = function() {
                null !== n.overlayElement && KTUtil.remove(n.overlayElement)
            },
            f = function(e) {
                if (!0 === n.element.hasAttribute("data-kt-drawer-" + e)) {
                    var t = n.element.getAttribute("data-kt-drawer-" + e),
                        i = KTUtil.getResponsiveValue(t);
                    return null !== i && "true" === String(i) ? i = !0 : null !== i && "false" === String(i) && (i = !1), i
                }
                var r = KTUtil.snakeToCamel(e);
                return n.options[r] ? KTUtil.getResponsiveValue(n.options[r]) : null
            },
            p = function() {
                var e = f("width");
                return "auto" === e && (e = KTUtil.css(n.element, "width")), e
            };
        KTUtil.data(e).has("drawer") ? n = KTUtil.data(e).get("drawer") : o(), n.toggle = function() {
            return l()
        }, n.show = function() {
            return u()
        }, n.hide = function() {
            return s()
        }, n.isShown = function() {
            return n.shown
        }, n.update = function() {
            d()
        }, n.goElement = function() {
            return n.element
        }, n.destroy = function() {
            KTUtil.data(n.element).remove("drawer")
        }, n.on = function(e, t) {
            return KTEventHandler.on(n.element, e, t)
        }, n.one = function(e, t) {
            return KTEventHandler.one(n.element, e, t)
        }, n.off = function(e) {
            return KTEventHandler.off(n.element, e)
        }, n.trigger = function(e, t) {
            return KTEventHandler.trigger(n.element, e, t, n, t)
        }
    }
};
KTDrawer.getInstance = function(e) {
    return null !== e && KTUtil.data(e).has("drawer") ? KTUtil.data(e).get("drawer") : null
}, KTDrawer.createInstances = function(e = '[data-kt-drawer="true"]') {
    var t = document.getElementsByTagName("BODY")[0].querySelectorAll(e);
    if (t && t.length > 0)
        for (var n = 0, i = t.length; n < i; n++) new KTDrawer(t[n])
}, KTDrawer.handleShow = function() {
    KTUtil.on(document.body, '[data-kt-drawer-show="true"][data-kt-drawer-target]', "click", (function(e) {
        var t = document.querySelector(this.getAttribute("data-kt-drawer-target"));
        t && KTDrawer.getInstance(t).show()
    }))
}, KTDrawer.handleDismiss = function() {
    KTUtil.on(document.body, '[data-kt-drawer-dismiss="true"]', "click", (function(e) {
        var t = this.closest('[data-kt-drawer="true"]');
        if (t) {
            var n = KTDrawer.getInstance(t);
            n.isShown() && n.hide()
        }
    }))
}, window.addEventListener("resize", (function() {
    var e = document.getElementsByTagName("BODY")[0];
    KTUtil.throttle(undefined, (function() {
        var t = e.querySelectorAll('[data-kt-drawer="true"]');
        if (t && t.length > 0)
            for (var n = 0, i = t.length; n < i; n++) {
                var r = KTDrawer.getInstance(t[n]);
                r && r.update()
            }
    }), 200)
})), KTDrawer.init = function() {
    KTDrawer.createInstances(), KTDrawer.handleShow(), KTDrawer.handleDismiss()
}, "loading" === document.readyState ? document.addEventListener("DOMContentLoaded", KTDrawer.init) : KTDrawer.init(), "undefined" != typeof module && void 0 !== module.exports && (module.exports = KTDrawer);

