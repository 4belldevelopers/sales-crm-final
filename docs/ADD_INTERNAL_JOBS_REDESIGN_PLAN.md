# Add Internal Jobs redesign

## Purpose and scope
Frontend-only redesign of `service_company.jobs.create`, with four steps in the existing form. Other consumers of the Blade retain their existing UI. No controller, route, validation, database, defaults, API or payload changes.

## Reference mapping
Images 1–3: integrated navigation, description/skills split and review columns. Image 8: locations. Image 9: compensation, retaining separate currency fields. Image 7: hiring team with yellow accents. Images 10, 12, 13: separate position/work-mode/shift choices. Image 15: additional details. Image 11's body stepper is superseded. Screening uses the same card system; no dedicated reference was supplied.

## Preservation inventory
Existing IDs/names remain the source of truth: client_id, address_id, title, category_id, min_exp, max_exp, total_positions, required_columns, employment_type, work_from_home, work_shifts, minimum_salary, maximum_salary, min_salary_currency, max_salary_currency, min_sal_mode, hiring_namager, recruiter, recruiter_id, qualification, end_date, tags, kt_docs_tinymce_hidden and customTitleId. All remaining hidden/backend fields are retained verbatim.

Locations use citySearch/cityDropdown, selectedCount and their existing hidden-field synchronization. Qualifications use qualSearch/qualDropdown/qualList. Hiring-team controls retain their original drawers, hidden values and removal handlers. Editor content remains in jobDescription and syncDescription. Questions retain creation, edit, removal, ordering, templates and serialization.

goToStep validates forward transitions; updateStepper controls visual states; populateReview reads the existing fields; syncAll serializes client selections; publishJob checks compliance and sends the existing POST through __submitUrl, then redirects through __indexUrl. Save Draft is currently a synchronization toast. Publishing visibility checkboxes have no names. AI/coin placeholder handlers are preserved without adding capabilities.

## Phases
0. Save this document; inspect contracts and conditional view reuse.
1. Move the existing navigation and publish button into an integrated sticky header. Add accessible state labels, responsive scrolling and yellow tokens.
2. Render choices from existing select options; retain selects and change events. Style basic information, locations, employment, compensation, team and additional details.
3. Place description and skills in a 60/40 responsive grid; preserve editor and skills handlers.
4. Apply the yellow card system to screening and existing dynamic rows.
5. Move existing review cards into three columns; retain all ancillary coin, source and compliance content.

## Acceptance checks
- Syntax, Blade compilation, JavaScript syntax and diff whitespace checks.
- Unchanged named-field inventory, option values and request code.
- Navigation, validation, keyboard focus, description/skills/questions persistence.
- Viewports 1366×768, 1920×1080, 1280×720, 768px and 390px; no page overflow or clipped menus.
- Publishing compliance, offline/loading/errors and staging success; unchanged edit/requisition UI.
- No added calls, polling, libraries or backend workload.

## Implementation record
Baseline authenticated browser capture is not available in this session. UI restructuring is create-route gated and moves existing DOM nodes so their handlers and values remain intact. Static and browser verification results must be reported separately. No 10K capacity claim follows from this redesign.

### Completed frontend phases and verification
Phases 1–5 implemented in create-only CSS/JavaScript assets, loaded conditionally from the existing Blade. Phase 0 documentation and source inventory completed; authenticated baseline remains unavailable.

- PHP source lint, JavaScript syntax and git diff whitespace checks passed.
- Isolated headless Edge fixture: header present, three choice sections, six review cards, simulated Next transition, final publish control visible, no JavaScript exceptions.
- Fixture widths 1920, 1366, 1280, 768 and 390px: no document horizontal overflow. Bootstrap modal hiding was represented in the fixture; the full application shell and server-rendered conditional fields were not reproduced.
- Actual route boot blocked by `View [vendor.froiden-envato.install_message] not found`.
- Standalone Blade compilation also blocked by unresolved existing `jero-phone-input` component dependencies without a booted view factory.
- Real validation, authenticated drawers, server-rendered option lists, payload capture, offline/server errors and staging publication remain unverified. Integration tests were not run against shared remote dependencies.
