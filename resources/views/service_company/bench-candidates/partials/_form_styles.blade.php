{{-- Phase 8 Frontend Optimization: Modular Form Styles Partial --}}
<link rel="stylesheet" href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="{{ asset('demo/css/bench-candidate-create.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}&bench_commercials=20260914&professional_profile=20260914&basic_availability=20260914&location_dropdown=20260914&location_typography=20260914&availability_date=20260914&fa6_fix=20260914&skills_header=20260914&location_enterprise=20260914&availability_picker=20260914&resume_summary_cards=20260914&relationship_manager_card=20260914&top_header=20260914&additional_details_card=20260914&enterprise_polish=20260914&professional_row_alignment=20260914&final_enterprise_polish=20260914&rm_summary_fix=20260914&availability_additional_fix=20260914&resume_note_visibility=20260915&create_card_order_icons=20260915&create_field_polish=20260915&create_global_phone_experience=20260915&commercial_state_colors=20260921&talent_stage_privacy=20260921&commercial_blue_green=20260921&availability_work_colors=20260921&skills_expertise=20260921&talent_stage_header=20260921&alignment_icon_comments=20260921&font_aliases=20260921&page_header_flow=20260921&talent_stage_skill_selects=20260921&sticky_compact_header=20260921&create_defaults_icons=20260921&bench_header_lavender=20260928" rel="stylesheet" type="text/css" />

<style>
.candidate-field-control--interactive {
    position: relative;
    display: flex;
    align-items: center;
}
.candidate-field-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #e4e6ef;
    color: #7e8299;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 2;
}
.candidate-field-clear:hover {
    background: #f1416c;
    color: #ffffff;
}
.candidate-email-typo-hint {
    margin-top: 5px;
    font-size: 11.5px;
    font-family: 'Poppins', sans-serif;
    color: #b45309;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 6px;
    padding: 5px 10px;
    display: none;
    align-items: center;
    gap: 6px;
}
.candidate-email-typo-hint a {
    color: #1d4ed8;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
}
</style>
