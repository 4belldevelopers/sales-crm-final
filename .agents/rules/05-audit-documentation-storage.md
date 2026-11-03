# 📑 Rule 05: Mandatory Document Storage for Audits, Feedback, Changes & Feature Suggestions

> **Scope**: Always active across all conversations, AI coding assistants (Claude, Cursor, Windsurf, Copilot, Antigravity), and human developers.
> **Target Folder**: `docs/audit documents/`
> **Purpose**: Guarantees that any new document generated for audits, feedback, modifications/changelogs, new features, or optimization suggestions is persistently stored in `docs/audit documents/` for easy future review.

---

### Non-Negotiable Directives

1. **Mandatory Storage Location**:
   - Whenever an AI assistant or engineer creates, drafts, or updates any document relating to:
     - **Audits** (architectural audits, security audits, CQRS compliance checks, UI/UX audits)
     - **Feedback** (stakeholder, recruiter, or code review feedback)
     - **Changes** (changelogs, technical implementation writeups, refactor breakdowns)
     - **New Features** (feature proposals, technical blueprints, DTO & schema designs)
     - **Suggestions** (performance improvements, UX enhancements, architectural ideas)
   - The resulting document **MUST ALWAYS BE STORED IN**: `docs/audit documents/`.

2. **No Ephemeral-Only Reports**:
   - Do NOT output substantial audit findings, feedback summaries, or new feature proposals solely to transient chat messages or temporary brain artifacts.
   - Always persist a permanent markdown file inside `docs/audit documents/` so the user and team can review it at any time.

3. **Standard File Naming Convention**:
   - Files saved in `docs/audit documents/` must follow the format:
     `YYYY-MM-DD-<module-or-topic>-<type>.md`
   - Common type tags: `-audit.md`, `-feedback.md`, `-changes.md`, `-feature.md`, `-suggestions.md`.

4. **Integration with Audit Skills & Workflows**:
   - When executing the `god-audit` skill or conducting pre-change reviews, output the complete audit deliverable directly into `docs/audit documents/` before proceeding with code modifications.
