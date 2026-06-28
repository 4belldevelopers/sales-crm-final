"use strict";

// Class definition
var KTAddCandidate = function () {
    // Elements
    var form;
	var updateButton;
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
                            notEmpty:
                            {
                                message: "Resume is required"
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
                            notEmpty:
                            {
                                message: "Current company is required"
                            }
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
					
                    'secondary_mobile':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Secondary number is required"
                            },
                            digits:
                            {
                                message: "Secondary number must contain only digits"
                            },
                            stringLength:
                            {
                                min: 10,
                                max: 10,
                                message: "Secondary number must contain 10 digits only"
                            }
                        }
                    },
					
                    'dob':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Date of Birth is required"
                            }
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
                            emailAddress:
                            {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
					
                    'secondrary_email':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Secondrary email is required'
                            },
                            emailAddress:
                            {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
					
                    'qualification':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: 'Qualification Name is required'
                            }
                        }
                    },
					
                    'total_exp':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message:"Total Experience is required"
                            },
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
                            notEmpty:
                            {
                                message:"Relevant Experience is required"
                            },
                            digits:
                            {
                                message: "Relevant Experience must contain only digits"
                            }
                        }
                    },
					
                    'ddl_np_id':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message:"Serving notice period is required"
                            }
                        }
                    },
					
                    'ctc':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Current CTC is required"
                            }
                        }
                    },
					
                    'ectc':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Expected CTC is required"
                            }
                        }
                    },
					
                    'percentage_hike':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "Hike is required"
                            },
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
                            notEmpty:
                            {
                                message: "Country is required"
                            }
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            notEmpty:
                            {
                                message: "State is required"
                            }
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
                            notEmpty:
                            {
                                message: "Pincode is required"
                            },
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
                            notEmpty:
                            {
                                message: 'Skills is required'
                            }
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

        // Handle form update		
		updateButton.addEventListener('click', function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    updateButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click 
                    updateButton.disabled = true;


                    // Simulate ajax request
                    setTimeout(function () {
                        // Hide loading indication
                        updateButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        updateButton.disabled = true;

                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
									text				: "You have successfully Submitted",
									icon				: "success",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
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
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
								text				: "Sorry, looks like there are some errors detected, please try again.",
								icon				: "error",
								buttonsStyling		: false,
								confirmButtonText	: "Ok, got it!",
								customClass			: {
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
            updateButton 	=	document.querySelector('#update-form');
            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
});