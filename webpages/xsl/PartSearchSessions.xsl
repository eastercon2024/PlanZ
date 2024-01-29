<?xml version="1.0" encoding="UTF-8" ?>
<!--
	PartSearchSessions.xsl
	Created by Peter Olszowka on 2020-08-22.
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml"/>
    <xsl:param name="may_I" />
    <xsl:param name="conName" />
    <xsl:param name="trackIsPrimary" />
    <xsl:param name="showTags" />
    <xsl:param name="showTrack" />
    <xsl:param name="collapse_list" />
    <xsl:param name="title" />
    <xsl:param name="tagMatch" />
    <xsl:param name="showingAll" />
    <xsl:variable name="interested" select="/doc/query[@queryName='interested']/row/@interested = '1'"/>
    <xsl:variable name="mayISubmitPanelInterests" select="$interested and $may_I" />

    <xsl:template match="/">
        <form class="container mt-2 mb-4 px-0" method="GET" action="PartSearchSessions.php">    
            <div class="card">
                <div class="card-header">
                    <h2>Search Sessions</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <label for="title-txtinp">Title Search</label>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-auto">
                            <input id="title-txtinp" name="title" size="35" placeholder="Session title" class="form-control">
                                <xsl:attribute name="value"><xsl:value-of select="$title" /></xsl:attribute>
                            </input>
                        </div>
                    </div>
                    <xsl:choose>
                        <xsl:when test="$showTags">
                            <div class="row">
                                <div class="col-auto">
                                    <label>Tags</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto">
                                    <div class="tag-chk-container">
                                        <xsl:apply-templates select="doc/query[@queryName='tags']/row" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto align-self-center">
                                    <label class="tag-match-label">
                                        <input type="radio" id="tagmatch1" name="tagmatch" class="tag-match-radio" value="any">
                                            <xsl:if test="$tagMatch = 'any'">
                                                <xsl:attribute name="checked"></xsl:attribute>
                                            </xsl:if>
                                        </input>
                                        Match any selected
                                    </label>
                                    <label class="tag-match-label">
                                        <input type="radio" id="tagmatch2" name="tagmatch" class="tag-match-radio" value="all">
                                            <xsl:if test="$tagMatch = 'all'">
                                                <xsl:attribute name="checked"></xsl:attribute>
                                            </xsl:if>
                                        </input>
                                        Match all selected
                                    </label>
                                </div>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <input type="hidden" name="tags[]" value="0" />
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit" value="search">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="sessionInterestFRM" name="resform" class="container mt-2 mb-4 px-0">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-auto my-auto">
                            <span>
                                <xsl:choose>
                                    <xsl:when test="$showingAll = '1'">
                                        Showing all sessions
                                    </xsl:when>
                                    <xsl:when test="count(doc/query[@queryName='sessions']/row) = 1">
                                        <xsl:value-of select="count(doc/query[@queryName='sessions']/row)" /> matching session (<a href="/PartSearchSessions.php">Show all sessions</a>)
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="count(doc/query[@queryName='sessions']/row)" /> matching sessions (<a href="/PartSearchSessions.php">Show all sessions</a>)
                                    </xsl:otherwise>
                                </xsl:choose>
                            </span>
                        </div>
                        <div class="col text-right">
                            <a class="btn btn-info" data-toggle="collapse" href=".multi-collapse" role="button" aria-expanded="false" aria-controls="{$collapse_list}">Expand All</a>
                        </div>
                    </div>
                    <xsl:if test="not($interested)">
                        <div class="row mt-2">
                            <div class="alert alert-warning">
                                <h4>Warning!</h4>
                                <span>
                                    You have not indicated in <a href="/my_contact.php">your profile</a> that you will be attending <xsl:value-of select="$conName"/>.
                                    You will not be able to save your panel choices until you do so.
                                </span>
                            </div>
                        </div>
                    </xsl:if>
                </div>
                <div class="card-body">
                    <xsl:choose>
                        <xsl:when test="doc/query[@queryName='sessions']/row">
                            <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="row">
                                <div class="col alert alert-warning">
                                    No sessions available for participant sign up matched your search.
                                </div>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
            </div>
        </form>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tracks']/row">
        <option value="{@trackid}"><xsl:value-of select="@trackname" /></option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tags']/row">
        <div class="tag-chk-label-wrapper">
            <label class="tag-chk-label">
                <input type="checkbox" name="tags[]" class="tag-chk" value="{@tagid}">
                    <xsl:if test="@selected = '1'">
                        <xsl:attribute name="checked"></xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@tagname" />
            </label>
        </div>
        
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <div class="card p-2 mb-2">
            <div class="row">
                <div class="col-auto"><h5><xsl:value-of select="@title" /></h5></div>
            </div>
            <div class="row mb-1">
                <div class="col-auto">
                    <xsl:value-of select="@typename" />
                    <xsl:text disable-output-escaping="yes"> &amp;bull; </xsl:text>
                    <xsl:value-of select="@duration" />
                    <xsl:if test="not(normalize-space(@taglist) = '')">
                        <xsl:text disable-output-escaping="yes"> &amp;bull; </xsl:text>
                        <xsl:value-of select="@taglist" />
                    </xsl:if>
                </div>
            </div>
            <xsl:if test="$interested">
                <div class="row mb-1">
                    <div class="col-auto">
                        <label class="mb-0">
                            <input type="checkbox" id="int{@sessoinid}" name="int{@sessionid}" class="interestsCHK"
                                value="{@sessionid}">
                                <xsl:if test="@badgeid">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                                <xsl:if test="not($mayISubmitPanelInterests)">
                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                </xsl:if>
                            </input>
                            I am interested
                        </label>
                    </div>
                </div>
            </xsl:if>
            <div class="row">
                <div class="col-auto">
                    <a id="toggle-{@sessionid}" href="#collapse-{@sessionid}" data-toggle="collapse" class="collapsed" aria-expanded="true" aria-controls="#collapse-{@sessionid}">Show details</a>
                </div>
            </div>
            <div id="collapse-{@sessionid}" class="collapse multi-collapse">
                <xsl:if test="not(normalize-space(@progguiddesc) = '')">
                    <div class="row mt-2">
                        <div class="col-auto"><xsl:value-of select="@progguiddesc" /></div>
                    </div>
                </xsl:if>
                <xsl:if test="not(normalize-space(@persppartinfo) = '')">
                    <div class="row mt-2">
                        <div class="col-auto"><b>Prospective participant info</b></div>
                    </div>
                    <div class="row">
                        <div class="col-auto"><xsl:value-of select="@persppartinfo" /></div>
                    </div>
                </xsl:if>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>
