"use strict";

// Class definition
var KTWizard1 = function() {
    // Base elements
    var _wizardEl;
    var _formEl;
    var _wizardObj;
    var _validations = [];

    // Private functions
    var _initValidation = function() {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        // Step 1
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: 'Client Name is required'
                            }
                        }
                    },
                    logo: {
                        validators: {
                            file: {
                                maxSize: 2097152, // 5 MB in bytes
                                message: "The file size must not exceed 5 MB"
                            }
                        }
                    },
                    website: {
                        validators: {
                            notEmpty: {
                                message: 'Website is required'
                            }
                        }
                    },
                    industry: {
                        validators: {
                            notEmpty: {
                                message: 'Industry is required'
                            }
                        }
                    },
                    company_sizes: {
                        validators: {
                            notEmpty: {
                                message: "Company Size is required"
                            },
                            digits: {
                                message: "Company Size must contain only digits"
                            }
                        }
                    },
                    company_type: {
                        validators: {
                            notEmpty: {
                                message: "Company Type is required"
                            }
                        }
                    },
                    founded: {
                        validators: {
                            notEmpty: {
                                message: "Founded Year is required"
                            },
                            digits: {
                                message: "The Value is not a valid digits"
                            },
                            stringLength: {
                                min: 4,
                                max: 4,
                                message: "Founded Year must contain 4 digits only"
                            }
                        }
                    },
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

        // Step 2
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    kt_docs_tinymce_hidden: {
                        validators: {
                            callback: {
                                message: 'The Description must be between 5 and 1500 characters long',
                                callback: function(value) {
                                    const text = tinyMCE.activeEditor.getContent({
                                        format: 'text',
                                    });
                                    return text.length <= 9999999999999999000500 && text.length >= 5;
                                },
                            },
                        },
                    },
                    tags: {
                        validators: {
                            notEmpty: {
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
            _formEl, {
                fields: {
                    'address[]': {
                        validators: {
                            notEmpty: {
                                message: "Address is required"
                            }
                        }
                    },
                    'country[]': {
                        validators: {
                            notEmpty: {
                                message: "country is required"
                            }
                        }
                    },
                    'state[]': {
                        validators: {
                            notEmpty: {
                                message: "State is required"
                            }
                        }
                    },
                    'postcode[]': {
                        validators: {
                            notEmpty: {
                                message: "Postcode is required"
                            },
                            digits: {
                                message: "Postcode must contain only digits"
                            },
                            stringLength: {
                                min: 6,
                                max: 6,
                                message: "Postcode must contain 6 digits only"
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

        // Step 4
        _validations.push(FormValidation.formValidation(
            _formEl, {
                fields: {
                    'cp_name[]': {
                        validators: {
                            notEmpty: {
                                message: "Contact person name is required"
                            }
                        }
                    },
                    'cp_email[]': {
                        validators: {
                            notEmpty: {
                                message: 'Email is required'
                            },
                            emailAddress: {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    'cp_number[]': {
                        validators: {
                            digits: {
                                message: "Contact must contain only digits"
                            },
                            stringLength: {
                                min: 10,
                                max: 10,
                                message: "Contact must contain 10 digits only"
                            }
                        }
                    },
                    'cp_desigination[]': {
                        validators: {
                            notEmpty: {
                                message: "Desigination is required"
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
    }

    var _initWizard = function() {
        // Initialize form wizard
        _wizardObj = new KTWizard(_wizardEl, {
            startStep: 1, // initial active step number
            clickableSteps: false // allow step clicking
        });

        // Validation before going to next page
        _wizardObj.on('change', function(wizard) {
            if (wizard.getStep() > wizard.getNewStep()) {
                return; // Skip if stepped back
            }
            var getStepcheck = true;
            if (wizard.getStep() == 3) {

                let addressInputs = document.querySelectorAll('input[name="address[]"]');
                let validaddress = true;
                addressInputs.forEach((input) => {
                    let addressValue = input.value.trim();
                    if (addressValue === "") {
                        validaddress = false;
                        alert("Address is required");
                    }
                });
                if (validaddress) {
                    getStepcheck = true;
                } else {
                    getStepcheck = false;
                    // alert("Please enter valid 10-digit contact numbers.");
                }

                let cityInputs = document.querySelectorAll('input[name="kt_tagify_work_location[]"]');
                let validcity = true;
                cityInputs.forEach((input) => {
                    let cityValue = input.value.trim();
                    if (cityValue === "") {
                        validcity = false;
                        alert("City name is required");
                    }
                });
                if (validcity) {
                    getStepcheck = true;
                } else {
                    getStepcheck = false;
                    // alert("Please enter valid 10-digit contact numbers.");
                }




            }
            if (wizard.getStep() == 4) {

                let nameInputs = document.querySelectorAll('input[name="cp_name[]"]');
                let validname = true;
                nameInputs.forEach((input) => {
                    let nameValue = input.value.trim();
                    if (nameValue === "") {
                        validname = false;
                        alert("Contact person name is required");
                    }
                });
                if (validname) {
                    let emailInputs = document.querySelectorAll('input[name="cp_email[]"]');
                    let valid = true;

                    emailInputs.forEach((input) => {
                        // Basic email validation regex
                        let emailValue = input.value.trim();
                        let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (emailValue === "") {

                        } else {
                            if (!emailPattern.test(input.value)) {
                                input.classList.add("is-invalid"); // Highlight invalid input
                                valid = false;
                                alert("This Email " + emailValue + " Please enter valid emails.");
                            } else {
                                input.classList.remove("is-invalid"); // Remove highlight if valid
                                input.classList.add("is-valid");
                            }
                        }
                    });

                    if (valid) {
                        getStepcheck = true;
                        let numberInputs = document.querySelectorAll('input[name="cp_number[]"]');
                        let validNo = true;

                        numberInputs.forEach((input) => {
                            // Check if the input value is a 10-digit number
                            let numberValue = input.value.trim();
                            if (numberValue === "") {

                            } else {
                                if (numberValue.length !== 10 || isNaN(numberValue)) {
                                    input.classList.add("is-invalid"); // Highlight invalid input
                                    validNo = false;
                                    alert("This contact number " + numberValue + " Please enter valid 10-digit contact numbers");
                                } else {
                                    input.classList.remove("is-invalid"); // Remove highlight if valid
                                    input.classList.add("is-valid");
                                }
                            }

                        });

                        if (validNo) {
                            getStepcheck = true;
                            // alert("All contact numbers are valid!");
                            // Submit the form if needed
                            // this.submit();
                        } else {
                            getStepcheck = false;
                            // alert("Please enter valid 10-digit contact numbers.");
                        }

                    } else {
                        getStepcheck = false;
                        // alert("Please enter valid emails.");
                    }
                } else {
                    getStepcheck = false;
                }


                // alert(wizard.getStep());

            }

            // Validate form before change wizard step
            var validator = _validations[wizard.getStep() - 1]; // get validator for currnt step


            if (getStepcheck) {
                if (validator) {
                    validator.validate().then(function(status) {



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
                            }).then(function() {
                                KTUtil.scrollTop();
                            });
                        }


                    });
                }
            }

            return false; // Do not change wizard step, further action will be handled by he validator
        });

        // Change event
        _wizardObj.on('changed', function(wizard) {
            KTUtil.scrollTop();
        });

        // Submit event
        _wizardObj.on('submit', function(wizard) {
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
            }).then(function(result) {
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
        init: function() {
            _wizardEl = KTUtil.getById('kt_wizard');
            _formEl = KTUtil.getById('kt_form');

            _initValidation();
            _initWizard();
        }
    };
}();

jQuery(document).ready(function() {
    KTWizard1.init();
});