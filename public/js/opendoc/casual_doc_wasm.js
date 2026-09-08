/* @ts-self-types="./casual_doc_wasm.d.ts" */

/**
 * A caret position returned by navigation (no edit): node id + byte offset.
 */
export class Caret {
    static __wrap(ptr) {
        const obj = Object.create(Caret.prototype);
        obj.__wbg_ptr = ptr;
        CaretFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        CaretFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_caret_free(ptr, 0);
    }
    /**
     * The caret's paragraph node id (32-hex string).
     * @returns {string}
     */
    get node() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.caret_node(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The caret's byte offset within the node.
     * @returns {number}
     */
    get offset() {
        const ret = wasm.caret_offset(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) Caret.prototype[Symbol.dispose] = Caret.prototype.free;

/**
 * A model-owned contiguous text range for one table cell.
 */
export class CellTextRange {
    static __wrap(ptr) {
        const obj = Object.create(CellTextRange.prototype);
        obj.__wbg_ptr = ptr;
        CellTextRangeFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        CellTextRangeFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_celltextrange_free(ptr, 0);
    }
    /**
     * @returns {string}
     */
    get endNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.celltextrange_endNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get endOffset() {
        const ret = wasm.celltextrange_endOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {boolean}
     */
    get found() {
        const ret = wasm.celltextrange_found(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {string}
     */
    get startNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.celltextrange_startNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get startOffset() {
        const ret = wasm.celltextrange_startOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) CellTextRange.prototype[Symbol.dispose] = CellTextRange.prototype.free;

/**
 * Document statistics for the status footer.
 */
export class DocStats {
    static __wrap(ptr) {
        const obj = Object.create(DocStats.prototype);
        obj.__wbg_ptr = ptr;
        DocStatsFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        DocStatsFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_docstats_free(ptr, 0);
    }
    /**
     * Character count excluding whitespace (Word's "Characters (no spaces)").
     * @returns {number}
     */
    get characters() {
        const ret = wasm.docstats_characters(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Character count including spaces (Word's "Characters (with spaces)").
     * @returns {number}
     */
    get charactersWithSpaces() {
        const ret = wasm.docstats_characters_with_spaces(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Page count.
     * @returns {number}
     */
    get pages() {
        const ret = wasm.docstats_pages(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Paragraph count (body + table cells + content controls).
     * @returns {number}
     */
    get paragraphs() {
        const ret = wasm.docstats_paragraphs(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Whitespace-delimited word count across every paragraph.
     * @returns {number}
     */
    get words() {
        const ret = wasm.docstats_words(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) DocStats.prototype[Symbol.dispose] = DocStats.prototype.free;

/**
 * The result of an edit: the new caret, the revision, the page count, and the
 * **dirty page indices** (0-based) whose layout changed — the only pages the
 * frontend must re-raster.
 */
export class EditResult {
    static __wrap(ptr) {
        const obj = Object.create(EditResult.prototype);
        obj.__wbg_ptr = ptr;
        EditResultFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        EditResultFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_editresult_free(ptr, 0);
    }
    /**
     * The 0-based indices of pages whose layout changed — re-raster only these.
     * @returns {Uint32Array}
     */
    get dirtyPages() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.editresult_dirtyPages(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayU32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The caret anchor node id (32-hex string).
     * @returns {string}
     */
    get node() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.editresult_node(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The caret byte offset within the node.
     * @returns {number}
     */
    get offset() {
        const ret = wasm.editresult_offset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * The page count after the edit (so the frontend can add/remove pages).
     * @returns {number}
     */
    get pageCount() {
        const ret = wasm.editresult_pageCount(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * The model revision after the edit.
     * @returns {number}
     */
    get revision() {
        const ret = wasm.editresult_revision(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) EditResult.prototype[Symbol.dispose] = EditResult.prototype.free;

/**
 * The uniform run-format state of a selection (each `true` only when every
 * covered run sets that toggle) — for a formatting toolbar.
 */
export class Format {
    static __wrap(ptr) {
        const obj = Object.create(Format.prototype);
        obj.__wbg_ptr = ptr;
        FormatFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        FormatFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_format_free(ptr, 0);
    }
    /**
     * Every covered run is bold.
     * @returns {boolean}
     */
    get bold() {
        const ret = wasm.format_bold(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Bold state: 0 off, 1 on, 2 mixed.
     * @returns {number}
     */
    get boldState() {
        const ret = wasm.format_boldState(this.__wbg_ptr);
        return ret;
    }
    /**
     * Every covered run is italic.
     * @returns {boolean}
     */
    get italic() {
        const ret = wasm.format_italic(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Italic state: 0 off, 1 on, 2 mixed.
     * @returns {number}
     */
    get italicState() {
        const ret = wasm.format_italicState(this.__wbg_ptr);
        return ret;
    }
    /**
     * Every covered run is struck through.
     * @returns {boolean}
     */
    get strike() {
        const ret = wasm.format_strike(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Strike-through state: 0 off, 1 on, 2 mixed.
     * @returns {number}
     */
    get strikeState() {
        const ret = wasm.format_strikeState(this.__wbg_ptr);
        return ret;
    }
    /**
     * Every covered run is underlined.
     * @returns {boolean}
     */
    get underline() {
        const ret = wasm.format_underline(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Underline state: 0 off, 1 on, 2 mixed.
     * @returns {number}
     */
    get underlineState() {
        const ret = wasm.format_underlineState(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) Format.prototype[Symbol.dispose] = Format.prototype.free;

/**
 * The model anchor a point resolved to (doc 58 §2 `TextCaret`/`Outside`): a
 * `NodeId` (32-hex string), a node-relative UTF-8 byte offset, and whether the
 * point was inside content or snapped in from outside.
 */
export class HitPayload {
    static __wrap(ptr) {
        const obj = Object.create(HitPayload.prototype);
        obj.__wbg_ptr = ptr;
        HitPayloadFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        HitPayloadFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_hitpayload_free(ptr, 0);
    }
    /**
     * The anchor node id (32-hex string; compare/pass back, never arithmetic).
     * @returns {string}
     */
    get node() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.hitpayload_node(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The node-relative UTF-8 byte offset.
     * @returns {number}
     */
    get offset() {
        const ret = wasm.hitpayload_offset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * `"content"` (inside a line) or `"outside"` (snapped from a margin).
     * @returns {string}
     */
    get zone() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.hitpayload_zone(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
}
if (Symbol.dispose) HitPayload.prototype[Symbol.dispose] = HitPayload.prototype.free;

/**
 * A paragraph's indentation (twips), for the ruler markers.
 */
export class Indents {
    static __wrap(ptr) {
        const obj = Object.create(Indents.prototype);
        obj.__wbg_ptr = ptr;
        IndentsFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        IndentsFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_indents_free(ptr, 0);
    }
    /**
     * Right (end) indent in twips.
     * @returns {number}
     */
    get endTwip() {
        const ret = wasm.indents_endTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * First-line indent in twips (relative to the left indent).
     * @returns {number}
     */
    get firstLineTwip() {
        const ret = wasm.indents_firstLineTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Hanging indent in twips (mutually exclusive with first-line).
     * @returns {number}
     */
    get hangingTwip() {
        const ret = wasm.indents_hangingTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Left (start) indent in twips.
     * @returns {number}
     */
    get startTwip() {
        const ret = wasm.indents_startTwip(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) Indents.prototype[Symbol.dispose] = Indents.prototype.free;

/**
 * A hyperlink resolved at a page-local point. The runtime reports both the
 * authored target and, for internal links, the resolved bookmark caret/page;
 * the embedding host decides whether and how navigation is allowed.
 */
export class LinkHit {
    static __wrap(ptr) {
        const obj = Object.create(LinkHit.prototype);
        obj.__wbg_ptr = ptr;
        LinkHitFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        LinkHitFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_linkhit_free(ptr, 0);
    }
    /**
     * Internal bookmark name, or empty for an external target.
     * @returns {string}
     */
    get anchor() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_anchor(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Paragraph node ending the linked range (currently identical to startNode).
     * @returns {string}
     */
    get endNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_endNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * End byte offset of the linked range.
     * @returns {number}
     */
    get endOffset() {
        const ret = wasm.linkhit_endOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * `"external"` or `"internal"`.
     * @returns {string}
     */
    get kind() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_kind(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Paragraph node containing the linked range.
     * @returns {string}
     */
    get startNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_startNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Start byte offset of the linked range.
     * @returns {number}
     */
    get startOffset() {
        const ret = wasm.linkhit_startOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Resolved internal-bookmark paragraph node, or empty if unresolved/external.
     * @returns {string}
     */
    get targetNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_targetNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Resolved internal-bookmark byte offset.
     * @returns {number}
     */
    get targetOffset() {
        const ret = wasm.linkhit_targetOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Resolved internal-bookmark 1-based page, or 0 if unresolved/external.
     * @returns {number}
     */
    get targetPage() {
        const ret = wasm.linkhit_targetPage(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Optional authored screen tip (empty when absent).
     * @returns {string}
     */
    get tooltip() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_tooltip(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * External target URL, or empty for an internal target.
     * @returns {string}
     */
    get url() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.linkhit_url(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
}
if (Symbol.dispose) LinkHit.prototype[Symbol.dispose] = LinkHit.prototype.free;

/**
 * A selectable object resolved at a page-local point (docs/85 §3.1): its model
 * `NodeId` (32-hex), an `ObjectKind`-style tag, the 1-based page, placed rect
 * `[page, x, y, w, h]` in twips, and structural capability truth.
 */
export class ObjectHitPayload {
    static __wrap(ptr) {
        const obj = Object.create(ObjectHitPayload.prototype);
        obj.__wbg_ptr = ptr;
        ObjectHitPayloadFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        ObjectHitPayloadFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_objecthitpayload_free(ptr, 0);
    }
    /**
     * Whether this reference's root owns a floating anchor. For a group-of-one,
     * `subject` remains the leaf while movement and wrapping target `root`.
     * @returns {boolean}
     */
    get anchored() {
        const ret = wasm.objecthitpayload_anchored(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this exact node owns mutable alternative-text metadata.
     * @returns {boolean}
     */
    get canAltText() {
        const ret = wasm.objecthitpayload_canAltText(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this exact node owns a mutable picture crop rectangle.
     * @returns {boolean}
     */
    get canCrop() {
        const ret = wasm.objecthitpayload_canCrop(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this reference's root can be deleted with an exact inverse.
     * @returns {boolean}
     */
    get canDelete() {
        const ret = wasm.objecthitpayload_canDelete(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this exact node contains an editable text-box surface.
     * @returns {boolean}
     */
    get canEditText() {
        const ret = wasm.objecthitpayload_canEditText(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this exact node owns mutable shape fill properties.
     * @returns {boolean}
     */
    get canFill() {
        const ret = wasm.objecthitpayload_canFill(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this reference's root owns a movable anchor position.
     * @returns {boolean}
     */
    get canMove() {
        const ret = wasm.objecthitpayload_canMove(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this reference supports an exact, undoable extent mutation.
     * @returns {boolean}
     */
    get canResize() {
        const ret = wasm.objecthitpayload_canResize(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this exact node owns mutable shape stroke properties.
     * @returns {boolean}
     */
    get canStroke() {
        const ret = wasm.objecthitpayload_canStroke(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether this reference's root owns a mutable text-wrapping mode.
     * @returns {boolean}
     */
    get canWrap() {
        const ret = wasm.objecthitpayload_canWrap(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * The object kind: `"image"`, `"textbox"`, or `"shape"` (docs/85 `ObjectKind`).
     * @returns {string}
     */
    get kind() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_kind(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Compatibility alias for [`subject`](Self::subject).
     * @returns {string}
     */
    get node() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_node(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The 1-based page the object sits on.
     * @returns {number}
     */
    get page() {
        const ret = wasm.objecthitpayload_page(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * The bounded child-index path from `root` to `subject`.
     * @returns {Uint32Array}
     */
    get path() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_path(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayU32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The placed rect `[page, xTwip, yTwip, wTwip, hTwip]` (page-local).
     * @returns {Int32Array}
     */
    get rect() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_rect(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The removable/movable top-level object carrier (32-hex string).
     * @returns {string}
     */
    get root() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_root(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The exact leaf whose kind-specific properties or text are edited.
     * @returns {string}
     */
    get subject() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_subject(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The owning document story. Object selection is body-only in this slice.
     * @returns {string}
     */
    get surface() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.objecthitpayload_surface(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
}
if (Symbol.dispose) ObjectHitPayload.prototype[Symbol.dispose] = ObjectHitPayload.prototype.free;

/**
 * A rasterized page: RGBA8888 premultiplied pixels plus their device dimensions
 * (doc 57 §5.1). Blit with `new ImageData(rgba, widthPx, heightPx)`.
 */
export class PageBitmap {
    static __wrap(ptr) {
        const obj = Object.create(PageBitmap.prototype);
        obj.__wbg_ptr = ptr;
        PageBitmapFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        PageBitmapFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_pagebitmap_free(ptr, 0);
    }
    /**
     * Bitmap height in device pixels.
     * @returns {number}
     */
    get heightPx() {
        const ret = wasm.pagebitmap_heightPx(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * The premultiplied RGBA8888 pixels, row-major, as a `Uint8ClampedArray` —
     * the exact backing an `ImageData` takes. Copies out of the surface.
     * @returns {Uint8ClampedArray}
     */
    get rgba() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.pagebitmap_rgba(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getClampedArrayU8FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 1, 1);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Bitmap width in device pixels.
     * @returns {number}
     */
    get widthPx() {
        const ret = wasm.pagebitmap_widthPx(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) PageBitmap.prototype[Symbol.dispose] = PageBitmap.prototype.free;

/**
 * A page box size in twips (doc 57 §4.2).
 */
export class PageSize {
    static __wrap(ptr) {
        const obj = Object.create(PageSize.prototype);
        obj.__wbg_ptr = ptr;
        PageSizeFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        PageSizeFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_pagesize_free(ptr, 0);
    }
    /**
     * Page box height in twips (1/1440 in).
     * @returns {number}
     */
    get heightTwip() {
        const ret = wasm.pagesize_heightTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Page box width in twips (1/1440 in).
     * @returns {number}
     */
    get widthTwip() {
        const ret = wasm.pagesize_widthTwip(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) PageSize.prototype[Symbol.dispose] = PageSize.prototype.free;

/**
 * A paragraph's line-and-page-break flags, for the paragraph-properties inspector.
 */
export class ParagraphFlags {
    static __wrap(ptr) {
        const obj = Object.create(ParagraphFlags.prototype);
        obj.__wbg_ptr = ptr;
        ParagraphFlagsFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        ParagraphFlagsFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_paragraphflags_free(ptr, 0);
    }
    /**
     * Keep all lines of this paragraph on one page (`w:keepLines`).
     * @returns {boolean}
     */
    get keepLines() {
        const ret = wasm.paragraphflags_keepLines(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Keep this paragraph on the same page as the next (`w:keepNext`).
     * @returns {boolean}
     */
    get keepNext() {
        const ret = wasm.paragraphflags_keepNext(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Force a page break before this paragraph (`w:pageBreakBefore`).
     * @returns {boolean}
     */
    get pageBreakBefore() {
        const ret = wasm.paragraphflags_pageBreakBefore(this.__wbg_ptr);
        return ret !== 0;
    }
}
if (Symbol.dispose) ParagraphFlags.prototype[Symbol.dispose] = ParagraphFlags.prototype.free;

/**
 * A paragraph's spacing, for the toolbar's line-&-paragraph-spacing menu.
 */
export class ParagraphSpacing {
    static __wrap(ptr) {
        const obj = Object.create(ParagraphSpacing.prototype);
        obj.__wbg_ptr = ptr;
        ParagraphSpacingFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        ParagraphSpacingFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_paragraphspacing_free(ptr, 0);
    }
    /**
     * Space after the paragraph in twips, or `-1` when unset.
     * @returns {number}
     */
    get afterTwip() {
        const ret = wasm.paragraphspacing_afterTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Space before the paragraph in twips, or `-1` when unset.
     * @returns {number}
     */
    get beforeTwip() {
        const ret = wasm.paragraphspacing_beforeTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Line-spacing percentage for the `auto` rule (0 when unset or a fixed rule).
     * @returns {number}
     */
    get linePercent() {
        const ret = wasm.paragraphspacing_linePercent(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * Line rule: `0` auto (percent), `1` atLeast, `2` exact (twips).
     * @returns {number}
     */
    get lineRule() {
        const ret = wasm.paragraphspacing_lineRule(this.__wbg_ptr);
        return ret;
    }
    /**
     * Fixed line height in twips for the atLeast/exact rules (0 for auto).
     * @returns {number}
     */
    get lineTwip() {
        const ret = wasm.paragraphspacing_lineTwip(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) ParagraphSpacing.prototype[Symbol.dispose] = ParagraphSpacing.prototype.free;

/**
 * Uniform paragraph properties over every paragraph touched by a selection.
 *
 * Values carry explicit mixed flags so the host never has to infer
 * disagreement from a sentinel. Boolean states are 0=off, 1=on, 2=mixed.
 */
export class ParagraphState {
    static __wrap(ptr) {
        const obj = Object.create(ParagraphState.prototype);
        obj.__wbg_ptr = ptr;
        ParagraphStateFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        ParagraphStateFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_paragraphstate_free(ptr, 0);
    }
    /**
     * @returns {boolean}
     */
    get afterMixed() {
        const ret = wasm.paragraphstate_afterMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get afterTwip() {
        const ret = wasm.paragraphstate_afterTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {string}
     */
    get alignment() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.paragraphstate_alignment(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {boolean}
     */
    get alignmentMixed() {
        const ret = wasm.paragraphstate_alignmentMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {boolean}
     */
    get beforeMixed() {
        const ret = wasm.paragraphstate_beforeMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get beforeTwip() {
        const ret = wasm.paragraphstate_beforeTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * top=1, bottom=2, start=4, end=8.
     * @returns {number}
     */
    get borderEdges() {
        const ret = wasm.paragraphstate_borderEdges(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {boolean}
     */
    get bordersMixed() {
        const ret = wasm.paragraphstate_bordersMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get count() {
        const ret = wasm.paragraphstate_count(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {boolean}
     */
    get endMixed() {
        const ret = wasm.paragraphstate_endMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get endTwip() {
        const ret = wasm.paragraphstate_endTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {boolean}
     */
    get firstLineMixed() {
        const ret = wasm.paragraphstate_firstLineMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get firstLineTwip() {
        const ret = wasm.paragraphstate_firstLineTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {boolean}
     */
    get hangingMixed() {
        const ret = wasm.paragraphstate_hangingMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get hangingTwip() {
        const ret = wasm.paragraphstate_hangingTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get keepLinesState() {
        const ret = wasm.paragraphstate_keepLinesState(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get keepNextState() {
        const ret = wasm.paragraphstate_keepNextState(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {boolean}
     */
    get lineMixed() {
        const ret = wasm.paragraphstate_lineMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get linePercent() {
        const ret = wasm.paragraphstate_linePercent(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {number}
     */
    get pageBreakBeforeState() {
        const ret = wasm.paragraphstate_pageBreakBeforeState(this.__wbg_ptr);
        return ret;
    }
    /**
     * Packed `0xRRGGBB`, or `-1` when uniformly unset.
     * @returns {number}
     */
    get shading() {
        const ret = wasm.paragraphstate_shading(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {boolean}
     */
    get shadingMixed() {
        const ret = wasm.paragraphstate_shadingMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {boolean}
     */
    get startMixed() {
        const ret = wasm.paragraphstate_startMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get startTwip() {
        const ret = wasm.paragraphstate_startTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {string}
     */
    get style() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.paragraphstate_style(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {boolean}
     */
    get styleMixed() {
        const ret = wasm.paragraphstate_styleMixed(this.__wbg_ptr);
        return ret !== 0;
    }
}
if (Symbol.dispose) ParagraphState.prototype[Symbol.dispose] = ParagraphState.prototype.free;

/**
 * A page's ruler geometry (twips).
 */
export class RulerGeometry {
    static __wrap(ptr) {
        const obj = Object.create(RulerGeometry.prototype);
        obj.__wbg_ptr = ptr;
        RulerGeometryFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        RulerGeometryFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_rulergeometry_free(ptr, 0);
    }
    /**
     * Right (end) margin in twips.
     * @returns {number}
     */
    get marginEndTwip() {
        const ret = wasm.rulergeometry_marginEndTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Left (start) margin in twips.
     * @returns {number}
     */
    get marginStartTwip() {
        const ret = wasm.rulergeometry_marginStartTwip(this.__wbg_ptr);
        return ret;
    }
    /**
     * Page width in twips.
     * @returns {number}
     */
    get widthTwip() {
        const ret = wasm.rulergeometry_widthTwip(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) RulerGeometry.prototype[Symbol.dispose] = RulerGeometry.prototype.free;

/**
 * The uniform run styling of a selection, for reflecting current values in the
 * toolbar. Values and explicit mixed flags are separate so a uniformly unset
 * property is never conflated with disagreement across the selection.
 */
export class RunStyle {
    static __wrap(ptr) {
        const obj = Object.create(RunStyle.prototype);
        obj.__wbg_ptr = ptr;
        RunStyleFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        RunStyleFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_runstyle_free(ptr, 0);
    }
    /**
     * Common text color as `#rrggbb` (empty if mixed/unset or a theme color).
     * @returns {string}
     */
    get color() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_color(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection contains more than one effective text color.
     * @returns {boolean}
     */
    get colorMixed() {
        const ret = wasm.runstyle_colorMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common font family (empty if mixed/unset).
     * @returns {string}
     */
    get font() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_font(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection contains more than one effective font family.
     * @returns {boolean}
     */
    get fontMixed() {
        const ret = wasm.runstyle_fontMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common highlight name, `"none"` when uniformly clear, or empty when mixed.
     * @returns {string}
     */
    get highlight() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_highlight(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection contains more than one effective highlight value.
     * @returns {boolean}
     */
    get highlightMixed() {
        const ret = wasm.runstyle_highlightMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether the selection contains more than one effective font size.
     * @returns {boolean}
     */
    get sizeMixed() {
        const ret = wasm.runstyle_sizeMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common font size in points (0 if mixed/unset).
     * @returns {number}
     */
    get sizePoints() {
        const ret = wasm.runstyle_sizePoints(this.__wbg_ptr);
        return ret;
    }
    /**
     * Every covered run is subscript.
     * @returns {boolean}
     */
    get subscript() {
        const ret = wasm.runstyle_subscript(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Every covered run is superscript.
     * @returns {boolean}
     */
    get superscript() {
        const ret = wasm.runstyle_superscript(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common explicit underline color as `#rrggbb`; empty means automatic/text
     * color, or mixed when [`underline_color_mixed`](Self::underline_color_mixed)
     * is true.
     * @returns {string}
     */
    get underlineColor() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_underlineColor(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection contains more than one effective underline color.
     * @returns {boolean}
     */
    get underlineColorMixed() {
        const ret = wasm.runstyle_underlineColorMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common underline line style (`single`, `double`, `thick`, `dotted`,
     * `dashed`, `dotDash`, `wavy`, or `words`), or empty when mixed/unset.
     * @returns {string}
     */
    get underlineStyle() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_underlineStyle(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection contains more than one effective underline style.
     * @returns {boolean}
     */
    get underlineStyleMixed() {
        const ret = wasm.runstyle_underlineStyleMixed(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Common vertical alignment: `super`, `sub`, `baseline`, or empty when mixed.
     * @returns {string}
     */
    get verticalAlign() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runstyle_verticalAlign(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the selection mixes baseline, superscript, or subscript.
     * @returns {boolean}
     */
    get verticalAlignMixed() {
        const ret = wasm.runstyle_verticalAlignMixed(this.__wbg_ptr);
        return ret !== 0;
    }
}
if (Symbol.dispose) RunStyle.prototype[Symbol.dispose] = RunStyle.prototype.free;

/**
 * A position resolved inside a page's running content: the same `NodeId` +
 * offset pair any body position uses, plus which band it came from. The
 * position needs no sub-document address because header, footer and note
 * content already live in the document's single id space.
 */
export class RunningHitPayload {
    static __wrap(ptr) {
        const obj = Object.create(RunningHitPayload.prototype);
        obj.__wbg_ptr = ptr;
        RunningHitPayloadFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        RunningHitPayloadFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_runninghitpayload_free(ptr, 0);
    }
    /**
     * `"header"` or `"footer"`.
     * @returns {string}
     */
    get band() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runninghitpayload_band(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The anchor node id (32-hex string; compare/pass back, never arithmetic).
     * @returns {string}
     */
    get node() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.runninghitpayload_node(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The node-relative UTF-8 byte offset.
     * @returns {number}
     */
    get offset() {
        const ret = wasm.runninghitpayload_offset(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) RunningHitPayload.prototype[Symbol.dispose] = RunningHitPayload.prototype.free;

/**
 * Maps a highlight name (case-insensitive) to a [`HighlightColor`]; unknown
 * names fall back to `Yellow`, `"none"`/`""` clear the highlight.
 * Parses a `#rrggbb` (or `rrggbb`) hex color, or `None` if malformed.
 * What [`shapeFormat`](WasmDocument::shape_format) reports to the host: the
 * shape's own fill and outline, each `#rrggbb` or `undefined` for none.
 */
export class ShapeFormat {
    static __wrap(ptr) {
        const obj = Object.create(ShapeFormat.prototype);
        obj.__wbg_ptr = ptr;
        ShapeFormatFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        ShapeFormatFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_shapeformat_free(ptr, 0);
    }
    /**
     * The fill color, or `undefined` when the shape has no fill.
     * @returns {string | undefined}
     */
    get fill() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.__wbg_get_shapeformat_fill(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            let v1;
            if (r0 !== 0) {
                v1 = getStringFromWasm0(r0, r1).slice();
                wasm.__wbindgen_export3(r0, r1 * 1, 1);
            }
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The outline width in EMU, when the shape has an outline. `f64` rather than
     * `i64` so JS receives a plain number: a `BigInt` cannot be handed straight
     * back to `setShapeOutline`, which is exactly what the host does with it.
     * @returns {number | undefined}
     */
    get outlineWidthEmu() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.__wbg_get_shapeformat_outlineWidthEmu(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r2 = getDataViewMemory0().getFloat64(retptr + 8 * 1, true);
            return r0 === 0 ? undefined : r2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The outline color, or `undefined` when the shape has no outline.
     * @returns {string | undefined}
     */
    get outline() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.__wbg_get_shapeformat_outline(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            let v1;
            if (r0 !== 0) {
                v1 = getStringFromWasm0(r0, r1).slice();
                wasm.__wbindgen_export3(r0, r1 * 1, 1);
            }
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The fill color, or `undefined` when the shape has no fill.
     * @param {string | null} [arg0]
     */
    set fill(arg0) {
        var ptr0 = isLikeNone(arg0) ? 0 : passStringToWasm0(arg0, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        var len0 = WASM_VECTOR_LEN;
        wasm.__wbg_set_shapeformat_fill(this.__wbg_ptr, ptr0, len0);
    }
    /**
     * The outline width in EMU, when the shape has an outline. `f64` rather than
     * `i64` so JS receives a plain number: a `BigInt` cannot be handed straight
     * back to `setShapeOutline`, which is exactly what the host does with it.
     * @param {number | null} [arg0]
     */
    set outlineWidthEmu(arg0) {
        wasm.__wbg_set_shapeformat_outlineWidthEmu(this.__wbg_ptr, !isLikeNone(arg0), isLikeNone(arg0) ? 0 : arg0);
    }
    /**
     * The outline color, or `undefined` when the shape has no outline.
     * @param {string | null} [arg0]
     */
    set outline(arg0) {
        var ptr0 = isLikeNone(arg0) ? 0 : passStringToWasm0(arg0, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        var len0 = WASM_VECTOR_LEN;
        wasm.__wbg_set_shapeformat_outline(this.__wbg_ptr, ptr0, len0);
    }
}
if (Symbol.dispose) ShapeFormat.prototype[Symbol.dispose] = ShapeFormat.prototype.free;

/**
 * The resolved visual preview of a paragraph style — what a paragraph carrying
 * only that style renders with. The Styles gallery draws each card in its own
 * style from these fields (Word's Styles gallery).
 */
export class StylePreview {
    static __wrap(ptr) {
        const obj = Object.create(StylePreview.prototype);
        obj.__wbg_ptr = ptr;
        StylePreviewFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        StylePreviewFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_stylepreview_free(ptr, 0);
    }
    /**
     * The paragraph alignment: `start`, `center`, `end`, or `justify`.
     * @returns {string}
     */
    get alignment() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.stylepreview_alignment(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the style renders bold.
     * @returns {boolean}
     */
    get bold() {
        const ret = wasm.stylepreview_bold(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * The text color as `#rrggbb`, or empty for automatic/theme colors.
     * @returns {string}
     */
    get color() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.stylepreview_color(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The requested (authored) font family — never a physical fallback. Empty
     * when the style is unknown.
     * @returns {string}
     */
    get fontFamily() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.stylepreview_fontFamily(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the style renders italic.
     * @returns {boolean}
     */
    get italic() {
        const ret = wasm.stylepreview_italic(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * The font size in points (0 when unknown/unset).
     * @returns {number}
     */
    get sizePoints() {
        const ret = wasm.stylepreview_sizePoints(this.__wbg_ptr);
        return ret;
    }
    /**
     * Whether the style renders underlined.
     * @returns {boolean}
     */
    get underline() {
        const ret = wasm.stylepreview_underline(this.__wbg_ptr);
        return ret !== 0;
    }
}
if (Symbol.dispose) StylePreview.prototype[Symbol.dispose] = StylePreview.prototype.free;

/**
 * Current table context for editor table-selection and table-property controls.
 */
export class TableInfo {
    static __wrap(ptr) {
        const obj = Object.create(TableInfo.prototype);
        obj.__wbg_ptr = ptr;
        TableInfoFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        TableInfoFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_tableinfo_free(ptr, 0);
    }
    /**
     * @returns {string}
     */
    get alignment() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.tableinfo_alignment(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {string}
     */
    get caption() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.tableinfo_caption(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get cellMarginTwips() {
        const ret = wasm.tableinfo_cellMarginTwips(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get cellSpacingTwips() {
        const ret = wasm.tableinfo_cellSpacingTwips(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get column() {
        const ret = wasm.tableinfo_column(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {number}
     */
    get columnWidthTwips() {
        const ret = wasm.tableinfo_columnWidthTwips(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get columns() {
        const ret = wasm.tableinfo_columns(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {string}
     */
    get description() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.tableinfo_description(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {boolean}
     */
    get fixedLayout() {
        const ret = wasm.tableinfo_fixedLayout(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {boolean}
     */
    get found() {
        const ret = wasm.tableinfo_found(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {boolean}
     */
    get headerRow() {
        const ret = wasm.tableinfo_headerRow(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {boolean}
     */
    get regular() {
        const ret = wasm.tableinfo_regular(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {number}
     */
    get row() {
        const ret = wasm.tableinfo_row(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {string}
     */
    get rowHeightRule() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.tableinfo_rowHeightRule(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get rowHeightTwips() {
        const ret = wasm.tableinfo_rowHeightTwips(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get rows() {
        const ret = wasm.tableinfo_rows(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {string}
     */
    get table() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.tableinfo_table(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get tableIndentTwips() {
        const ret = wasm.tableinfo_tableIndentTwips(this.__wbg_ptr);
        return ret;
    }
    /**
     * @returns {number}
     */
    get tableWidthTwips() {
        const ret = wasm.tableinfo_tableWidthTwips(this.__wbg_ptr);
        return ret;
    }
}
if (Symbol.dispose) TableInfo.prototype[Symbol.dispose] = TableInfo.prototype.free;

/**
 * A find result: either no match, or a same-node byte range that can become the
 * editor selection and feed replaceSelection.
 */
export class TextMatch {
    static __wrap(ptr) {
        const obj = Object.create(TextMatch.prototype);
        obj.__wbg_ptr = ptr;
        TextMatchFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        TextMatchFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_textmatch_free(ptr, 0);
    }
    /**
     * @returns {string}
     */
    get endNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.textmatch_endNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get endOffset() {
        const ret = wasm.textmatch_endOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * @returns {boolean}
     */
    get found() {
        const ret = wasm.textmatch_found(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * @returns {string}
     */
    get startNode() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.textmatch_startNode(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * @returns {number}
     */
    get startOffset() {
        const ret = wasm.textmatch_startOffset(this.__wbg_ptr);
        return ret >>> 0;
    }
}
if (Symbol.dispose) TextMatch.prototype[Symbol.dispose] = TextMatch.prototype.free;

/**
 * An open document: the imported model, its current pagination, and the shaper
 * (and its font registry) that produced it, plus the media bytes rendering needs.
 *
 * Held together because rendering must serve the faces the shaper actually
 * resolved during pagination — [`ParleyShaper::registry`] is only complete after
 * [`paginate_document`] has shaped every paragraph.
 */
export class WasmDocument {
    static __wrap(ptr) {
        const obj = Object.create(WasmDocument.prototype);
        obj.__wbg_ptr = ptr;
        WasmDocumentFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        WasmDocumentFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_wasmdocument_free(ptr, 0);
    }
    /**
     * The document's structure as a **read-only** JSON array of
     * `A11yBlockJson` nodes in document order — headings (with a 1-based
     * level), paragraphs, list items (with an `ordered` flag), and tables
     * (a grid of cell plain-text). A host renders this into an off-screen
     * ARIA tree so assistive technology can read the canvas document, which
     * otherwise exposes nothing to a screen reader (docs/67 row 9). The model
     * stays the source of truth: this is a projection, never an editing
     * surface (docs/67 Open Risks — "Accessibility bridges cannot become
     * hidden DOM editors"). Empty paragraphs are omitted as reading noise;
     * content controls flatten into their blocks; a paragraph inside a table
     * cell contributes to that cell's text, not a top-level node.
     * @returns {string}
     */
    accessibilityTree() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_accessibilityTree(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the current host-supplied review identity as JSON
     * (`{"name":..., "initials":..., "id":...}`), or the JSON literal `null`
     * when no identity has been set (initial state, or after
     * `setActiveAuthor("")`).
     * @returns {string}
     */
    activeAuthor() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_activeAuthor(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Creates a comment over a selection that may span multiple paragraphs.
     * The `CommentRangeStart` marker lands in `start_node` at `start`, the
     * `CommentRangeEnd` marker and the `CommentReference` in `end_node` at
     * `end`, all keyed to the same [`CommentId`]; when `start_node == end_node`
     * this is a single-paragraph span and behaves exactly as before. The
     * endpoints are expected in document order (the editor's `selectionEdge`).
     * The marker and definition changes are one undoable review transaction.
     * @param {string} start_node
     * @param {number} start
     * @param {string} end_node
     * @param {number} end
     * @param {string} text
     * @param {string | null} [author]
     * @param {string | null} [initials]
     * @param {string | null} [date]
     * @returns {EditResult}
     */
    addComment(start_node, start, end_node, end, text, author, initials, date) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(initials) ? 0 : passStringToWasm0(initials, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            wasm.wasmdocument_addComment(retptr, this.__wbg_ptr, ptr0, len0, start, ptr1, len1, end, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Adjusts the left (start) indent by `delta_twips` (positive indents,
     * negative outdents, clamped at 0) over the selection's paragraphs.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} delta_twips
     * @returns {EditResult}
     */
    adjustIndent(start_node, start_offset, end_node, end_offset, delta_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_adjustIndent(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, delta_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Promotes or demotes list paragraphs by one numbering level, preserving
     * their list instance. Non-list paragraphs are rejected so the host can
     * retain ordinary indentation behavior.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} delta
     * @returns {EditResult}
     */
    adjustListLevel(start_node, start_offset, end_node, end_offset, delta) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_adjustListLevel(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, delta);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The alignment of the first paragraph the selection touches (`"start"`,
     * `"center"`, `"end"`, `"justify"`), or `"start"` if unset — for toolbar
     * state.
     * @param {string} node
     * @param {number} offset
     * @returns {string}
     */
    alignmentAt(node, offset) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_alignmentAt(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * Applies the table-properties inspector draft as one undoable action.
     *
     * Only fields present in the JSON patch are changed, so opening and applying
     * the inspector does not materialize model defaults that were previously
     * absent. Table-, active-row-, and active-column-level values are committed
     * together through one [`Operation::ReplaceTable`].
     * @param {string} node
     * @param {string} properties_json
     * @returns {EditResult}
     */
    applyTableProperties(node, properties_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(properties_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_applyTableProperties(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Applies a named table style, or clears the table style when `name` is empty.
     * @param {string} node
     * @param {string} name
     * @returns {EditResult}
     */
    applyTableStyle(node, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_applyTableStyle(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Returns stable format identifiers currently available for export.
     * @returns {string[]}
     */
    availableExportFormats() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_availableExportFormats(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Which running band `(x, y)` is in on `page` — `"header"`, `"footer"`, or
     * `""` for the content area.
     *
     * The host asked this question from its own copy of the page setup, which
     * is document-level: in a document whose sections have different margins,
     * the host and the engine disagreed about where a page's header ends. One
     * rule, owned by the layout that defines it.
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {string}
     */
    bandAt(page, x_twip, y_twip) {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_bandAt(retptr, this.__wbg_ptr, page, x_twip, y_twip);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Every bookmark as `id\tname`, in ascending id order — the id-bearing
     * accessor a manager surface needs to target [`renameBookmark`](Self::rename_bookmark)
     * and [`deleteBookmark`](Self::delete_bookmark) unambiguously (names are not
     * unique). The id is the 32-hex bookmark id; a fresh [`createBookmark`](Self::create_bookmark)
     * appears here on the next read.
     * @returns {string[]}
     */
    bookmarkEntries() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_bookmarkEntries(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves an authored bookmark to `node\toffset`, or an empty string.
     * @param {string} name
     * @returns {string}
     */
    bookmarkPosition(name) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_bookmarkPosition(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * Calculates a bounded table formula and writes its numeric result into the
     * active cell. Supported forms are `=SUM|AVERAGE|MIN|MAX(ABOVE|LEFT)`.
     * @param {string} node
     * @param {string} formula
     * @returns {EditResult}
     */
    calculateTableFormula(node, formula) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(formula, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_calculateTableFormula(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Whether [`continue_list`](Self::continue_list) would succeed at `node`: the
     * paragraph is a numbered list item with an earlier numbered list at the same
     * level (a different instance) to resume. Drives the toolbar button's enabled
     * state so it is offered only when there is actually a list to continue.
     * @param {string} node
     * @returns {boolean}
     */
    canContinueList(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_canContinueList(this.__wbg_ptr, ptr0, len0);
        return ret !== 0;
    }
    /**
     * Whether the document has at least one undone action available to redo.
     * @returns {boolean}
     */
    get canRedo() {
        const ret = wasm.wasmdocument_canRedo(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Whether the document has at least one user action available to undo.
     * @returns {boolean}
     */
    get canUndo() {
        const ret = wasm.wasmdocument_canUndo(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * The run formatting a collapsed caret inherits — what new typing there would
     * carry (the run to the caret's left, Word's rule). Drives the toolbar's active
     * state at a caret and the "type bold" toggle direction, where `formatAt` on an
     * empty range reports all-false.
     * @param {string} node
     * @param {number} offset
     * @returns {Format}
     */
    caretFormat(node, offset) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_caretFormat(this.__wbg_ptr, ptr0, len0, offset);
        return Format.__wrap(ret);
    }
    /**
     * The caret box for a model anchor, as `[page, xTwip, yTwip, wTwip, hTwip]`
     * (page-local; `w` is 0). Empty if the anchor resolves to no line (e.g. a
     * stale position) or `node` is malformed.
     * @param {string} node
     * @param {number} offset
     * @returns {Int32Array}
     */
    caretRect(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_caretRect(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The effective run styling a **collapsed caret** inherits — size / authored
     * font / color / super-sub after document defaults and style inheritance.
     * Renderer substitutions and glyph-coverage fallbacks are intentionally not
     * exposed as authored formatting.
     * @param {string} node
     * @param {number} offset
     * @returns {RunStyle}
     */
    caretRunStyle(node, offset) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_caretRunStyle(this.__wbg_ptr, ptr0, len0, offset);
        return RunStyle.__wrap(ret);
    }
    /**
     * The cell's border edges as a bitmask (top=1, bottom=2, left=4, right=8) — for
     * reflecting the active cell-border presets.
     * @param {string} node
     * @returns {number}
     */
    cellBorderEdges(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_cellBorderEdges(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * The active cell's border box as a flat `[page, x, y, w, h]` in page-local
     * twips (empty when the caret is not in a table) — the geometry the frontend
     * draws the active-cell highlight from.
     * @param {string} node
     * @returns {Int32Array}
     */
    cellRect(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_cellRect(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The shading fill of the cell containing `node` as packed `0xRRGGBB`, or `-1`.
     * @param {string} node
     * @returns {number}
     */
    cellShadingAt(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_cellShadingAt(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * The ordered text bounds of the innermost table cell containing `node`.
     *
     * Cell ownership is model state: the browser must not infer these bounds
     * from canvas geometry or the accessibility mirror. A cell containing an
     * embedded text-box story deliberately returns `found = false`; such a
     * story is a separate editing surface and a single contiguous model range
     * would cross that boundary.
     * @param {string} node
     * @returns {CellTextRange}
     */
    cellTextRange(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_cellTextRange(this.__wbg_ptr, ptr0, len0);
        return CellTextRange.__wrap(ret);
    }
    /**
     * The vertical alignment of the cell containing `node`: `"top"`/`"center"`/
     * `"bottom"` (defaults to `"top"`); `""` when not in a cell.
     * @param {string} node
     * @returns {string}
     */
    cellVerticalAlignAt(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_cellVerticalAlignAt(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The checked state of the checklist item at `node`: `1` checked, `0`
     * unchecked, `-1` when the paragraph is not a checklist item.
     * @param {string} node
     * @returns {number}
     */
    checkboxStateAt(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_checkboxStateAt(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * The clickable checkbox markers for every checklist item, as a JSON array of
     * `{ node, page, x, y, w, h, checked }` (page-local twips). The host paints a
     * transparent click target over each (the checkbox glyph itself is baked into
     * the page raster) and calls [`toggle_checklist_item`](Self::toggle_checklist_item)
     * on click. The rect is the hanging gutter just left of the item's text — where
     * the marker sits — sized from the line height so it scales with the font.
     * @returns {string}
     */
    checklistMarkers() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_checklistMarkers(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Clears direct character formatting over a range while preserving the
     * paragraph's named style. The selection remains intact in the host.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @returns {EditResult}
     */
    clearFormatting(node, start, end) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_clearFormatting(retptr, this.__wbg_ptr, ptr0, len0, start, end);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Clears every explicit tab stop over the selection.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {EditResult}
     */
    clearTabStops(start_node, start_offset, end_node, end_offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_clearTabStops(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * One comment thread — the identified comment plus every descendant
     * reply, ordered root-first by creation order — as a typed JSON array of
     * `ReviewCommentSummaryJson` (docs/81 REVIEW-GAP-022; docs/68
     * `commentThread`). Passing a reply's id resolves to that reply's thread
     * root first, mirroring [`delete_comment`](Self::delete_comment)'s
     * existing parent-chain walk (matching by either the parent's node id or
     * its `para_id` join key). Errs if `comment` is not a known comment id.
     * @param {string} comment
     * @returns {string}
     */
    commentThread(comment) {
        let deferred3_0;
        let deferred3_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(comment, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_commentThread(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            var r3 = getDataViewMemory0().getInt32(retptr + 4 * 3, true);
            var ptr2 = r0;
            var len2 = r1;
            if (r3) {
                ptr2 = 0; len2 = 0;
                throw takeObject(r2);
            }
            deferred3_0 = ptr2;
            deferred3_1 = len2;
            return getStringFromWasm0(ptr2, len2);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred3_0, deferred3_1, 1);
        }
    }
    /**
     * Continues the caret's numbered list from the nearest **preceding** numbered
     * list at the same level — the inverse of [`restart_list`](Self::restart_list).
     * Counters are keyed by `(instance, level)`, so adopting that earlier list's
     * instance resumes its sequence (e.g. an item restarted at `1.` rejoins the
     * list above it and renumbers `4.`, `5.`, …). The adopted instance is carried
     * through the contiguous following items that shared the caret paragraph's
     * current instance and level, so an entire restarted run rejoins at once.
     *
     * Bullets and non-list paragraphs are rejected; so is a paragraph that has no
     * distinct preceding numbered list at its level (nothing to continue from) or
     * one already contiguous with the list above it (already continuous).
     * @param {string} node
     * @returns {EditResult}
     */
    continueList(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_continueList(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The selection as a rich-run clipboard fragment, serialized as JSON (the
     * internal payload doc 67's "rich clipboard" row calls for): paragraph
     * breaks, run formatting (bold/italic/underline/strike/size/color/
     * highlight/vertAlign/font), and hyperlink targets. Scoped to paragraph
     * text — a `Table` block or any exotic inline (field, content control,
     * drawing, …) intersecting the range falls back to its existing plain-text
     * extraction, unstyled, so nothing is silently dropped, only unstyled.
     * Returns `"[]"` if either anchor is unknown.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {string}
     */
    copyRichRuns(start_node, start_offset, end_node, end_offset) {
        let deferred3_0;
        let deferred3_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_copyRichRuns(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred3_0 = r0;
            deferred3_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred3_0, deferred3_1, 1);
        }
    }
    /**
     * The selection as a **structured** clipboard fragment (JSON), for internal
     * OpenDoc-to-OpenDoc paste that must survive block structure the flat
     * [`copy_rich_runs`](Self::copy_rich_runs) fragment flattens: whole tables and
     * list paragraphs (their `numbering` reference). Returns the top-level body
     * blocks the selection touches, but **only** when at least one is a table or a
     * list item and every touched block is a paragraph or table (SDT/AltChunk
     * bail); otherwise returns `""` so the caller keeps the flat rich-run path for
     * ordinary prose. Whole blocks are captured even on a partial selection — Word
     * copies the whole table when a selection crosses it. Cross-document remapping
     * (numbering/media) is out of scope; this is the internal same-document payload.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {string}
     */
    copyStructured(start_node, start_offset, end_node, end_offset) {
        let deferred3_0;
        let deferred3_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_copyStructured(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred3_0 = r0;
            deferred3_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred3_0, deferred3_1, 1);
        }
    }
    /**
     * The plain text a selection covers — the first (read-only) action of the
     * interaction pipeline (doc 58 §4). Walks the model between the two anchors
     * in document order, slicing each node's shaped text at the byte offsets and
     * joining paragraphs with `\n`. Empty if either anchor is unknown.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {string}
     */
    copyText(start_node, start_offset, end_node, end_offset) {
        let deferred3_0;
        let deferred3_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_copyText(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred3_0 = r0;
            deferred3_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred3_0, deferred3_1, 1);
        }
    }
    /**
     * Creates a bookmark named `name` over the current selection (which may span
     * two paragraphs), inserting its start/end markers and registering the name
     * under a fresh id. One undoable action. The new id is discoverable via
     * [`bookmarkEntries`](Self::bookmark_entries). Rejects an empty or oversized
     * (> 255-byte) name, or a selection whose endpoints do not resolve.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} name
     * @returns {EditResult}
     */
    createBookmark(start_node, start_offset, end_node, end_offset, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_createBookmark(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Creates an empty header or footer for the section a page belongs to, and
     * links the section's default variant to it, as one undoable action. Returns
     * the `EditResult` whose caret is the new body's first paragraph, so the
     * host can enter the context it just created.
     *
     * `Edit header` on a document that has none previously had to refuse: the
     * ops to make one (docs/85 §8.3) exist but nothing called them. The body is
     * created carrying ONE empty paragraph rather than no blocks, because an
     * empty `Vec<BlockNode>` has nowhere to put a caret — the user would enter a
     * header they could not type in.
     * @param {string} region
     * @returns {EditResult}
     */
    createRunningContent(region) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(region, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_createRunningContent(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Creates a new paragraph style `name` from the current selection's
     * formatting (based on the selection's current paragraph style) and applies
     * it to every selected paragraph — Word's "Create a Style". Errors if a
     * paragraph style with that name already exists or the name is blank.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} name
     * @returns {EditResult}
     */
    createStyleFromSelection(start_node, start_offset, end_node, end_offset, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_createStyleFromSelection(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * @param {boolean} accept
     * @returns {EditResult}
     */
    decideAllRevisions(accept) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_decideAllRevisions(retptr, this.__wbg_ptr, accept);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Accepts or rejects one imported tracked move as an atomic source /
     * destination pair. The exact marker identities prevent an opaque or
     * producer-reused revision id from joining unrelated move ranges.
     * @param {string} from_start
     * @param {string} to_start
     * @param {boolean} accept
     * @returns {EditResult}
     */
    decideMovePair(from_start, to_start, accept) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(from_start, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(to_start, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_decideMovePair(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, accept);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Accepts or rejects a tracked revision by inline node id.
     * @param {string} revision
     * @param {boolean} accept
     * @returns {EditResult}
     */
    decideRevision(revision, accept) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(revision, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_decideRevision(retptr, this.__wbg_ptr, ptr0, len0, accept);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Accepts or rejects every editor-authored revision in one logical group.
     * Replacements are deletion/insertion pairs; formatting groups contain one
     * or more standard run-property changes without duplicating text.
     * @param {string} group
     * @param {boolean} accept
     * @returns {EditResult}
     */
    decideRevisionGroup(group, accept) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(group, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_decideRevisionGroup(retptr, this.__wbg_ptr, ptr0, len0, accept);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Backspace at a collapsed caret: deletes the character before `offset`, or —
     * at a paragraph start — joins this paragraph into the previous one.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    deleteBackward(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteBackward(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the bookmark with the 32-hex `id`, removing its definition and both
     * markers. One undoable action. Rejects an unknown id or a bookmark whose
     * markers do not resolve at paragraph top level.
     * @param {string} id
     * @returns {EditResult}
     */
    deleteBookmark(id) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(id, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteBookmark(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the column containing the caret's cell from a **regular** table
     * (refusing a table's only column), removing that cell from every row. The
     * caret lands in an adjacent surviving cell of the same row. Errors if the
     * caret is not inside a regular table.
     * @param {string} node
     * @returns {EditResult}
     */
    deleteColumn(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteColumn(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Removes a comment thread and all of its range/reference markers.
     * @param {string} comment
     * @returns {EditResult}
     */
    deleteComment(comment) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(comment, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteComment(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Forward-delete at a collapsed caret: deletes the character at `offset`, or —
     * at a paragraph end — joins the next paragraph into this one.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    deleteForward(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteForward(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Removes the object `node` (an inline or floating drawing, text box, or group)
     * as one undoable action (`DeleteObject`); undo restores it verbatim at its
     * original position. Errors if `node` is not a removable object or removing it
     * would leave the model invalid (it is a wrapper's sole child, or would leave
     * two mergeable runs adjacent — the host handles those before deleting).
     * @param {string} node
     * @returns {EditResult}
     */
    deleteObject(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteObject(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the text range `[start, end)` within one paragraph.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @returns {EditResult}
     */
    deleteRange(node, start, end) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteRange(retptr, this.__wbg_ptr, ptr0, len0, start, end);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes a single reply — and any nested replies beneath it — from a
     * comment thread as one undoable review action (docs/81 REVIEW-GAP-011),
     * leaving the thread root and every sibling reply intact. Errs if
     * `reply` is unknown, or if it is a thread root (no `parent_para_id`):
     * deleting a whole thread is [`delete_comment`](Self::delete_comment)'s
     * job, so this fails closed rather than silently removing an entire
     * anchored thread when handed a root id. A reply carries no body range
     * markers (only the anchored root does), so this touches the comment map
     * only and never rewrites body paragraphs.
     * @param {string} reply
     * @returns {EditResult}
     */
    deleteReply(reply) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(reply, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteReply(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the table row containing the caret's paragraph. Refuses to delete a
     * table's only row (a table's rows are non-empty — delete the table instead).
     * The caret lands in an adjacent surviving row. Errors if not inside a table.
     * @param {string} node
     * @returns {EditResult}
     */
    deleteRow(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteRow(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes a selection that may span paragraphs (a selection + Backspace/
     * Delete). Same-paragraph → one delete; cross-paragraph → delete the end
     * pieces + join, as one undoable action. Caret lands at the selection start.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {EditResult}
     */
    deleteSelection(start_node, start_offset, end_node, end_offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteSelection(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the whole table the caret is in (the innermost table for a nested
     * one). Refuses when the table is a cell's only content. The caret lands on the
     * first top-level body paragraph. Errors if the caret is not inside a table.
     * @param {string} node
     * @returns {EditResult}
     */
    deleteTable(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteTable(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the Unicode word before a collapsed caret as one undoable edit.
     * At a paragraph boundary this has the same safe join behavior as ordinary
     * Backspace.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    deleteWordBackward(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteWordBackward(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Deletes the Unicode word after a collapsed caret as one undoable edit.
     * At a paragraph boundary this has the same safe join behavior as ordinary
     * Delete.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    deleteWordForward(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_deleteWordForward(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Distributes the current preferred width evenly across every regular
     * table column, preserving the total width as one undoable resize.
     * @param {string} node
     * @returns {EditResult}
     */
    distributeTableColumns(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_distributeTableColumns(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Distributes explicit row heights evenly across every regular table row.
     * Auto-height rows are refused because assigning an arbitrary height can
     * clip content without a deterministic layout measurement.
     * @param {string} node
     * @returns {EditResult}
     */
    distributeTableRows(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_distributeTableRows(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * All modeled DOCX metadata (`docProps/core.xml`, `app.xml`, and
     * `custom.xml`) as one read-only JSON object. This complements
     * [`document_properties`](Self::document_properties), whose core-only
     * payload remains the stable mutation shape used by
     * [`set_document_properties`](Self::set_document_properties).
     * @returns {string}
     */
    documentMetadata() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_documentMetadata(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The document's heading outline as flat `"{level}\t{node}\t{text}"` rows (in
     * document order) — what the left Outline panel renders and navigates from. A
     * paragraph is a heading if it carries an `outlineLvl` or a Title/Heading N
     * style; `level` is 1-based (1 = top). Empty-text headings are skipped.
     * @returns {string[]}
     */
    documentOutline() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_documentOutline(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The document's core properties (`docProps/core.xml` — title, author,
     * subject, …) as a JSON object matching the model's own camelCase field
     * names (e.g. `{"title":"...","creator":"...","lastModifiedBy":null,...}`).
     * A field is `null` when unset. Empty (`{}`) when the document carries no
     * core properties at all.
     * @returns {string}
     */
    documentProperties() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_documentProperties(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Document statistics for the status footer: word count (whitespace-delimited
     * tokens across every paragraph, body + table cells + content controls),
     * paragraph count, and page count.
     * @returns {DocStats}
     */
    documentStats() {
        const ret = wasm.wasmdocument_documentStats(this.__wbg_ptr);
        return DocStats.__wrap(ret);
    }
    /**
     * Exports through one explicitly selected format and mode.
     *
     * Accepted modes are `semantic`, `preserve_when_safe`, and
     * `exact_if_unchanged`.
     * @param {string} format_id
     * @param {string} mode
     * @returns {WasmExportArtifact}
     */
    exportAs(format_id, mode) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(format_id, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(mode, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_exportAs(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return WasmExportArtifact.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Serializes the current (edited) document to a `.docx` package the host can
     * save. Uses the semantic writer (`v1::Document` → WordprocessingML), so all
     * modeled content and edits are written; unmodeled opaque parts from the
     * original import are not carried through in this first cut (a follow-up wires
     * `write_document_with_retained_parts`).
     * @returns {Uint8Array}
     */
    exportDocx() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_exportDocx(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            var r3 = getDataViewMemory0().getInt32(retptr + 4 * 3, true);
            if (r3) {
                throw takeObject(r2);
            }
            var v1 = getArrayU8FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 1, 1);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Finds the next/previous literal text match from a model position. Matches
     * are constrained to one text-bearing node; cross-paragraph phrases remain a
     * later text-layer search increment.
     * @param {string} query
     * @param {string} start_node
     * @param {number} start_offset
     * @param {boolean} forward
     * @param {boolean} match_case
     * @returns {TextMatch}
     */
    findText(query, start_node, start_offset, forward, match_case) {
        const ptr0 = passStringToWasm0(query, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len1 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_findText(this.__wbg_ptr, ptr0, len0, ptr1, len1, start_offset, forward, match_case);
        return TextMatch.__wrap(ret);
    }
    /**
     * The first caret position in the document (⌘↑ / select-all anchor).
     * @returns {Caret}
     */
    firstPosition() {
        const ret = wasm.wasmdocument_firstPosition(this.__wbg_ptr);
        return Caret.__wrap(ret);
    }
    /**
     * The uniform format state of a range (each toggle `true` only when every
     * covered run sets it) — drives a toolbar's active state and toggle direction.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @returns {Format}
     */
    formatAt(node, start, end) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_formatAt(this.__wbg_ptr, ptr0, len0, start, end);
        return Format.__wrap(ret);
    }
    /**
     * Like [`format_text`](Self::format_text) but across a selection that may span
     * paragraphs — each covered paragraph's sub-range is formatted, as one
     * undoable action.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @returns {EditResult}
     */
    formatSelection(start_node, start_offset, end_node, end_offset, bold, italic, underline, strike) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_formatSelection(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Applies a run-property change (bold/italic/underline/strike) over a range
     * within one paragraph. Each argument is a tri-state: `true`/`false` sets the
     * toggle, `undefined` leaves it unchanged. Runs straddling the range split so
     * the change lands exactly on the selection. The selection is preserved by
     * the frontend (formatting does not collapse it).
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @returns {EditResult}
     */
    formatText(node, start, end, bold, italic, underline, strike) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_formatText(retptr, this.__wbg_ptr, ptr0, len0, start, end, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves a page-local point (twips) on 1-based `page` to the nearest caret
     * anchor. Returns `undefined` if the page has no lines. `zone` is `"content"`
     * (inside a line) or `"outside"` (snapped in from a margin).
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {HitPayload | undefined}
     */
    hitTest(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_hitTest(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : HitPayload.__wrap(ret);
    }
    /**
     * Returns the deterministic import compatibility report as JSON.
     * @returns {string}
     */
    get importReportJson() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_importReportJson(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Whether the caret's paragraph is inside a table cell — drives the table
     * controls' enabled state.
     * @param {string} node
     * @returns {boolean}
     */
    inTable(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_inTable(this.__wbg_ptr, ptr0, len0);
        return ret !== 0;
    }
    /**
     * Inserts an empty column left or right of the caret's cell (`after` = to its
     * right) in a **regular** table (no merged cells; grid matches the cell count),
     * adding one empty cell to every row and a grid column. The caret stays in the
     * same row, moving to the new cell. Errors if the caret is not inside a regular
     * table.
     * @param {string} node
     * @param {boolean} after
     * @returns {EditResult}
     */
    insertColumn(node, after) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertColumn(retptr, this.__wbg_ptr, ptr0, len0, after);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts an endnote at the caret, the endnote counterpart of
     * [`Self::insert_footnote`]: the note is created in the endnotes collection
     * and its reference spliced at the caret. Same [`EditResult`] contract — the
     * caret lands in the new endnote body.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    insertEndnote(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertEndnote(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts a common field at the caret (`node`, `offset`). `kind` is one of
     * `"page"`, `"numpages"`, `"date"`, `"time"`, `"filename"`, `"author"`
     * (case-insensitive). `PAGE`/`NUMPAGES` recompute at pagination and ignore
     * `result_text`; the clock/context kinds cache `result_text` as their display
     * value (this engine reads no clock or filesystem, so the caller supplies the
     * already-formatted string). One undoable action. Rejects an unknown kind or a
     * caret that does not resolve to an editable paragraph position.
     * @param {string} node
     * @param {number} offset
     * @param {string} kind
     * @param {string | null} [result_text]
     * @returns {EditResult}
     */
    insertField(node, offset, kind, result_text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(kind, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(result_text) ? 0 : passStringToWasm0(result_text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertField(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts a footnote at the caret (paragraph `node`, byte `offset`): creates
     * an empty, ready-to-type footnote body and splices its reference mark into
     * the paragraph as one undoable action. The reference's displayed number is
     * derived at render — this only creates the reference and the body. The
     * returned [`EditResult`]'s caret (`node`/`offset`) is the model position of
     * the new note body's first paragraph, so the host can focus it for typing.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    insertFootnote(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertFootnote(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts a picture at the caret `(node, offset)`. The host supplies the
     * decoded `bytes`, its natural size in EMU, and the `mime` (the engine owns
     * no image codec — see docs/85 §Q8). The bytes are registered as a media
     * part and referenced by a fresh inline `Drawing`, inserted as one undoable
     * action. Rejects an unsupported image type. `mime` is one of image/png,
     * image/jpeg, image/gif, image/bmp, image/tiff, image/webp.
     * @param {string} node
     * @param {number} offset
     * @param {Uint8Array} bytes
     * @param {number} width_emu
     * @param {number} height_emu
     * @param {string} mime
     * @returns {EditResult}
     */
    insertImage(node, offset, bytes, width_emu, height_emu, mime) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(mime, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertImage(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, width_emu, height_emu, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces a selection (which may be collapsed) with normalized plain text
     * as one atomic user action. Newlines become paragraph splits inside the
     * same operation group, so paste, IME commit, and replace-with-Enter each
     * consume exactly one undo step.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} text
     * @returns {EditResult}
     */
    insertPlainText(start_node, start_offset, end_node, end_offset, text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertPlainText(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The same atomic plain-text command with a closed semantic history kind.
     * The host may distinguish paste and paragraph-break gestures, but cannot
     * inject arbitrary history labels.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} text
     * @param {string} action_kind
     * @returns {EditResult}
     */
    insertPlainTextAs(start_node, start_offset, end_node, end_offset, text, action_kind) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            const ptr3 = passStringToWasm0(action_kind, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len3 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertPlainTextAs(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2, ptr3, len3);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts an empty table row above or below the row containing the caret's
     * paragraph (`below` = after it). The new row mirrors the anchor row's column
     * structure (cell count, widths, spans) with empty cells; the caret lands in
     * its first cell. Errors if the caret is not inside a table.
     * @param {string} node
     * @param {boolean} below
     * @returns {EditResult}
     */
    insertRow(node, below) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertRow(retptr, this.__wbg_ptr, ptr0, len0, below);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Word's Insert ▸ Shapes: a floating preset shape at the caret, filled and
     * outlined in the default accent, as one undoable action. `geometry` is one
     * of `rectangle`, `roundRectangle`, `ellipse`, `triangle`, `rightTriangle`,
     * `diamond`, `line`.
     *
     * The shape is wrapped in a group-of-one because that is the only shape a
     * shape takes in this model — a lone autoshape imports the same way.
     * @param {string} node
     * @param {number} offset
     * @param {string} geometry
     * @returns {EditResult}
     */
    insertShape(node, offset, geometry) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(geometry, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertShape(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts `text` at a collapsed caret carrying explicit run formatting — typing
     * while Bold/Italic/… is *armed* at the caret. Inserts the text, then formats
     * exactly the inserted range, as one atomic single-undo action; the caret lands
     * after the inserted text. A toggle left `undefined` inherits the surrounding
     * run, so this equals `insertText` when nothing is armed. Consecutive armed
     * typing coalesces into one run (the second char inserts into the run the first
     * created).
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @param {number | null} [size_half_points]
     * @param {string | null} [color]
     * @param {string | null} [highlight]
     * @param {string | null} [vert_align]
     * @param {string | null} [font]
     * @returns {EditResult}
     */
    insertStyledText(node, offset, text, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertStyledText(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Additive typed-underline counterpart of [`insert_styled_text`](Self::insert_styled_text).
     * The extra optional tokens preserve an armed underline style/color without
     * changing the established host signature.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @param {number | null} [size_half_points]
     * @param {string | null} [color]
     * @param {string | null} [highlight]
     * @param {string | null} [vert_align]
     * @param {string | null} [font]
     * @param {string | null} [underline_style]
     * @param {string | null} [underline_color]
     * @returns {EditResult}
     */
    insertStyledTextWithUnderline(node, offset, text, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font, underline_style, underline_color) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            var ptr6 = isLikeNone(underline_style) ? 0 : passStringToWasm0(underline_style, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len6 = WASM_VECTOR_LEN;
            var ptr7 = isLikeNone(underline_color) ? 0 : passStringToWasm0(underline_color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len7 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertStyledTextWithUnderline(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5, ptr6, len6, ptr7, len7);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts a fresh `rows`×`cols` table into the body right after the caret's
     * top-level block, with equal columns spanning the content width and a thin
     * single-line grid so it is visible. The caret lands in the first cell.
     * @param {string} node
     * @param {number} rows
     * @param {number} cols
     * @returns {EditResult}
     */
    insertTable(node, rows, cols) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertTable(retptr, this.__wbg_ptr, ptr0, len0, rows, cols);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Inserts `text` at a caret anchor. Returns the new caret + revision.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @returns {EditResult}
     */
    insertText(node, offset, text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertText(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Word's Insert ▸ Text Box: a floating, square-wrapped text box at the caret,
     * sized 3" × 1" with one empty paragraph, as one undoable action. The caret
     * is left in the new box's paragraph (the host enters it), which is where
     * Word leaves it too.
     *
     * Floating rather than inline because that is what both Word and this
     * editor's own object chrome expect: an anchored box can be dragged,
     * wrapped and resized, and there is no inline↔floating conversion.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    insertTextBox(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_insertTextBox(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The last caret position in the document (⌘↓ / select-all focus).
     * @returns {Caret}
     */
    lastPosition() {
        const ret = wasm.wasmdocument_lastPosition(this.__wbg_ptr);
        return Caret.__wrap(ret);
    }
    /**
     * The line-spacing percentage of the paragraph at `node` (0 if unset) — for
     * the toolbar's spacing dropdown.
     * @param {string} node
     * @returns {number}
     */
    lineSpacingAt(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_lineSpacingAt(this.__wbg_ptr, ptr0, len0);
        return ret >>> 0;
    }
    /**
     * Resolves a direct content click to the hyperlink painted at that point.
     * External targets are returned verbatim for the host to policy-check before
     * opening. Internal targets additionally resolve their bookmark marker to a
     * model caret and 1-based target page, enabling TOC/page navigation without
     * making the runtime own browser navigation policy.
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {LinkHit | undefined}
     */
    linkAt(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_linkAt(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : LinkHit.__wrap(ret);
    }
    /**
     * Authored bookmark names, sorted for host navigation surfaces.
     * @returns {string[]}
     */
    listBookmarks() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_listBookmarks(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * All comments and threaded replies, in stable id order, as a typed JSON
     * array of `ReviewCommentSummaryJson`. This is the typed, single-purpose
     * replacement for `reviewSummary`'s nested `comments` array (docs/81
     * REVIEW-GAP-022; docs/68 `listComments`); `reviewSummary` keeps working
     * unchanged for existing callers. Use [`comment_thread`](Self::comment_thread)
     * to fetch just one thread instead of filtering this list by hand.
     * @returns {string}
     */
    listComments() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_listComments(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The marker format of the caret paragraph's own list level, as a compact
     * token the toolbar's bullet/number gallery highlights the current choice
     * with: a numbered level returns its `ST_NumberFormat` token
     * (`"decimal"`, `"lowerLetter"`, `"upperLetter"`, `"lowerRoman"`,
     * `"upperRoman"`, `"decimalZero"`, `"ordinal"`, `"cardinalText"`,
     * `"ordinalText"`, `"none"`, or a verbatim `Other` token); a bullet level
     * returns `"bullet:"` followed by its glyph (the `w:lvlText`). Returns `""`
     * when the paragraph is not a list item. The same tokens are accepted back
     * by [`set_list_format`](Self::set_list_format).
     * @param {string} node
     * @returns {string}
     */
    listFormatAt(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_listFormatAt(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The paragraph's numbering level (0 for a top-level item), or `-1` when
     * the paragraph is not part of a list. The host uses this to apply the
     * Word-style Backspace/Enter boundary rule without inspecting model data.
     * @param {string} node
     * @returns {number}
     */
    listLevelAt(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_listLevelAt(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * All tracked-change and formatting revisions, in document order, as a
     * typed JSON array of `ReviewRevisionSummaryJson`. This is the typed,
     * single-purpose replacement for `reviewSummary`'s nested `revisions`
     * array (docs/81 REVIEW-GAP-022; docs/68 `listRevisions`), using the exact
     * same final-with-markup anchor projection; `reviewSummary` keeps working
     * unchanged for existing callers.
     * @returns {string}
     */
    listRevisions() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_listRevisions(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The list kind the paragraph belongs to: `"bullet"`, `"numbered"`, or `""` —
     * drives the toolbar's list-button active state.
     * @param {string} node
     * @returns {string}
     */
    listStyleAt(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_listStyleAt(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The paragraph style names defined in the document (for a style dropdown),
     * sorted and de-duplicated.
     * @returns {string[]}
     */
    listStyles() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_listStyles(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The named table styles defined by the document, sorted for a gallery.
     * @returns {string[]}
     */
    listTableStyles() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_listTableStyles(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayJsValueFromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Merges the active table selection mode (`"row"`, `"column"`, `"table"`) in
     * a regular table. Selected-cell content is preserved by moving it into the
     * top-left merged cell in row-major order.
     * @param {string} node
     * @param {string} mode
     * @returns {EditResult}
     */
    mergeTableSelection(node, mode) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(mode, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_mergeTableSelection(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The code points the last pagination could **not** cover with any available
     * face — i.e. what renders as `.notdef` tofu (▯). Returned as `u32` scalar
     * values. A host queries this to decide which fallback fonts to fetch
     * (typically CJK / complex scripts absent from the bundled Latin faces).
     * @returns {Uint32Array}
     */
    missingCoverage() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_missingCoverage(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayU32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Moves a caret by one step in `dir` (`"left"`, `"right"`, `"up"`, `"down"`),
     * crossing line, paragraph, and page boundaries. Pure navigation — no edit.
     * @param {string} node
     * @param {number} offset
     * @param {string} dir
     * @returns {Caret}
     */
    moveCaret(node, offset, dir) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(dir, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_moveCaret(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return Caret.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Moves the tab stop at `from_twips` to `to_twips` (keeping its alignment) over
     * the selection — one undoable action, for a ruler drag.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} from_twips
     * @param {number} to_twips
     * @returns {EditResult}
     */
    moveTabStop(start_node, start_offset, end_node, end_offset, from_twips, to_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_moveTabStop(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, from_twips, to_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Moves the caret to the next/previous editable cell in the current innermost
     * table. Pure navigation: no edit and no row creation at table boundaries.
     * @param {string} node
     * @param {boolean} forward
     * @returns {Caret}
     */
    moveTableCell(node, forward) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_moveTableCell(retptr, this.__wbg_ptr, ptr0, len0, forward);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return Caret.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {ObjectHitPayload | undefined}
     */
    objectAt(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_objectAt(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : ObjectHitPayload.__wrap(ret);
    }
    /**
     * Returns the current source crop as normalized edge fractions. A zero
     * vector means the whole source is visible; `None` means the identity is
     * not a croppable picture. The lookup walks every supported document
     * surface so the host never resets an imported crop just because the
     * picture lives in a table, text box, header, or footer.
     * @param {string} node
     * @returns {Float64Array | undefined}
     */
    objectCrop(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectCrop(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            let v2;
            if (r0 !== 0) {
                v2 = getArrayF64FromWasm0(r0, r1).slice();
                wasm.__wbindgen_export3(r0, r1 * 8, 8);
            }
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves the deepest painted descendant of multi-child group `root` at a
     * page-local point. Ordinary `objectAt` deliberately returns the group root
     * so initial selection is a unit; double-click uses this bounded second
     * query to descend without the host reconstructing group geometry.
     * @param {string} root
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {ObjectHitPayload | undefined}
     */
    objectDescendantAt(root, page, x_twip, y_twip) {
        const ptr0 = passStringToWasm0(root, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_objectDescendantAt(this.__wbg_ptr, ptr0, len0, page, x_twip, y_twip);
        return ret === 0 ? undefined : ObjectHitPayload.__wrap(ret);
    }
    /**
     * The paint-ordered leaf references inside multi-child group `root`, as
     * JSON entries with the same identity/capability schema as `objectOrder`.
     * Keyboard Enter uses the first entry to descend into a selected group.
     * @param {string} root
     * @returns {string}
     */
    objectDescendants(root) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(root, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectDescendants(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * Sets or clears an object's alt text (`wp:docPr@descr`) as one undoable action
     * (`SetObjectDescr`). `descr` is the new text, or `None` to clear it. Errors if
     * `node` is not an alt-text-bearing object (an inline or floating drawing), or
     * the text is empty / longer than the model's byte bound.
     * The object `node`'s current alt text (`wp:docPr@descr`), or `None` when it
     * has none or does not resolve to a drawing. Lets a host prefill an alt-text
     * editor with the existing description instead of blind-overwriting it.
     * @param {string} node
     * @returns {string | undefined}
     */
    objectDescr(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectDescr(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            let v2;
            if (r0 !== 0) {
                v2 = getStringFromWasm0(r0, r1).slice();
                wasm.__wbindgen_export3(r0, r1 * 1, 1);
            }
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The current authored extent of the object `node` as `[widthEmu, heightEmu]`
     * (EMU), or the *placed* size (converted from the twip rect) when the object
     * has no authored extent — the base a drag-resize scales from. Empty if
     * `node` is not a currently placed selectable object.
     * @param {string} node
     * @returns {Float64Array}
     */
    objectExtent(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectExtent(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayF64FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 8, 8);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The supported resize-handle centers of the selected object `node`, as a
     * flat `[page, cxTwip, cyTwip, kind]` per handle (docs/85 §3.3
     * `objectHandles`), in the order NW, N, NE, E, SE, S, SW, W (`kind` = that
     * index). A carrier-specific mask may omit entries: inline objects expose
     * E/SE/S only because their flow anchor cannot move, while eligible floats
     * and groups expose all eight. The frontend paints a fixed screen-size grip
     * centered at each — engine-drawn chrome from the object's placed rect, so
     * it matches the raster with zero drift (docs/58). Empty if `node` is not
     * placed or its reference has no compatible exact-inverse resize command.
     * @param {string} node
     * @returns {Int32Array}
     */
    objectHandles(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectHandles(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Every selectable object in the document, in paint order, as JSON. Each
     * entry carries the stable `{surface, root, subject, path}` reference,
     * compatibility alias `node = subject`, `kind`, `page`, legacy `anchored`,
     * and the explicit structural capability bits consumed by the host
     * (docs/101 UXOBJ-001/002).
     *
     * Floating objects are otherwise reachable only by pointing at them:
     * `objectAt` needs a coordinate, so a keyboard user cannot select an image
     * or text box at all, and a screen reader cannot find a text box while
     * reading body text because it floats above the text layer. This is the
     * ordered list Tab / Shift+Tab traversal walks (docs/85 §8d, Q1) — Word
     * cycles floating objects with Tab once one is selected, and Word for the
     * web moves between graphics the same way.
     * @returns {string}
     */
    objectOrder() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_objectOrder(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The placed rectangle of the object `node`, as `[page, xTwip, yTwip,
     * wTwip, hTwip]` — the geometry the frontend paints the selection outline
     * from (docs/85 §3.3 `objectRect`). Empty if `node` is not a currently
     * placed selectable object.
     * @param {string} node
     * @returns {Int32Array}
     */
    objectRect(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectRect(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The current wrap mode of a floating object as one of the
     * [`set_object_wrap`](Self::set_object_wrap) tokens, or `""` if `node` is not
     * a floating object — drives the context bar's active-wrap reflection.
     * @param {string} node
     * @returns {string}
     */
    objectWrap(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_objectWrap(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The number of laid-out pages.
     * @returns {number}
     */
    get pageCount() {
        const ret = wasm.wasmdocument_pageCount(this.__wbg_ptr);
        return ret >>> 0;
    }
    /**
     * The first section's page geometry (width + side margins, in twips) — what the
     * horizontal ruler draws its scale and margin zones from.
     * @returns {RulerGeometry}
     */
    pageGeometry() {
        const ret = wasm.wasmdocument_pageGeometry(this.__wbg_ptr);
        return RulerGeometry.__wrap(ret);
    }
    /**
     * The first section's full page-setup geometry (page size, margins, and
     * orientation) as a JSON object — the "Page Setup" dialog's read side.
     * `null` if the document has no section (should not occur for an
     * imported DOCX, which always carries at least one).
     * @returns {string}
     */
    pageSetup() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_pageSetup(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * All section geometries plus the section containing `node` as the current
     * selection context. The host uses this to make Page Setup explicitly
     * section-aware instead of silently editing only the first section.
     * @param {string} node
     * @returns {string}
     */
    pageSetupSections(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_pageSetupSections(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The page box size of page `index` (0-based), in twips, resolved against
     * that page's own section (`w:sectPr`) so multi-section documents report
     * per-section geometry.
     *
     * Throws if `index` is out of range.
     * @param {number} index
     * @returns {PageSize}
     */
    pageSize(index) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_pageSize(retptr, this.__wbg_ptr, index);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return PageSize.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The paragraph's border edges as a bitmask — top=1, bottom=2, left=4, right=8 —
     * for reflecting which edge presets are active in the paragraph menu.
     * @param {string} node
     * @returns {number}
     */
    paragraphBorderEdges(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphBorderEdges(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * The paragraph's line-and-page-break flags (keep-with-next, keep-lines,
     * page-break-before) for reflecting the paragraph-properties inspector.
     * @param {string} node
     * @returns {ParagraphFlags}
     */
    paragraphFlags(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphFlags(this.__wbg_ptr, ptr0, len0);
        return ParagraphFlags.__wrap(ret);
    }
    /**
     * The paragraph's indentation (left/right/first-line/hanging, in twips; 0 when
     * unset) — for positioning the ruler's indent markers.
     * @param {string} node
     * @returns {Indents}
     */
    paragraphIndent(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphIndent(this.__wbg_ptr, ptr0, len0);
        return Indents.__wrap(ret);
    }
    /**
     * The byte length of paragraph `node`'s text (triple-click paragraph select).
     * @param {string} node
     * @returns {number}
     */
    paragraphLength(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphLength(this.__wbg_ptr, ptr0, len0);
        return ret >>> 0;
    }
    /**
     * The paragraph's shading fill as a packed `0xRRGGBB` int, or `-1` when unset —
     * for the paragraph-properties inspector's shading swatch.
     * @param {string} node
     * @returns {number}
     */
    paragraphShadingAt(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphShadingAt(this.__wbg_ptr, ptr0, len0);
        return ret;
    }
    /**
     * The paragraph's spacing (space before/after in twips, and line spacing) — for
     * the toolbar's line-&-paragraph-spacing menu to reflect current state. `-1`
     * means unset for before/after; `line_rule` is `0` auto (percent), `1` atLeast,
     * `2` exact (twips).
     * @param {string} node
     * @returns {ParagraphSpacing}
     */
    paragraphSpacing(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_paragraphSpacing(this.__wbg_ptr, ptr0, len0);
        return ParagraphSpacing.__wrap(ret);
    }
    /**
     * The name of the paragraph style applied at `node` (empty if none) — for
     * reflecting the current style in a dropdown.
     * @param {string} node
     * @returns {string}
     */
    paragraphStyleAt(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_paragraphStyleAt(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * The paragraph's explicit tab stops as a flat `[pos0, code0, pos1, code1, …]`
     * (position in twips, alignment code 0 start / 1 center / 2 end / 3 decimal /
     * 4 bar) — what the ruler renders its tab glyphs from.
     * @param {string} node
     * @returns {Int32Array}
     */
    paragraphTabs(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_paragraphTabs(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Editing-mode paste of **externally**-parsed structure (foreign HTML: an
     * Excel/Word/Google-Docs/web `<table>` or `<ul>`/`<ol>`), reconstructed as
     * real tables and bullet/numbered list paragraphs at a **collapsed caret in a
     * body paragraph**, as one undoable action. Distinct from
     * [`paste_structured`](Self::paste_structured) (the model-native round-trip of
     * an *internal* OpenDoc copy): the fragment is a small JS-built contract
     * (`ExternalFragment`) so the clipboard bridge never hand-writes model node
     * ids or numbering instances — run formatting rides the same `ClipboardRun`
     * the flat rich paste already uses, and each list paragraph names a
     * `{ ordered, level }` bound to a real numbering instance here via
     * `ensure_list`. Rejects a non-collapsed selection or a
     * caret outside a top-level body paragraph (the caller falls back to the flat
     * rich-run paste), matching `paste_structured`'s target contract. Text,
     * formatting, links, tables, and list levels survive; merged cells and deep
     * nesting degrade gracefully to a valid rectangular table / flat levels.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} fragment_json
     * @returns {EditResult}
     */
    pasteExternalStructured(start_node, start_offset, end_node, end_offset, fragment_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(fragment_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_pasteExternalStructured(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces the selection (if any) with a rich-run clipboard fragment — the
     * paste counterpart of [`copy_rich_runs`](Self::copy_rich_runs). Builds one
     * `InsertText`/`FormatText`/`SetHyperlink`/`SplitParagraph` op per run or
     * paragraph-break marker and commits them as a single undoable action (the
     * same atomic multi-op batching `replaceSelection` and `insertStyledText`
     * already use). `runs_json` must be a JSON array in the shape
     * `copyRichRuns` produces.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} runs_json
     * @returns {EditResult}
     */
    pasteRichRuns(start_node, start_offset, end_node, end_offset, runs_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(runs_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_pasteRichRuns(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Pastes a [`copy_structured`](Self::copy_structured) fragment at a **collapsed
     * caret in a body paragraph**, reconstructing the copied paragraphs and tables
     * with fresh ids as one undoable action. Rejects a non-collapsed selection or a
     * caret inside a table cell / SDT (the caller falls back to the flat rich-run
     * paste for those), so the reconstruction target is always the document body.
     * Inline content is sanitized to text, formatting, and hyperlinks; exotic
     * inline objects (drawings, fields, comments, bookmarks, …) are dropped rather
     * than duplicated with dangling references.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} fragment_json
     * @returns {EditResult}
     */
    pasteStructured(start_node, start_offset, end_node, end_offset, fragment_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(fragment_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_pasteStructured(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Redoes the last undone action.
     * @returns {EditResult}
     */
    redo() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_redo(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * User-facing label for the next redoable action, or an empty string when
     * the redo stack is empty.
     * @returns {string}
     */
    get redoLabel() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_redoLabel(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Registers a host-provided font (e.g. a network-fetched Noto face) as a
     * **coverage fallback** for the given ISO-15924 scripts (`"Hani"`, `"Hira"`,
     * `"Kana"`, `"Hang"`, …), then re-paginates so runs the bundled faces miss now
     * shape and render with it. This is the browser half of the font-provisioning
     * strategy — the single host-populatable seam.
     *
     * `scripts` may be empty to register the face without wiring script fallback
     * (see [`WasmDocument::register_font`]).
     * @param {Uint8Array} bytes
     * @param {string[]} scripts
     */
    registerFallbackFont(bytes, scripts) {
        const ptr0 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passArrayJsValueToWasm0(scripts, wasm.__wbindgen_export);
        const len1 = WASM_VECTOR_LEN;
        wasm.wasmdocument_registerFallbackFont(this.__wbg_ptr, ptr0, len0, ptr1, len1);
    }
    /**
     * Registers a host-provided font by family (no script-fallback wiring), then
     * re-paginates. Use when a document names a face the host can supply directly;
     * for CJK / complex-script coverage prefer
     * [`register_fallback_font`](Self::register_fallback_font).
     * @param {Uint8Array} bytes
     */
    registerFont(bytes) {
        const ptr0 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
        const len0 = WASM_VECTOR_LEN;
        wasm.wasmdocument_registerFont(this.__wbg_ptr, ptr0, len0);
    }
    /**
     * Registers a bounded batch of host-provided named-family fonts and
     * re-paginates exactly once. `bytes` is the concatenation of every font blob;
     * `lengths` gives each blob's byte length in the same order.
     *
     * The packed form keeps the JS↔WASM ABI simple and avoids one complete
     * document reflow per face when the browser provisions the Roboto/Noto
     * families before first paint.
     * @param {Uint8Array} bytes
     * @param {Uint32Array} lengths
     */
    registerFonts(bytes, lengths) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passArray32ToWasm0(lengths, wasm.__wbindgen_export);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_registerFonts(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            if (r1) {
                throw takeObject(r0);
            }
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Removes the hyperlink wrapper occupying exactly the supplied selection,
     * preserving its linked text and formatting.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @returns {EditResult}
     */
    removeHyperlink(node, start, end) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_removeHyperlink(retptr, this.__wbg_ptr, ptr0, len0, start, end);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Removes the tab stop at exactly `position_twips` over the selection.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} position_twips
     * @returns {EditResult}
     */
    removeTabStop(start_node, start_offset, end_node, end_offset, position_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_removeTabStop(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, position_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Renames the bookmark with the 32-hex `id` to `name`. One undoable action.
     * Rejects an unknown id or an empty/oversized name.
     * @param {string} id
     * @param {string} name
     * @returns {EditResult}
     */
    renameBookmark(id, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(id, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_renameBookmark(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Rasterizes page `index` (0-based) at `dpi` device pixels per inch and
     * returns raw premultiplied RGBA8888 the frontend blits via `putImageData`.
     *
     * Throws if `index` is out of range or the surface size is invalid.
     * @param {number} index
     * @param {number} dpi
     * @returns {PageBitmap}
     */
    renderPage(index, dpi) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_renderPage(retptr, this.__wbg_ptr, index, dpi);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return PageBitmap.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces MANY ranges with the same text as ONE undoable action (Replace
     * All). The caller supplies the match ranges as four parallel arrays and must
     * pass them in DESCENDING document order (last match first), so applying each
     * replacement in turn never shifts an earlier, not-yet-applied range's
     * offsets. Every range's delete+insert ops are concatenated into a single
     * action, so one Ctrl+Z undoes the whole Replace All (matching Word/Docs).
     * @param {string[]} start_nodes
     * @param {Uint32Array} start_offsets
     * @param {string[]} end_nodes
     * @param {Uint32Array} end_offsets
     * @param {string} text
     * @returns {EditResult}
     */
    replaceRanges(start_nodes, start_offsets, end_nodes, end_offsets, text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passArrayJsValueToWasm0(start_nodes, wasm.__wbindgen_export);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passArray32ToWasm0(start_offsets, wasm.__wbindgen_export);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passArrayJsValueToWasm0(end_nodes, wasm.__wbindgen_export);
            const len2 = WASM_VECTOR_LEN;
            const ptr3 = passArray32ToWasm0(end_offsets, wasm.__wbindgen_export);
            const len3 = WASM_VECTOR_LEN;
            const ptr4 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len4 = WASM_VECTOR_LEN;
            wasm.wasmdocument_replaceRanges(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, ptr2, len2, ptr3, len3, ptr4, len4);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces a selection with `text` (type-over) as one undoable action.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} text
     * @returns {EditResult}
     */
    replaceSelection(start_node, start_offset, end_node, end_offset, text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_replaceSelection(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Adds a reply to an existing comment thread.
     * @param {string} parent
     * @param {string} text
     * @param {string | null} [author]
     * @param {string | null} [initials]
     * @param {string | null} [date]
     * @returns {EditResult}
     */
    replyToComment(parent, text, author, initials, date) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(parent, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(initials) ? 0 : passStringToWasm0(initials, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            wasm.wasmdocument_replyToComment(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, ptr2, len2, ptr3, len3, ptr4, len4);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Commits the final page-local rectangle of one handle drag as a single
     * exact-inverse history action (`UXOBJ-003`, doc 101 §7.3). Inline objects
     * replace extent only and therefore accept rectangles whose top-left did
     * not move. Floating drawings/text boxes replace extent plus a canonical
     * page-relative anchor. Eligible groups additionally scale their outer
     * `wp:extent` and the scale-bearing `a:xfrm` fields while retaining child
     * coordinates. The host previews freely but calls this exactly once on
     * pointer-up.
     * @param {string} root
     * @param {number} left_emu
     * @param {number} top_emu
     * @param {number} width_emu
     * @param {number} height_emu
     * @returns {EditResult}
     */
    resizeObject(root, left_emu, top_emu, width_emu, height_emu) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(root, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_resizeObject(retptr, this.__wbg_ptr, ptr0, len0, left_emu, top_emu, width_emu, height_emu);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves a click at `(x, y)` on `page` to a caret AND the story it lands
     * in, honouring the story currently open.
     *
     * This is the whole entry/exit rule in one place, which is why it exists:
     * the host used to infer "the user left the header" from a hit-test that
     * found no glyph, so clicking the empty right-hand end of a header line
     * threw the user out of the context they were typing in. Geometry decides
     * the story; content decides only the offset within it.
     *
     * Returns the caret plus `band` — `""` for the body — so the host applies
     * the answer instead of deriving it.
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {RunningHitPayload | undefined}
     */
    resolveClick(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_resolveClick(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : RunningHitPayload.__wrap(ret);
    }
    /**
     * Restarts a numbered list at the caret's paragraph and carries the new
     * numbering instance through the contiguous following items at the same
     * level. Bullets and non-list paragraphs are rejected.
     * @param {string} node
     * @returns {EditResult}
     */
    restartList(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_restartList(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Review inventory for the margin UI. Anchors use the same final-with-markup
     * byte projection as layout/editing, including collapsed deleted ranges.
     * @returns {string}
     */
    reviewSummary() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_reviewSummary(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * The comments that overlap one tracked-change/formatting revision —
     * the typed data (a `ReviewCommentSummaryJson` array, the same shape
     * [`comment_thread`](Self::comment_thread) returns) backing a review UI
     * that threads a comment card beneath the change it discusses (docs/81
     * REVIEW-GAP-012, "comment-to-revision overlap mapping"). A comment
     * overlaps a revision when their resolved anchor ranges lie in the same
     * paragraph and intersect in the shared final-with-markup byte space
     * that both [`list_comments`](Self::list_comments) and
     * [`list_revisions`](Self::list_revisions) already report. Ordered by
     * stable comment id. Errs if `revision` is not a known revision id.
     *
     * This is the read/overlap half of REVIEW-GAP-012 and changes no DOCX
     * comment-ownership semantics: it invents no model link between a
     * comment and a revision, it only reports where their existing anchor
     * ranges coincide. Authoring a reply *to* a revision is `addComment`
     * over that revision's own anchor range (available from
     * [`list_revisions`](Self::list_revisions)); the new comment then
     * appears here. A pure deletion revision is zero-width in the
     * final-with-markup projection, so it cannot itself carry a new
     * same-range comment anchor — that authoring case remains deferred
     * (docs/81 REVIEW-GAP-012).
     * @param {string} revision
     * @returns {string}
     */
    revisionThread(revision) {
        let deferred3_0;
        let deferred3_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(revision, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_revisionThread(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            var r3 = getDataViewMemory0().getInt32(retptr + 4 * 3, true);
            var ptr2 = r0;
            var len2 = r1;
            if (r3) {
                ptr2 = 0; len2 = 0;
                throw takeObject(r2);
            }
            deferred3_0 = ptr2;
            deferred3_1 = len2;
            return getStringFromWasm0(ptr2, len2);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred3_0, deferred3_1, 1);
        }
    }
    /**
     * Resolves the first caret in the existing default header/footer body.
     * This is deliberately model-based: a missed geometry probe must never be
     * interpreted as permission to replace an existing running story.
     * @param {string} region
     * @returns {HitPayload | undefined}
     */
    runningContentCaret(region) {
        const ptr0 = passStringToWasm0(region, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_runningContentCaret(this.__wbg_ptr, ptr0, len0);
        return ret === 0 ? undefined : HitPayload.__wrap(ret);
    }
    /**
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {RunningHitPayload | undefined}
     */
    runningHitTest(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_runningHitTest(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : RunningHitPayload.__wrap(ret);
    }
    /**
     * The caret nearest `(x, y)` inside `band` on `page`, snapping from any
     * point in the band — the resolution to use while that band is open, where
     * empty space still belongs to the header. `None` if the band has no
     * content on that page.
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @param {string} band
     * @returns {RunningHitPayload | undefined}
     */
    runningSnap(page, x_twip, y_twip, band) {
        const ptr0 = passStringToWasm0(band, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_runningSnap(this.__wbg_ptr, page, x_twip, y_twip, ptr0, len0);
        return ret === 0 ? undefined : RunningHitPayload.__wrap(ret);
    }
    /**
     * Whether the first page uses its own header/footer, and whether even pages
     * do, as JSON `{ firstPage, evenOdd }` — the read side of Word's two
     * running-content toggles (docs/85 Q6).
     * @returns {string}
     */
    runningVariants() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_runningVariants(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the ordered start or end edge of a selection. Selection ordering
     * belongs to the engine because anchors in different paragraphs cannot be
     * compared correctly by the browser host.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {boolean} toward_end
     * @returns {Caret}
     */
    selectionEdge(start_node, start_offset, end_node, end_offset, toward_end) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_selectionEdge(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, toward_end);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return Caret.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The uniform format state across a (possibly multi-paragraph) selection — a
     * toggle is `true` only when every covered run in every covered paragraph sets
     * it.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {Format}
     */
    selectionFormat(start_node, start_offset, end_node, end_offset) {
        const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len1 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_selectionFormat(this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
        return Format.__wrap(ret);
    }
    /**
     * Uniform paragraph properties over every paragraph touched by the model
     * selection. Each value has an explicit mixed flag; booleans use state
     * 0=off, 1=on, 2=mixed.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {ParagraphState}
     */
    selectionParagraphState(start_node, start_offset, end_node, end_offset) {
        const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len1 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_selectionParagraphState(this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
        return ParagraphState.__wrap(ret);
    }
    /**
     * Highlight rectangles for a selection, flattened as `[page, x, y, w, h, …]`
     * (page-local twips), one 5-tuple per covered line-fragment. The range may
     * span nodes and pages and be given in either direction (a backward/upward
     * drag) — the endpoints are ordered in document order first.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {Int32Array}
     */
    selectionRects(start_node, start_offset, end_node, end_offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_selectionRects(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v3 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v3;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The uniform effective run styling of the selection
     * (size/color/authored-font/vert-align) for reflecting the current values in
     * the toolbar. Blank/zero means mixed or unset.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @returns {RunStyle}
     */
    selectionRunStyle(start_node, start_offset, end_node, end_offset) {
        const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len1 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_selectionRunStyle(this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset);
        return RunStyle.__wrap(ret);
    }
    /**
     * Sets or updates the host-supplied review identity that `addComment`,
     * `replyToComment`, and the `suggest*` tracked-change calls fall back to
     * when their own `author`/`initials` arguments are omitted. This is the
     * explicit host identity seam described in docs/68 and docs/82: the
     * engine has no built-in concept of "current user," never reads DOM
     * state, and never invents an author — the host calls this once (or
     * again whenever its active user changes) instead.
     *
     * `name` is required and trimmed; passing an empty/whitespace-only name
     * clears the active identity, after which authoring calls must supply an
     * explicit `author` or fail their existing empty-author validation.
     * `initials` is used as given (trimmed) when non-empty, otherwise derived
     * from up to the first two words of `name`. `id` is an opaque,
     * host-defined stable identifier (for example for deterministic per-author
     * coloring); OpenDoc never serializes it into the document or interprets it.
     * @param {string} name
     * @param {string | null} [initials]
     * @param {string | null} [id]
     */
    setActiveAuthor(name, initials, id) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(initials) ? 0 : passStringToWasm0(initials, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(id) ? 0 : passStringToWasm0(id, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setActiveAuthor(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            if (r1) {
                throw takeObject(r0);
            }
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets paragraph alignment over every paragraph the selection touches:
     * `"start"`, `"center"`, `"end"`, or `"justify"`.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} align
     * @returns {EditResult}
     */
    setAlignment(start_node, start_offset, end_node, end_offset, align) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setAlignment(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Applies a border `edges` preset to the cell containing `node`: `"none"`/`"box"`
     * clear-or-set the four cell edges; `"top"`/`"bottom"`/`"left"`/`"right"` toggle
     * one edge. Set edges use a single line of `size_eighth_points` in the RGB.
     * @param {string} node
     * @param {string} edges
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @param {number} size_eighth_points
     * @returns {EditResult}
     */
    setCellBorder(node, edges, r, g, b, size_eighth_points) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(edges, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setCellBorder(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, r, g, b, size_eighth_points);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets or clears the background shading fill of the cell containing `node`.
     * @param {string} node
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @param {boolean} clear
     * @returns {EditResult}
     */
    setCellShading(node, r, g, b, clear) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setCellShading(retptr, this.__wbg_ptr, ptr0, len0, r, g, b, clear);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the cell's vertical text alignment: `"top"` | `"center"` | `"bottom"`.
     * @param {string} node
     * @param {string} align
     * @returns {EditResult}
     */
    setCellVerticalAlign(node, align) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setCellVerticalAlign(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves or reopens an existing comment as one undoable review action.
     * @param {string} comment
     * @param {boolean} done
     * @returns {EditResult}
     */
    setCommentResolved(comment, done) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(comment, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setCommentResolved(retptr, this.__wbg_ptr, ptr0, len0, done);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces the document's core properties from a JSON object in the same
     * shape [`document_properties`](Self::document_properties) returns — an
     * absent/`null` field clears that property. One undoable action.
     * @param {string} properties_json
     * @returns {EditResult}
     */
    setDocumentProperties(properties_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(properties_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setDocumentProperties(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resolves a page-local point inside a page's HEADER or FOOTER content, as
     * `{ node, offset, band }`, or `None` if the point is in neither band.
     *
     * Deliberately separate from [`hit_test`]: the body walk must keep
     * answering exactly as it does today — a click in the top margin still
     * snaps to the nearest body line — so entering a header stays a gesture the
     * host asks for rather than a side effect of clicking near the top of a
     * page. The host tries this first only when it means to (a double-click in
     * the margin band, or the header marker), and falls back to `hitTest`.
     *
     * The position it returns is an ordinary `NodeId` + offset, which the edit
     * ops already resolve wherever it lives (the surface resolution in
     * `casual-doc-edit`), so nothing downstream needs a sub-document address.
     *
     * [`hit_test`]: WasmDocument::hit_test
     * Declares the story the editor is in: `band` is `"header"`/`"footer"` with
     * the page whose copy is open, or `None` for the page body.
     *
     * Held once, as the editing context it is, rather than threaded through
     * every geometry call — where a single missed argument silently restores
     * the caret-on-the-wrong-page defect.
     * @param {string | null} [band]
     * @param {number | null} [page]
     */
    setEditContext(band, page) {
        var ptr0 = isLikeNone(band) ? 0 : passStringToWasm0(band, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        var len0 = WASM_VECTOR_LEN;
        wasm.wasmdocument_setEditContext(this.__wbg_ptr, ptr0, len0, isLikeNone(page) ? Number.MAX_SAFE_INTEGER : (page) >>> 0);
    }
    /**
     * Turns distinct even/odd headers and footers on or off (Word's "Different
     * Odd & Even Pages"). Document-scoped, as OOXML carries it.
     * @param {boolean} enabled
     * @returns {EditResult}
     */
    setEvenOddVariant(enabled) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_setEvenOddVariant(retptr, this.__wbg_ptr, enabled);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the first-line indent (relative to the left indent) to `twips` — the
     * ruler's top marker drag. Positive is a first-line indent; negative becomes a
     * hanging indent (mutually exclusive, as in Word); zero clears both.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @returns {EditResult}
     */
    setFirstLineIndent(start_node, start_offset, end_node, end_offset, twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setFirstLineIndent(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Turns the distinct first-page header/footer on or off for the first
     * section (Word's "Different First Page").
     * @param {boolean} enabled
     * @returns {EditResult}
     */
    setFirstPageVariant(enabled) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_setFirstPageVariant(retptr, this.__wbg_ptr, enabled);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the font family over the selection (`w:rFonts`).
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} family
     * @returns {EditResult}
     */
    setFont(start_node, start_offset, end_node, end_offset, family) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(family, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setFont(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the font size over the selection, in **points**.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} points
     * @returns {EditResult}
     */
    setFontSize(start_node, start_offset, end_node, end_offset, points) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setFontSize(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, points);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the highlight over the selection to a named color (`"yellow"`,
     * `"green"`, … or `"none"` to clear).
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} name
     * @returns {EditResult}
     */
    setHighlight(start_node, start_offset, end_node, end_offset, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setHighlight(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Creates or updates a hyperlink over a non-empty same-paragraph selection.
     * A target beginning with `#` is an internal bookmark anchor; all other
     * targets are retained as external URLs. Activation policy remains host-owned.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @param {string} target
     * @param {string | null} [tooltip]
     * @returns {EditResult}
     */
    setHyperlink(node, start, end, target, tooltip) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(target, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(tooltip) ? 0 : passStringToWasm0(tooltip, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setHyperlink(retptr, this.__wbg_ptr, ptr0, len0, start, end, ptr1, len1, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets or clears the source-rectangle crop (`a:srcRect`) of the picture `node`
     * as one undoable action (`SetImageCrop`). `insets` is `[left, top, right,
     * bottom]`, each the fraction (0..=1) of that source edge to hide; `None` (or
     * an all-zero inset set) clears the crop so the whole source fills the box.
     * Out-of-range edges are clamped. Errors if `node` is not a croppable picture
     * (an inline or floating drawing) or `insets` is not exactly four values.
     * @param {string} node
     * @param {Float64Array | null} [insets]
     * @returns {EditResult}
     */
    setImageCrop(node, insets) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(insets) ? 0 : passArrayF64ToWasm0(insets, wasm.__wbindgen_export);
            var len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setImageCrop(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets "keep lines together" (`w:keepLines`) over the selection's paragraphs.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {boolean} on
     * @returns {EditResult}
     */
    setKeepLinesTogether(start_node, start_offset, end_node, end_offset, on) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setKeepLinesTogether(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, on);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets "keep with next" (`w:keepNext`) over the selection's paragraphs.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {boolean} on
     * @returns {EditResult}
     */
    setKeepWithNext(start_node, start_offset, end_node, end_offset, on) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setKeepWithNext(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, on);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the left (start) indent to an absolute `twips` (clamped ≥ 0) — the
     * ruler's left-indent marker drag.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @returns {EditResult}
     */
    setLeftIndent(start_node, start_offset, end_node, end_offset, twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setLeftIndent(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets multiple line spacing (`w:lineRule="auto"`) as a percentage of single
     * (100 = single, 150 = 1.5×, 200 = double) over the selection's paragraphs. Per
     * the model, the `auto` rule leaves `line_rule` `None` and rides `line_percent`,
     * so exact/atLeast values are cleared.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} percent
     * @returns {EditResult}
     */
    setLineSpacing(start_node, start_offset, end_node, end_offset, percent) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setLineSpacing(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, percent);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets a fixed line height in twips over the selection's paragraphs: `at_least`
     * true → `w:lineRule="atLeast"` (grows for tall content), false → `"exact"`
     * (clipped). Clears the `auto` percentage.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @param {boolean} at_least
     * @returns {EditResult}
     */
    setLineSpacingExact(start_node, start_offset, end_node, end_offset, twips, at_least) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setLineSpacingExact(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips, at_least);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the marker format of the caret paragraph's list — the bullet glyph
     * (for a bullet list) or the number format (for a numbered list) — matching
     * the picker in Word/Docs, as one undoable action. `spec` is a token from
     * [`list_format_at`](Self::list_format_at): `"bullet:<glyph>"` sets a bullet
     * list whose marker is `<glyph>`; a bare `ST_NumberFormat` token
     * (`"decimal"`, `"lowerLetter"`, `"upperLetter"`, `"lowerRoman"`,
     * `"upperRoman"`, `"decimalZero"`) sets a numbered list to that numeral
     * system.
     *
     * The change is scoped to the caret's list only: a fresh abstract definition
     * (cloned from the current one, with the caret level's `w:numFmt`/`w:lvlText`
     * retargeted) and a fresh instance are minted, and every paragraph in the
     * contiguous run that shares the caret's list instance is repointed at the
     * new instance — so other lists sharing the editor's reusable definition are
     * untouched. Only single-level marker formatting is handled here; authoring a
     * full multilevel list is a separate follow-up.
     *
     * Errors when the caret is not a list item, or when `spec` is empty or names
     * no supported format.
     * @param {string} node
     * @param {string} spec
     * @returns {EditResult}
     */
    setListFormat(node, spec) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(spec, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setListFormat(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Moves a **floating** object to an absolute page position: `left_emu` /
     * `top_emu` are page-local EMU (the top-left of the object measured from the
     * page edge — a free drag positions relative to the page). Commits one
     * undoable `SetAnchor` (docs/85 §5.3): the anchor's position becomes
     * `page`-relative `posOffset`s while wrap, wrap distances, and z-order are
     * preserved. Errors if `node` is not a currently floating object. Object
     * geometry is untracked, so the host blocks this in Suggesting/Viewing mode.
     * @param {string} node
     * @param {number} left_emu
     * @param {number} top_emu
     * @returns {EditResult}
     */
    setObjectAnchorPosition(node, left_emu, top_emu) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setObjectAnchorPosition(retptr, this.__wbg_ptr, ptr0, len0, left_emu, top_emu);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * @param {string} node
     * @param {string | null} [descr]
     * @returns {EditResult}
     */
    setObjectDescr(node, descr) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(descr) ? 0 : passStringToWasm0(descr, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setObjectDescr(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Resizes an inline drawing / text box to `width_emu` × `height_emu`
     * (`wp:extent`, EMU) as one undoable action — the commit of a handle
     * drag-resize (docs/85 §5.3, `SetExtent`). A drag previews as host chrome and
     * commits exactly once here on release, so a resize is one undo step. Object
     * geometry has no tracked-revision representation, so the host blocks this in
     * Suggesting/Viewing mode before calling. Errors if `node` is not a resizable
     * inline object.
     * @param {string} node
     * @param {number} width_emu
     * @param {number} height_emu
     * @returns {EditResult}
     */
    setObjectExtent(node, width_emu, height_emu) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setObjectExtent(retptr, this.__wbg_ptr, ptr0, len0, width_emu, height_emu);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Changes a **floating** object's text-wrap mode (docs/85 §5.3), committing
     * one undoable `SetAnchor`. Accepts `"square"`, `"tight"`, `"through"`,
     * `"topAndBottom"`, `"behind"` (wrap-none behind the text), and `"front"`
     * (wrap-none over the text); everything else is ignored. Errors if `node` is
     * not a floating object. Blocked in Suggesting/Viewing mode by the host.
     * @param {string} node
     * @param {string} mode
     * @returns {EditResult}
     */
    setObjectWrap(node, mode) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(mode, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setObjectWrap(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets "page break before" (`w:pageBreakBefore`) over the selection's paragraphs.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {boolean} on
     * @returns {EditResult}
     */
    setPageBreakBefore(start_node, start_offset, end_node, end_offset, on) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setPageBreakBefore(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, on);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Installs a new page-setup geometry from a JSON object in the shape
     * [`page_setup`](Self::page_setup) returns — the dialog's write side. One
     * undoable action.
     * @param {string} page_setup_json
     * @returns {EditResult}
     */
    setPageSetup(page_setup_json) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(page_setup_json, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setPageSetup(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Applies a paragraph border `edges` preset over the selection: `"none"` clears
     * all four edges; `"box"` sets all four; `"top"`/`"bottom"`/`"left"`/`"right"`
     * toggle that single edge (start = left, end = right). Set edges use a single
     * line of `size_eighth_points` (eighth-points) in the given RGB.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} edges
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @param {number} size_eighth_points
     * @returns {EditResult}
     */
    setParagraphBorder(start_node, start_offset, end_node, end_offset, edges, r, g, b, size_eighth_points) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(edges, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setParagraphBorder(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2, r, g, b, size_eighth_points);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the paragraph background shading (`w:shd` fill) over the selection to
     * an RGB, or clears it when `clear` is true.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @param {boolean} clear
     * @returns {EditResult}
     */
    setParagraphShading(start_node, start_offset, end_node, end_offset, r, g, b, clear) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setParagraphShading(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, r, g, b, clear);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the paragraph style (`w:pStyle`) over the selection's paragraphs to the
     * style with the given name (e.g. `"Heading 1"`, `"Title"`, `"Normal"`), or
     * clears it when `style_name` is empty. Throws if no such paragraph style
     * exists in the document.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} style_name
     * @returns {EditResult}
     */
    setParagraphStyle(start_node, start_offset, end_node, end_offset, style_name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(style_name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setParagraphStyle(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the right (end) indent to an absolute `twips` (clamped ≥ 0) — the ruler's
     * right-indent marker drag.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @returns {EditResult}
     */
    setRightIndent(start_node, start_offset, end_node, end_offset, twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setRightIndent(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Word's Shape Fill: sets a shape's fill to a solid colour, or clears it.
     * `rgba` is `#rrggbb`; `None` removes the fill.
     * @param {string} shape
     * @param {string | null} [rgba]
     * @returns {EditResult}
     */
    setShapeFill(shape, rgba) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(shape, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(rgba) ? 0 : passStringToWasm0(rgba, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setShapeFill(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Word's Shape Outline: sets a shape's outline colour and width (EMU), or
     * clears the outline entirely with `rgba = None`.
     *
     * Whatever the caller leaves out is INHERITED from the outline the shape
     * already has — colour, width, and equally the dash pattern and the
     * head/tail line ends, which no control exposes yet. Rebuilding the stroke
     * from scratch would silently straighten a dashed callout's leader the
     * moment someone recoloured it.
     * @param {string} shape
     * @param {string | null} [rgba]
     * @param {number | null} [width_emu]
     * @returns {EditResult}
     */
    setShapeOutline(shape, rgba, width_emu) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(shape, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(rgba) ? 0 : passStringToWasm0(rgba, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setShapeOutline(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, !isLikeNone(width_emu), isLikeNone(width_emu) ? 0 : width_emu);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Toggles the read-only "show changes" markup view (docs/93). When on, a
     * separate markup layout is built (struck deletions, author-colored/
     * underlined insertions, highlighted comment ranges) and `renderPage` /
     * `pageCount` render from it; caret/selection/hit-test always use the editing
     * layout, so this is a pure read-only preview. Toggling off drops it. Idempotent.
     * @param {boolean} on
     */
    setShowChanges(on) {
        wasm.wasmdocument_setShowChanges(this.__wbg_ptr, on);
    }
    /**
     * Sets space after the paragraph (`w:spacing w:after`) in twips over the
     * selection; a negative value clears it. Setting an explicit value also turns
     * off `afterAutospacing`.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @returns {EditResult}
     */
    setSpaceAfter(start_node, start_offset, end_node, end_offset, twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setSpaceAfter(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets space before the paragraph (`w:spacing w:before`) in twips over the
     * selection; a negative value clears it (back to the style default). Setting an
     * explicit value also turns off `beforeAutospacing`.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} twips
     * @returns {EditResult}
     */
    setSpaceBefore(start_node, start_offset, end_node, end_offset, twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setSpaceBefore(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Adds or replaces a tab stop at `position_twips` (from the leading margin) with
     * alignment `align_code` (0 start, 1 center, 2 end, 3 decimal) over the
     * selection's paragraphs. Any existing stop at the same position is replaced;
     * stops stay sorted by position.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} position_twips
     * @param {number} align_code
     * @returns {EditResult}
     */
    setTabStop(start_node, start_offset, end_node, end_offset, position_twips, align_code) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTabStop(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, position_twips, align_code);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the table's horizontal alignment on the page: `"left"`/`"center"`/
     * `"right"` (the table containing `node`).
     * @param {string} node
     * @param {string} align
     * @returns {EditResult}
     */
    setTableAlignment(node, align) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableAlignment(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Applies a border `edges` preset to the whole table containing `node`:
     * `"none"` clears all; `"box"` sets the four outer edges; `"all"` sets outer +
     * inside gridlines; `"top"`/`"bottom"`/`"left"`/`"right"` toggle one outer edge.
     * @param {string} node
     * @param {string} edges
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @param {number} size_eighth_points
     * @returns {EditResult}
     */
    setTableBorder(node, edges, r, g, b, size_eighth_points) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(edges, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableBorder(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, r, g, b, size_eighth_points);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets uniform default cell margins on the active table; a negative value
     * clears the table-level default margins.
     * @param {string} node
     * @param {number} margin_twips
     * @returns {EditResult}
     */
    setTableCellMargins(node, margin_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableCellMargins(retptr, this.__wbg_ptr, ptr0, len0, margin_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets default cell spacing on the active table; a negative value clears it.
     * @param {string} node
     * @param {number} spacing_twips
     * @returns {EditResult}
     */
    setTableCellSpacing(node, spacing_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableCellSpacing(retptr, this.__wbg_ptr, ptr0, len0, spacing_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the active regular table column's preferred width in twips. Updates
     * the shared grid and each cell in that visual column so DOCX round-tripping
     * and layout stay in agreement.
     * @param {string} node
     * @param {number} width_twips
     * @returns {EditResult}
     */
    setTableColumnWidth(node, width_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableColumnWidth(retptr, this.__wbg_ptr, ptr0, len0, width_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets a specific regular-table column's preferred width in twips.
     * @param {string} node
     * @param {number} column
     * @param {number} width_twips
     * @returns {EditResult}
     */
    setTableColumnWidthAt(node, column, width_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableColumnWidthAt(retptr, this.__wbg_ptr, ptr0, len0, column, width_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Switches table layout between fixed-width and AutoFit-style behavior.
     * @param {string} node
     * @param {boolean} fixed
     * @returns {EditResult}
     */
    setTableFixedLayout(node, fixed) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableFixedLayout(retptr, this.__wbg_ptr, ptr0, len0, fixed);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets or clears the current row's repeat-header flag.
     * @param {string} node
     * @param {boolean} enabled
     * @returns {EditResult}
     */
    setTableHeaderRow(node, enabled) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableHeaderRow(retptr, this.__wbg_ptr, ptr0, len0, enabled);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the table leading indent in twips.
     * @param {string} node
     * @param {number} indent_twips
     * @returns {EditResult}
     */
    setTableIndent(node, indent_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableIndent(retptr, this.__wbg_ptr, ptr0, len0, indent_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the active row height: `"auto"` clears the explicit value,
     * `"atLeast"` stores a minimum, and `"exact"` stores an exact height.
     * @param {string} node
     * @param {number} height_twips
     * @param {string} rule
     * @returns {EditResult}
     */
    setTableRowHeight(node, height_twips, rule) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(rule, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableRowHeight(retptr, this.__wbg_ptr, ptr0, len0, height_twips, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the table's preferred width in twips, or clears it when negative.
     * @param {string} node
     * @param {number} width_twips
     * @returns {EditResult}
     */
    setTableWidth(node, width_twips) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTableWidth(retptr, this.__wbg_ptr, ptr0, len0, width_twips);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces a text box's complete body-property record in one undoable edit.
     * @param {string} node
     * @param {string} properties
     * @returns {EditResult}
     */
    setTextBoxBodyProperties(node, properties) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(properties, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTextBoxBodyProperties(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the text color over the selection to an explicit RGB.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {number} r
     * @param {number} g
     * @param {number} b
     * @returns {EditResult}
     */
    setTextColor(start_node, start_offset, end_node, end_offset, r, g, b) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setTextColor(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, r, g, b);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets an explicit underline RGB color over the selection. Empty/`"auto"`
     * clears the direct color so the underline follows the text color. Choosing
     * an explicit color also turns underline on.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} color
     * @returns {EditResult}
     */
    setUnderlineColor(start_node, start_offset, end_node, end_offset, color) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setUnderlineColor(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the underline line style over the selection. `"none"` removes the
     * underline and its direct style/color; the remaining canonical tokens turn
     * underline on. `"single"` clears the direct style because a single line is
     * the model default.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} style
     * @returns {EditResult}
     */
    setUnderlineStyle(start_node, start_offset, end_node, end_offset, style) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(style, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setUnderlineStyle(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Sets the baseline alignment over the selection: `"super"`, `"sub"`, or
     * `"baseline"`.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} which
     * @returns {EditResult}
     */
    setVertAlign(start_node, start_offset, end_node, end_offset, which) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(which, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_setVertAlign(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The selected shape's current fill and outline, so the host reflects what
     * the shape HAS rather than what was last applied: `{fill, outline,
     * outlineWidthEmu}`, each `#rrggbb` or null. A gradient reports its flat
     * color — the picker applies solids, and reporting null would read as "no
     * fill" on a shape that plainly has one. `null` if `shape` is not a shape.
     * @param {string} shape
     * @returns {ShapeFormat | undefined}
     */
    shapeFormat(shape) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(shape, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_shapeFormat(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return r0 === 0 ? undefined : ShapeFormat.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Whether the read-only markup view is currently shown.
     * @returns {boolean}
     */
    get showingChanges() {
        const ret = wasm.wasmdocument_showingChanges(this.__wbg_ptr);
        return ret !== 0;
    }
    /**
     * Sorts regular-table rows by a chosen column's plain text as one undoable
     * edit. `column` is a 0-based column index; a negative value sorts by the
     * caret's own column. Out-of-range indices clamp to the last column.
     * @param {string} node
     * @param {string} direction
     * @param {number} column
     * @returns {EditResult}
     */
    sortTable(node, direction, column) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(direction, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_sortTable(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1, column);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Returns the stable format identifier detected for the open source.
     * @returns {string}
     */
    get sourceFormat() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_sourceFormat(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Splits the active merged cell in the current regularized merged table back
     * into normal cells. The top-left cell keeps the merged content; recreated
     * cells are empty but valid.
     * @param {string} node
     * @param {number | null} [requested_rows]
     * @param {number | null} [requested_columns]
     * @returns {EditResult}
     */
    splitMergedCell(node, requested_rows, requested_columns) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_splitMergedCell(retptr, this.__wbg_ptr, ptr0, len0, isLikeNone(requested_rows) ? Number.MAX_SAFE_INTEGER : (requested_rows) >>> 0, isLikeNone(requested_columns) ? Number.MAX_SAFE_INTEGER : (requested_columns) >>> 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Splits the paragraph at the caret into two (Enter). The caret lands at the
     * start of the new trailing paragraph.
     * @param {string} node
     * @param {number} offset
     * @returns {EditResult}
     */
    splitParagraph(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_splitParagraph(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The resolved visual preview of paragraph style `name` — the font family,
     * size, weight, slant, underline, text color, and alignment a paragraph
     * carrying *only* that style would render with. Powers the Styles gallery,
     * where each card is drawn in its own style (Word's Styles gallery). Returns
     * a default (empty family, zero size) when the style is unknown, so the
     * gallery degrades gracefully instead of throwing.
     * @param {string} name
     * @returns {StylePreview}
     */
    stylePreview(name) {
        const ptr0 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_stylePreview(this.__wbg_ptr, ptr0, len0);
        return StylePreview.__wrap(ret);
    }
    /**
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @param {string | null} [author]
     * @param {string | null} [date]
     * @returns {EditResult}
     */
    suggestDelete(node, start, end, author, date) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_suggestDelete(retptr, this.__wbg_ptr, ptr0, len0, start, end, ptr1, len1, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Tracks a character-formatting change as standard `w:rPrChange` metadata:
     * each selected run exists once, carries its current properties, and retains
     * the complete prior snapshot for Reject.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @param {number | null} [size_half_points]
     * @param {string | null} [color]
     * @param {string | null} [highlight]
     * @param {string | null} [vert_align]
     * @param {string | null} [font]
     * @param {string | null} [author]
     * @param {string | null} [date]
     * @returns {EditResult}
     */
    suggestFormat(node, start, end, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font, author, date) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            var ptr1 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            var ptr6 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len6 = WASM_VECTOR_LEN;
            wasm.wasmdocument_suggestFormat(retptr, this.__wbg_ptr, ptr0, len0, start, end, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr1, len1, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5, ptr6, len6);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Adds a tracked insertion at a same-paragraph caret.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {string | null} [author]
     * @param {string | null} [date]
     * @param {number | null} [typing_session]
     * @returns {EditResult}
     */
    suggestInsert(node, offset, text, author, date, typing_session) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            wasm.wasmdocument_suggestInsert(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, ptr2, len2, ptr3, len3, isLikeNone(typing_session) ? Number.MAX_SAFE_INTEGER : (typing_session) >>> 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Replaces a selected range with a tracked deletion plus tracked insertion.
     * @param {string} node
     * @param {number} start
     * @param {number} end
     * @param {string} text
     * @param {string | null} [author]
     * @param {string | null} [date]
     * @param {number | null} [typing_session]
     * @returns {EditResult}
     */
    suggestReplace(node, start, end, text, author, date, typing_session) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            wasm.wasmdocument_suggestReplace(retptr, this.__wbg_ptr, ptr0, len0, start, end, ptr1, len1, ptr2, len2, ptr3, len3, isLikeNone(typing_session) ? Number.MAX_SAFE_INTEGER : (typing_session) >>> 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Adds a tracked insertion carrying the typing format armed at the caret.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {boolean | null} [bold]
     * @param {boolean | null} [italic]
     * @param {boolean | null} [underline]
     * @param {boolean | null} [strike]
     * @param {number | null} [size_half_points]
     * @param {string | null} [color]
     * @param {string | null} [highlight]
     * @param {string | null} [vert_align]
     * @param {string | null} [font]
     * @param {string | null} [author]
     * @param {string | null} [date]
     * @param {number | null} [typing_session]
     * @returns {EditResult}
     */
    suggestStyledInsert(node, offset, text, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font, author, date, typing_session) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            var ptr6 = isLikeNone(author) ? 0 : passStringToWasm0(author, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len6 = WASM_VECTOR_LEN;
            var ptr7 = isLikeNone(date) ? 0 : passStringToWasm0(date, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len7 = WASM_VECTOR_LEN;
            wasm.wasmdocument_suggestStyledInsert(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5, ptr6, len6, ptr7, len7, isLikeNone(typing_session) ? Number.MAX_SAFE_INTEGER : (typing_session) >>> 0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Internal column-resize edges for the active regular table, flattened as
     * `[page, x, y, h, column, …]` in page-local twips. `column` is the column to
     * the left of the edge; resizing it preserves the rest of the table.
     * @param {string} node
     * @returns {Int32Array}
     */
    tableColumnResizeHandles(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_tableColumnResizeHandles(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Preferred width for a specific regular-table column, or `-1` when unset.
     * @param {string} node
     * @param {number} column
     * @returns {number}
     */
    tableColumnWidthAt(node, column) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_tableColumnWidthAt(this.__wbg_ptr, ptr0, len0, column);
        return ret;
    }
    /**
     * Metadata about the innermost table containing `node`, used by the editor's
     * table-selection affordances. Returns an empty/not-found object when the
     * node is outside a table.
     * @param {string} node
     * @returns {TableInfo}
     */
    tableInfo(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_tableInfo(this.__wbg_ptr, ptr0, len0);
        return TableInfo.__wrap(ret);
    }
    /**
     * Cell rectangles for a table selection mode (`"row"`, `"column"`, `"table"`)
     * around `node`, flattened as `[page, x, y, w, h, …]`. This deliberately
     * reports cell boxes, not text ranges: table column selection is not a
     * contiguous document text range.
     * @param {string} node
     * @param {string} mode
     * @returns {Int32Array}
     */
    tableSelectionRects(node, mode) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(mode, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_tableSelectionRects(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v3 = getArrayI32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v3;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * @param {string} node
     * @returns {string}
     */
    tableStyleAt(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_tableStyleAt(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * Returns authored text-box body properties from any document surface.
     * @param {string} node
     * @returns {string}
     */
    textBoxBodyProperties(node) {
        let deferred2_0;
        let deferred2_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_textBoxBodyProperties(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred2_0 = r0;
            deferred2_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred2_0, deferred2_1, 1);
        }
    }
    /**
     * Returns a text-box's first model caret without requiring a shaped line
     * box. This keeps entry into empty/newly-created boxes deterministic.
     * @param {string} node
     * @returns {HitPayload | undefined}
     */
    textBoxCaret(node) {
        const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len0 = WASM_VECTOR_LEN;
        const ret = wasm.wasmdocument_textBoxCaret(this.__wbg_ptr, ptr0, len0);
        return ret === 0 ? undefined : HitPayload.__wrap(ret);
    }
    /**
     * The selectable object (drawing/image/text box) under a page-local point
     * (twips) on 1-based `page`, or `undefined` if the point hits no object —
     * the object-selection counterpart of [`hit_test`](Self::hit_test) (docs/85
     * §3.1 `HitTarget::InlineObject`). The frontend calls this *before* text
     * hit-testing: a hit selects the object as a unit (`Selection::Object`,
     * docs/58 §3), a miss falls through to a text caret. Read-only; no mutation.
     *
     * Scope: inline images/text boxes in top-level body paragraphs
     * (`P1G-OBJ-SELECT`) **and** top-level floating/anchored images and text
     * boxes (`P1G-OBJ-ANCHOR-SELECT`; `anchored` = true). Group children,
     * table-cell and header/footer objects remain later slices.
     * Resolves a page-local point inside an inline TEXT BOX's content, as
     * `{ node, offset, zone }`, or `None` if the point is in no text box.
     *
     * Separate from [`hit_test`] for the same reason `runningHitTest` is: a
     * single click on a text box selects the OBJECT (docs/85 §4), so ordinary
     * clicks keep resolving against the body. The host asks for this only once
     * it has entered the box's text.
     *
     * [`hit_test`]: WasmDocument::hit_test
     * @param {number} page
     * @param {number} x_twip
     * @param {number} y_twip
     * @returns {HitPayload | undefined}
     */
    textBoxHitTest(page, x_twip, y_twip) {
        const ret = wasm.wasmdocument_textBoxHitTest(this.__wbg_ptr, page, x_twip, y_twip);
        return ret === 0 ? undefined : HitPayload.__wrap(ret);
    }
    /**
     * Toggles the checked state of the checklist item at `node` — the interaction
     * behind a click on the checkbox marker. Swaps the item's numbering between the
     * unchecked (`☐`) and checked (`☑`) checklist definitions, preserving its
     * level, as one undoable action. Errors if `node` is not a checklist item, so
     * the host only wires it to a checklist marker.
     * @param {string} node
     * @returns {EditResult}
     */
    toggleChecklistItem(node) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_toggleChecklistItem(retptr, this.__wbg_ptr, ptr0, len0);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Toggles a `"bullet"` or `"numbered"` list over the selection's paragraphs, as
     * one undoable action. The shared list definition is created on first use and
     * reused after; the toggle direction (on vs off) is decided by the first
     * selected paragraph — if it already belongs to this list it is turned off,
     * else every selected paragraph is turned on. Pressing Enter in a list item
     * continues the list automatically (split copies the paragraph properties).
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} kind
     * @returns {EditResult}
     */
    toggleList(start_node, start_offset, end_node, end_offset, kind) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(kind, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_toggleList(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Styled counterpart of [`type_text`](Self::type_text): preserves an armed
     * run format while allowing adjacent keyboard calls in the same host typing
     * session to undo/redo as one user action.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {boolean | null | undefined} bold
     * @param {boolean | null | undefined} italic
     * @param {boolean | null | undefined} underline
     * @param {boolean | null | undefined} strike
     * @param {number | null | undefined} size_half_points
     * @param {string | null | undefined} color
     * @param {string | null | undefined} highlight
     * @param {string | null | undefined} vert_align
     * @param {string | null | undefined} font
     * @param {number} session
     * @returns {EditResult}
     */
    typeStyledText(node, offset, text, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font, session) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            wasm.wasmdocument_typeStyledText(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5, session);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Additive typed-underline counterpart of [`type_styled_text`](Self::type_styled_text),
     * retaining the same gesture-grouped undo behavior.
     * @param {string} node
     * @param {number} offset
     * @param {string} text
     * @param {boolean | null | undefined} bold
     * @param {boolean | null | undefined} italic
     * @param {boolean | null | undefined} underline
     * @param {boolean | null | undefined} strike
     * @param {number | null | undefined} size_half_points
     * @param {string | null | undefined} color
     * @param {string | null | undefined} highlight
     * @param {string | null | undefined} vert_align
     * @param {string | null | undefined} font
     * @param {number} session
     * @param {string | null} [underline_style]
     * @param {string | null} [underline_color]
     * @returns {EditResult}
     */
    typeStyledTextWithUnderline(node, offset, text, bold, italic, underline, strike, size_half_points, color, highlight, vert_align, font, session, underline_style, underline_color) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            var ptr2 = isLikeNone(color) ? 0 : passStringToWasm0(color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len2 = WASM_VECTOR_LEN;
            var ptr3 = isLikeNone(highlight) ? 0 : passStringToWasm0(highlight, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len3 = WASM_VECTOR_LEN;
            var ptr4 = isLikeNone(vert_align) ? 0 : passStringToWasm0(vert_align, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len4 = WASM_VECTOR_LEN;
            var ptr5 = isLikeNone(font) ? 0 : passStringToWasm0(font, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len5 = WASM_VECTOR_LEN;
            var ptr6 = isLikeNone(underline_style) ? 0 : passStringToWasm0(underline_style, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len6 = WASM_VECTOR_LEN;
            var ptr7 = isLikeNone(underline_color) ? 0 : passStringToWasm0(underline_color, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len7 = WASM_VECTOR_LEN;
            wasm.wasmdocument_typeStyledTextWithUnderline(retptr, this.__wbg_ptr, ptr0, len0, offset, ptr1, len1, isLikeNone(bold) ? 0xFFFFFF : bold ? 1 : 0, isLikeNone(italic) ? 0xFFFFFF : italic ? 1 : 0, isLikeNone(underline) ? 0xFFFFFF : underline ? 1 : 0, isLikeNone(strike) ? 0xFFFFFF : strike ? 1 : 0, isLikeNone(size_half_points) ? Number.MAX_SAFE_INTEGER : (size_half_points) >>> 0, ptr2, len2, ptr3, len3, ptr4, len4, ptr5, len5, session, ptr6, len6, ptr7, len7);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Incremental keyboard typing with explicit host gesture identity. Each call
     * still edits/reflows immediately; adjacent calls with the same `session`
     * coalesce into one history entry only when the previous caret exactly
     * matches this insertion start.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} text
     * @param {number} session
     * @returns {EditResult}
     */
    typeText(start_node, start_offset, end_node, end_offset, text, session) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_typeText(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2, session);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Undoes the last user action (one or many ops), returning the restored
     * caret + revision.
     * @returns {EditResult}
     */
    undo() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_undo(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * User-facing label for the next undoable action, or an empty string when
     * history is empty. The engine owns this vocabulary so the host never has to
     * reverse-engineer a command from its inverse operations.
     * @returns {string}
     */
    get undoLabel() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmdocument_undoLabel(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Replaces the body text of a comment or reply as one undoable review
     * action (docs/81 REVIEW-GAP-011). Works on a thread root or any reply;
     * only the target's own body text changes — its threading
     * (`para_id`/`parent_para_id`, so a reply stays a reply and export's
     * `w14:paraId`/`w15:paraIdParent` join keys survive reopen), resolved
     * state, author identity, and any anchor markers are preserved
     * untouched. Reviewer edit permission ("edit your own comment") is host
     * policy per the identity seam (docs/68), so the engine trusts the
     * caller and does not enforce authorship here.
     * @param {string} comment
     * @param {string} text
     * @returns {EditResult}
     */
    updateComment(comment, text) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(comment, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(text, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            wasm.wasmdocument_updateComment(retptr, this.__wbg_ptr, ptr0, len0, ptr1, len1);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Redefines paragraph style `name` so its run and paragraph formatting match
     * the current selection — Word's "Update `<style>` to Match Selection". Every
     * paragraph that references the style reflows. Round-trip safe (the style
     * registry is regenerated on export). Errors if no paragraph style with that
     * name exists.
     * @param {string} start_node
     * @param {number} start_offset
     * @param {string} end_node
     * @param {number} end_offset
     * @param {string} name
     * @returns {EditResult}
     */
    updateStyleFromSelection(start_node, start_offset, end_node, end_offset, name) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(start_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            const ptr1 = passStringToWasm0(end_node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len1 = WASM_VECTOR_LEN;
            const ptr2 = passStringToWasm0(name, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len2 = WASM_VECTOR_LEN;
            wasm.wasmdocument_updateStyleFromSelection(retptr, this.__wbg_ptr, ptr0, len0, start_offset, ptr1, len1, end_offset, ptr2, len2);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
            if (r2) {
                throw takeObject(r1);
            }
            return EditResult.__wrap(r0);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * The byte range `[start, end]` of the word at `offset` (double-click select),
     * as `[start, end]`; empty if the offset is not within a word.
     * @param {string} node
     * @param {number} offset
     * @returns {Uint32Array}
     */
    wordAt(node, offset) {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            const ptr0 = passStringToWasm0(node, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            const len0 = WASM_VECTOR_LEN;
            wasm.wasmdocument_wordAt(retptr, this.__wbg_ptr, ptr0, len0, offset);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v2 = getArrayU32FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 4, 4);
            return v2;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
}
if (Symbol.dispose) WasmDocument.prototype[Symbol.dispose] = WasmDocument.prototype.free;

/**
 * A format-neutral export result returned across the JS/WASM boundary.
 */
export class WasmExportArtifact {
    static __wrap(ptr) {
        const obj = Object.create(WasmExportArtifact.prototype);
        obj.__wbg_ptr = ptr;
        WasmExportArtifactFinalization.register(obj, obj.__wbg_ptr, obj);
        return obj;
    }
    __destroy_into_raw() {
        const ptr = this.__wbg_ptr;
        this.__wbg_ptr = 0;
        WasmExportArtifactFinalization.unregister(this);
        return ptr;
    }
    free() {
        const ptr = this.__destroy_into_raw();
        wasm.__wbg_wasmexportartifact_free(ptr, 0);
    }
    /**
     * Returns a copy of the encoded file bytes.
     * @returns {Uint8Array}
     */
    get bytes() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_bytes(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            var v1 = getArrayU8FromWasm0(r0, r1).slice();
            wasm.__wbindgen_export3(r0, r1 * 1, 1);
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
    /**
     * Returns the stable emitted format identifier.
     * @returns {string}
     */
    get format() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_format(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the emitted MIME type.
     * @returns {string}
     */
    get mimeType() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_mimeType(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the deterministic export compatibility report as JSON.
     * @returns {string}
     */
    get reportJson() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_reportJson(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the suggested filename extension without a leading dot.
     * @returns {string}
     */
    get suggestedExtension() {
        let deferred1_0;
        let deferred1_1;
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_suggestedExtension(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            deferred1_0 = r0;
            deferred1_1 = r1;
            return getStringFromWasm0(r0, r1);
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
            wasm.__wbindgen_export3(deferred1_0, deferred1_1, 1);
        }
    }
    /**
     * Returns the emitted format profile/version, when known.
     * @returns {string | undefined}
     */
    get version() {
        try {
            const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
            wasm.wasmexportartifact_version(retptr, this.__wbg_ptr);
            var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
            var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
            let v1;
            if (r0 !== 0) {
                v1 = getStringFromWasm0(r0, r1).slice();
                wasm.__wbindgen_export3(r0, r1 * 1, 1);
            }
            return v1;
        } finally {
            wasm.__wbindgen_add_to_stack_pointer(16);
        }
    }
}
if (Symbol.dispose) WasmExportArtifact.prototype[Symbol.dispose] = WasmExportArtifact.prototype.free;

/**
 * Auto-detects a registered document format, imports it, and paginates it.
 *
 * Errors (bad ZIP, admission failure, import failure) throw a JS `Error`.
 * @param {Uint8Array} bytes
 * @returns {WasmDocument}
 */
export function open(bytes) {
    try {
        const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
        const ptr0 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
        const len0 = WASM_VECTOR_LEN;
        wasm.open(retptr, ptr0, len0);
        var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
        var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
        var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
        if (r2) {
            throw takeObject(r1);
        }
        return WasmDocument.__wrap(r0);
    } finally {
        wasm.__wbindgen_add_to_stack_pointer(16);
    }
}

/**
 * Imports bytes through one explicitly selected stable format identifier.
 * @param {Uint8Array} bytes
 * @param {string} format_id
 * @returns {WasmDocument}
 */
export function openAs(bytes, format_id) {
    try {
        const retptr = wasm.__wbindgen_add_to_stack_pointer(-16);
        const ptr0 = passArray8ToWasm0(bytes, wasm.__wbindgen_export);
        const len0 = WASM_VECTOR_LEN;
        const ptr1 = passStringToWasm0(format_id, wasm.__wbindgen_export, wasm.__wbindgen_export2);
        const len1 = WASM_VECTOR_LEN;
        wasm.openAs(retptr, ptr0, len0, ptr1, len1);
        var r0 = getDataViewMemory0().getInt32(retptr + 4 * 0, true);
        var r1 = getDataViewMemory0().getInt32(retptr + 4 * 1, true);
        var r2 = getDataViewMemory0().getInt32(retptr + 4 * 2, true);
        if (r2) {
            throw takeObject(r1);
        }
        return WasmDocument.__wrap(r0);
    } finally {
        wasm.__wbindgen_add_to_stack_pointer(16);
    }
}
function __wbg_get_imports() {
    const import0 = {
        __proto__: null,
        __wbg_Error_92b29b0548f8b746: function(arg0, arg1) {
            const ret = Error(getStringFromWasm0(arg0, arg1));
            return addHeapObject(ret);
        },
        __wbg___wbindgen_string_get_b0ca35b86a603356: function(arg0, arg1) {
            const obj = getObject(arg1);
            const ret = typeof(obj) === 'string' ? obj : undefined;
            var ptr1 = isLikeNone(ret) ? 0 : passStringToWasm0(ret, wasm.__wbindgen_export, wasm.__wbindgen_export2);
            var len1 = WASM_VECTOR_LEN;
            getDataViewMemory0().setInt32(arg0 + 4 * 1, len1, true);
            getDataViewMemory0().setInt32(arg0 + 4 * 0, ptr1, true);
        },
        __wbg___wbindgen_throw_344f42d3211c4765: function(arg0, arg1) {
            throw new Error(getStringFromWasm0(arg0, arg1));
        },
        __wbindgen_cast_0000000000000001: function(arg0, arg1) {
            // Cast intrinsic for `Ref(String) -> Externref`.
            const ret = getStringFromWasm0(arg0, arg1);
            return addHeapObject(ret);
        },
        __wbindgen_object_drop_ref: function(arg0) {
            takeObject(arg0);
        },
    };
    return {
        __proto__: null,
        "./casual_doc_wasm_bg.js": import0,
    };
}

const CaretFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_caret_free(ptr, 1));
const CellTextRangeFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_celltextrange_free(ptr, 1));
const DocStatsFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_docstats_free(ptr, 1));
const EditResultFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_editresult_free(ptr, 1));
const FormatFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_format_free(ptr, 1));
const HitPayloadFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_hitpayload_free(ptr, 1));
const IndentsFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_indents_free(ptr, 1));
const LinkHitFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_linkhit_free(ptr, 1));
const ObjectHitPayloadFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_objecthitpayload_free(ptr, 1));
const PageBitmapFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_pagebitmap_free(ptr, 1));
const PageSizeFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_pagesize_free(ptr, 1));
const ParagraphFlagsFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_paragraphflags_free(ptr, 1));
const ParagraphSpacingFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_paragraphspacing_free(ptr, 1));
const ParagraphStateFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_paragraphstate_free(ptr, 1));
const RulerGeometryFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_rulergeometry_free(ptr, 1));
const RunStyleFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_runstyle_free(ptr, 1));
const RunningHitPayloadFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_runninghitpayload_free(ptr, 1));
const ShapeFormatFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_shapeformat_free(ptr, 1));
const StylePreviewFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_stylepreview_free(ptr, 1));
const TableInfoFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_tableinfo_free(ptr, 1));
const TextMatchFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_textmatch_free(ptr, 1));
const WasmDocumentFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_wasmdocument_free(ptr, 1));
const WasmExportArtifactFinalization = (typeof FinalizationRegistry === 'undefined')
    ? { register: () => {}, unregister: () => {} }
    : new FinalizationRegistry(ptr => wasm.__wbg_wasmexportartifact_free(ptr, 1));

function addHeapObject(obj) {
    if (heap_next === heap.length) heap.push(heap.length + 1);
    const idx = heap_next;
    heap_next = heap[idx];

    heap[idx] = obj;
    return idx;
}

function dropObject(idx) {
    if (idx < 1028) return;
    heap[idx] = heap_next;
    heap_next = idx;
}

function getArrayF64FromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    return getFloat64ArrayMemory0().subarray(ptr / 8, ptr / 8 + len);
}

function getArrayI32FromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    return getInt32ArrayMemory0().subarray(ptr / 4, ptr / 4 + len);
}

function getArrayJsValueFromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    const mem = getDataViewMemory0();
    const result = [];
    for (let i = ptr; i < ptr + 4 * len; i += 4) {
        result.push(takeObject(mem.getUint32(i, true)));
    }
    return result;
}

function getArrayU32FromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    return getUint32ArrayMemory0().subarray(ptr / 4, ptr / 4 + len);
}

function getArrayU8FromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    return getUint8ArrayMemory0().subarray(ptr / 1, ptr / 1 + len);
}

function getClampedArrayU8FromWasm0(ptr, len) {
    ptr = ptr >>> 0;
    return getUint8ClampedArrayMemory0().subarray(ptr / 1, ptr / 1 + len);
}

let cachedDataViewMemory0 = null;
function getDataViewMemory0() {
    if (cachedDataViewMemory0 === null || cachedDataViewMemory0.buffer.detached === true || (cachedDataViewMemory0.buffer.detached === undefined && cachedDataViewMemory0.buffer !== wasm.memory.buffer)) {
        cachedDataViewMemory0 = new DataView(wasm.memory.buffer);
    }
    return cachedDataViewMemory0;
}

let cachedFloat64ArrayMemory0 = null;
function getFloat64ArrayMemory0() {
    if (cachedFloat64ArrayMemory0 === null || cachedFloat64ArrayMemory0.byteLength === 0) {
        cachedFloat64ArrayMemory0 = new Float64Array(wasm.memory.buffer);
    }
    return cachedFloat64ArrayMemory0;
}

let cachedInt32ArrayMemory0 = null;
function getInt32ArrayMemory0() {
    if (cachedInt32ArrayMemory0 === null || cachedInt32ArrayMemory0.byteLength === 0) {
        cachedInt32ArrayMemory0 = new Int32Array(wasm.memory.buffer);
    }
    return cachedInt32ArrayMemory0;
}

function getStringFromWasm0(ptr, len) {
    return decodeText(ptr >>> 0, len);
}

let cachedUint32ArrayMemory0 = null;
function getUint32ArrayMemory0() {
    if (cachedUint32ArrayMemory0 === null || cachedUint32ArrayMemory0.byteLength === 0) {
        cachedUint32ArrayMemory0 = new Uint32Array(wasm.memory.buffer);
    }
    return cachedUint32ArrayMemory0;
}

let cachedUint8ArrayMemory0 = null;
function getUint8ArrayMemory0() {
    if (cachedUint8ArrayMemory0 === null || cachedUint8ArrayMemory0.byteLength === 0) {
        cachedUint8ArrayMemory0 = new Uint8Array(wasm.memory.buffer);
    }
    return cachedUint8ArrayMemory0;
}

let cachedUint8ClampedArrayMemory0 = null;
function getUint8ClampedArrayMemory0() {
    if (cachedUint8ClampedArrayMemory0 === null || cachedUint8ClampedArrayMemory0.byteLength === 0) {
        cachedUint8ClampedArrayMemory0 = new Uint8ClampedArray(wasm.memory.buffer);
    }
    return cachedUint8ClampedArrayMemory0;
}

function getObject(idx) { return heap[idx]; }

let heap = new Array(1024).fill(undefined);
heap.push(undefined, null, true, false);

let heap_next = heap.length;

function isLikeNone(x) {
    return x === undefined || x === null;
}

function passArray32ToWasm0(arg, malloc) {
    const ptr = malloc(arg.length * 4, 4) >>> 0;
    getUint32ArrayMemory0().set(arg, ptr / 4);
    WASM_VECTOR_LEN = arg.length;
    return ptr;
}

function passArray8ToWasm0(arg, malloc) {
    const ptr = malloc(arg.length * 1, 1) >>> 0;
    getUint8ArrayMemory0().set(arg, ptr / 1);
    WASM_VECTOR_LEN = arg.length;
    return ptr;
}

function passArrayF64ToWasm0(arg, malloc) {
    const ptr = malloc(arg.length * 8, 8) >>> 0;
    getFloat64ArrayMemory0().set(arg, ptr / 8);
    WASM_VECTOR_LEN = arg.length;
    return ptr;
}

function passArrayJsValueToWasm0(array, malloc) {
    const ptr = malloc(array.length * 4, 4) >>> 0;
    const mem = getDataViewMemory0();
    for (let i = 0; i < array.length; i++) {
        mem.setUint32(ptr + 4 * i, addHeapObject(array[i]), true);
    }
    WASM_VECTOR_LEN = array.length;
    return ptr;
}

function passStringToWasm0(arg, malloc, realloc) {
    if (realloc === undefined) {
        const buf = cachedTextEncoder.encode(arg);
        const ptr = malloc(buf.length, 1) >>> 0;
        getUint8ArrayMemory0().subarray(ptr, ptr + buf.length).set(buf);
        WASM_VECTOR_LEN = buf.length;
        return ptr;
    }

    let len = arg.length;
    let ptr = malloc(len, 1) >>> 0;

    const mem = getUint8ArrayMemory0();

    let offset = 0;

    for (; offset < len; offset++) {
        const code = arg.charCodeAt(offset);
        if (code > 0x7F) break;
        mem[ptr + offset] = code;
    }
    if (offset !== len) {
        if (offset !== 0) {
            arg = arg.slice(offset);
        }
        ptr = realloc(ptr, len, len = offset + arg.length * 3, 1) >>> 0;
        const view = getUint8ArrayMemory0().subarray(ptr + offset, ptr + len);
        const ret = cachedTextEncoder.encodeInto(arg, view);

        offset += ret.written;
        ptr = realloc(ptr, len, offset, 1) >>> 0;
    }

    WASM_VECTOR_LEN = offset;
    return ptr;
}

function takeObject(idx) {
    const ret = getObject(idx);
    dropObject(idx);
    return ret;
}

let cachedTextDecoder = new TextDecoder('utf-8', { ignoreBOM: true, fatal: true });
cachedTextDecoder.decode();
const MAX_SAFARI_DECODE_BYTES = 2146435072;
let numBytesDecoded = 0;
function decodeText(ptr, len) {
    numBytesDecoded += len;
    if (numBytesDecoded >= MAX_SAFARI_DECODE_BYTES) {
        cachedTextDecoder = new TextDecoder('utf-8', { ignoreBOM: true, fatal: true });
        cachedTextDecoder.decode();
        numBytesDecoded = len;
    }
    return cachedTextDecoder.decode(getUint8ArrayMemory0().subarray(ptr, ptr + len));
}

const cachedTextEncoder = new TextEncoder();

if (!('encodeInto' in cachedTextEncoder)) {
    cachedTextEncoder.encodeInto = function (arg, view) {
        const buf = cachedTextEncoder.encode(arg);
        view.set(buf);
        return {
            read: arg.length,
            written: buf.length
        };
    };
}

let WASM_VECTOR_LEN = 0;

let wasmModule, wasmInstance, wasm;
function __wbg_finalize_init(instance, module) {
    wasmInstance = instance;
    wasm = instance.exports;
    wasmModule = module;
    cachedDataViewMemory0 = null;
    cachedFloat64ArrayMemory0 = null;
    cachedInt32ArrayMemory0 = null;
    cachedUint32ArrayMemory0 = null;
    cachedUint8ArrayMemory0 = null;
    cachedUint8ClampedArrayMemory0 = null;
    return wasm;
}

async function __wbg_load(module, imports) {
    if (typeof Response === 'function' && module instanceof Response) {
        if (typeof WebAssembly.instantiateStreaming === 'function') {
            try {
                return await WebAssembly.instantiateStreaming(module, imports);
            } catch (e) {
                const validResponse = module.ok && expectedResponseType(module.type);

                if (validResponse && module.headers.get('Content-Type') !== 'application/wasm') {
                    console.warn("`WebAssembly.instantiateStreaming` failed because your server does not serve Wasm with `application/wasm` MIME type. Falling back to `WebAssembly.instantiate` which is slower. Original error:\n", e);

                } else { throw e; }
            }
        }

        const bytes = await module.arrayBuffer();
        return await WebAssembly.instantiate(bytes, imports);
    } else {
        const instance = await WebAssembly.instantiate(module, imports);

        if (instance instanceof WebAssembly.Instance) {
            return { instance, module };
        } else {
            return instance;
        }
    }

    function expectedResponseType(type) {
        switch (type) {
            case 'basic': case 'cors': case 'default': return true;
        }
        return false;
    }
}

function initSync(module) {
    if (wasm !== undefined) return wasm;


    if (module !== undefined) {
        if (Object.getPrototypeOf(module) === Object.prototype) {
            ({module} = module)
        } else {
            console.warn('using deprecated parameters for `initSync()`; pass a single object instead')
        }
    }

    const imports = __wbg_get_imports();
    if (!(module instanceof WebAssembly.Module)) {
        module = new WebAssembly.Module(module);
    }
    const instance = new WebAssembly.Instance(module, imports);
    return __wbg_finalize_init(instance, module);
}

async function __wbg_init(module_or_path) {
    if (wasm !== undefined) return wasm;


    if (module_or_path !== undefined) {
        if (Object.getPrototypeOf(module_or_path) === Object.prototype) {
            ({module_or_path} = module_or_path)
        } else {
            console.warn('using deprecated parameters for the initialization function; pass a single object instead')
        }
    }

    if (module_or_path === undefined) {
        module_or_path = new URL('casual_doc_wasm_bg.wasm', import.meta.url);
    }
    const imports = __wbg_get_imports();

    if (typeof module_or_path === 'string' || (typeof Request === 'function' && module_or_path instanceof Request) || (typeof URL === 'function' && module_or_path instanceof URL)) {
        module_or_path = fetch(module_or_path);
    }

    const { instance, module } = await __wbg_load(await module_or_path, imports);

    return __wbg_finalize_init(instance, module);
}

export { initSync, __wbg_init as default };
