"use strict";

// Class definition
var KTWizard1 = function () {
    // Base elements
    var _wizardEl;
    var _formEl;
    var _wizardObj;
    var _validations = [];

    // Private functions
    var _initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        // Step 1
        _validations.push(FormValidation.formValidation(
            _formEl,
            {
                fields:
                {
                    company_name:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Company Name is required'
                            }
                        }
                    },
                    company_website:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Website is required'
                            }
                        }
                    },
                    company_email:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Email is required"
                            }
                        }
                    },
                    company_phone:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Phone number is required"
                            },
                            digits:
                            {
                                message: "Phone number must contain only digits"
                            },
                            stringLength:
                            {
                                min: 10,
                                max: 10,
                                message: "Phone number must contain 10 digits only"
                            }
                        }
                    },
                    show_in_frontend:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "show in frontend is required"
                            }
                        }
                    },
                    industry:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Industry is required'
                            }
                        }
                    },
                    company_sizes:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Company Size is required"
                            },
                            digits:
                            {
                                message: "Company Size must contain only digits"
                            }
                        }
                    },
                    company_type:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Company Type is required"
                            }
                        }
                    },
                    founded:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Founded Year is required"
                            },
                            digits:
                            {
                                message: "The Value is not a valid digits"
                            },
                            stringLength:
                            {
                                min: 4,
                                max: 4,
                                message: "Founded Year must contain 4 digits only"
                            }
                        }
                    },
                    full_name:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Name is required"
                            }
                        }
                    },
                    email:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Email is required"
                            },
                            emailAddress:
                            {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    password:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Password is required"
                            }
                        }
                    },
                    start_date:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Start date is required"
                            }
                        }
                    },
                    end_date:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "End date is required"
                            }
                        }
                    }
                },
                plugins:
                {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap(
                    {
                        //eleInvalidClass: '',
                        eleValidClass: '',
                    })
                }
            }
        ));

        // Step 2
        _validations.push(FormValidation.formValidation(
            _formEl,
            {
                fields:
                {
                    tinymce:
                    {
                        validators:
                        {
                            callback:
                            {
                                message: 'The Job Description must be between 5 and 1500 characters long',callback: function (value)
                                {
                                    const text = tinyMCE.activeEditor.getContent(
                                    {
                                        format: 'text', 
                                    });
                                    return text.length <= 1500 && text.length >= 5;
                                },
                            },
                        },
                    },
                    tags:
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Tags is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap({
                        //eleInvalidClass: '',
                        eleValidClass: '',
                    })
                }
            }
        ));

        // Step 3
        _validations.push(FormValidation.formValidation(
            _formEl,
            {
                fields:
                {
                    'address[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Address is required"
                            }
                        }
                    },
                    'country[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "country is required"
                            }
                        }
                    },
                    'state[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "State is required"
                            }
                        }
                    },
                    'postcode[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Postcode is required"
                            },
                            digits:
                            {
                                message: "Postcode must contain only digits"
                            },
                            stringLength:
                            {
                                min: 6,
                                max: 6,
                                message: "Postcode must contain 6 digits only"
                            }
                        }
                    }
                },
                plugins:
                {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap(
                    {
                        //eleInvalidClass: '',
                        eleValidClass: '',
                    })
                }
            }
        ));

        // Step 4
        _validations.push(FormValidation.formValidation(
            _formEl,
            {
                fields: {
                    'cp_name[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Contact person name is required"
                            }
                        }
                    },
                    'cp_email[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Email is required'
                            },
                            emailAddress:
                            {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    'cp_number[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Contact Number is required"
                            },
                            digits:
                            {
                                message: "Contact must contain only digits"
                            },
                            stringLength:
                            {
                                min: 10,
                                max: 10,
                                message: "Contact must contain 10 digits only"
                            }
                        }
                    },
                    'cp_desigination[]':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Desigination is required"
                            }
                        }
                    }
                },
                plugins:
                {
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap(
                    {
                        //eleInvalidClass: '',
                        eleValidClass: '',
                    })
                }
            }
        ));
    }

    var _initWizard = function () {
        // Initialize form wizard
        _wizardObj = new KTWizard(_wizardEl, {
            startStep: 1, // initial active step number
            clickableSteps: false  // allow step clicking
        });

        // Validation before going to next page
        _wizardObj.on('change', function (wizard) {
            if (wizard.getStep() > wizard.getNewStep()) {
                return; // Skip if stepped back
            }

            // Validate form before change wizard step
            var validator = _validations[wizard.getStep() - 1]; // get validator for currnt step

            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        wizard.goTo(wizard.getNewStep());

                        KTUtil.scrollTop();
                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there are some errors detected, please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn font-weight-bold btn-light"
                            }
                        }).then(function () {
                            KTUtil.scrollTop();
                        });
                    }
                });
            }

            return false;  // Do not change wizard step, further action will be handled by he validator
        });

        // Change event
        _wizardObj.on('changed', function (wizard) {
            KTUtil.scrollTop();
        });

        // Submit event
        _wizardObj.on('submit', function (wizard) {
            Swal.fire({
                text: "All is good! Please confirm the form submission.",
                icon: "success",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, submit!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn font-weight-bold btn-primary",
                    cancelButton: "btn font-weight-bold btn-default"
                }
            }).then(function (result) {
                if (result.value) {
                    _formEl.submit(); // Submit form
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been submitted!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-primary",
                        }
                    });
                }
            });
        });
    }

    return {
        // public functions
        init: function () {
            _wizardEl = KTUtil.getById('kt_wizard');
            _formEl = KTUtil.getById('kt_form');

            _initValidation();
            _initWizard();
        }
    };
}();

jQuery(document).ready(function () {
    KTWizard1.init();
});
