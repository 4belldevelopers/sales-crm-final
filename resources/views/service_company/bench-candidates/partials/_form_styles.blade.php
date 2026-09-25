{{-- Phase 8 Frontend Optimization: Modular Form Styles Partial --}}
<style>
body { overflow-x: hidden; }
.dropzone { padding: 1.8rem 1.75rem !important; border: 2px dashed #e6e7e8 !important; background: transparent !important; text-align: inherit !important; }
.font-size-icon { font-size: 1.3em; }
.dashed-border-top { border-top: 1px dashed gray; }
.form { clear: both; }
.form--disabled input, .form--disabled textarea, .form--disabled select { padding-left: 0; padding-top: 4px; border: 0 solid #efefef; background: white; height: auto; transition: all 0.2s ease-in-out; font-size: 1.05rem; font-weight: 500; color: #3f4254; text-transform: capitalize; }
.enabled_input { display: block; width: 100%; border-radius: 2px; border: 1px solid #efefef; outline: none; -webkit-appearance: none; background-color: #f5f8fa; border-color: #f5f8fa; color: #5e6278; padding: 0.5em; transition: color 0.2s ease, background-color 0.2s ease; }
#calender::-webkit-inner-spin-button,
#calender::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
@media only screen and (min-width: 1024px) and (max-width: 1440px) {
  #kt_activitiesRecruiter { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px) {
  #kt_activitiesRecruiter { width: 1200px; }
}
.tagify .tagify__tag div .tagify__tag-text { color: #0c0c0c !important; font-family: 'Poppins', sans-serif !important; font-weight: 500 !important; font-size: 13px !important; }
.tagify { height: 38px !important; font-family: 'Poppins', sans-serif !important; }
.disablesectionOn.setOpacity { cursor: not-allowed; background: #8a878d; opacity: 0.5; }
</style>
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&resume_career_yellow=20260925&candidate_profile_yellow=20260925&primary_skills_match_yellow=20260925&experience_details_yellow=20260925&professional_profile_yellow=20260925&salary_details_yellow_sync=20260925" rel="stylesheet" type="text/css" />
