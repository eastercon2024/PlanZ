// Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var partSearchSessionsSubmit = new PartSearchSessionsSubmit;

function PartSearchSessionsSubmit() {
	this.checkboxClicked = function checkboxClicked(e) {
		var checkbox = e.target;
		var checked = checkbox.checked;
		var sessionId = checkbox.name.substring(3);

		var postdata = {
			sessionId: sessionId,
			checked: checked ? 1 : 0
		};
		$.ajax({
				url: "SubmitMySessions1.php",
				dataType: "html",
				data: postdata,
				success: partSearchSessionsSubmit.showUpdateResults,
				error: partSearchSessionsSubmit.showAjaxError,
				type: "POST"
		});
	}

	this.initialize = function initialize() {
		$("#resultBoxDIV").html("&nbsp;").css("display", "none");

		var checkboxes = document.getElementsByClassName("interestsCHK");
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].addEventListener("click", this.checkboxClicked);
		}
	};

	this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
		console.log("Successfully added");
	}

	this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
    var $resultBoxDIV = $("#resultBoxDIV");
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $resultBoxDIV.html(content).show();
    window.scrollTo(0, 0);
	}
}
