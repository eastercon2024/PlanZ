var changePassword = new ChangePassword

function ChangePassword() {
  var $oldPassword;
  var $newPassword;
  var $confirmPassword;
  var $submitBttn;

  this.initialize = function initialize() {
    $oldPassword = $("#oldPassword");
    $newPassword = $("#newPassword");
    $confirmPassword = $("#confirmPassword");
    $submitBttn = $("button[type='submit']");

    $("#changePasswordForm").on("submit", changePassword.handleSubmit);
    $oldPassword.on("change", changePassword.validatePasswords);
    $newPassword.on("change", changePassword.validatePasswords);
    $confirmPassword.on("change", changePassword.validatePasswords);
    $oldPassword.on("keyup", changePassword.validatePasswords);
    $newPassword.on("keyup", changePassword.validatePasswords);
    $confirmPassword.on("keyup", changePassword.validatePasswords);

    $("#resultBoxDIV").css("display", "none");

    $submitBttn.prop("disabled", true);
  }

  this.handleSubmit = function handleSubmit(e) {
      e.preventDefault();

      $.ajax({
        url: "ChangePasswordSubmit.php",
        dataType: "html",
        data: ({
            oldPassword: $oldPassword.val(),
            newPassword: $newPassword.val()
        }),
        success: changePassword.handleSuccess,
        error: changePassword.showAjaxError,
        type: "POST"
      });

      return false;
  }

  this.validatePasswords = function validatePasswords() {
    if ($newPassword.val() !== $confirmPassword.val() && $confirmPassword.val().length > 0) {
      $newPassword.addClass("is-invalid");
      $confirmPassword.addClass("is-invalid");
      $("#passwordError").text("Passwords do not match");
    } else {
      $newPassword.removeClass("is-invalid");
      $confirmPassword.removeClass("is-invalid");
      $("#passwordError").text("");
    }
    var canChange = $oldPassword.val().length > 0 && $newPassword.val().length > 0 && $confirmPassword.val().length > 0 && $newPassword.val() === $confirmPassword.val();
    $submitBttn.prop("disabled", !canChange);
  }

  this.handleSuccess = function handleSuccess(data) {
    window.location = '/login.php';
  }

  this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
    var content;
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $("#resultBoxDIV").html(content).css("display", "block")[0].scrollIntoView(false);
  };
}