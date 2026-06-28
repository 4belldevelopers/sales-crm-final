"use strict";

var KTAddCandidate = function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function (e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'upload_cv':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Upload CV is required"
                            }
                        }
                    },
                    'full_name':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Full name is required"
                            }
                        }
                    },
                    'contact_number':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Contact number is required"
                            },
                            digits:
                            {
                                message: "Contact number must contains digits only"
                            },
                            stringLength:
                            {
                                min: 10,
                                max: 10,
                                message: "Contact number must contains 10 digits only"
                            }
                        }
                    },
                    'email':
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
                    'designation':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Designation is required"
                            }
                        }
                    },
                    'experience':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Experience is required"
                            },
                            digits:
                            {
                                message: "Experience must contains digits only"
                            },
                            stringLength:
                            {
                                min: 1,
                                max: 3,
                                message: "experience must contains digits only"
                            }
                        }
                    },
                    'current_ctc':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Current CTC is required"
                            },
                            digits:
                            {
                                message: "Current CTC must contains digits only"
                            },
                            stringLength:
                            {
                                message: "Current CTC must contain digits only"
                            }
                        }
                    },
                    'notice_period':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Notice period is required"
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            }
        );

        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click 
                    submitButton.disabled = true;


                    // Simulate ajax request
                    setTimeout(function () {
                        // Hide loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = true;

                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            text: "You have successfully Applied for this Job",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (isConfirm) {
                            if (isConfirm)
                            {
                                form.submit();
                            }

                            // .then(function (result)
                            // {
                            //     if (result.isConfirmed)
                            //     {
                            //         form.querySelector('[name="email"]').value = "";
                            //     }
                            // }

                        });
                    }, 2000);
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    }

    // Public functions
    return {
        // Initialization
        init: function () {
            form = document.querySelector('#applicantForm');
            submitButton = document.querySelector('#submit-form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});