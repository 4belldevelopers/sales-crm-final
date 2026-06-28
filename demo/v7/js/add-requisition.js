"use strict";

// Class definition
var KTContactsAdd = function () {
	// Base elements
	var _wizardEl;
	var _formEl;
	var _wizardObj;
	var _avatar;
	var _validations = [];

	// Private functions
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

						VTUtil.scrollTop();
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
							VTUtil.scrollTop();
						});
					}
				});
			}

			return false;  // Do not change wizard step, further action will be handled by he validator
		});

		// Change event
		_wizardObj.on('changed', function (wizard) {
			VTUtil.scrollTop();
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

	var _initValidation = function () {
		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

		// Step 1
		_validations.push(FormValidation.formValidation(
			_formEl,
			{
				fields: {
					client_id:
					{
						validators:
						{
							notEmpty:
							{
								message:"Product Company Name is required"
							}
						}
					},
					/**
					 * kt_select2_3:
					 * {
						*validators:
						* {
							* notEmpty:
							* {
								* message: "Location is required"
							* }
						* }
					 * },**/
					address_id:
					{
						validators:
						{
							notEmpty:
							{
								message: "Job Address is required"
							}
						}
					},
					kt_tagify_work_location:
					{
						validators:
						{
							notEmpty:
							{
								message: "Job location of the candidate is required"
							}
						}
					},
					qualification:
					{
						validators:
						{
							notEmpty:
							{
								message: "Job qualification is required"
							}
						}
					},
					param:
					{
						validators:
						{
							notEmpty:
							{
								message: "Work Location for candidate is required"
							}
						}
					},
					work_location:
					{
						validators:
						{
							notEmpty:
							{
								message: "Work Location for candidate is required"
							}
						}
					},
					work_from_home:
					{
						validators:
						{
							notEmpty:
							{
								message: "Work from home is required"
							}
						}
					},
					show_client_name:
					{
						validators:
						{
							notEmpty:
							{
								message: "Show client name to vendors is required"
							}
						}
					},
					title:
					{
						validators:
						{
							notEmpty:
							{
								message: "Job Title is required"
							}
						}
					},
					total_positions:
					{
						validators:
						{
							notEmpty:
							{
								message: "Total  Openings is required"
							},
							digits:
							{
								message: "Total Openings must contain only digits"
							},
							stringLength:
							{
								min: 5,
								max: 7
							}
						}
					},
					category_id:
					{
						validators:
						{
							notEmpty:
							{
								message: "Job Department is required"
							}
						}
					},
					employment_type:
					{
						validators:
						{
							notEmpty:
							{
								message: "Position Type is required"
							}
						}
					},
					min_exp:
					{
						validators:
						{
							notEmpty:
							{
								message: "Minimum Experience is required"
							}
						}
					},
					max_exp:
					{
						validators:
						{
							notEmpty:
							{
								message: "Maximum Experience is required"
							}
						}
					},
					minimum_salary:
					{
						validators:
						{
							notEmpty:
							{
								message: "Min Salary is required"
							},
							digits:
							{
								message: "Minimum Salary must contain only digits"
							},
							stringLength:
							{
								min: 8,
								max: 9
							}
						}
					},
					maximum_salary:
					{
						validators:
						{
							notEmpty:
							{
								message: "Max Salary is required"
							},
							digits:
							{
								message: "Maximum Salary must contain only digits"
							},
							stringLength:
							{
								min: 8,
								max: 9
							}
						}
					},
					end_date:
					{
						validators:
						{
							notEmpty:
							{
								message: "Due Date is required"
							}
						}
					},
					hiring_namager:
					{
						validators:
						{
							notEmpty:
							{
								message: "Contacts Person & Hiring Manager is required"
							}
						}
					},
					recruiter:
					{
						validators:
						{
							notEmpty:
							{
								message: "Recruiter is required"
							}
						}
					}
				},

				plugins: {
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
					bootstrap: new FormValidation.plugins.Bootstrap(
					{
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
					address1:
					{
						validators:
						{
							notEmpty:
							{
								message: 'Address is required'
							}
						}
					},
					postcode:
					{
						validators:
						{
							notEmpty:
							{
								message: 'Postcode is required'
							}
						}
					},
					city:
					{
						validators:
						{
							notEmpty:
							{
								message: 'City is required'
							}
						}
					},
					state:
					{
						validators:
						{
							notEmpty:
							{
								message: 'state is required'
							}
						}
					},
					country:
					{
						validators:
						{
							notEmpty:
							{
								message:
								'Country is required'
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
	}

	var _initAvatar = function () {
		_avatar = new KTImageInput('kt_contact_add_avatar');
	}

	return {
		// public functions
		init: function () {
			_wizardEl = VTUtil.getById('kt_contact_add');
			_formEl = VTUtil.getById('kt_contact_add_form');

			_initWizard();
			_initValidation();
			_initAvatar();
		}
	};
}();

jQuery(document).ready(function () {
	KTContactsAdd.init();
});
