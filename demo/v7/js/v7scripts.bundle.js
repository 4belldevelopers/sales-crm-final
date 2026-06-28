"use strict";


$.noConflict();

// Global variables
window.VTUtilElementDataStore = {};
window.VTUtilElementDataStoreID = 0;
window.VTUtilDelegatedEventHandlers = {};

var VTUtil = function() {
   


    return { 
		
        //main function to initiate the theme
        init: function(settings) {},

        deepExtend: function(out) {
            out = out || {};

            for (var i = 1; i < arguments.length; i++) {
                var obj = arguments[i];
                if (!obj) continue;

                for (var key in obj) {
                    if (!obj.hasOwnProperty(key)) {
                        continue;
                    }

                    // based on https://javascriptweblog.wordpress.com/2011/08/08/fixing-the-javascript-typeof-operator/
                    if ( Object.prototype.toString.call(obj[key]) === '[object Object]' ) {
                        out[key] = VTUtil.deepExtend(out[key], obj[key]);
                        continue;
                    }

                    out[key] = obj[key];
                }
            }

            return out;
        },

        getById: function(el) {
            if (typeof el === 'string') {
                return document.getElementById(el);
            } else {
                return el;
            }
        },

        getByTag: function(query) {
            return document.getElementsByTagName(query);
        },
   
        getBody: function() {
            return document.getElementsByTagName('body')[0];
        },

        remove: function(el) {
            if (el && el.parentNode) {
                el.parentNode.removeChild(el);
            }
        },

        find: function(parent, query) {
            parent = VTUtil.getById(parent);
            if (parent) {
                return parent.querySelector(query);
            }
        },

        findAll: function(parent, query) {
            parent = VTUtil.getById(parent);
            if (parent) {
                return parent.querySelectorAll(query);
            }
        },
      
        matches: function(el, selector, log) {
            var p = Element.prototype;
            var f = p.matches || p.webkitMatchesSelector || p.mozMatchesSelector || p.msMatchesSelector || function(s) {
                return [].indexOf.call(document.querySelectorAll(s), this) !== -1;
            };

            if (el && el.tagName) {
                return f.call(el, selector);
            } else {
                return false;
            }
        },

        data: function(el) {
            return {
                set: function(name, data) {
                    if (!el) {
                        return;
                    }

                    if (el.customDataTag === undefined) {
                        window.VTUtilElementDataStoreID++;
                        el.customDataTag = window.VTUtilElementDataStoreID;
                    }

                    if (window.VTUtilElementDataStore[el.customDataTag] === undefined) {
                        window.VTUtilElementDataStore[el.customDataTag] = {};
                    }

                    window.VTUtilElementDataStore[el.customDataTag][name] = data;
                },

                get: function(name) {
                    if (!el) {
                        return;
                    }

                    if (el.customDataTag === undefined) {
                        return null;
                    }

                    return this.has(name) ? window.VTUtilElementDataStore[el.customDataTag][name] : null;
                },

                has: function(name) {
                    if (!el) {
                        return false;
                    }

                    if (el.customDataTag === undefined) {
                        return false;
                    }

                    return (window.VTUtilElementDataStore[el.customDataTag] && window.VTUtilElementDataStore[el.customDataTag][name]) ? true : false;
                },

                remove: function(name) {
                    if (el && this.has(name)) {
                        delete window.VTUtilElementDataStore[el.customDataTag][name];
                    }
                }
            };
        },      

        offset: function(el) {
            var rect, win;

            if ( !el ) {
                return;
            }

            // Return zeros for disconnected and hidden (display: none) elements (gh-2310)
            // Support: IE <=11 only
            // Running getBoundingClientRect on a
            // disconnected node in IE throws an error

            if ( !el.getClientRects().length ) {
                return { top: 0, left: 0 };
            }

            // Get document-relative position by adding viewport scroll to viewport-relative gBCR
            rect = el.getBoundingClientRect();
            win = el.ownerDocument.defaultView;

            return {
                top: rect.top + win.pageYOffset,
                left: rect.left + win.pageXOffset
            };
        },

        attr: function(el, name, value) {
            if (el == undefined) {
                return;
            }

            if (value !== undefined) {
                el.setAttribute(name, value);
            } else {
                return el.getAttribute(name);
            }
        },

        removeAttr: function(el, name) {
            if (el == undefined) {
                return;
            }

            el.removeAttribute(name);
        },

        animate: function(from, to, duration, update, easing, done) {
            /**
             * TinyAnimate.easings
             *  Adapted from jQuery Easing
             */
            var easings = {};
            var easing;

            easings.linear = function(t, b, c, d) {
                return c * t / d + b;
            };

            easing = easings.linear;

            // Early bail out if called incorrectly
            if (typeof from !== 'number' ||
                typeof to !== 'number' ||
                typeof duration !== 'number' ||
                typeof update !== 'function') {
                return;
            }

            // Create mock done() function if necessary
            if (typeof done !== 'function') {
                done = function() {};
            }

            // Pick implementation (requestAnimationFrame | setTimeout)
            var rAF = window.requestAnimationFrame || function(callback) {
                window.setTimeout(callback, 1000 / 50);
            };

            // Animation loop
            var canceled = false;
            var change = to - from;

            function loop(timestamp) {
                var time = (timestamp || +new Date()) - start;

                if (time >= 0) {
                    update(easing(time, from, change, duration));
                }
                if (time >= 0 && time >= duration) {
                    update(to);
                    done();
                } else {
                    rAF(loop);
                }
            }

            update(from);

            // Start animation loop
            var start = window.performance && window.performance.now ? window.performance.now() : +new Date();

            rAF(loop);
        },

        addEvent: function(el, type, handler, one) {
            if (typeof el !== 'undefined' && el !== null) {
                el.addEventListener(type, handler);
            }
        },
 
        scrollTo: function(target, offset, duration) {
            var duration = duration ? duration : 500;
            var targetPos = target ? VTUtil.offset(target).top : 0;
            var scrollPos = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            var from, to;

            if (offset) {
                scrollPos += offset;
            }

            from = scrollPos;
            to = targetPos;

            VTUtil.animate(from, to, duration, function(value) {
                document.documentElement.scrollTop = value;
                document.body.parentNode.scrollTop = value;
                document.body.scrollTop = value;
            }); //, easing, done
        },

        scrollTop: function(offset, duration) {
            VTUtil.scrollTo(null, offset, duration);
        },     

        ready: function(callback) {
            if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
                callback();
            } else {
                document.addEventListener('DOMContentLoaded', callback);
            }
        },
      
    }
}();

