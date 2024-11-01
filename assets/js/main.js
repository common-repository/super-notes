! function(t) {
    var e = {};

    function n(o) {
        if (e[o]) return e[o].exports;
        var a = e[o] = {
            i: o,
            l: !1,
            exports: {}
        };
        return t[o].call(a.exports, a, a.exports, n), a.l = !0, a.exports
    }
    n.m = t, n.c = e, n.d = function(t, e, o) {
        n.o(t, e) || Object.defineProperty(t, e, {
            enumerable: !0,
            get: o
        })
    }, n.r = function(t) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {
            value: "Module"
        }), Object.defineProperty(t, "__esModule", {
            value: !0
        })
    }, n.t = function(t, e) {
        if (1 & e && (t = n(t)), 8 & e) return t;
        if (4 & e && "object" == typeof t && t && t.__esModule) return t;
        var o = Object.create(null);
        if (n.r(o), Object.defineProperty(o, "default", {
                enumerable: !0,
                value: t
            }), 2 & e && "string" != typeof t)
            for (var a in t) n.d(o, a, function(e) {
                return t[e]
            }.bind(null, a));
        return o
    }, n.n = function(t) {
        var e = t && t.__esModule ? function() {
            return t.default
        } : function() {
            return t
        };
        return n.d(e, "a", e), e
    }, n.o = function(t, e) {
        return Object.prototype.hasOwnProperty.call(t, e)
    }, n.p = "", n(n.s = 1)
}([function(t, e) {
    jQuery.fn.wdpsnRichtext = function(t) {
        "use strict";
        var e = this.attr("id"),
            n = {
                mode: "html",
                mceInit: {
                    theme: "modern",
                    skin: "lightgray",
                    language: "en",
                    formats: {
                        alignleft: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            styles: {
                                textAlign: "left"
                            },
                            deep: !1,
                            remove: "none"
                        }, {
                            selector: "img,table,dl.wp-caption",
                            classes: ["alignleft"],
                            deep: !1,
                            remove: "none"
                        }],
                        aligncenter: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            styles: {
                                textAlign: "center"
                            },
                            deep: !1,
                            remove: "none"
                        }, {
                            selector: "img,table,dl.wp-caption",
                            classes: ["aligncenter"],
                            deep: !1,
                            remove: "none"
                        }],
                        alignright: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            styles: {
                                textAlign: "right"
                            },
                            deep: !1,
                            remove: "none"
                        }, {
                            selector: "img,table,dl.wp-caption",
                            classes: ["alignright"],
                            deep: !1,
                            remove: "none"
                        }],
                        strikethrough: {
                            inline: "del",
                            deep: !0,
                            split: !0
                        }
                    },
                    relative_urls: !1,
                    remove_script_host: !1,
                    convert_urls: !1,
                    browser_spellcheck: !0,
                    fix_list_elements: !0,
                    entities: "38,amp,60,lt,62,gt",
                    entity_encoding: "raw",
                    keep_styles: !1,
                    paste_webkit_styles: "font-weight font-style color",
                    preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
                    wpeditimage_disable_captions: !1,
                    wpeditimage_html5_captions: !1,
                    plugins: "charmap,hr,paste,tabfocus,textcolor,wordpress,wplink,wpdialogs,wpview,image",
                    content_css: "".concat(wdpsnData.includes_url, "css/dashicons.css?ver=3.9,").concat(wdpsnData.includes_url, "js/mediaelement/mediaelementplayer.min.css?ver=3.9,").concat(wdpsnData.includes_url, "js/mediaelement/wp-mediaelement.css?ver=3.9,").concat(wdpsnData.includes_url, "js/tinymce/skins/wordpress/wp-content.css?ver=3.9"),
                    selector: "#".concat(e),
                    resize: "vertical",
                    menubar: !1,
                    wpautop: !0,
                    indent: !1,
                    toolbar1: "bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_adv",
                    toolbar2: "formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                    toolbar3: "",
                    toolbar4: "",
                    tabfocus_elements: ":prev,:next",
                    body_class: e
                }
            };
        return jQuery(this).is("textarea") && "undefined" != typeof tinyMCEPreInit && "undefined" != typeof QTags && "undefined" != typeof wdpsnData ? (tinyMCEPreInit.mceInit[e] && (n.mceInit = tinyMCEPreInit.mceInit[e]), t = jQuery.extend(!0, n, {}), this.each(function() {
            var n, o, a, s, d, p = jQuery(this).attr("id"),
                i = new RegExp(e, "g"),
                r = this;
            jQuery.each(t.mceInit, function(e, n) {
                "string" === jQuery.type(n) && (t.mceInit[e] = n.replace(i, p))
            }), t.mode = "tmce" === t.mode ? "tmce" : "html", tinyMCEPreInit.mceInit[p] = t.mceInit, jQuery(this).addClass("wp-editor-area").show(), jQuery(this).closest(".wp-editor-wrap").length && (n = jQuery(this).closest(".wp-editor-wrap").parent(), jQuery(this).closest(".wp-editor-wrap").before(jQuery(this).clone()), jQuery(this).closest(".wp-editor-wrap").remove(), r = n.find('textarea[id="'.concat(p, '"]'))), o = jQuery('<div id="wp-'.concat(p, '-wrap" class="wp-core-ui wp-editor-wrap ').concat(t.mode, '-active" />')), a = jQuery('<div id="wp-'.concat(p, '-editor-tools" class="wp-editor-tools hide-if-no-js" />')), s = jQuery('<div class="wp-editor-tabs" />'), d = jQuery('<div id="wp-'.concat(p, '-editor-container" class="wp-editor-container" />')), jQuery('<a id="'.concat(p, '-html" class="wp-switch-editor switch-html" data-wp-editor-id="').concat(p, '">').concat(wdpsnData.text, "</a>")).appendTo(s), jQuery('<a id="'.concat(p, '-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="').concat(p, '">').concat(wdpsnData.visual, "</a>")).appendTo(s), s.appendTo(a), a.appendTo(o), d.appendTo(o), d.append(jQuery(r).clone().addClass("wp-editor-area")), jQuery(r).before('<link rel="stylesheet" id="editor-buttons-css" href="'.concat(wdpsnData.includes_url, 'css/editor.css" type="text/css" media="all">')), jQuery(r).before(o), jQuery(r).remove(), new QTags(p), QTags._buttonsInit(), switchEditors.go(p, t.mode)
        })) : this
    }
}, function(t, e, n) {
    t.exports = n(2)
}, function(t, e, n) {
    "use strict";
    n.r(e);
    var o = function(t) {
        if ("" === t || null === t) return !1;
        try {
            jQuery.parseJSON(t)
        } catch (t) {
            return !1
        }
        return !0
    };

    function a(t, e) {
        for (var n = 0; n < e.length; n++) {
            var o = e[n];
            o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o)
        }
    }
    var s = function() {
        function t() {
            ! function(t, e) {
                if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
            }(this, t)
        }
        var e, n, s;
        return e = t, (n = [{
            key: "updateHolders",
            value: function() {
                var t = {
                    action: "wdpsn_get_note_holders",
                    nonce: wdpsnData.nonce_get_note_holders,
                    elements: []
                };
                jQuery(".wdpsn-holder-element-loader").each(function() {
                    var e = jQuery(this),
                        n = e.data("element-type"),
                        o = e.data("element-id");
                    t.elements.push({
                        type: n,
                        id: o
                    })
                }), jQuery.post(wdpsnData.ajax_url, t, function(t) {
                    var e, n, a, s;
                    if (!o(t)) return jQuery(".wdpsn-holder-element-loader").parent().remove(), !0;
                    t = jQuery.parseJSON(t);
                    var d = !0,
                        p = !1,
                        i = void 0;
                    try {
                        for (var r, l = t[Symbol.iterator](); !(d = (r = l.next()).done); d = !0)
                            if (e = r.value, 0 !== (n = jQuery('.wdpsn-holder-element-loader[data-element-type="'.concat(e.type, '"][data-element-id="').concat(e.id, '"]'))).length) {
                                if (a = n.parent(), "global" !== e.type) {
                                    a.html(e.holder);
                                    continue
                                }
                                if (!1 === (s = jQuery.parseJSON(e.holder)).has_any_permission) {
                                    a.remove();
                                    continue
                                }
                                a.html('<a class="ab-item" href="#" title="'.concat(wdpsnData.global_notes, '">').concat(wdpsnData.global_notes, "</a>")), a.data("element-type", "global").attr("data-element-type", "global"), a.data("element-id", "global").attr("data-element-type", "global"), 0 < s.notes_count && a.find("a.ab-item").append(' <span class="wdpsn-holder-element-notes-counter">'.concat(s.notes_count, "</span>"))
                            }
                    } catch (t) {
                        p = !0, i = t
                    } finally {
                        try {
                            d || null == l.return || l.return()
                        } finally {
                            if (p) throw i
                        }
                    }
                })
            }
        }, {
            key: "maybeAddHolderToMediaPopup",
            value: function(t) {
                var e, n = jQuery(".media-modal").find(".attachment-details"),
                    o = this;
                if (0 !== n.length) return void 0 !== (e = n.data("id")) && this.addHolderToMediaPopup(e, 1), !0;
                10 >= t && setTimeout(function() {
                    o.maybeAddHolderToMediaPopup(t + 1)
                }, 500)
            }
        }, {
            key: "addHolderToMediaPopup",
            value: function(t, e) {
                var n, o, a = !1,
                    s = jQuery(".media-modal"),
                    d = this;
                0 !== s.length && (0 === (n = s.find(".edit-attachment-frame .attachment-info .settings")).length && (n = s.find(".media-sidebar .attachment-details")), 0 !== n.length && ((o = n.find("label.setting").last()).hasClass("wdpsn-holder-element-wrapper") || o.after('\n\t\t\t\t\t\t<label class="setting wdpsn-holder-element-wrapper">\n\t\t\t\t\t\t    <span class="name">'.concat(wdpsnData.plugin_name, '</span>\n\t\t\t\t\t\t    <span class="value">\n\t\t\t\t\t\t        <span class="wdpsn-holder-element-loader" data-element-type="attachment" data-element-id="').concat(t, '"></span>\n\t\t\t\t\t\t    </span>\n\t\t\t\t\t\t</label>')), a = !0)), !1 === a && 10 >= e ? setTimeout(function() {
                    d.addHolderToMediaPopup(t, e + 1)
                }, 500) : this.updateHolders()
            }
        }]) && a(e.prototype, n), s && a(e, s), t
    }();

    function d(t, e) {
        for (var n = 0; n < e.length; n++) {
            var o = e[n];
            o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o)
        }
    }
    var p = function() {
        function t() {
            ! function(t, e) {
                if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
            }(this, t)
        }
        var e, n, o;
        return e = t, (n = [{
            key: "deleteNotes",
            value: function(t) {
                var e = {
                    action: "wdpsn_delete_note_through_post_notes_summary",
                    nonce: wdpsnData.nonce_delete_note_through_notes_summary,
                    note: t
                };
                jQuery.post(wdpsnData.ajax_url, e, function(t) {
                    var e, n, o;
                    (e = jQuery("#wdpsn-post-notes-summary .inside")).html(t), e.find(".wdpsn-post-notes-summary__wrapper").removeAttr("data-availability-confirmed"), n = e.find(".notice"), (o = e.find(".wdpsn-post-notes-summary-messages")).html(""), n.appendTo(o)
                })
            }
        }, {
            key: "hideSwitcherIfUnavailable",
            value: function() {
                var t, e = jQuery("#wdpsn-post-notes-summary"),
                    n = e.find(".wdpsn-post-notes-summary__wrapper"),
                    o = e.find(".wdpsn-post-notes-summary-note-holder"),
                    a = e.find(".wdpsn-post-notes-summary-preview");
                if (1 === o.find(".wdpsn-empty-placeholder").length && 1 === a.find(".wdpsn-empty-placeholder").length) return t = e.find(".wdpsn-post-notes-summary__more-less"), n.data("mode", "short").attr("data-mode", "short"), t.remove(), void a.remove();
                n.removeAttr("data-availability-confirmed")
            }
        }]) && d(e.prototype, n), o && d(e, o), t
    }();
    n(0);

    function i(t, e) {
        for (var n = 0; n < e.length; n++) {
            var o = e[n];
            o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o)
        }
    }
    var r = function() {
        function t() {
            ! function(t, e) {
                if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
            }(this, t)
        }
        var e, n, a;
        return e = t, (n = [{
            key: "isBlockEditor",
            value: function() {
                return jQuery("body").hasClass("block-editor-page")
            }
        }, {
            key: "getCorrectZIndex",
            value: function() {
                return jQuery("body").hasClass("modal-open") ? "999999" : "1500"
            }
        }, {
            key: "getEditorContent",
            value: function(t) {
                return this.isBlockEditor() ? wp.oldEditor.getContent(t.attr("id")) : wp.editor.getContent(t.attr("id"))
            }
        }, {
            key: "updateLoaderState",
            value: function(t) {
                switch (t) {
                    case "show":
                        window.wdpsnVars.popup.waitContent.show(), window.wdpsnVars.popup.container.data("loader-visible", "yes").attr("data-loader-visible", "yes");
                        break;
                    case "hide":
                        window.wdpsnVars.popup.waitContent.hide(), window.wdpsnVars.popup.container.data("loader-visible", "no").attr("data-loader-visible", "no")
                }
            }
        }, {
            key: "appendPopupWrapper",
            value: function() {
                var t = this.isBlockEditor() ? jQuery("#wpbody") : jQuery("body .wrap");
                0 === t.length && 0 === (t = jQuery("body #wpbody-content #editor.gutenberg__editor")).length || void 0 === window.wdpsnVars.popup.container && (t.prepend('\n\t\t\t\t<div id="wdpsn-note-popup" class="wdpsn-note-popup">\n\t\t\t\t    <div class="wdpsn-note-popup__background"></div>\n\t\t\t\t    <div class="wdpsn-note-popup__container">\n\t\t\t\t        <div class="wdpsn-note-popup__container__wait-content">\n\t\t\t\t            <span class="wdpsn-note-popup__container__wait-content__loader"></span>\n\t\t\t\t            <div class="wdpsn-note-popup__container__wait-content__middle-holder">\n\t\t\t\t                <h1>'.concat(wdpsnData.loading, "</h1>\n\t\t\t\t                <h2>").concat(wdpsnData.please_wait_a_second, '</h2>\n\t\t\t\t            </div>\n\t\t\t\t        </div>\n\t\t\t\t        <div class="wdpsn-note-popup__container__content"></div>\n\t\t\t\t    </div>\n\t\t\t\t</div>')), window.wdpsnVars.popup = {
                    container: jQuery("#wdpsn-note-popup"),
                    waitContent: jQuery("#wdpsn-note-popup .wdpsn-note-popup__container__wait-content"),
                    content: jQuery("#wdpsn-note-popup .wdpsn-note-popup__container__content")
                })
            }
        }, {
            key: "openAddNoteFromOutsideThePopup",
            value: function(t, e) {
                var n, o, a, s = this;
                this.updateLoaderState("show"), event.preventDefault(), window.wdpsnVars.popup.container.css({
                    visibility: "visible",
                    "z-index": this.getCorrectZIndex()
                }), window.wdpsnVars.latestHolder = e.parents("#wdpsn-post-notes-summary").find(".wdpsn-holder-element"), n = window.wdpsnVars.latestHolder.data("element-type"), o = window.wdpsnVars.latestHolder.data("element-id"), window.wdpsnVars.popup.container.data("element-type", n).attr("data-element-type", n), window.wdpsnVars.popup.container.data("element-id", o).attr("data-element-id", o), a = {
                    action: "wdpsn_open_popup_add_note_form",
                    nonce: wdpsnData.nonce_open_popup_add_note_form,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: t
                }, jQuery.post(wdpsnData.ajax_url, a, function(t) {
                    window.wdpsnVars.popup.content.html(t), jQuery(".wdpsn_rich_editor").attr("id", "wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)), jQuery("#wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)).wdpsnRichtext(), window.wdpsnVars.nextRichEditorID++, "yes" === wdpsnData.add_extras && s.refreshNoteTagsSelector("add"), s.updateLoaderState("hide")
                })
            }
        }, {
            key: "updateNotesCounter",
            value: function(t) {
                var e;
                if ("global" === window.wdpsnVars.popup.container.data("element-id")) {
                    if (e = window.wdpsnVars.latestHolder.find(".wdpsn-holder-element-notes-counter"), 0 === t && 0 === e.length) return;
                    return 0 === e.length ? void window.wdpsnVars.latestHolder.find("a.ab-item").append('<span class="wdpsn-holder-element-notes-counter">'.concat(t, "</span>")) : 0 < t ? void window.wdpsnVars.latestHolder.find(".wdpsn-holder-element-notes-counter").html(t) : void e.remove()
                }
                e = window.wdpsnVars.latestHolder.find(".post-com-count.post-com-count-pending"), 0 === t && 0 === e.length || (0 !== e.length ? 0 < t ? window.wdpsnVars.latestHolder.find(".post-com-count.post-com-count-pending .comment-count-pending").html(t) : e.remove() : window.wdpsnVars.latestHolder.append('\n\t\t\t\t<span class="post-com-count post-com-count-pending">\n\t\t\t\t    <span class="comment-count-pending" aria-hidden="true">'.concat(t, "</span>\n\t\t\t\t</span>")))
            }
        }, {
            key: "openNoteEditorFormOutsideThePopup",
            value: function(t, e) {
                var n, o = e.data("note-id"),
                    a = e.data("assigned-to-element-type"),
                    s = e.data("assigned-to-element-id"),
                    d = this;
                this.updateLoaderState("show"), event.preventDefault(), window.wdpsnVars.popup.container.css({
                    visibility: "visible",
                    "z-index": this.getCorrectZIndex()
                }), window.wdpsnVars.popup.container.data("element-type", a).attr("data-element-type", a), window.wdpsnVars.popup.container.data("element-id", s).attr("data-element-id", s), n = {
                    action: "wdpsn_open_popup_edit_note_form",
                    nonce: wdpsnData.nonce_open_popup_edit_note_form,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: t,
                    note_data: {
                        note_id: o,
                        note_type_id: e.data("note-type-id")
                    }
                }, jQuery.post(wdpsnData.ajax_url, n, function(t) {
                    window.wdpsnVars.popup.content.html(t), jQuery(".wdpsn_rich_editor").attr("id", "wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)), jQuery("#wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)).wdpsnRichtext(), window.wdpsnVars.nextRichEditorID++, "yes" === wdpsnData.add_extras && d.refreshNoteTagsSelector("edit"), d.updateLoaderState("hide")
                })
            }
        }, {
            key: "closeNotesPopup",
            value: function() {
                window.wdpsnVars.latestHolder = !1, window.wdpsnVars.popup.container.css({
                    visibility: "hidden",
                    "z-index": "-99999"
                }), window.wdpsnVars.popup.container.data("element-type", "").attr("data-element-type", ""), window.wdpsnVars.popup.container.data("element-id", "").attr("data-element-id", ""), window.wdpsnVars.popup.content.html(""), this.updateLoaderState("show")
            }
        }, {
            key: "getDefaultErrorTemplate",
            value: function() {
                window.wdpsnVars.popup.content.html('\n\t\t\t<div class="wdpsn-note-popup__container__content__heading">\n\t\t\t    <h1>'.concat(wdpsnData.something_went_wrong, '</h1>\n\t\t\t    <span class="wdpsn-note-popup__close">×</span>\n\t\t\t</div>\n\t\t\t<p>').concat(wdpsnData.unexpected_error_try_again, "</p>")), this.updateLoaderState("hide")
            }
        }, {
            key: "refreshPostNotesSummary",
            value: function() {
                var t = {
                    action: "wdpsn_refresh_post_notes_summary",
                    nonce: wdpsnData.nonce_refresh_post_notes_summary,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type")
                };
                jQuery.post(wdpsnData.ajax_url, t, function(e) {
                    jQuery('#wdpsn-post-notes-summary .inside .wdpsn-post-notes-summary__wrapper[data-element-id="'.concat(t.element_id, '"][data-element-type="').concat(t.element_type, '"]')).replaceWith(jQuery(e).removeAttr("data-availability-confirmed"))
                })
            }
        }, {
            key: "getAddedTags",
            value: function(t) {
                var e = window.wdpsnVars.popup.content.find(".wdpsn-note-popup__container__content__note-editor__field__tags").find(".wdpsn-note-tag, .wdpsn-data-deleted"),
                    n = {};
                return e.each(function() {
                    var e = jQuery(this),
                        o = e.data("tag-id"),
                        a = e.text();
                    n[o] = {
                        id: o,
                        title: a
                    }, !0 === t && (n[o].element = e)
                }), n
            }
        }, {
            key: "removeUnavailableTags",
            value: function(t, e) {
                jQuery.each(t, function(t, n) {
                    n.element.hasClass("wdpsn-data-deleted") || void 0 !== e[t] || n.element.remove()
                })
            }
        }, {
            key: "refreshNoteTagsSelector",
            value: function(t) {
                var e, n = jQuery('#wdpsn-note-popup *[name="wdpsn_'.concat(t, '_note_note_type_id"]')).val(),
                    a = jQuery("#wdpsn-note-popup select.wdpsn-note-popup__container__content__note-editor__field__tags-selector"),
                    s = a.siblings("button.button.wdpsn-note-popup__container__content__note-editor__field__add-tag-button"),
                    d = this,
                    p = {
                        action: "wdpsn_get_available_tags",
                        nonce: wdpsnData.nonce_get_available_tags,
                        note_type_id: n
                    };
                a.html('<option value="">'.concat(wdpsnData.please_wait_option, "</option>")), e = a.find("option").first(), jQuery.post(wdpsnData.ajax_url, p, function(t) {
                    var n, p, i;
                    o(t) && (n = d.getAddedTags(!0), p = jQuery.parseJSON(t), i = 0, d.removeUnavailableTags(n, p), jQuery.each(p, function(t, e) {
                        var o = void 0 !== n[t];
                        "" === e && (e = "..."), a.append('<option value="'.concat(t, '"').concat(o ? ' disabled="disabled"' : "", ">").concat(e, "</option>")), o || i++
                    }), 0 === i ? (e.text(wdpsnData.no_more_tags_available), s.prop("disabled", !0)) : (e.text(wdpsnData.choose_tag), s.prop("disabled", !1)))
                })
            }
        }]) && i(e.prototype, n), a && i(e, a), t
    }();

    function l(t) {
        return (l = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
            return typeof t
        } : function(t) {
            return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
        })(t)
    }

    function c(t, e) {
        for (var n = 0; n < e.length; n++) {
            var o = e[n];
            o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o)
        }
    }
    var u = function() {
            function t() {
                ! function(t, e) {
                    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
                }(this, t)
            }
            var e, n, a;
            return e = t, (n = [{
                key: "initialize",
                value: function() {
                    var t = this;
                    this.updateDynamicFields(!0, !1), jQuery(".wdpsn-meta-box-single-field__input__colorpicker__field").wpColorPicker({
                        change: function(e, n) {
                            var o, a = n.color.toString(),
                                s = jQuery('input[name="' + e.target.name + '"]');
                            switch (e.target.name) {
                                case "wdpsn_note_types_styles[colors][main-color]":
                                    1 === (o = jQuery(".wdpsn-note-container--preview")).length && t.updateCustomColorSchemeForNoteType(a, o, s);
                                    break;
                                case "wdpsn_note_tag_tag_color[tag_text_color]":
                                    t.updateTagLivePreview("text-color", a);
                                    break;
                                case "wdpsn_note_tag_tag_color[tag_color]":
                                    t.updateTagLivePreview("background", a)
                            }
                        }
                    }), this.updateAdditionalDescription("owners"), this.updateAdditionalDescription("viewers"), "yes" === wdpsnData.add_extras && this.updateAdditionalDescription("tag_moderators")
                }
            }, {
                key: "controllDatasetVal",
                value: function(t) {
                    return 0 === t.indexOf("wdpsn_note_types_") && (t = t.replace("wdpsn_note_types_", "")), t
                }
            }, {
                key: "updateDynamicFields",
                value: function(t, e) {
                    var n = this;
                    !1 !== e ? this.updateSingleDynamicField(t, e) : jQuery(".wdpsn-meta-box-single-field .wdpsn-meta-box-single-field__input__rules__row__col:last-child select").each(function() {
                        n.updateSingleDynamicField(t, jQuery(this))
                    })
                }
            }, {
                key: "updateSingleDynamicField",
                value: function(t, e) {
                    var n, o = e.data("selected");
                    if (e.find("option, optgroup").remove(), e.append('<option value="'.concat(!0 === t ? o : "", '">').concat(wdpsnData.please_wait, "</option>")), !1 === t && e.data("selected", "").removeAttr("data-selected"), n = e.parents(".wdpsn-meta-box-single-field__input__rules__row__col").siblings().first().find("select").val(), void 0 !== window.wdpsnNoteTypesDataJSON && void 0 !== window.wdpsnNoteTypesDataJSON[n]) return e.find("option, optgroup").remove(), jQuery.each(window.wdpsnNoteTypesDataJSON[n], function(a, s) {
                        var d = a,
                            p = "";
                        if ("object" !== l(s)) return e.append('<option value="'.concat(a, '"').concat(!0 === t && o === a ? ' selected="selected"' : "", ">").concat(s, "</option>")), !0;
                        switch (n) {
                            case "post":
                                void 0 !== window.wdpsnNoteTypesDataJSON["post-type"][a] && (d = window.wdpsnNoteTypesDataJSON["post-type"][a]);
                                break;
                            case "post-taxonomy":
                                void 0 !== window.wdpsnNoteTypesDataJSON["taxonomy-term"][a] && (d = window.wdpsnNoteTypesDataJSON["taxonomy-term"][a])
                        }
                        jQuery.each(window.wdpsnNoteTypesDataJSON[n][a], function(e, n) {
                            p += '<option value="'.concat(e, '"').concat(!0 === t && o === e ? ' selected="selected"' : "", ">").concat(n, "</option>")
                        }), e.append('<optgroup label="'.concat(d, '">').concat(p, "</optgroup>"))
                    }), !0;
                    e.find("option, optgroup").remove(), e.append('<option value="'.concat(!0 === t ? o : "", '">').concat(wdpsnData.unexpected_error, "</option>"))
                }
            }, {
                key: "updateRulesFieldsGroupsClasses",
                value: function() {
                    jQuery(".wdpsn-meta-box-single-field__input__rules .wdpsn-meta-box-single-field__input__rules__rows-groups__group").each(function() {
                        var t = jQuery(this);
                        1 >= t.find(".wdpsn-meta-box-single-field__input__rules__row").length ? t.removeClass("wdpsn-meta-box-single-field__input__rules__rows-groups__group--multiple") : t.addClass("wdpsn-meta-box-single-field__input__rules__rows-groups__group--multiple")
                    })
                }
            }, {
                key: "updateDependencies",
                value: function(t) {
                    var e = {},
                        n = {};
                    jQuery(".postbox").each(function() {
                        var t = jQuery(this);
                        t.find(".wdpsn-meta-box-single-field").each(function() {
                            var o = jQuery(this),
                                a = o.find(".wdpsn-meta-box-single-field__input .wdpsn-meta-box-single-field__input__yes-no"),
                                s = o.find(".wdpsn-meta-box-single-field__input select"),
                                d = o.data("dependency-rules"),
                                p = !1;
                            1 === a.length && (e[a.find('input[type="hidden"]').attr("name")] = a.data("value")), 1 === s.length && (e[s.attr("name")] = s.val()), "object" === l(d) && (!0 === (p = !0 === (p = "==" === d.operator ? e[t.attr("id") + "[" + d.field + "]"] === d.value : e[t.attr("id") + "[" + d.field + "]"] !== d.value) && (void 0 === n[t.attr("id") + "[" + d.field + "]"] || n[t.attr("id") + "[" + d.field + "]"])) ? o.removeClass("wdpsn-meta-box-single-field--dependency-hidden") : o.addClass("wdpsn-meta-box-single-field--dependency-hidden"), n[t.attr("id") + "[" + o.data("field") + "]"] = p)
                        })
                    }), !1 !== t && void 0 !== t && this.updateAdditionalDescription(t)
                }
            }, {
                key: "updateCustomColorSchemeForNoteType",
                value: function(t, e, n) {
                    var o, a, s, d, p = this.colorLightness(.72, t),
                        i = this.colorLightness(.65, t),
                        r = this.colorLightness(.89, t),
                        l = this.convertHex(this.colorLightness(.72, t), 25);
                    e.css({
                        "border-color": p,
                        background: r,
                        color: t,
                        "box-shadow": "0 5px 15px 0 " + l
                    }), !1 !== n && (o = n.parents(".wdpsn-meta-box-single-field__input").find('input[name="wdpsn_note_types_styles[colors][border-color]"]'), a = n.parents(".wdpsn-meta-box-single-field__input").find('input[name="wdpsn_note_types_styles[colors][border-bottom-color]"]'), s = n.parents(".wdpsn-meta-box-single-field__input").find('input[name="wdpsn_note_types_styles[colors][background-color]"]'), d = n.parents(".wdpsn-meta-box-single-field__input").find('input[name="wdpsn_note_types_styles[colors][box-shadow-color]"]'), o.val(p).attr("value", p), a.val(i).attr("value", i), s.val(r).attr("value", r), d.val(l).attr("value", l))
                }
            }, {
                key: "colorLightness",
                value: function(t, e, n) {
                    var o, a, s, d, p, i;
                    return "number" != typeof t || -1 > t || 1 < t || "string" != typeof e || "r" !== e[0] && "#" !== e[0] || n && "string" != typeof n ? null : (this.color || (this.color = function(t) {
                        t.length;
                        var e = {};
                        return t = o(t.slice(1), 16), e[0] = t >> 16 & 255, e[1] = t >> 8 & 255, e[2] = 255 & t, e[3] = -1, e
                    }), o = parseInt, a = Math.round, s = 9 < e.length, s = "string" == typeof n ? 9 < n.length || "c" === n && !s : s, t = (d = 0 > t) ? -1 * t : t, n = n && "c" !== n ? n : d ? "#000000" : "#FFFFFF", p = this.color(e), i = this.color(n), p && i ? s ? "rgb" + (-1 < p[3] || -1 < i[3] ? "a(" : "(") + a((i[0] - p[0]) * t + p[0]) + "," + a((i[1] - p[1]) * t + p[1]) + "," + a((i[2] - p[2]) * t + p[2]) + (0 > p[3] && 0 > i[3] ? ")" : "," + (-1 < p[3] && -1 < i[3] ? a(1e4 * ((i[3] - p[3]) * t + p[3])) / 1e4 : 0 > i[3] ? p[3] : i[3]) + ")") : "#" + (4294967296 + 16777216 * a((i[0] - p[0]) * t + p[0]) + 65536 * a((i[1] - p[1]) * t + p[1]) + 256 * a((i[2] - p[2]) * t + p[2]) + (-1 < p[3] && -1 < i[3] ? a(255 * ((i[3] - p[3]) * t + p[3])) : -1 < i[3] ? a(255 * i[3]) : -1 < p[3] ? a(255 * p[3]) : 255)).toString(16).slice(1, -1 < p[3] || -1 < i[3] ? void 0 : -2) : null)
                }
            }, {
                key: "convertHex",
                value: function(t, e) {
                    if (null !== t) return t = t.replace("#", ""), "rgba(" + parseInt(t.substring(0, 2), 16) + "," + parseInt(t.substring(2, 4), 16) + "," + parseInt(t.substring(4, 6), 16) + "," + e / 100 + ")"
                }
            }, {
                key: "updateAdditionalDescription",
                value: function(t) {
                    var e, n, a;
                    "location" !== t && "locations" !== t && ((n = jQuery('.wdpsn-meta-box-single-field__input__rules[data-set="'.concat(t, '"]')).siblings(".wdpsn-meta-box-single-field__input__additional-description")).html("<p>".concat(wdpsnData.calculating, "</p>")), a = {
                        action: "wdpsn_update_additional_description",
                        nonce: wdpsnData.nonce_update_desc,
                        rules: {
                            owners: this.createCustomRulesValuesArray("wdpsn_note_types", "owners"),
                            viewers: this.createCustomRulesValuesArray("wdpsn_note_types", "viewers")
                        },
                        messages: {
                            owners_no_one: wdpsnData.owners_no_one,
                            owners_one: wdpsnData.owners_one,
                            owners_multiple: wdpsnData.owners_multiple,
                            viewers_no_one: wdpsnData.viewers_no_one,
                            viewers_one: wdpsnData.viewers_one,
                            viewers_multiple: wdpsnData.viewers_multiple
                        }
                    }, "yes" === wdpsnData.add_extras && (a.rules.tag_moderators = this.createCustomRulesValuesArray("wdpsn_note_tag", "tag_moderators"), a.messages.tag_moderators_no_one = wdpsnData.tag_moderators_no_one, a.messages.tag_moderators_one = wdpsnData.tag_moderators_one, a.messages.tag_moderators_multiple = wdpsnData.tag_moderators_multiple), void 0 !== a.rules[t] && (e = void 0 !== window.wdpsnVars.ajaxCallID[t] ? window.wdpsnVars.ajaxCallID[t] + 1 : 1, window.wdpsnVars.ajaxCallID[t] = e, jQuery.post(wdpsnData.ajax_url, a, function(a) {
                        var s, d, p = !0,
                            i = "";
                        if (e === window.wdpsnVars.ajaxCallID[t]) {
                            if (o(a) && (a = jQuery.parseJSON(a), t = t.replace("-", "_"), "ok" === a.status && (p = !1, n.html(a.result[t].message), s = a.result[t].users.length, 0 < a.result[t].users.length))) {
                                for (d = 0; d < s; d++) i += ("" !== i ? ", " : "") + a.result[t].users[d].name;
                                n.append('<div class="wdpsn-meta-box-single-field__input__additional-description__users-list">'.concat(i, "</div>"))
                            }!0 === p && n.html("<p>".concat(wdpsnData.something_went_wrong_please_try_again, "</p>"))
                        }
                    })))
                }
            }, {
                key: "createCustomRulesValuesArray",
                value: function(t, e) {
                    var n, o, a, s, d, p, i = {},
                        r = {};
                    jQuery("div#" + t + "_" + e).find('input[type="hidden"], select').each(function() {
                        var t = jQuery(this);
                        i[t.attr("name")] = t.val()
                    });
                    t: for (n in i)
                        for (p in o = i[n], d = r, a = n.split("[")) {
                            if ("]" === (s = a[p]).substr(-1) && (s = s.substr(0, s.length - 1)), p == a.length - 1) {
                                d[s] = o;
                                continue t
                            }
                            d.hasOwnProperty(s) || (d[s] = {}), d = d[s]
                        }
                    return r[t + "_" + e]
                }
            }, {
                key: "updateTagLivePreview",
                value: function(t, e) {
                    var n, o = jQuery("#wdpsn_note_tag_tag_color .wdpsn-note-tag-preview");
                    switch (t) {
                        case "text-color":
                            o.css({
                                color: e
                            });
                            break;
                        case "background":
                            o.css({
                                "background-color": e
                            });
                            break;
                        case "title":
                            n = o.siblings(".wdpsn-meta-box-single-field__input__tag-preview-container__hint"), o.html("" !== e ? e : "..."), "" === e ? n.removeClass("wdpsn-meta-box-single-field__input__tag-preview-container__hint--hidden") : n.addClass("wdpsn-meta-box-single-field__input__tag-preview-container__hint--hidden")
                    }
                }
            }, {
                key: "getPureText",
                value: function(t) {
                    var e = document.createElement("div");
                    return e.innerHTML = t, e.textContent || e.innerText || ""
                }
            }]) && c(e.prototype, n), a && c(e, a), t
        }(),
        _ = new s;

    function w(t, e) {
        for (var n = 0; n < e.length; n++) {
            var o = e[n];
            o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(t, o.key, o)
        }
    }
    var m = new(function() {
            function t() {
                ! function(t, e) {
                    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
                }(this, t)
            }
            var e, n, a;
            return e = t, (n = [{
                key: "deleteNotes",
                value: function(t) {
                    var e = {
                        action: "wdpsn_delete_notes_through_table",
                        nonce: wdpsnData.nonce_delete_notes_through_table,
                        url_path: {
                            paged: this.getPagedAttr(window.location.href),
                            http_host: wdpsnData.http_host,
                            request_uri: wdpsnData.request_uri
                        },
                        notes: t
                    };
                    jQuery.post(wdpsnData.ajax_url, e, function(t) {
                        o(t) && (t = jQuery.parseJSON(t), window.location.href = t.redirect_url)
                    })
                }
            }, {
                key: "getPagedAttr",
                value: function(t) {
                    var e = "1";
                    return -1 !== t.indexOf("&paged=") && -1 !== (e = t.substr(t.indexOf("&paged=")).replace("&paged=", "")).indexOf("&") && (e = e.substr(0, e.indexOf("&"))), e.match(/^[0-9]+$/gm) ? e : "1"
                }
            }]) && w(e.prototype, n), a && w(e, a), t
        }()),
        y = new p,
        f = new r,
        h = new u,
        v = new s,
        g = new r,
        b = new p,
        j = new u;
    window.wdpsnVars = {
        popup: {},
        latest_holder: !1,
        next_rich_editor_id: 1,
        ajaxCallID: {}
    }, jQuery(document).ready(function() {
        jQuery("body").on("click", ".media-frame ul.attachments > li .attachment-preview", function() {
            var t = jQuery(this).parents("li").data("id");
            _.addHolderToMediaPopup(t, 1)
        }), jQuery("body").on("click", '.super-notes_page_wdpsn-all-notes.wp-list-table span[data-action="delete"]', function() {
            var t = jQuery(this),
                e = jQuery(this).parents(".wdpsn-note-container"),
                n = [{
                    element_id: e.data("assigned-to-element-id"),
                    element_type: e.data("assigned-to-element-type"),
                    note_data: {
                        note_id: e.data("note-id"),
                        note_type_id: e.data("note-type-id")
                    }
                }];
            t.hasClass("wdpsn-confirm") ? (t.html(wdpsnData.please_wait), m.deleteNotes(n)) : t.addClass("wdpsn-confirm").html(wdpsnData.please_confirm_deletion)
        }), jQuery("body").on("click", "form#wdpsn-all-notes #doaction.button, form#wdpsn-all-notes #doaction2.button", function(t) {
            var e = jQuery(this),
                n = e.siblings("select").val(),
                o = [];
            switch (t.preventDefault(), n) {
                case "delete":
                    jQuery('form#wdpsn-all-notes input[type="checkbox"][name="wdpsn_single_note[]"]').each(function() {
                        var t, e = jQuery(this);
                        !0 === e.is(":checked") && (t = e.parents("tr").find(".wdpsn-note-container"), o.push({
                            element_id: t.data("assigned-to-element-id"),
                            element_type: t.data("assigned-to-element-type"),
                            note_data: {
                                note_id: t.data("note-id"),
                                note_type_id: t.data("note-type-id")
                            }
                        }))
                    }), m.deleteNotes(o);
                    break;
                default:
                    e.val(wdpsnData.please_select_the_action), setTimeout(function() {
                        e.val(wdpsnData.apply)
                    }, 1e3)
            }
        }), jQuery("body").on("click", ".wdpsn-welcome-page .wdpsn-welcome-page__container__sidebar__widget button#wdpsn-install-dummy-data", function() {
            var t = jQuery(this),
                e = "wdpsn-install-dummy-data-confirmed",
                n = {
                    action: "wdpsn_install_dummy_data",
                    nonce: wdpsnData.nonce_install_dummy_data,
                    confirmed: t.hasClass(e)
                };
            t.prop("disabled", !0).html(wdpsnData.please_wait), jQuery.post(wdpsnData.ajax_url, n, function(n) {
                var o = "";
                switch (jQuery(".wdpsn-welcome-page .wdpsn-welcome-page__container__sidebar__widget .wdpsn-note-container").remove(), t.prop("disabled", !1).removeClass(e), n) {
                    case "confirm_needed":
                        t.html(wdpsnData.yes_overwrite_current_note_type).addClass(e), o = '\n\t\t\t\t\t\t\t\t<div class="wdpsn-note-container wdpsn-note-container--alert">\n\t\t\t\t\t\t\t\t    <div class="wdpsn-note-container__content">\n\t\t\t\t\t\t\t\t        <p>'.concat(wdpsnData.dummy_notice_top, "</p>\n\t\t\t\t\t\t\t\t        <p>").concat(wdpsnData.dummy_notice_bottom, "</p>\n\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t\t</div>"), jQuery(o).insertBefore(t);
                        break;
                    case "try_again":
                        t.html(wdpsnData.install_now), o = '\n\t\t\t\t\t\t\t\t<div class="wdpsn-note-container wdpsn-note-container--error">\n\t\t\t\t\t\t\t\t\t<div class="wdpsn-note-container__content">\n\t\t\t\t\t\t\t\t\t\t<p>'.concat(wdpsnData.something_went_wrong_please_try_again, "</p>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>"), jQuery(o).insertBefore(t);
                        break;
                    case "ok":
                        t.html(wdpsnData.install_again), o = '\n\t\t\t\t\t\t\t<div class="wdpsn-note-container wdpsn-note-container--success">\n\t\t\t\t\t\t\t    <div class="wdpsn-note-container__content">\n\t\t\t\t\t\t\t        <p>'.concat(wdpsnData.dummy_data_correctly_installed, "</p>\n\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t</div>"), jQuery(o).insertBefore(t)
                }
            })
        }), jQuery("body").on("click", '#wdpsn-post-notes-summary .wdpsn-note-container span[data-action="delete"]', function() {
            var t = jQuery(this),
                e = jQuery(this).parents(".wdpsn-note-container"),
                n = {
                    element_id: e.data("assigned-to-element-id"),
                    element_type: e.data("assigned-to-element-type"),
                    note_id: e.data("note-id"),
                    note_type_id: e.data("note-type-id")
                };
            t.hasClass("wdpsn-confirm") ? (t.html(wdpsnData.please_wait), y.deleteNotes(n)) : t.addClass("wdpsn-confirm").html(wdpsnData.please_confirm_deletion)
        }), jQuery("body").on("click", ".wdpsn-post-notes-summary__more-less", function() {
            var t = jQuery(this).parents(".wdpsn-post-notes-summary__wrapper"),
                e = "short" === t.data("mode") ? "long" : "short";
            t.data("mode", e).attr("data-mode", e), jQuery.post(wdpsnData.ajax_url, {
                action: "wdpsn_update_post_notes_summary_mode",
                nonce: wdpsnData.nonce_update_post_notes_summary_mode,
                new_mode: e
            })
        }), jQuery("body").on("click", "#wdpsn-note-popup .wdpsn-note-popup__container__content__note-editor__field__yes-no", function() {
            var t = jQuery(this),
                e = t.find('input[type="hidden"]'),
                n = "yes" === t.data("value") ? "no" : "yes";
            t.data("value", n).attr("data-value", n), e.attr("value", n).val(n).trigger("changed")
        }), jQuery("body").on("click", ".wdpsn-holder-element", function(t) {
            var e, n, o;
            f.updateLoaderState("show"), window.wdpsnVars.latestHolder = jQuery(this), t.preventDefault(), window.wdpsnVars.popup.container.css({
                visibility: "visible",
                "z-index": f.getCorrectZIndex()
            }), e = window.wdpsnVars.latestHolder.data("element-type"), n = window.wdpsnVars.latestHolder.data("element-id"), window.wdpsnVars.popup.container.data("element-type", e).attr("data-element-type", e), window.wdpsnVars.popup.container.data("element-id", n).attr("data-element-id", n), o = {
                action: "wdpsn_get_popup_notes_preview_template",
                nonce: wdpsnData.nonce_get_popup_notes_preview_template,
                element_id: n,
                element_type: e,
                caller_type: "popup"
            }, jQuery.post(wdpsnData.ajax_url, o, function(t) {
                window.wdpsnVars.popup.content.html(t), f.updateLoaderState("hide")
            })
        }), jQuery("body").on("click", '#wdpsn-note-popup button[data-action="wdpsn-open-add-note-form"]', function() {
            var t = {
                action: "wdpsn_open_popup_add_note_form",
                nonce: wdpsnData.nonce_open_popup_add_note_form,
                element_id: window.wdpsnVars.popup.container.data("element-id"),
                element_type: window.wdpsnVars.popup.container.data("element-type"),
                caller_type: "popup"
            };
            f.updateLoaderState("show"), jQuery.post(wdpsnData.ajax_url, t, function(t) {
                window.wdpsnVars.popup.content.html(t), jQuery(".wdpsn_rich_editor").attr("id", "wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)), jQuery("#wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)).wdpsnRichtext(), window.wdpsnVars.nextRichEditorID++, "yes" === wdpsnData.add_extras && f.refreshNoteTagsSelector("add"), f.updateLoaderState("hide")
            })
        }), jQuery("body").on("click", '#wdpsn-note-popup button[data-action="wdpsn-add-note"]', function() {
            var t = jQuery(this).data("caller-type"),
                e = {
                    action: "wdpsn_add_note",
                    nonce: wdpsnData.nonce_add_note,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: t,
                    note_data: {
                        content: f.getEditorContent(window.wdpsnVars.popup.content.find(".wdpsn_rich_editor"))
                    }
                };
            f.updateLoaderState("show"), "yes" === wdpsnData.add_extras && (e.note_data.tags = f.getAddedTags(!1), e.note_data.note_type_id = window.wdpsnVars.popup.content.find('*[name="wdpsn_add_note_note_type_id"]').val(), e.note_data.notify_owners = window.wdpsnVars.popup.content.find('input[name="wdpsn_add_note_notify_owners"]').val(), e.note_data.notify_viewers = window.wdpsnVars.popup.content.find('input[name="wdpsn_add_note_notify_viewers"]').val()), jQuery.post(wdpsnData.ajax_url, e, function(e) {
                var n, a;
                if (o(e) && "success" === (e = jQuery.parseJSON(e)).status) switch (f.updateNotesCounter(e.notes_count_for_holder), t) {
                    case "popup":
                        return window.wdpsnVars.popup.content.html(e.output), f.updateLoaderState("hide"), void f.refreshPostNotesSummary();
                    case "post-notes-summary":
                        return (n = jQuery("#wdpsn-post-notes-summary .inside")).find(".wdpsn-post-notes-summary-preview").html(e.output), n.find(".wdpsn-post-notes-summary__wrapper").removeAttr("data-availability-confirmed"), (a = n.find(".wdpsn-post-notes-summary-messages")).html(""), n.find(".notice").appendTo(a), void f.closeNotesPopup()
                }
                f.getDefaultErrorTemplate()
            })
        }), jQuery("body").on("click", '#wdpsn-post-notes-summary button.button[data-action="wdpsn-open-add-note-form"]', function() {
            f.openAddNoteFromOutsideThePopup("post-notes-summary", jQuery(this))
        }), jQuery("body").on("click", '#wdpsn-note-popup .wdpsn-note-container__footer__note-options span[data-action="edit"]', function() {
            var t = jQuery(this).parents(".wdpsn-note-container"),
                e = t.data("note-id"),
                n = {
                    action: "wdpsn_open_popup_edit_note_form",
                    nonce: wdpsnData.nonce_open_popup_edit_note_form,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: "popup",
                    note_data: {
                        note_id: e,
                        note_type_id: t.data("note-type-id")
                    }
                };
            f.updateLoaderState("show"), jQuery.post(wdpsnData.ajax_url, n, function(t) {
                window.wdpsnVars.popup.content.html(t), jQuery(".wdpsn_rich_editor").attr("id", "wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)), jQuery("#wdpsn_rich_editor_".concat(window.wdpsnVars.nextRichEditorID)).wdpsnRichtext(), window.wdpsnVars.nextRichEditorID++, "yes" === wdpsnData.add_extras && f.refreshNoteTagsSelector("edit"), f.updateLoaderState("hide")
            })
        }), jQuery("body").on("click", '#wdpsn-note-popup button[data-action="wdpsn-edit-note"]', function() {
            var t = jQuery(this).data("caller-type"),
                e = {
                    action: "wdpsn_edit_note",
                    nonce: wdpsnData.nonce_edit_note,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: t,
                    note_data: {
                        note_id: window.wdpsnVars.popup.content.find('input[name="wdpsn_edit_note_note_id"]').val(),
                        note_type_id: window.wdpsnVars.popup.content.find('input[name="wdpsn_edit_note_note_type_id"]').val(),
                        content: f.getEditorContent(window.wdpsnVars.popup.content.find(".wdpsn_rich_editor"))
                    }
                };
            f.updateLoaderState("show"), "yes" === wdpsnData.add_extras && (e.note_data.tags = f.getAddedTags(!1), e.note_data.notify_owners = window.wdpsnVars.popup.content.find('input[name="wdpsn_edit_note_notify_owners"]').val(), e.note_data.notify_viewers = window.wdpsnVars.popup.content.find('input[name="wdpsn_edit_note_notify_viewers"]').val()), jQuery.post(wdpsnData.ajax_url, e, function(n) {
                var a, s;
                return !1 === o(n) ? (f.getDefaultErrorTemplate(), !0) : "success" !== (n = jQuery.parseJSON(n)).status ? (f.getDefaultErrorTemplate(), !0) : (a = jQuery('.wdpsn-note-wrapper[data-note-id="'.concat(e.note_data.note_id, '"]')), s = {
                    action: "wdpsn_get_note_wrapper",
                    nonce: wdpsnData.nonce_get_note_wrapper,
                    note_id: e.note_data.note_id
                }, void jQuery.post(wdpsnData.ajax_url, s, function(o) {
                    var s, d, p;
                    switch (a.replaceWith(o), t) {
                        case "post-notes-summary":
                            return (d = jQuery('#wdpsn-post-notes-summary .inside .wdpsn-post-notes-summary__wrapper[data-element-id="'.concat(e.element_id, '"][data-element-type="').concat(e.element_type, '"]'))).find(".notice").remove(), d.find(".wdpsn-post-notes-summary-messages").append('\n\t\t\t\t\t\t\t\t\t\t<div class="notice notice-success">\n\t\t\t\t\t\t\t\t\t\t    <p>'.concat(wdpsnData.note_successfully_updated, "</p>\n\t\t\t\t\t\t\t\t\t\t</div>")), void f.closeNotesPopup();
                        case "notes-list-table":
                            return (s = jQuery("form#wdpsn-all-notes")).parents(".wrap").find(".notice").remove(), jQuery('<div class="notice notice-success"><p>'.concat(wdpsnData.note_successfully_updated, "</p></div>")).insertBefore(s), void f.closeNotesPopup();
                        default:
                            return 0 !== (p = jQuery('#wdpsn-post-notes-summary .inside .wdpsn-post-notes-summary__wrapper[data-element-id="'.concat(e.element_id, '"][data-element-type="').concat(e.element_type, '"] .wdpsn-post-notes-summary-messages'))).length && p.html(""), window.wdpsnVars.popup.content.html(n.output), void f.updateLoaderState("hide")
                    }
                }))
            })
        }), jQuery("body").on("click", '.wp-list-table.super-notes_page_wdpsn-all-notes .wdpsn-note-container__footer__note-options span[data-action="edit"]', function() {
            f.openNoteEditorFormOutsideThePopup("notes-list-table", jQuery(this).parents(".wdpsn-note-container"))
        }), jQuery("body").on("click", '#wdpsn-post-notes-summary .wdpsn-note-container span[data-action="edit"]', function() {
            f.openNoteEditorFormOutsideThePopup("post-notes-summary", jQuery(this).parents(".wdpsn-note-container"))
        }), jQuery("body").on("click", '#wdpsn-note-popup .wdpsn-note-container__footer__note-options span[data-action="delete"]', function() {
            var t = jQuery(this),
                e = jQuery(this).parents(".wdpsn-note-container"),
                n = {
                    action: "wdpsn_delete_note_through_popup",
                    nonce: wdpsnData.nonce_delete_note_through_popup,
                    element_id: window.wdpsnVars.popup.container.data("element-id"),
                    element_type: window.wdpsnVars.popup.container.data("element-type"),
                    caller_type: "popup",
                    note_data: {
                        note_id: e.data("note-id"),
                        note_type_id: e.data("note-type-id")
                    }
                };
            t.hasClass("wdpsn-confirm") ? (f.updateLoaderState("show"), jQuery.post(wdpsnData.ajax_url, n, function(t) {
                var e;
                if (o(t) && "success" === (t = jQuery.parseJSON(t)).status) return f.updateNotesCounter(t.notes_count_for_holder), window.wdpsnVars.popup.content.html(t.output), 0 < (e = jQuery('.wdpsn-note-wrapper[data-note-id="'.concat(n.note_data.note_id, '"]'))).length && e.each(function() {
                    var t;
                    1 === e.parents(".wp-list-table.super-notes_page_wdpsn-all-notes").length && 1 === (t = e.parents("tr")).length && t.replaceWith('\n\t\t\t\t\t\t\t\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t\t\t\t\t\t\t    <td colspan="6">\n\t\t\t\t\t\t\t\t\t\t\t\t\t        <div class="wdpsn-empty-placeholder">\n\t\t\t\t\t\t\t\t\t\t\t\t\t            <p>'.concat(wdpsnData.note_has_been_removed, "</p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t        </div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t    </td>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</tr>"))
                }), 1 === jQuery('#wdpsn-post-notes-summary .wdpsn-post-notes-summary__wrapper[data-element-id="'.concat(n.element_id, '"][data-element-type="').concat(n.element_type, '"]')).length && f.refreshPostNotesSummary(), void f.updateLoaderState("hide");
                f.getDefaultErrorTemplate()
            })) : t.addClass("wdpsn-confirm").html(wdpsnData.please_confirm_deletion)
        }), jQuery("body").on("click", "#wdpsn-note-popup .wdpsn-note-popup__close", function() {
            f.closeNotesPopup()
        }), jQuery("body").on("click", "#wdpsn-note-popup .button.wdpsn-note-popup__cancel-button", function() {
            var t, e = jQuery(this).data("action");
            switch (f.updateLoaderState("show"), e) {
                case "wdpsn-cancel-add-note":
                case "wdpsn-cancel-edit-note":
                    t = {
                        action: "wdpsn_get_popup_notes_preview_template",
                        nonce: wdpsnData.nonce_get_popup_notes_preview_template,
                        element_id: window.wdpsnVars.popup.container.data("element-id"),
                        element_type: window.wdpsnVars.popup.container.data("element-type"),
                        caller_type: "popup"
                    }, jQuery.post(wdpsnData.ajax_url, t, function(t) {
                        window.wdpsnVars.popup.content.html(t), f.updateLoaderState("hide")
                    })
            }
        }), jQuery("body").on("click", "#wdpsn-note-popup button.button.wdpsn-note-popup__container__content__note-editor__field__add-tag-button", function() {
            var t, e, n, o, a = jQuery(this),
                s = a.siblings("select.wdpsn-note-popup__container__content__note-editor__field__tags-selector"),
                d = s.val();
            "" !== d && ("" === (e = (t = s.find('option[value="'.concat(d, '"]'))).text()) && (e = "..."), 0 === (n = a.siblings(".wdpsn-note-popup__container__content__note-editor__field__tags")).find("span.wdpsn-note-tag-id-".concat(d)).length && (n.append('<span class="wdpsn-note-tag wdpsn-note-tag-id-'.concat(d, ' wdpsn-note-tag-preview" data-tag-id="').concat(d, '">').concat(e, "</span>")), t.attr("disabled", "disabled"), s.val(""), 1 === (o = s.find("option:enabled")).length && (o.text(wdpsnData.no_more_tags_available), a.attr("disabled", "disabled"))))
        }), jQuery("body").on("click", "#wdpsn-note-popup .wdpsn-note-popup__container__content__note-editor__field__tags span.wdpsn-note-tag:not(.wdpsn-note-tag-preview-only)", function() {
            var t = jQuery(this),
                e = t.data("tag-id"),
                n = t.parents(".wdpsn-note-popup__container__content__note-editor__field__tags"),
                o = n.siblings("select.wdpsn-note-popup__container__content__note-editor__field__tags-selector"),
                a = n.siblings("button.button.wdpsn-note-popup__container__content__note-editor__field__add-tag-button");
            t.remove(), o.find('option[value="'.concat(e, '"]')).prop("disabled", !1).removeAttr("disabled"), o.find('option[value=""]').text(wdpsnData.choose_tag), a.prop("disabled", !1)
        }), jQuery("body").on("change", '#wdpsn-note-popup select[name="wdpsn_add_note_note_type_id"]', function() {
            f.refreshNoteTagsSelector("add")
        }), jQuery("body").on("change", ".wdpsn-meta-box-single-field .wdpsn-meta-box-single-field__input__rules__row__col:first-child select", function() {
            var t = jQuery(this).parents(".wdpsn-meta-box-single-field__input__rules__row__col").siblings().last().find("select");
            h.updateDynamicFields(!1, t)
        }), jQuery("body").on("click", ".wdpsn-meta-box-single-field__input__yes-no", function() {
            var t = jQuery(this),
                e = t.find('input[type="hidden"]'),
                n = "yes" === t.data("value") ? "no" : "yes",
                o = h.controllDatasetVal(t.data("set"));
            t.data("value", n).attr("data-value", n), e.attr("value", n).val(n).trigger("changed"), h.updateDependencies(o)
        }), jQuery("body").on("click", ".wdpsn-meta-box-single-field__input__rules button", function() {
            var t, e, n, o = jQuery(this),
                a = o.parent(),
                s = o.data("rule-type"),
                d = o.parents(".wdpsn-meta-box-single-field__input__rules"),
                p = h.controllDatasetVal(d.data("set")),
                i = d.data("field-name"),
                r = d.find('input[name="' + i + '[next-rule-id]"]'),
                l = parseInt(r.val(), 10);
            switch (p) {
                case "tag_moderators":
                case "owners":
                case "viewers":
                    t = '\n\t\t\t\t\t\t<option value="user-role" selected="selected">'.concat(wdpsnData.user_role, '</option>\n\t\t\t\t\t\t<option value="user">').concat(wdpsnData.user, "</option>");
                    break;
                case "locations":
                    t = '\n\t\t\t\t\t\t<option value="post-type" selected="selected">'.concat(wdpsnData.post_type, '</option>\n\t\t\t\t\t\t<optgroup label="').concat(wdpsnData.posts, '">\n\t\t\t\t\t\t\t<option value="post">').concat(wdpsnData.post, '</option>\n\t\t\t\t\t\t\t<option value="post-status">').concat(wdpsnData.post_status, '</option>\n\t\t\t\t\t\t\t<option value="post-format">').concat(wdpsnData.post_format, '</option>\n\t\t\t\t\t\t\t<option value="post-taxonomy">').concat(wdpsnData.post_taxonomy, '</option>\n\t\t\t\t\t\t\t<option value="post-author">').concat(wdpsnData.post_author, '</option>\n\t\t\t\t\t\t\t<option value="post-author-role">').concat(wdpsnData.post_author_role, '</option>\n\t\t\t\t\t\t</optgroup>\n\t\t\t\t\t\t\t<optgroup label="').concat(wdpsnData.pages, '">\n\t\t\t\t\t\t\t<option value="page">').concat(wdpsnData.page, '</option>\n\t\t\t\t\t\t\t<option value="page-template">').concat(wdpsnData.page_template, '</option>\n\t\t\t\t\t\t\t<option value="page-type">').concat(wdpsnData.page_type, '</option>\n\t\t\t\t\t\t\t<option value="page-parent">').concat(wdpsnData.page_parent, '</option>\n\t\t\t\t\t\t</optgroup>\n\t\t\t\t\t\t<optgroup label="').concat(wdpsnData.other, '">\n\t\t\t\t\t\t\t<option value="taxonomy-term">').concat(wdpsnData.taxonomy_term, '</option>\n\t\t\t\t\t\t\t<option value="user-role">').concat(wdpsnData.user_role, '</option>\n\t\t\t\t\t\t\t<option value="user">').concat(wdpsnData.user, '</option>\n\t\t\t\t\t\t\t<option value="plugin">').concat(wdpsnData.plugin, '</option>\n\t\t\t\t\t\t\t<option value="special-location">').concat(wdpsnData.special_location, "</option>\n\t\t\t\t\t\t</optgroup>")
            }
            e = '\n\t\t\t\t<div class="wdpsn-meta-box-single-field__input__rules__row wdpsn-meta-box-single-field__input__rules__row--js-inserted">\n\t\t\t\t    <div class="wdpsn-meta-box-single-field__input__rules__row__col">\n\t\t\t\t        <input type="hidden" name="'.concat(i, "[rules][").concat(l, '][rule-type]" value="').concat(s, '">\n\t\t\t\t        <select name="').concat(i, "[rules][").concat(l, '][rule-condition]">').concat(t, '</select>\n\t\t\t\t    </div>\n\t\t\t\t    <div class="wdpsn-meta-box-single-field__input__rules__row__col">\n\t\t\t\t        <select name="').concat(i, "[rules][").concat(l, '][rule-operator]">\n\t\t\t\t            <option value="==">').concat(wdpsnData.is_equal_to, '</option>\n\t\t\t\t            <option value="!=">').concat(wdpsnData.is_not_equal_to, '</option>\n\t\t\t\t        </select>\n\t\t\t\t    </div>\n\t\t\t\t    <div class="wdpsn-meta-box-single-field__input__rules__row__col">\n\t\t\t\t        <select name="').concat(i, "[rules][").concat(l, '][rule-value]"></select>\n\t\t\t\t        <span class="wdpsn-meta-box-single-field__input__rules__row__remove">×</span>\n\t\t\t\t    </div>\n\t\t\t\t</div>'), a.hasClass("wdpsn-meta-box-single-field__input__rules__buttons") ? "and" === s ? a.siblings(".wdpsn-meta-box-single-field__input__rules__rows-groups").find(".wdpsn-meta-box-single-field__input__rules__rows-groups__group").last().find(".wdpsn-meta-box-single-field__input__rules__rows-groups__group__container").append(e) : a.siblings(".wdpsn-meta-box-single-field__input__rules__rows-groups").append(jQuery("\n\t\t\t\t\t\t\t<p>".concat(wdpsnData.or, '</p>\n\t\t\t\t\t\t\t<div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group">\n\t\t\t\t\t\t\t    <div class="wdpsn-meta-box-single-field__input__rules__rows-groups__group__container">\n\t\t\t\t\t\t\t        ').concat(e, '\n\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t    <button class="button button-secondary" type="button" data-rule-type="and">').concat(wdpsnData.and, "</button>\n\t\t\t\t\t\t\t</div>"))) : o.siblings(".wdpsn-meta-box-single-field__input__rules__rows-groups__group__container").append(e), n = d.find(".wdpsn-meta-box-single-field__input__rules__row.wdpsn-meta-box-single-field__input__rules__row--js-inserted"), h.updateDynamicFields(!1, n.find(".wdpsn-meta-box-single-field__input__rules__row__col").last().find("select")), n.removeClass("wdpsn-meta-box-single-field__input__rules__row--js-inserted"), r.val(l + 1).attr("value", l + 1), h.updateRulesFieldsGroupsClasses(), h.updateAdditionalDescription(p)
        }), jQuery("body").on("click", ".wdpsn-meta-box-single-field__input__rules .wdpsn-meta-box-single-field__input__rules__row__remove", function() {
            var t = jQuery(this).parents(".wdpsn-meta-box-single-field__input__rules__row"),
                e = t.parents(".wdpsn-meta-box-single-field__input__rules__rows-groups__group"),
                n = e.find(".wdpsn-meta-box-single-field__input__rules__row").length,
                o = t.parents(".wdpsn-meta-box-single-field__input__rules"),
                a = h.controllDatasetVal(o.data("set"));
            e.is(":first-child") && t.is(":first-child") || (1 === n ? (e.prev().remove(), e.remove()) : 2 < n ? t.remove() : (t.remove(), e.removeClass("wdpsn-meta-box-single-field__input__rules__rows-groups__group--multiple")), h.updateAdditionalDescription(a))
        }), jQuery("body").on("change", ".wdpsn-meta-box-single-field__input select", function() {
            var t = jQuery(this).parents(".wdpsn-meta-box-single-field__input__rules"),
                e = 0 !== t.length && h.controllDatasetVal(t.data("set"));
            h.updateDependencies(e)
        }), jQuery("body").on("change", 'select[name="wdpsn_note_types_styles[color_scheme]"]', function() {
            var t = jQuery(this),
                e = t.val(),
                n = t.parents(".wdpsn-meta-box-single-field__input").find(".wdpsn-note-container--preview");
            n.removeClass().addClass("wdpsn-note-container wdpsn-note-container--preview wdpsn-note-container--" + e).removeAttr("style"), "custom" === e && (t = jQuery('input[name="wdpsn_note_types_styles[colors][main-color]"]'), h.updateCustomColorSchemeForNoteType(t.val(), n, !1))
        }), jQuery("body").on("keyup", 'input#title[name="post_title"]', function() {
            var t = jQuery(this);
            h.updateTagLivePreview("title", h.getPureText(t.val()))
        }), jQuery(window).load(function() {
            v.updateHolders(), v.maybeAddHolderToMediaPopup(1), b.hideSwitcherIfUnavailable(), g.appendPopupWrapper(), j.initialize()
        })
    })
}]);