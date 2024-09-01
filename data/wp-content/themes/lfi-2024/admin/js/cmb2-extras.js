window.cmb2_extras = window.cmb2_extras || {};
(function (window, document, $, cmb2_extras) {
    "use strict";
    
    cmb2_extras.init = function (element) {
        cmb2_extras.$cmbGrp = $(".cmb-type-group");
        cmb2_extras.replaceTitles();

        cmb2_extras.$cmbGrp.on("cmb2_add_row", cmb2_extras.addRow);
        cmb2_extras.$cmbGrp.on("cmb2_remove_row", cmb2_extras.removeRow);
        cmb2_extras.$cmbGrp.on("cmb2_shift_rows_complete", cmb2_extras.shiftRows);

        // self.$el.on("cmb2_add_row", { cmb2Group: self }, self.autoComplete);
        cmb2_extras.$cmbGrp.on("keyup mouseup", cmb2_extras.replaceOnKeyUp);
    };

    cmb2_extras.getValue = function (element) {
        let tagName = $(element).prop("tagName");
        if (tagName === "INPUT") {
            return $(element).val();
        }
        if (tagName === "SELECT") {
            if ($(element).val().length) {
                return $(element).find("option:selected").text();
            }
        }
    };

    cmb2_extras.addRow = function (evt) {
        cmb2_extras.replaceTitles();
    };
    cmb2_extras.removeRow = function (evt) {
        cmb2_extras.replaceTitles();
    };
    cmb2_extras.shiftRows = function (evt) {
        cmb2_extras.replaceTitles();
    };

    cmb2_extras.replaceTitles = function (e) {
        let self;
        let grp;
        if (typeof e === "undefined") {
            self = this;
            grp = self.$el;
        } else {
            grp = $(e.target);
            self = e.data.cmb2Group;
        }

        $.each(grp, function (i, el) {
            $(el)
                .find(".cmb-group-title")
                .each(function () {
                    self.replaceTitle(this, el);
                });
        });
    };

    cmb2_extras.replaceTitle = function (title) {
        let self = this;
        let $title = $(title);
        let $parentEl = $(title).parents(".cmb-type-group");
        let txt = $parentEl.find("[data-grouptitle]").data("grouptitle");
        if (typeof txt === "undefined") {
            return;
        }
        let count = (txt.match(/{#/g) || []).length;

        for (let i = 1; i < count + 1; i++) {
            let name = $title.next().find(".grp-title-" + i);
            let value = cmb2_extras.getValue(name);
            if (typeof value !== "undefined") {
                let pattern = new RegExp("{#" + i + "}", "g");
                txt = txt.replace(pattern, value);
            } else {
                let num =
                    parseInt(
                        $title
                            .parent(".cmb-repeatable-grouping")
                            .attr("data-iterator")
                    ) + 1;
                txt = txt.replace("{#}", num);
            }
        }

        if (txt) {
            $title.text(txt);
        }
    };

    cmb2_extras.replaceOnKeyUp = function (evt) {
        let $input = $(evt.target);
        let title = $input
            .parents(".cmb-row.cmb-repeatable-grouping")
            .find(".cmb-group-title");
        cmb2_extras.replaceTitle(title);
    };

    
    $(document).ready(function () {
        $(cmb2_extras.init);
    });
})(window, document, jQuery, window.cmb2_extras);