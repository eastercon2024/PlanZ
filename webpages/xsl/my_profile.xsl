<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2011-07-24;
	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="enableShareEmailQuestion" select="'0'"/>
    <xsl:param name="enableUsePhotoQuestion" select="'0'"/>
    <xsl:param name="enableLiveStreamQuestion" select="'0'"/>
    <xsl:param name="enableVodQuestion" select="'0'"/>
    <xsl:param name="enableBestwayQuestion" select="'0'"/>
    <xsl:param name="useRegSystem" select="0"/>
    <xsl:param name="maxBioLen" select="500"/>
    <xsl:param name="enableBioEdit" select="'0'"/>
    <xsl:param name="htmlbio" select="'0'"/>
    <xsl:param name="userIdPrompt" select="''"/>
    <xsl:param name="photoPath" select="''"/>
    <xsl:param name="defaultPhoto" select="''"/>
    <xsl:param name="enableDayJobQuestion" select="0"/>
    <xsl:param name="enableAgeRangeQuestion" select="0"/>
    <xsl:param name="enableAccessibilityQuestion" select="0"/>
    <xsl:param name="enableEthnicityQuestion" select="0"/>
    <xsl:param name="enableGenderQuestion" select="0"/>
    <xsl:param name="enableSexualOrientationQuestion" select="0"/>
    <xsl:param name="enablePronounsQuestion" select="0"/>
    <xsl:param name="RESET_PASSWORD_SELF" select="true()" /><!-- TRUE/FALSE -->
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="use_photo" select="/doc/query[@queryName='participant_info']/row/@use_photo" />
        <xsl:variable name="live_stream" select="/doc/query[@queryName='participant_info']/row/@live_stream" />
        <xsl:variable name="vod" select="/doc/query[@queryName='participant_info']/row/@vod" />
        <xsl:variable name="share_email" select="/doc/query[@queryName='participant_info']/row/@share_email" />
        <xsl:variable name="interested" select="/doc/query[@queryName='participant_info']/row/@interested" />
        <xsl:variable name="bestway" select="/doc/query[@queryName='participant_info']/row/@bestway" />
        <xsl:variable name="bioNote" select="/doc/customText/@biography_note" />
        <xsl:variable name="regDataNote" select="/doc/customText/@registration_data" />
        <form name="partform" class="container mt-2 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>
                        <xsl:choose>
                            <xsl:when test="$photoPath != '' and /doc/query[@queryName='participant_info']/row/@approvedphotofilename">
                                <img class="rounded-circle participant-avatar" style="width: 2rem;" alt="Participant Photo/Avatar">
                                    <xsl:attribute name="src"> 
                                        <xsl:value-of select="concat($photoPath, '/', /doc/query[@queryName='participant_info']/row/@approvedphotofilename)" />
                                    </xsl:attribute>
                                </img>
                            </xsl:when>
                            <xsl:when test="$photoPath != '' and $defaultPhoto != ''" alt="Default Photo/Avatar">
                                <img class="rounded-circle participant-avatar" style="width: 2rem;">
                                    <xsl:attribute name="src"> 
                                        <xsl:value-of select="concat($photoPath, '/', $defaultPhoto)" />
                                    </xsl:attribute>
                                </img>
                            </xsl:when>
                        </xsl:choose>
                        <span> Profile</span>
                    </h2>
                    <div id="resultBoxDIV">
                        <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span>
                    </div>            
                </div>
                <div class="card-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-auto">
                                <label for="interested">
                                    I am interested and able to participate in programming for <xsl:value-of select="$conName"/>:
                                </label>
                            </div>
                        </div>
                        <xsl:choose>
                            <xsl:when test="$enableBestwayQuestion = '1'">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-auto">
                                            <label for="bestway">Preferred mode of contact:</label>
                                        </div>
                                        <div class="col-auto">
                                            <div class="verticalRadioButs">
                                                <div class="radioNlabel">
                                                    <span class="radio">
                                                        <input name="bestway" id="bwemailRB" value="Email" type="radio" class="mycontrol">
                                                            <xsl:if test="$bestway='Email' or not($bestway)">
                                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                        </input>
                                                    </span>
                                                    <span class="radioLabel">
                                                        <label for="bwemailRB">Email</label>
                                                    </span>
                                                </div>
                                                <div class="radioNlabel">
                                                    <span class="radio">
                                                        <input name="bestway" id="bwpmailRB" value="Postal mail" type="radio" class="mycontrol">
                                                            <xsl:if test="$bestway='Postal mail'">
                                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                        </input>
                                                    </span>
                                                    <span class="radioLabel">
                                                        <label for="bwpmailRB">Postal Mail</label>
                                                    </span>
                                                </div>
                                                <div class="radioNlabel">
                                                    <span class="radio">
                                                        <input name="bestway" id="bwphoneRB" value="Phone" type="radio" class="mycontrol">
                                                            <xsl:if test="$bestway='Phone'">
                                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                        </input>
                                                    </span>
                                                    <span class="radioLabel">
                                                        <label for="bwphoneRB">Phone</label>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="bestway" id="bestway" type="hidden" value="{$bestway}"/>
                            </xsl:otherwise>
                        </xsl:choose>    
                        <div class="row">
                            <div class="col-auto">
                                <select id="interested" name="interested" class="mb-2 pl-2 pr-4 mycontrol">
                                    <option value="0">
                                        <xsl:if test="$interested=0 or not ($interested)">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                    </option>
                                    <option value="1">
                                        <xsl:if test="$interested=1">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        Yes
                                    </option>
                                    <option value="2">
                                        <xsl:if test="$interested=2">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        No
                                    </option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row mt-3">
                        <legend class="col-auto">Permissions</legend>
                    </div>
                    <xsl:choose>
                        <xsl:when test="$enableShareEmailQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="share_email">
                                            <strong>Share e-mail:</strong> I give permission for <xsl:value-of select="$conName"/>
                                            to share my email address with other participants.
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <select id="share_email" name="share_email" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($share_email) and $share_email !='0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$share_email = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$share_email = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="share_email" type="hidden" value="{$share_email}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="$enableUsePhotoQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="use_photo">
                                            <strong>Photos:</strong> I give permission for <xsl:value-of select="$conName"/> to photograph me while
                                            I am on programme items and to use those images in the promotion of the convention.
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <select id="use_photo" name="use_photo" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($use_photo) and $use_photo != '0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$use_photo = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$use_photo = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="use_photo" type="hidden" value="{$use_photo}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="$enableLiveStreamQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="live_stream">
                                            <strong>Live stream:</strong> I give permission for <xsl:value-of select="$conName"/> to broadcast a live stream of me on programme items to the rest of the convention membership.
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <select id="live_stream" name="live_stream" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($live_stream) and $live_stream != '0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$live_stream = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$live_stream = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="live_stream" type="hidden" value="{$live_stream}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="$enableVODQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="vod">
                                            <strong>VOD:</strong> I give permission for <xsl:value-of select="$conName"/> to make recordings of me on programme items available to the rest of the convention membership after the programme item has ended.
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <select id="vod" name="vod" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($vod) and $vod != '0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$vod = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$vod = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="vod" type="hidden" value="{$vod}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                <xsl:if test="$RESET_PASSWORD_SELF">
                    <div class="row mt-3">
                        <legend class="col-auto">Change password</legend>
                    </div>
                    <div class="row mb-3">
                        <div class="col-auto">Leave passwords fields blank to leave password unchanged.</div>
                    </div>
                    <fieldset id="passGroup" class="control-group">
                        <div class="row">
                            <div class="col-2">
                                <label for="password">New Password:</label>
                            </div>
                            <div class="col-4">
                                <input type="password" size="40" maxlength="40" name="password" id="password"
                                    class="form-control mycontrol mb-2" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label for="cpassword">Confirm Password:</label>
                            </div>
                            <div class="col-4 mb-2">
                                <input type="password" size="40" maxlength="40" name="cpassword" id="cpassword"
                                    class="form-control mycontrol mb-2" />
                                <div class="invalid-feedback">
                                    Passwords don't match!
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </xsl:if>
                    <fieldset>
                        <div class="row mt-3">
                            <legend class="col-auto">Published Information</legend>
                        </div>
                        <xsl:if test="$enableBioEdit!='1'">
                            <div class="row">
                                <h3 class="noteWLfPad">At this time, you may not edit either your biography or your name for
                                    publication. They have already gone to print.
                                </h3>
                            </div>
                        </xsl:if>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="pubsname">Your name as you wish to have it published:</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" size="20" maxlength="50" name="pubsname"
                                    value="{/doc/query[@queryName='participant_info']/row/@pubsname}"
                                    id="pubsname" class="mycontrol userFormINPTXT">
                                    <xsl:if test="$enableBioEdit!='1'">
                                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="sortedpubsname">Your published name as it should be sorted:</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" size="20" maxlength="50" name="sortedpubsname"
                                    value="{/doc/query[@queryName='participant_info']/row/@sortedpubsname}"
                                    id="sortedpubsname" class="mycontrol userFormINPTXT">
                                    <xsl:if test="$enableBioEdit!='1'">
                                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="pronouns">Your pronouns (optional):</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" side="20" maxlength="50" name="pronouns"
                                    value="{/doc/query[@queryName='participant_info']/row/@pronounother}"
                                    id="pronouns" class="mycontrol userFormINPTXT">
                                    <xsl:if test="$enableBioEdit!='1'">
                                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                        </div>
                        <xsl:choose>
                            <xsl:when test="$htmlbio = '1'">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="htmlbio">
                                            Biography (<xsl:value-of select="$maxBioLen"/> characters or fewer including spaces):
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <textarea rows="5" cols="72" name="htmlbio" id="htmlbioTXTA"          
                                            onchange="myProfile.bioChange()" onkeyup="myProfile.bioChange()"
                                            data-max-length="{$maxBioLen}">
                                            <xsl:choose>
                                                <xsl:when test="$enableBioEdit!='1'">
                                                    <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                    <xsl:attribute name="class">col-sm-12 userFormTXT readonly mycontrol form-control</xsl:attribute>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:attribute name="class">col-sm-12 userFormTXT mycontrol form-control</xsl:attribute>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                            <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@htmlbio"/>
                                        </textarea>
                                        <div id="badBio" class="invalid-feedback">Biography is too long!</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <label for="bio">Plain Text Version (Automatically derived from HTML version on pressing UPDATE):</label>
                                        <textarea rows="5" cols="72" name="bio" id="bioTXTA">
                                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                                            <xsl:attribute name="class">col-sm-12 userFormTXT readonly</xsl:attribute>
                                            <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bio"/>
                                        </textarea>
                                    </div>
                                </div>
                            </xsl:when>
                            <xsl:otherwise>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="bio"> Biography (<xsl:value-of select="$maxBioLen"/> characters or fewer including spaces):
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <textarea rows="5" cols="72" name="bio" id="bioTXTA" data-max-length="{$maxBioLen}">
                                            <xsl:choose>
                                                <xsl:when test="$enableBioEdit!='1'">
                                                    <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                    <xsl:attribute name="class">span12 userFormTXT readonly mycontrol</xsl:attribute>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:attribute name="class">span12 userFormTXT mycontrol</xsl:attribute>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                            <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bio"/>
                                        </textarea>
                                        <div id="badBio" class="invalid-feedback">Biography is too long!</div>
                                    </div>
                                </div>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:if test="$bioNote">
                            <div class="row mt-1">
                                <div class="col note">
                                    <xsl:value-of select="$bioNote" disable-output-escaping="yes"/>
                                </div>
                            </div>
                        </xsl:if>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-check mt-3">
                                    <input class="form-check-input mycontrol" type="checkbox" value="" id="anonymous" name="anonymous">
                                        <xsl:if test="/doc/query[@queryName='participant_info']/row/@anonymous = 'Y'">
                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                        </xsl:if>
                                    </input>
                                    <label for="anonymous" class="form-check-label">
                                        Do not publish my name in online or other publically-available sources.
                                    </label>
                                    <small class="form-text text-muted">Physical programs and schedules available to authenticated members will still show your name.</small>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <xsl:if test="/doc/query[@queryName='credentials']/row">
                        <fieldset>
                            <div class="row mt-3">
                                <legend class="col-auto">Professions</legend>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div>Please indicate if you are any of the following:</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-9 col-lg-6">
                                    <div class="row">
                                        <xsl:for-each select="/doc/query[@queryName='credentials']/row">
                                            <xsl:sort select="@display_order" data-type="number"/>
                                            <div class="col-sm-6">
                                                <label class="checkbox">
                                                    <input class="checkbox mycontrol mr-3" id="credentialCHK{@credentialid}" type="checkbox">
                                                        <xsl:if test="@badgeid">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:if test="$enableBioEdit!='1'">
                                                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="@credentialname"/>
                                                </label>
                                            </div>
                                        </xsl:for-each>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </xsl:if>
                    <xsl:if test="$useRegSystem = 1"><!-- show button here if using reg system because data below not editable in that case -->
                        <div class="row mt-3">
                            <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN"
                                data-loading-text="Updating..." onclick="myProfile.updateBUTN();">
                                Update
                            </button>
                        </div>
                    </xsl:if>
                    <xsl:choose>
                        <xsl:when test="$useRegSystem = 1">
                            <div class="row mt-3">
                                <legend class="col-auto">Data from Registration System</legend>
                            </div>
                            <xsl:if test="$regDataNote != ''">
                                <div class="row">
                                    <div class="col">
                                        <xsl:value-of select="$regDataNote" disable-output-escaping="yes" />
                                    </div>
                                </div>
                            </xsl:if>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="row mt-3">
                                <legend class="col-auto">Contact Information</legend>
                            </div>
                            <div class="row">
                                <div class="col">Please confirm your contact information.</div>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                    <fieldset>
                        <input type="hidden" name="postaddress1" value="" />
                        <input type="hidden" name="postaddress2" value="" />
                        <input type="hidden" name="postcity" value="" />
                        <input type="hidden" name="poststate" value="" />
                        <input type="hidden" name="postzip" value="" />
                        <input type="hidden" name="postcountry" value="" />
                        <input type="hidden" name="regtype">
                            <xsl:attribute name="value">
                                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@regtype" />
                            </xsl:attribute>
                        </input>
                        <div>
                            <xsl:choose>
                                <xsl:when test="$useRegSystem = 1">
                                    <xsl:attribute name="class">row</xsl:attribute>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:attribute name="class">row mt-1 mb-2</xsl:attribute>
                                </xsl:otherwise>
                            </xsl:choose>
                            <div class="col-sm-3 col-md-2p5 col-lg-2">
                                <h5>
                                    <div class="badge badge-secondary badge-full-width">
                                        <xsl:value-of select="$userIdPrompt" />
                                    </div>
                                </h5>
                            </div>
                            <div class="col">
                                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3p5 col-md-3 col-lg-2">
                                <h5>
                                    <div class="badge badge-secondary badge-full-width">
                                        Registration Type
                                    </div>
                                </h5>
                            </div>
                            <div class="col">
                                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@regtype" />
                            </div>
                        </div>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">First Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@firstname" />
                            <xsl:with-param name="id">fname</xsl:with-param>
                            <xsl:with-param name="maxlength" select="30" />
                            <xsl:with-param name="fieldsize" select="30" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Last Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@lastname" />
                            <xsl:with-param name="id">lname</xsl:with-param>
                            <xsl:with-param name="maxlength" select="40" />
                            <xsl:with-param name="fieldsize" select="40" />

                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Badge Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@badgename" />
                            <xsl:with-param name="id">badgename</xsl:with-param>
                            <xsl:with-param name="maxlength" select="50" />
                            <xsl:with-param name="fieldsize" select="50" />

                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Phone Info</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@phone" />
                            <xsl:with-param name="id">phone</xsl:with-param>
                            <xsl:with-param name="maxlength" select="80" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Email Address</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@email" />
                            <xsl:with-param name="id">email</xsl:with-param>
                            <xsl:with-param name="maxlength" select="100" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                    </fieldset>

                    <div class="row mt-3">
                        <legend class="col-auto">Optional Demographic Information</legend>
                    </div>
                    <div class="row">
                        <div class="col">
                            <p>We are committed to diverse panelist representation on our program items. To help us do that, please consider filling in the following <em>optional</em> items of demographic information. All answers will be visible to people volunteering on the convention, but not the general membership.</p>
                        </div>
                    </div>
                    <fieldset>
                        <xsl:choose>
                            <xsl:when test="$enableDayJobQuestion = 1">    
                                <xsl:call-template name="regRowContents">
                                    <xsl:with-param name="label">Day job</xsl:with-param>
                                    <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@dayjob" />
                                    <xsl:with-param name="id">day_job</xsl:with-param>
                                    <xsl:with-param name="maxlength" select="100" />
                                    <xsl:with-param name="fieldsize" select="80" />
                                </xsl:call-template>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="day_job" type="hidden">
                                    <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@dayjob" />
                                </input>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:choose>
                            <xsl:when test="$enableAgeRangeQuestion = 1">
                                <div class="row">
                                    <div class="col-sm-3p5 col-md-3 col-lg-2">
                                        <h5>
                                            <label for="age_range" class="badge badge-secondary badge-full-width">
                                                Age range
                                            </label>
                                        </h5>
                                    </div>
                                    <div class="col">
                                        <select id="age_range" name="age_range" class="mycontrol">
                                            <xsl:for-each select="/doc/query[@queryName='agerange']/row">
                                                <option value="{@agerangeid}">
                                                    <xsl:if test="@agerangeid = /doc/query[@queryName='participant_info']/row/@agerangeid">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="@agerangename" />
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </div>
                                </div>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="age_range" type="hidden">
                                    <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@agerangeid" />
                                </input>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:choose>
                            <xsl:when test="$enableEthnicityQuestion = 1">    
                                <xsl:call-template name="regRowContents">
                                    <xsl:with-param name="label">Ethnicity</xsl:with-param>
                                    <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@ethnicity" />
                                    <xsl:with-param name="id">ethnicity</xsl:with-param>
                                    <xsl:with-param name="maxlength" select="100" />
                                    <xsl:with-param name="fieldsize" select="80" />
                                </xsl:call-template>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="ethnicity" type="hidden">
                                    <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@ethnicity" />
                                </input>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:choose>
                            <xsl:when test="$enableGenderQuestion = 1">    
                                <xsl:call-template name="regRowContents">
                                    <xsl:with-param name="label">Gender</xsl:with-param>
                                    <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@gender" />
                                    <xsl:with-param name="id">gender</xsl:with-param>
                                    <xsl:with-param name="maxlength" select="100" />
                                    <xsl:with-param name="fieldsize" select="80" />
                                </xsl:call-template>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="gender" type="hidden">
                                    <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@gender" />
                                </input>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:choose>
                            <xsl:when test="$enableSexualOrientationQuestion = 1">    
                                <xsl:call-template name="regRowContents">
                                    <xsl:with-param name="label">Sexual orientation</xsl:with-param>
                                    <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@sexualorientation" />
                                    <xsl:with-param name="id">sexual_orientation</xsl:with-param>
                                    <xsl:with-param name="maxlength" select="100" />
                                    <xsl:with-param name="fieldsize" select="80" />
                                </xsl:call-template>
                            </xsl:when>
                            <xsl:otherwise>
                                <input name="sexual_orientation" type="hidden">
                                    <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@sexualorientation" />
                                </input>
                            </xsl:otherwise>
                        </xsl:choose>
                    </fieldset>
                    <xsl:choose>
                        <xsl:when test="$enableAccessibilityQuestion = 1">
                            <div class="row mt-3">
                                <legend class="col-auto">Accessibility requirements</legend>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <p>Do you have any accessibility issues that we should be aware of? This information will be visible to people volunteering on the convention, but not the general membership. If you have something you would like to discuss with us, or tell us in more confidence, you can e-mail <a href="mailto:access@eastercon2024.co.uk">access@eastercon2024.co.uk</a>.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <textarea id="accessibility_issues" name="accessibility_issues" class="formcontrol mycontrol col-sm-12" rows="5" cols="72">
                                        <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@accessibilityissues" />
                                    </textarea>
                                </div>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="accessibility_issues" type="hidden">
                                <xsl:attribute name="value" select="/doc/query[@queryName='participant_info']/row/@accessibilityissues" />
                            </input>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
                <div class="card-footer">
                    <xsl:if test="$useRegSystem != 1"><!-- show button here if not using reg system -->
                        <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN"
                            data-loading-text="Updating..." onclick="myProfile.updateBUTN();">
                            Update
                        </button>
                    </xsl:if>
                </div>
            </div>
        </form>
    </xsl:template>
    <xsl:template name="regRowContents">
        <xsl:param name="label" />
        <xsl:param name="value" />
        <xsl:param name="id" />
        <xsl:param name="fieldsize" />
        <xsl:param name="maxlength" />
        <xsl:param name="readonly" select="'0'"/>
        <div class="row">
            <div class="col-sm-3p5 col-md-3 col-lg-2">
                <h5>
                    <xsl:choose>
                        <xsl:when test="$useRegSystem = 1">
                            <div class="badge badge-secondary badge-full-width">
                                <xsl:value-of select="$label" />
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <label for="{$id}" class="badge badge-secondary badge-full-width">
                                <xsl:value-of select="$label" />
                            </label>
                        </xsl:otherwise>
                    </xsl:choose>
                </h5>
            </div>
            <div class="col">
                <xsl:choose>
                    <xsl:when test="$useRegSystem = 1">
                        <xsl:value-of select="$value" />
                    </xsl:when>
                    <xsl:otherwise>
                        <input id="{$id}" name="{$id}" value="{$value}" type="text"
                            size="{$fieldsize}" maxlength="{$maxlength}" class="mycontrol">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                        </input>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
