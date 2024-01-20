<?php
$title="Change Password";
require ('PartCommonCode.php'); // initialize db; check login;

participant_header($title, false, 'Normal', true);

echo "<form id=\"changePasswordForm\" class=\"container\">\n";
echo "  <div class=\"card\">\n";
echo "    <div class=\"card-header\">\n";
echo "      <h2 class=\"card-title\">Change Password</h2>\n";
echo "      <div id=\"resultBoxDIV\"><span class=\"beforeResult\" id=\"resultBoxSPAN\">Result messages will appear here.</span></div>\n";
echo "    </div>\n";
echo "    <div class=\"card-body\">\n";
echo "      <div class=\"form-group\">\n";
echo "        <label for=\"oldPassword\">Old Password</label>\n";
echo "        <input type=\"password\" class=\"form-control\" id=\"oldPassword\" name=\"oldPassword\" placeholder=\"Old Password\">\n";
echo "      </div>\n";
echo "      <div class=\"form-group\">\n";
echo "        <label for=\"newPassword\">New Password</label>\n";
echo "        <input type=\"password\" class=\"form-control\" id=\"newPassword\" name=\"newPassword\" placeholder=\"New Password\">\n";
echo "      </div>\n";
echo "      <div class=\"form-group\">\n";
echo "        <label for=\"confirmPassword\">New Password (again)</label>\n";
echo "        <input type=\"password\" class=\"form-control\" id=\"confirmPassword\" name=\"confirmPassword\" placeholder=\"New Password (again)\">\n";
echo "      </div>\n";
echo "      <p id=\"passwordError\" class=\"text-danger\"></p>\n";
echo "    </div>\n";
echo "    <div class=\"card-footer\">\n";
echo "      <button type=\"submit\" class=\"btn btn-primary\">Change Password</button>\n";
echo "    </div>\n";
echo "  </div>\n";
echo "</form>\n";

participant_footer();