if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = VTUtil;
}

VTUtil.ready(function() {	
		VTUtil.init();	
});

"use strict";
var KTWizard = function(elementId, options) {
    // Main object
    var the = this;
    var init = false;

    // Get element object
    var element = VTUtil.getById(elementId);
    var body = VTUtil.getBody();

    if (!element) {
        return;
    }

    // Default options
    var defaultOptions = {
        startStep: 1,
        clickableSteps: false, // to make steps clickable this set value true and add data-wizard-clickable="true" in HTML for class="wizard" element
        navigation: true
    };

    ////////////////////////////
    // ** Private Methods  ** //
    ////////////////////////////

    var Plugin = {
        /**
         * Construct
         */

        construct: function(options) {
            if (VTUtil.data(element).has('wizard')) {
                the = VTUtil.data(element).get('wizard');
            } else {
                // reset menu
                Plugin.init(options);

                // build menu
                Plugin.build();

                VTUtil.data(element).set('wizard', the);
            }

            return the;
        },

        /**
         * Init wizard
         */
        init: function(options) {
            the.element = element;
            the.events = [];

            // merge default and user defined options
            the.options = VTUtil.deepExtend({}, defaultOptions, options);

            // Elements
            the.steps = VTUtil.findAll(element, '[data-wizard-type="step"]');

            the.btnNext = VTUtil.find(element, '[data-wizard-type="action-next"]');
            the.btnPrev = VTUtil.find(element, '[data-wizard-type="action-prev"]');
            the.btnSubmit = VTUtil.find(element, '[data-wizard-type="action-submit"]');

            // Variables
            the.events = [];
            the.lastStep = 0;
            the.currentStep = 1;
            the.newStep = 0;
            the.stopped = false;

            the.totalSteps = the.steps.length;

            // Init current step
            if (the.options.startStep > 1) {
                Plugin.goTo(the.options.startStep);
            }

            // Init UI
            Plugin.updateUI();
        },

        /**
         * Build Form Wizard
         */
        build: function() {
            if (the.options.navigation) {
                // Next button event handler
                VTUtil.addEvent(the.btnNext, 'click', function(e) {
                    e.preventDefault();

                    // Set new step number
                    Plugin.setNewStep(Plugin.getNextStep());

                    // Trigger change event
                    if (Plugin.eventTrigger('change') !== false) {
                        Plugin.goTo(Plugin.getNextStep());
                    }
                });

                // Prev button event handler
                VTUtil.addEvent(the.btnPrev, 'click', function(e) {
                    e.preventDefault();

                    // Set new step number
                    Plugin.setNewStep(Plugin.getPrevStep());

                    // Trigger change event
                    if (Plugin.eventTrigger('change') !== false) {
                        Plugin.goTo(Plugin.getPrevStep());
                    }
                });

                // Submit button event handler
                VTUtil.addEvent(the.btnSubmit, 'click', function(e) {
                    e.preventDefault();

                    Plugin.eventTrigger('submit');
                });
            }

            if (the.options.clickableSteps === true) {
                VTUtil.on(element, '[data-wizard-type="step"]', 'click', function() {
                    var index = VTUtil.index(this) + 1;

                    if (index !== the.currentStep) {
                        Plugin.setNewStep(index);

                        // Trigger change event
                        if (Plugin.eventTrigger('change') !== false) {
                            Plugin.goTo(index);
                        }
                    }
                });
            }
        },

        /**
         * Handles wizard click wizard
         */
        goTo: function(number) {
            // Skip if stopped
            if (the.stopped === true) {
                the.stopped = false;
                return;
            }

            // Skip if this step is already shown
            if (number === the.currentStep || number > the.totalSteps || number < 0) {
                return;
            }

            // Validate step number
            number = parseInt(number);

            // Set current step
            the.lastStep = the.currentStep;
            the.currentStep = number;
            the.newStep = 0;

            Plugin.updateUI();

            Plugin.eventTrigger('changed');

            return the;
        },

        /**
         * Stop wizard
         */
        stop: function() {
            the.stopped = true;
        },

        /**
         * Resume wizard
         */
        resume: function() {
            the.stopped = false;
        },

        /**
         * Check last step
         */
        isLastStep: function() {
            return the.currentStep === the.totalSteps;
        },

        /**
         * Check first step
         */
        isFirstStep: function() {
            return the.currentStep === 1;
        },

        /**
         * Check between step
         */
        isBetweenStep: function() {
            return Plugin.isLastStep() === false && Plugin.isFirstStep() === false;
        },

        /**
         * Update wizard UI after step change
         */
        updateUI: function() {
            var stepType = '';
            var index = the.currentStep - 1;

            if (Plugin.isLastStep()) {
                stepType = 'last';
            } else if (Plugin.isFirstStep()) {
                stepType = 'first';
            } else {
                stepType = 'between';
            }

            VTUtil.attr(the.element, 'data-wizard-state', stepType);

            // Steps
            var steps = VTUtil.findAll(the.element, '[data-wizard-type="step"]');

            if (steps && steps.length > 0) {
                for (var i = 0, len = steps.length; i < len; i++) {
                    if (i == index) {
                        VTUtil.attr(steps[i], 'data-wizard-state', 'current');
                    } else {
                        if (i < index) {
                            VTUtil.attr(steps[i], 'data-wizard-state', 'done');
                        } else {
                            VTUtil.attr(steps[i], 'data-wizard-state', 'pending');
                        }
                    }
                }
            }

            // Steps Info
            var stepsInfo = VTUtil.findAll(the.element, '[data-wizard-type="step-info"]');
            if (stepsInfo &&stepsInfo.length > 0) {
                for (var i = 0, len = stepsInfo.length; i < len; i++) {
                    if (i == index) {
                        VTUtil.attr(stepsInfo[i], 'data-wizard-state', 'current');
                    } else {
                        VTUtil.removeAttr(stepsInfo[i], 'data-wizard-state');
                    }
                }
            }

            // Steps Content
            var stepsContent = VTUtil.findAll(the.element, '[data-wizard-type="step-content"]');
            if (stepsContent&& stepsContent.length > 0) {
                for (var i = 0, len = stepsContent.length; i < len; i++) {
                    if (i == index) {
                        VTUtil.attr(stepsContent[i], 'data-wizard-state', 'current');
                    } else {
                        VTUtil.removeAttr(stepsContent[i], 'data-wizard-state');
                    }
                }
            }
        },

        /**
         * Get next step number
         */
        getNextStep: function() {
            if (the.totalSteps >= (the.currentStep + 1)) {
                return the.currentStep + 1;
            } else {
                return the.totalSteps;
            }
        },

        /**
         * Get prev step number
         */
        getPrevStep: function() {
            if ((the.currentStep - 1) >= 1) {
                return the.currentStep - 1;
            } else {
                return 1;
            }
        },

        /**
         * Get new step number
         */
        getNewStep: function() {
            return the.newStep;
        },

        /**
         * Set new step
         */
        setNewStep: function(step) {
            the.newStep = step;
        },

        /**
         * Trigger events
         */
        eventTrigger: function(name, nested) {
            //VTUtil.triggerCustomEvent(name);
            for (var i = 0; i < the.events.length; i++) {
                var event = the.events[i];
                if (event.name == name) {
                    if (event.one == true) {
                        if (event.fired == false) {
                            the.events[i].fired = true;
                            return event.handler.call(this, the);
                        }
                    } else {
                        return event.handler.call(this, the);
                    }
                }
            }
        },

        addEvent: function(name, handler, one) {
            the.events.push({
                name: name,
                handler: handler,
                one: one,
                fired: false
            });

            return the;
        }
    };

    //////////////////////////
    // ** Public Methods ** //
    //////////////////////////

    /**
     * Set default options
     */

    the.setDefaults = function(options) {
        defaultOptions = options;
    };

    /**
     * Go to the next step
     */
    the.goNext = function() {
        return Plugin.goTo(Plugin.getNextStep());
    };

    /**
     * Go to the prev step
     */
    the.goPrev = function() {
        return Plugin.goTo(Plugin.getPrevStep());
    };

    /**
     * Go to the last step
     */
    the.goLast = function() {
        return Plugin.goTo(Plugin.getLastStep());
    };

    /**
     * Go to the first step
     */
    the.goFirst = function() {
        return Plugin.goTo(Plugin.getFirstStep());
    };

    /**
     * Go to a step
     */
    the.goTo = function(number) {
        return Plugin.goTo(number);
    };

    /**
     * Stop wizard
     */
    the.stop = function() {
        return Plugin.stop();
    };

    /**
     * Resume wizard
     */
    the.resume = function() {
        return Plugin.resume();
    };

    /**
     * Get current step number
     */
    the.getStep = function() {
        return the.currentStep;
    };

    /**
     * Get new step number
     */
    the.getNewStep = function() {
        return Plugin.getNewStep();
    };

    /**
     * Set new step number
     */
    the.setNewStep = function(number) {
        Plugin.setNewStep(number);
    };

    /**
     * Check last step
     */
    the.isLastStep = function() {
        return Plugin.isLastStep();
    };

    /**
     * Check first step
     */
    the.isFirstStep = function() {
        return Plugin.isFirstStep();
    };

    /**
     * Attach event("change", "changed", "submit")
     */
    the.on = function(name, handler) {
        return Plugin.addEvent(name, handler);
    };

    /**
     * Attach event that will be fired once
     */
    the.one = function(name, handler) {
        return Plugin.addEvent(name, handler, true);
    };

    // Construct plugin
    Plugin.construct.apply(the, [options]);

    return the;
};

if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = KTWizard;
}
