//    Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var myAvailability = new MyAvailability;

function MyAvailability() {
  var $addButton;
  var $editButton;
  var $availabilityGrid;
  var $availabilityItems;
  var $addForm;
  var $addStartDaySelect, $addStartTimeSelect, $addEndDaySelect, $addEndTimeSelect, $addLocationSelect;
  var $editStartDaySelect, $editStartTimeSelect, $editEndDaySelect, $editEndTimeSelect, $editLocationSelect;
  var $addError;
  var $editForm;
  var $editError;
  var $editingRow;

  this.initialize = function initialize() {
    $("#generalAvailabilityForm").on("submit", myAvailability.handleGeneralAvailabilitySubmit);
    $("#generalAvailabilityResultBoxDIV").css("display", "none");

    $availabilityGrid = $("#availabilityGrid");
    $availabilityItems = $("#availabilityItems");
    $addForm = $("#addForm");
    $addStartDaySelect = $("#addStartDay");
    $addStartTimeSelect = $("#addStartTime");
    $addEndDaySelect = $("#addEndDay");
    $addEndTimeSelect = $("#addEndTime");
    $addLocationSelect = $("#addLocation");
    $editStartDaySelect = $("#editStartDay");
    $editStartTimeSelect = $("#editStartTime");
    $editEndDaySelect = $("#editEndDay");
    $editEndTimeSelect = $("#editEndTime");
    $editLocationSelect = $("#editLocation");
    $editForm = $("#editForm");
    $editError = $("#editError");
    
    $addButton = $("#addAvailabilityBttn");
    $editButton = $("#updateAvailabilityBttn");

    $addError = $("#addError");
    $addError.css("display", "none");

    $("#addAvailabilityBttn").on("click", myAvailability.handleAddAvailabilityClick);
    $("#cancelAvailabilityBttn").on("click", myAvailability.handleCancelEditAvailabilityClick);
    $("#updateAvailabilityBttn").on("click", myAvailability.handleUpdateAvailabilityClick);

    $("#addForm select").on("change", myAvailability.handleAddAvailabilityChange);
    $("#editForm select").on("change", myAvailability.handleEditAvailabilityChange);

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
    $availabilityItems.children().each(function(i, elm) {
      var $elm = $(elm);
      data["startday" + idx] = $elm.data("availstartday");
      data["starttime" + idx] = $elm.data("availstarttime");
      data["endday" + idx] = $elm.data("availendday");
      data["endtime" + idx] = $elm.data("availendtime");
      data["location" + idx] = $elm.data("availlocation");
      idx += 1;
    });

    data["startday" + idx] = $addStartDaySelect.val();
    data["starttime" + idx] = $addStartTimeSelect.val();
    data["endday" + idx] = $addEndDaySelect.val();
    data["endtime" + idx] = $addEndTimeSelect.val();
    data["location" + idx] = $addLocationSelect.val();

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
    $addLocationSelect.val("-1");
    $addButton.attr("disabled", "");
    myAvailability.handleSetAvailabilitySuccess(data, textStatus, jqXHR);
  }

  this.handleSetAvailabilitySuccess = function handleSetAvailabilitySuccess(data, textStatus, jqXHR) {
    $availabilityItems.html(data);
    myAvailability.hydrate();
  }

  this.handleSetAvailabilityError = function handleSetAvailabilityError(data, textStatus, jqXHR) {
    if (data && data.responseText) {
      $addError.text(data.responseText).css("display", "");
    } else {
      $addError.text("An error occurred on the server.").css("display", "");
    }
  };

  this.handleEditAvailabilityClick = function handleEditAvailabilityClick(e) {
    myAvailability.handleCancelEditAvailabilityClick();

    $("button", $availabilityGrid).attr("disabled", "");
    $("select", $availabilityGrid).attr("disabled", "");
    $("button", $editForm).removeAttr("disabled");
    $("select", $editForm).removeAttr("disabled");

    $editingRow = $(e.target).closest("div");
    $editingRow.css("display", "none");

    var startDay = $editingRow.data("availstartday");
    var startTime = $editingRow.data("availstarttime");
    var endDay = $editingRow.data("availendday");
    var endTime = $editingRow.data("availendtime");
    var location = $editingRow.data("availlocation");

    $editStartDaySelect.val(startDay);
    $editStartTimeSelect.val(startTime);
    $editEndDaySelect.val(endDay);
    $editEndTimeSelect.val(endTime);
    $editLocationSelect.val(location);

    $editForm.insertAfter($editingRow);
    $editForm.css("display", "");
    $editError.insertAfter($editForm);
  }

  this.handleCancelEditAvailabilityClick = function handleCancelEditAvailabilityClick() {
    if ($editingRow !== undefined) {
      $editingRow.css("display", "");

      $editForm.css("display", "none");
      $availabilityGrid.append($editForm);
      $editError.css("display", "none");
      $editError.insertAfter($editForm);

      $("select", $availabilityGrid).removeAttr("disabled");
      $("button", $availabilityGrid).removeAttr("disabled");
      myAvailability.handleAddAvailabilityChange();
      $editingRow = undefined;
    }
  }

  this.handleDeleteAvailabilityClick = function handleDeleteAvailabilityClick(e) {
    var data = {
      ajax_request_action: 'setAvailability',
    };

    var idx = 1;
    $availabilityItems.children().each(function(i, elm) {
      if (this === e.target.parentNode.parentNode) {
        return;
      }
      var $elm = $(elm);
      data["startday" + idx] = $elm.data("availstartday");
      data["starttime" + idx] = $elm.data("availstarttime");
      data["endday" + idx] = $elm.data("availendday");
      data["endtime" + idx] = $elm.data("availendtime");
      data["location" + idx] = $elm.data("availlocation");
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
    $availabilityItems.children().each(function(i, elm) {
      if (elm == $editForm[0] || elm == $editError[0]) {
        return;
      }
      if (elm == $editingRow[0]) {
        data["startday" + idx] = $editStartDaySelect.val();
        data["starttime" + idx] = $editStartTimeSelect.val();
        data["endday" + idx] = $editEndDaySelect.val();
        data["endtime" + idx] = $editEndTimeSelect.val();
        data["location" + idx] = $editLocationSelect.val();
      } else {
        var $elm = $(elm);
        data["startday" + idx] = $elm.data("availstartday");
        data["starttime" + idx] = $elm.data("availstarttime");
        data["endday" + idx] = $elm.data("availendday");
        data["endtime" + idx] = $elm.data("availendtime");
        data["location" + idx] = $elm.data("availlocation");
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
    $addError.css("display", "none");

    var enable = true;
    $("select", $addForm).each(function() {
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
        $addError.text("Start day must be before end day.").css("display", "");
      } else if (startDayIdx === endDayIdx) {
        if (startTimeIdx >= endTimeIdx) {
          enable = false;
          $addError.text("Start time must be before end time.").css("display", "");
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
    $("#editError").css("display", "none");

    var startDayIdx = parseInt($editStartDaySelect.val());
    var startTimeIdx = parseInt($editStartTimeSelect.val());
    var endDayIdx = parseInt($editEndDaySelect.val());
    var endTimeIdx = parseInt($editEndTimeSelect.val());

    var enable = true;
    if (startDayIdx > endDayIdx) {
      enable = false;
      $("#editError").css("display", "");
      $("#editError").text("Start day must be before end day.");
    } else if (startDayIdx === endDayIdx) {
      if (startTimeIdx >= endTimeIdx) {
        enable = false;
        $("#editError").css("display", "");
        $("#editError").text("Start time must be before end time.");
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