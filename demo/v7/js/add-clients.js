"use strict";

// Class definition
var KTWizard1 = function() {
    // Base elements
    var _wizardEl;
    var _formEl;
    var _wizardObj;
    var _validations = [];

    // Resolve the correct Bootstrap plugin for the FormValidation version loaded (Bootstrap5 in Metronic v7).
    function _bootstrapPlugin() {
        if (typeof FormValidation !== 'undefined' && FormValidation.plugins) {
            if (typeof FormValidation.plugins.Bootstrap5 === 'function') {
                return new FormValidation.plugins.Bootstrap5({ eleValidClass: '' });
            }
            if (typeof FormValidation.plugins.Bootstrap === 'function') {
                return new FormValidation.plugins.Bootstrap({ eleValidClass: '' });
            }
        }
        return null;
    }

    function _pluginSet() {
        var bp = _bootstrapPlugin();
        var set = { trigger: new FormValidation.plugins.Trigger() };
        if (bp) set.bootstrapPlugin = bp;
        return set;
    }

    var _initValidation = function() {

        // Step 1 — Client Information
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: { message: 'Client Name is required' }
                        }
                    },
                    website: {
                        validators: {
                            notEmpty: { message: 'Website is required' }
                        }
                    },
                    industry: {
                        validators: {
                            notEmpty: { message: 'Industry is required' }
                        }
                    },
                    company_sizes: {
                        validators: {
                            notEmpty: { message: 'Company Size is required' },
                            digits:   { message: 'Company Size must contain only digits' }
                        }
                    },
                    company_type: {
                        validators: {
                            notEmpty: { message: 'Company Type is required' }
                        }
                    },
                    founded: {
                        validators: {
                            notEmpty:     { message: 'Founded Year is required' },
                            digits:       { message: 'Founded Year must be a number' },
                            stringLength: { min: 4, max: 4, message: 'Founded Year must be 4 digits' }
                        }
                    }
                },
                plugins: _pluginSet()
            }
        ));

        // Step 2 — Client Description (description & tags are optional; validate only if filled)
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    kt_docs_tinymce_hidden: {
                        validators: {
                            callback: {
                                message: 'Description must be at least 5 characters if provided',
                                callback: function() {
                                    var editor = (typeof tinymce !== 'undefined') ? tinymce.get('kt_docs_tinymce_hidden') : null;
                                    if (!editor) return true;
                                    var text = editor.getContent({ format: 'text' }).trim();
                                    return text.length === 0 || text.length >= 5;
                                }
                            }
                        }
                    }
                },
                plugins: _pluginSet()
            }
        ));

        // Step 3 — Address Details
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    'address[]': {
                        validators: {
                            notEmpty: { message: 'Address is required' }
                        }
                    },
                    'country[]': {
                        validators: {
                            notEmpty: { message: 'Country is required' }
                        }
                    },
                    'state[]': {
                        validators: {
                            notEmpty: { message: 'State is required' }
                        }
                    },
                    'postcode[]': {
                        validators: {
                            notEmpty:     { message: 'Postcode is required' },
                            digits:       { message: 'Postcode must contain only digits' },
                            stringLength: { min: 6, max: 6, message: 'Postcode must be 6 digits' }
                        }
                    }
                },
                plugins: _pluginSet()
            }
        ));

        // Step 4 — Contact Person
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    'cp_name[]': {
                        validators: {
                            notEmpty: { message: 'Contact person name is required' }
                        }
                    },
                    'cp_email[]': {
                        validators: {
                            notEmpty:     { message: 'Email is required' },
                            emailAddress: { message: 'Please enter a valid email address' }
                        }
                    },
                    'cp_number[]': {
                        validators: {
                            digits:       { message: 'Contact number must contain only digits' },
                            stringLength: { min: 10, max: 10, message: 'Contact number must be 10 digits' }
                        }
                    },
                    'cp_desigination[]': {
                        validators: {
                            notEmpty: { message: 'Designation is required' }
                        }
                    }
                },
                plugins: _pluginSet()
            }
        ));
    };

    var _initWizard = function() {
        _wizardObj = new KTWizard(_wizardEl, {
            startStep: 1,
            clickableSteps: false
        });

        // Validate current step before moving forward
        _wizardObj.on('change', function(wizard) {
            if (wizard.getStep() > wizard.getNewStep()) {
                return; // going back — skip validation
            }

            var currentStep  = wizard.getStep();
            var getStepcheck = true;

            // Step 3: custom address/city checks (FormValidation handles field-level, this catches empty tags)
            if (currentStep === 3) {
                var addressInputs = document.querySelectorAll('textarea[name="address[]"], input[name="address[]"]');
                addressInputs.forEach(function(input) {
                    if (input.value.trim() === '') {
                        getStepcheck = false;
                        Swal.fire({ text: 'Address is required for all entries.', icon: 'warning', confirmButtonText: 'Ok' });
                    }
                });

                if (getStepcheck) {
                    var cityInputs = document.querySelectorAll('input[name="kt_tagify_work_location[]"]');
                    cityInputs.forEach(function(input) {
                        if (input.value.trim() === '') {
                            getStepcheck = false;
                            Swal.fire({ text: 'City is required for all address entries.', icon: 'warning', confirmButtonText: 'Ok' });
                        }
                    });
                }
            }

            // Step 4: custom contact checks
            if (currentStep === 4) {
                var nameInputs = document.querySelectorAll('input[name="cp_name[]"]');
                nameInputs.forEach(function(input) {
                    if (input.value.trim() === '') {
                        getStepcheck = false;
                    }
                });

                if (!getStepcheck) {
                    Swal.fire({ text: 'Contact person name is required.', icon: 'warning', confirmButtonText: 'Ok' });
                }

                if (getStepcheck) {
                    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    document.querySelectorAll('input[name="cp_email[]"]').forEach(function(input) {
                        var v = input.value.trim();
                        if (v && !emailPattern.test(v)) {
                            input.classList.add('is-invalid');
                            getStepcheck = false;
                            Swal.fire({ text: 'Please enter a valid email: ' + v, icon: 'warning', confirmButtonText: 'Ok' });
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                }

                if (getStepcheck) {
                    document.querySelectorAll('input[name="cp_number[]"]').forEach(function(input) {
                        var v = input.value.trim();
                        if (v && (v.length !== 10 || isNaN(v))) {
                            input.classList.add('is-invalid');
                            getStepcheck = false;
                            Swal.fire({ text: 'Contact number must be 10 digits: ' + v, icon: 'warning', confirmButtonText: 'Ok' });
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                }
            }

            if (!getStepcheck) {
                return false;
            }

            var validator = _validations[currentStep - 1];
            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                        wizard.goTo(wizard.getNewStep());
                        KTUtil.scrollTop();
                    } else {
                        Swal.fire({
                            text: 'Please fill in all required fields before continuing.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: { confirmButton: 'btn font-weight-bold btn-light' }
                        }).then(function() {
                            KTUtil.scrollTop();
                        });
                    }
                });
            } else {
                // No validator for this step — go straight through
                wizard.goTo(wizard.getNewStep());
                KTUtil.scrollTop();
            }

            return false; // always prevent default step change; handled in callback above
        });

        _wizardObj.on('changed', function() {
            KTUtil.scrollTop();
        });

        _wizardObj.on('submit', function() {
            Swal.fire({
                text: 'All is good! Please confirm the form submission.',
                icon: 'success',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Yes, submit!',
                cancelButtonText: 'No, cancel',
                customClass: {
                    confirmButton: 'btn font-weight-bold btn-primary',
                    cancelButton:  'btn font-weight-bold btn-default'
                }
            }).then(function(result) {
                if (result.value) {
                    _formEl.submit();
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: 'Your form has not been submitted!',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: { confirmButton: 'btn font-weight-bold btn-primary' }
                    });
                }
            });
        });
    };

    return {
        init: function() {
            _wizardEl = KTUtil.getById('kt_wizard');
            _formEl   = KTUtil.getById('kt_form');

            if (!_wizardEl || !_formEl) {
                console.error('KTWizard1: #kt_wizard or #kt_form element not found.');
                return;
            }

            try {
                _initValidation();
            } catch (e) {
                console.error('KTWizard1: FormValidation init failed —', e);
            }

            try {
                _initWizard();
            } catch (e) {
                console.error('KTWizard1: KTWizard init failed —', e);
            }
        }
    };
}();

jQuery(document).ready(function() {
    KTWizard1.init();
});
