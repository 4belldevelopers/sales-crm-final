"use strict";
var KTCreateAccount = (function () {
    var e,
        t,
        i,
        o,
        r,
        s,
        a = [];
    return {
        init: function () {
            (t = document.querySelector("#kt_modal_create_account")) && (e = new bootstrap.Modal(t)),
                (i = document.querySelector("#kt_create_account_stepper")) &&
                ((o = i.querySelector("#kt_create_account_form")),
                    (r = i.querySelector('[data-kt-stepper-action="submit"]')),
                    (s = new KTStepper(i)).on("kt.stepper.next", function (e) {
                        console.log("stepper.next");
                        var t = a[e.getCurrentStepIndex() - 1];
                        t
                            ? t.validate().then(function (t) {
                                console.log("validated!"),
                                    "Valid" == t
                                        ? (e.goNext(), KTUtil.scrollTop())
                                        : Swal.fire({
                                            text: "Sorry, looks like there are some errors detected, please try again.",
                                            icon: "error",
                                            buttonsStyling: !1,
                                            confirmButtonText: "Ok, got it!",
                                            customClass:
                                            {
                                                confirmButton: "btn btn-light"
                                            },
                                        }).then(function () {
                                            KTUtil.scrollTop();
                                        });
                            })
                            : (e.goNext(), KTUtil.scrollTop());
                    }),
                    s.on("kt.stepper.previous", function (e) {
                        console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop();
                    }),
                    a.push(
                        FormValidation.formValidation(o, {
                            fields:
                            {
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
                                            message: "The value is not a valid email address"
                                        }
                                    }
                                },

                                password:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Please enter password"
                                        },
                                        stringLength:
                                        {
                                            min: 8,
                                            max: 25,
                                            message: 'Use 8 or more characters with a mix of letters, numbers & symbols.'
                                        }

                                    }
                                },
                                confirm_password:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Please enter confirm password"
                                        },
                                        stringLength:
                                        {
                                            min: 8,
                                            max: 25,
                                            message: 'The password and its confirm are not the same'
                                        }
                                    }
                                },
                            },
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap: new FormValidation.plugins.Bootstrap5({
                                    rowSelector: '.fv-row',
                                    eleInvalidClass: '',
                                    eleValidClass: ''
                                })
                            }
                        })
                    ),
                    a.push(
                        FormValidation.formValidation(o, {
                            fields:
                            {
                                company_name:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Company name is required"
                                        }
                                    }
                                },
                                team_size:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Team size is required"
                                        }
                                    }
                                },
                                company_type:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Company type is required"
                                        }
                                    }
                                },
                            },
                            plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                        })
                    ),
                    a.push(
                        FormValidation.formValidation(o, {
                            fields:
                            {
                                website:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Website URL is required"
                                        }
                                    }
                                },
                                specialties:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Specialties is required"
                                        }
                                    }
                                },
                                contact_number:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Contact Number is required"
                                        },
                                        digits:
                                        {
                                            message: "Contact Number must contain only digits"
                                        },
                                        stringLength:
                                        {
                                            min: 10,
                                            max: 10,
                                            message: "Contact Number must contain 10 digits only"
                                        }
                                    }
                                },
                            },
                            plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                        })
                    ),
                    a.push(
                        FormValidation.formValidation(o, {
                            fields:
                            {
                                subscription:
                                {
                                    validators:
                                    {
                                        notEmpty:
                                        {
                                            message: "Subscription is required"
                                        }
                                    }
                                }
                            },
                            plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                        })
                    ),
                    r.addEventListener("click", function (t) {
                        t.preventDefault(),
                            (r.disabled = !0),
                            r.setAttribute("data-kt-indicator", "on"),
                            setTimeout(function (){
                            $.ajax(
                                {
                                    url: "{{route('company-register')}}",
                                    container: '#createForm',
                                    type: "POST",
                                    data: $('#createForm').serialize(),
                                    success: function(response) {
                                        $('#createForm').trigger("reset");
                                        swal("Sent!", "Your account has been created successfully.\n Check your email to activate your account.", "success");
                                    }
                                    // ,
                                    // error: function (response) {
                                    //     swal("Error!", "Your need to fill all the form fields!", "error");
                                    // }
                                }),                                                
                                    r.removeAttribute("data-kt-indicator"),
                                        (r.disabled = !1),
                                        Swal.fire({ text: "Form has been successfully submitted!", icon: "success", buttonsStyling: !1, confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-primary" } }).then(function (t) {
                                            t.isConfirmed && e && e.hide();
                                        });
                                }, 2e3);
                            }),
                    $(o.querySelector('[name="card_expiry_month"]')).on("change", function () {
                        a[3].revalidateField("card_expiry_month");
                    }),
                    $(o.querySelector('[name="card_expiry_year"]')).on("change", function () {
                        a[3].revalidateField("card_expiry_year");
                    }),
                    $(o.querySelector('[name="business_type"]')).on("change", function () {
                        a[2].revalidateField("business_type");
                    }));
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTCreateAccount.init();
});
