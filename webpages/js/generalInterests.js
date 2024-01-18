//    Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var generalInterests = new GeneralInterests;

function GeneralInterests() {
    var anyDirty = false;
    var $submitBTN;
    var $resultBoxDiv;

    this.anyChange = function anyChange(event) {
        $resultBoxDiv.html("&nbsp;").css("display", "none");
        anyDirty = true;
        $("#submitBTN").prop("disabled", false);
    };

    this.initialize = function initialize() {
        //called when JQuery says My Profile page has loaded
        var boundAnyChange = this.anyChange.bind(this);
        $submitBTN = $("#submitBTN");
        $submitBTN.button().prop("disabled", true);
        $("select.mycontrol").on("change", boundAnyChange);
        $("input.mycontrol[type='checkbox']").on("input", boundAnyChange);
        $("input.mycontrol[type='text']").on("input", boundAnyChange);
        $("input.mycontrol[type='password']").on("input", boundAnyChange);
        $(":checkbox.mycontrol").on("change", boundAnyChange);
        $(":radio.mycontrol").on("change", boundAnyChange);
        $("textarea.mycontrol").on("input", boundAnyChange);
        $resultBoxDiv = $("#resultBoxDIV");
        $resultBoxDiv.html("&nbsp;").css("display", "none");
    };

    this.updateBUTN = function updateBUTN() {
        $("#submitBTN").button('loading');

        var postdata = {
            ajax_request_action: "update_participant"
        };
        $(".mycontrol").each(function() { // this is element
            var $elem = $(this);
            if ($elem.is(":disabled") || $elem.attr("readonly")) {
                return;
            }
            if ($elem.attr("type") === "radio") {
                postdata[$elem.attr("name")] = $elem.val();
            } else if ($elem.prop("tagName") === "SELECT") {
                postdata[$elem.attr("name")] = $elem.val();
            } else if ($elem.attr("type") === "checkbox") {
                postdata[$elem.attr("id")] = $elem.is(":checked") ? 1 : 0;
            } else { // text or textarea
                postdata[$elem.attr("name")] = $elem.val();
            }
        });
        $.ajax({
            url: "SubmitMyInterests.php",
            dataType: "html",
            data: postdata,
            success: generalInterests.showUpdateResults,
            error: generalInterests.showAjaxError,
            type: "POST"
        });
    };

    this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
        //ajax success callback function
        $resultBoxDiv.html(data).css("display", "block");
        anyDirty = false;
        $submitBTN.button('reset');
        setTimeout(function () {
            $submitBTN.button().prop("disabled", true);
        }, 0);
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    };

    this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
        var content;
        if (data && data.responseText) {
            content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
        } else {
            content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
        }
        $resultBoxDiv.html(content).css("display", "block");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    };
}