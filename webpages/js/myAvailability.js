//    Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var myAvailability = new MyAvailability;

function MyAvailability() {
  var $addButton;
  var $editButton;
  var $availableTimesTable;
  var $availabilityTableBody;
  var $availabilityTableFooter;
  var $addStartDaySelect, $addStartTimeSelect, $addEndDaySelect, $addEndTimeSelect;
  var $editStartDaySelect, $editStartTimeSelect, $editEndDaySelect, $editEndTimeSelect;
  var $addErrorTd;
  var $editRow;
  var $editErrorRow;
  var $editingRow;

  this.initialize = function initialize() {
    $("#generalAvailabilityForm").on("submit", myAvailability.handleGeneralAvailabilitySubmit);
    $("#generalAvailabilityResultBoxDIV").css("display", "none");

    $availableTimesTable = $("#availableTimesTable");
    $availabilityTableBody = $("#availableTimesTable tbody");
    $availabilityTableFooter = $("#availableTimesTable tfoot");
    $addStartDaySelect = $("#addStartDay");
    $addStartTimeSelect = $("#addStartTime");
    $addEndDaySelect = $("#addEndDay");
    $addEndTimeSelect = $("#addEndTime");
    $editStartDaySelect = $("#editStartDay");
    $editStartTimeSelect = $("#editStartTime");
    $editEndDaySelect = $("#editEndDay");
    $editEndTimeSelect = $("#editEndTime");
    $editRow = $("#editRow");
    $editErrorRow = $("#editRowError");
    
    $addButton = $("#addAvailabilityBttn");
    $editButton = $("#updateAvailabilityBttn");

    $addErrorTd = $("#availabilityTableError");
    $addErrorTd.css("display", "none");

    $("#addAvailabilityBttn").on("click", myAvailability.handleAddAvailabilityClick);
    $("#cancelAvailabilityBttn").on("click", myAvailability.handleCancelEditAvailabilityClick);
    $("#updateAvailabilityBttn").on("click", myAvailability.handleUpdateAvailabilityClick);

    $("#addRow select").on("change", myAvailability.handleAddAvailabilityChange);
    $("#editRow select").on("change", myAvailability.handleEditAvailabilityChange);

    myAvailability.hydrate();
  }

  this.hydrate = function hydrate() {
    $("button[name='edit']").on("click", myAvailability.handleEditAvailabilityClick);
    $("button[name='delete']").on("click", myAvailability.handleDeleteAvailabilityClick);
  }

  this.handleAddAvailabilityClick = function handleAddAvailabilityClick(e) {
    e.preventDefault();

    var data = {
      ajax_request_action: 'setAvailability',
    };

    var idx = 1;
    $availabilityTableBody.children().each(function(i, $elm) {
      data["startday" + idx] = $("[data-availstartday]", $elm).data("availstartday");
      data["starttime" + idx] = $("[data-availstarttime]", $elm).data("availstarttime");
      data["endday" + idx] = $("[data-availendday]", $elm).data("availendday");
      data["endtime" + idx] = $("[data-availendtime]", $elm).data("availendtime")
      idx += 1;
    });

    data["startday" + idx] = $addStartDaySelect.val();
    data["starttime" + idx] = $addStartTimeSelect.val();
    data["endday" + idx] = $addEndDaySelect.val();
    data["endtime" + idx] = $addEndTimeSelect.val();

    $.ajax({
      url: "SubmitMySchedConstr.php",
      dataType: "html",
      data: data,
      success: myAvailability.handleAddAvailabilitySuccess,
      error: myAvailability.handleSetAvailabilityError,
      type: "POST"
    });

    return false;
  }

  this.handleAddAvailabilitySuccess = function handleAddAvailabilitySuccess(data, textStatus, jqXHR) {
    $addStartDaySelect.val("-1");
    $addStartTimeSelect.val("-1");
    $addEndDaySelect.val("-1");
    $addEndTimeSelect.val("-1");
    $addButton.attr("disabled", "");
    myAvailability.handleSetAvailabilitySuccess(data, textStatus, jqXHR);
  }

  this.handleSetAvailabilitySuccess = function handleSetAvailabilitySuccess(data, textStatus, jqXHR) {
    $availabilityTableBody.html(data);
    myAvailability.hydrate();
  }

  this.handleSetAvailabilityError = function handleSetAvailabilityError(data, textStatus, jqXHR) {
    if (data && data.responseText) {
      $addErrorTd.text(data.responseText).css("display", "");
    } else {
      $addErrorTd.text("An error occurred on the server.").css("display", "");
    }
  };

  this.handleEditAvailabilityClick = function handleEditAvailabilityClick(e) {
    myAvailability.handleCancelEditAvailabilityClick();

    $("button", $availableTimesTable).attr("disabled", "");
    $("select", $availableTimesTable).attr("disabled", "");
    $("button", $editRow).removeAttr("disabled");
    $("select", $editRow).removeAttr("disabled");

    $editingRow = $(e.target).closest("tr");
    $editingRow.css("display", "none");

    var startDay = $("[data-availstartday]", $editingRow).data("availstartday");
    var startTime = $("[data-availstarttime]", $editingRow).data("availstarttime");
    var endDay = $("[data-availendday]", $editingRow).data("availendday");
    var endTime = $("[data-availendtime]", $editingRow).data("availendtime");

    $editStartDaySelect.val(startDay);
    $editStartTimeSelect.val(startTime);
    $editEndDaySelect.val(endDay);
    $editEndTimeSelect.val(endTime);

    $editRow.insertAfter($editingRow);
    $editRow.css("display", "");
    $editErrorRow.insertAfter($editRow);
  }

  this.handleCancelEditAvailabilityClick = function handleCancelEditAvailabilityClick() {
    if ($editingRow !== undefined) {
      $editingRow.css("display", "");

      $editRow.css("display", "none");
      $availabilityTableFooter.append($editRow);
      $editErrorRow.css("display", "none");
      $editErrorRow.insertAfter($editRow);

      $("select", $availableTimesTable).removeAttr("disabled");
      $("button", $availableTimesTable).removeAttr("disabled");
      myAvailability.handleAddAvailabilityChange();
      $editingRow = undefined;
    }
  }

  this.handleDeleteAvailabilityClick = function handleDeleteAvailabilityClick(e) {
    var data = {
      ajax_request_action: 'setAvailability',
    };

    var idx = 1;
    $availabilityTableBody.children().each(function(i, $elm) {
      if (this === e.target.parentNode.parentNode) {
        return;
      }
      data["startday" + idx] = $("[data-availstartday]", $elm).data("availstartday");
      data["starttime" + idx] = $("[data-availstarttime]", $elm).data("availstarttime");
      data["endday" + idx] = $("[data-availendday]", $elm).data("availendday");
      data["endtime" + idx] = $("[data-availendtime]", $elm).data("availendtime")
      idx += 1;
    });

    $.ajax({
      url: "SubmitMySchedConstr.php",
      dataType: "html",
      data: data,
      success: myAvailability.handleSetAvailabilitySuccess,
      error: myAvailability.handleSetAvailabilityError,
      type: "POST"
    });
  }

  this.handleUpdateAvailabilityClick = function handleUpdateAvailabilityClick(e) {
    e.preventDefault();

    var data = {
      ajax_request_action: 'setAvailability',
    };

    var idx = 1;
    $availabilityTableBody.children().each(function(i, $elm) {
      if ($elm == $editRow[0]) {
        return;
      }
      if ($elm == $editingRow[0]) {
        data["startday" + idx] = $editStartDaySelect.val();
        data["starttime" + idx] = $editStartTimeSelect.val();
        data["endday" + idx] = $editEndDaySelect.val();
        data["endtime" + idx] = $editEndTimeSelect.val();
      } else {
        data["startday" + idx] = $("[data-availstartday]", $elm).data("availstartday");
        data["starttime" + idx] = $("[data-availstarttime]", $elm).data("availstarttime");
        data["endday" + idx] = $("[data-availendday]", $elm).data("availendday");
        data["endtime" + idx] = $("[data-availendtime]", $elm).data("availendtime")
      }
      idx += 1;
    });

    $.ajax({
      url: "SubmitMySchedConstr.php",
      dataType: "html",
      data: data,
      success: myAvailability.handleEditAvailabilitySuccess,
      error: myAvailability.handleSetAvailabilityError,
      type: "POST"
    });

    return false;
  }

  this.handleEditAvailabilitySuccess = function handleEditAvailabilitySuccess(data, textStatus, jqXHR) {
    myAvailability.handleCancelEditAvailabilityClick();
    myAvailability.handleSetAvailabilitySuccess(data, textStatus, jqXHR);
  }

  this.handleAddAvailabilityChange = function handleAddAvailabilityChange(e) {
    $addErrorTd.css("display", "none");

    var enable = true;
    $("select", $availabilityTableFooter).each(function() {
      if ($(this).val() === "-1") {
        enable = false;
      }
    });

    if (enable) {
      var startDayIdx = parseInt($addStartDaySelect.val());
      var startTimeIdx = parseInt($addStartTimeSelect.val());
      var endDayIdx = parseInt($addEndDaySelect.val());
      var endTimeIdx = parseInt($addEndTimeSelect.val());
  
      if (startDayIdx > endDayIdx) {
        enable = false;
        $addErrorTd.text("Start day must be before end day.").css("display", "");
      } else if (startDayIdx === endDayIdx) {
        if (startTimeIdx >= endTimeIdx) {
          enable = false;
          $addErrorTd.text("Start time must be before end time.").css("display", "");
        }
      }
    }

    if (enable) {
      $addButton.removeAttr("disabled");
    } else {
      $addButton.attr("disabled", "");
    }
  }

  this.handleEditAvailabilityChange = function handleEditAvailabilityChange(e) {
    $("#editRowError").css("display", "none");

    var startDayIdx = parseInt($editStartDaySelect.val());
    var startTimeIdx = parseInt($editStartTimeSelect.val());
    var endDayIdx = parseInt($editEndDaySelect.val());
    var endTimeIdx = parseInt($editEndTimeSelect.val());

    var enable = true;
    if (startDayIdx > endDayIdx) {
      enable = false;
      $("#editRowError").css("display", "");
      $("#editRowError td").text("Start day must be before end day.");
    } else if (startDayIdx === endDayIdx) {
      if (startTimeIdx >= endTimeIdx) {
        enable = false;
        $("#editRowError").css("display", "");
        $("#editRowError td").text("Start time must be before end time.");
      }
    }

    if (enable) {
      $editButton.removeAttr("disabled");
    } else {
      $editButton.attr("disabled", "");
    }
  }

  this.handleGeneralAvailabilitySubmit = function handleGeneralAvailabilitySubmit(e) {
    e.preventDefault();
    
    $("#generalAvailabilityResultBoxDIV").css("display", "none");

    var data = {
      ajax_request_action: 'updateGeneralAvailability',
      maxprog: $("#maxprog").val(),
      preventconflict: $("#preventconflict").val(),
      otherconstraints: $("#otherconstraints").val(),
    };

    var i = 1;
    while (true) {
      var $elem = $("#maxprogday" + i);
      if ($elem.length === 0) {
        break;
      }
      data["maxprogday" + i] = $elem.val();
      i++;
    }

    $.ajax({
      url: "SubmitMySchedConstr.php",
      dataType: "html",
      data: data,
      success: myAvailability.handleGeneralAvailabilitySubmitSuccess,
      error: myAvailability.handleGeneralAvailabilitySubmitError,
      type: "POST"
    });

    return false;
  }

  this.handleGeneralAvailabilitySubmitSuccess = function handleGeneralAvailabilitySubmitError(data, textStatus, jqXHR) {
    console.log(data);
    var content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-success" role="alert">Your general availability has been updated.</div></div></div>`;
    $("#generalAvailabilityResultBoxDIV").html(content).css("display", "block");
  }

  this.handleGeneralAvailabilitySubmitError = function handleGeneralAvailabilitySubmitError(data, textStatus, jqXHR) {
    var content;
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $("#generalAvailabilityResultBoxDIV").html(content).css("display", "block");
  };
}