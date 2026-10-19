"use strict";

// Class definition
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
                    
					'resume':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Resume is required"
                            // }
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
					
                    'current_designation':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Current designation is required"
                            }
                        }
                    },
					
                    'current_company':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Current company is required"
                            // }
                        }
                    },
					
                    'phone':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Primary number is required"
                            },
                            digits:
                            {
                                message: "Primary number must contains only digits"
                            },
                            stringLength:
                            {
                                min: 10,
                                max: 10,
                                message: "Primary number must contains 10 digits only"
                            }
                        }
                    },
					
                    'dob':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Date of Birth is required"
                            // }
                        }
                    },
					
                    'email':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Primary email is required'
                            },
                            callback: {
                                message: 'The value is not a valid email address',
                                callback: function(input) {
                                    var val = (input.value || '').trim();
                                    if (!val) return false;
                                    if (window.JeroEmailBoxEngine) {
                                        var check = window.JeroEmailBoxEngine.validateSyntax(val);
                                        if (!check.isValid) {
                                            return {
                                                valid: false,
                                                message: check.error || 'The value is not a valid email address'
                                            };
                                        }
                                        return true;
                                    }
                                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                                }
                            },
                            stringLength: {
                                max: 254,
                                message: 'Email must not exceed 254 characters'
                            }
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: 'Qualification Name is required'
                            // }
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message:"Total Experience is required"
                            // },
                            digits:
                            {
                                message: "Total Experience must contain only digits"
                            }
                        }
                    },
					
                    'rel_exp':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message:"Relevant Experience is required"
                            // },
                            digits:
                            {
                                message: "Relevant Experience must contain only digits"
                            }
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Current CTC is required"
                            // }
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Exp. CTC is required"
                            // }
                        }
                    },
					
                    'percentage_hike':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Hike is required"
                            // },
                            digits:
                            {
                                message: "Hike must contain digits"
                            },
                            stringLength:
                            {
                                min: 1,
                                max: 2,
                                message: "Hike contain 2 digit"
                            }
                        }
                    },
					
                    'country':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Country is required"
                            // }
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "State is required"
                            // }
                        }
                    },
					
                   /* 'city':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "City is required"
                            }
                        }
                    },*/
					
                    'pincode':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: "Pincode is required"
                            // },
                            digits:
                            {
                                message: "Pincode must contain digits"
                            },
                            stringLength:
                            {
                                min: 6,
                                max: 6,
                                message: "Pincode contain 6 digit only"
                            }
                        }    
                    },                    
                    'skills':
                    {
                        validators:
                        {
                            // notEmpty:
                            // {
                            //     message: 'Skills is required'
                            // }
                        }
                    },
                    'specialties':
                    {
                        validators:
                        { notEmpty:
                            {
                                message: "Specialties is required"
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
                            text: "You have successfully Submitted",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.querySelector('[name="email1"]').value = "";
                                form.querySelector('[name="password1"]').value = "";
                                //form.submit(); // submit form
                            }
                        });
                    }, 2000);
                } 
				else 
				{
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
            form 			=	document.querySelector('#createForm');
            submitButton 	=	document.querySelector('#save-form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});