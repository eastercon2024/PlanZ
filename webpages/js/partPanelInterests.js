var panelInterests = new PanelInterests;

function PanelInterests() {
	this.initialize = function initialize() {
		$("#sessionFRM").on("submit", panelInterests.handleSubmit);
		$(".mycontrol").on("change", panelInterests.anyChange);
	}

	this.anyChange = function anyChange(e) {
		$("#resultBoxDIV").css("display", "none");
		$("#update-warning").css("display", "block");
	}

	this.handleSubmit = function submit(e) {
		e.preventDefault();

		$("#resultBoxDIV").html("&nbsp;").css("display", "none");
		$("#update-warning").css("display", "block");
		$("input[type='submit']").button('loading');

		var postdata = {
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
				url: "PartPanelInterests_POST2.php",
				dataType: "html",
				data: postdata,
				success: panelInterests.showUpdateResults,
				error: panelInterests.showAjaxError,
				type: "POST"
		});
		// TODO: Does it trigger showing the error panel?
		//       And then make it read the data, update it in the db, then return the new rows and have the js swap out the contents

		return false;
	}

	this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
		$("#interests_body").html(data);

		// Hook up the event handlers to the new dom elements
		$(".mycontrol").on("change", panelInterests.anyChange);

		$("#update-warning").css("display", "none");
		$("#resultBoxDIV").html("<div class=\"alert alert-success\">Database updated successfully</div>").css("display", "block")[0].scrollIntoView({block: "nearest"});
	}

	this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
		var content;
		if (data && data.responseText) {
				content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
		} else {
				content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
		}
		$("#update-warning").css("display", "none");
		$("#resultBoxDIV").html(content).css("display", "block")[0].scrollIntoView({block: "nearest"});
	}
}
