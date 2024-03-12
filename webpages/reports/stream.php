<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'OK to Stream/VOD -- Sessions';
$report['multi'] = 'true';
$report['output_filename'] = 'oktostreamorvod.csv';
$report['description'] = 'List of all sessions with participants and whether each participant granted permission for the session to be streamed and/or made available on VOD';
$report['categories'] = array(
    'Programming Reports' => 110,
    'Tech Reports' => 110,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        S.sessionid,
        S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        R.roomid,
        R.roomname,
        CASE WHEN NoStream.count IS NULL THEN CASE WHEN UnspecifiedStream.count IS NULL THEN 'Yes' ELSE 'Unknown' END ELSE 'No' END AS canstream,
        CASE WHEN NoVod.count IS NULL THEN CASE WHEN UnspecifiedVod.count IS NULL THEN 'Yes' ELSE 'Unknown' END ELSE 'No' END AS canvod
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        LEFT OUTER JOIN (SELECT sessionid, COUNT(*) count FROM Participants JOIN ParticipantOnSession USING (badgeid) WHERE live_stream = 0 GROUP BY sessionid) NoStream USING (sessionid)
        LEFT OUTER JOIN (SELECT sessionid, COUNT(*) count FROM Participants JOIN ParticipantOnSession USING (badgeid) WHERE live_stream IS NULL GROUP BY sessionid) UnspecifiedStream USING (sessionid)
        LEFT OUTER JOIN (SELECT sessionid, COUNT(*) count FROM Participants JOIN ParticipantOnSession USING (badgeid) WHERE vod = 0 GROUP BY sessionid) NoVod USING (sessionid)
        LEFT OUTER JOIN (SELECT sessionid, COUNT(*) count FROM Participants JOIN ParticipantOnSession USING (badgeid) WHERE vod IS NULL GROUP BY sessionid) UnspecifiedVod USING (sessionid)
    WHERE EXISTS (
        SELECT *
            FROM
                     Schedule SCH2
                JOIN ParticipantOnSession POS2 USING (sessionid)
            WHERE
                 SCH2.sessionid = S.sessionid
        )
    ORDER BY
        SCH.starttime;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid,
        P.badgeid,
        P.pubsname,
        P.live_stream,
        P.vod
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Room Name</th>
                            <th>Start Time</th>
                            <th>Can Stream</th>
                            <th>Can VOD</th>
                            <th>Pubsname</th>
                            <th>Person Id</th>
                            <th>Ok to Stream</th>
                            <th>Ok to VOD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='schedule']/row" />
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='schedule']/row">
        <xsl:variable name="sessionid" select="@sessionid" />
        <xsl:variable name="partCount" select="count(/doc/query[@queryName='participants']/row[@sessionid=$sessionid])" />
        <xsl:variable name="firstSchedRow" select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid][1]" />
        <tr>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell za-report-firstColCell">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:value-of select="@starttime" />
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:value-of select="@canstream" />
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:value-of select="@canvod" />
            </td>
            <xsl:choose>
                <xsl:when test="$partCount = 1">
                    <td class="report za-report-firstRowCell za-report-lastRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                            <xsl:with-param name="pubsname" select = "$firstSchedRow/@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td  class="report za-report-firstRowCell za-report-lastRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "$firstSchedRow/@live_stream" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "$firstSchedRow/@vod" />
                        </xsl:call-template>
                    </td>
                </xsl:when>
                <xsl:otherwise>
                    <td class="report za-report-firstRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                            <xsl:with-param name="pubsname" select = "$firstSchedRow/@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "$firstSchedRow/@live_stream" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "$firstSchedRow/@vod" />
                        </xsl:call-template>
                    </td>
                </xsl:otherwise>
            </xsl:choose>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid][position() > 1]" />
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row" >
        <tr>
            <xsl:choose>
                <xsl:when test="position() = last()">
                    <td class="report za-report-lastRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="pubsname" select = "@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "@live_stream" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "@vod" />
                        </xsl:call-template>
                    </td>
                </xsl:when>
                <xsl:otherwise>
                    <td>
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="pubsname" select = "@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td>
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "@live_stream" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="format_boolean">
                            <xsl:with-param name="boolean_value" select = "@vod" />
                        </xsl:call-template>
                    </td>
                </xsl:otherwise>
            </xsl:choose>
        </tr>
    </xsl:template>

    <xsl:template name="format_boolean">
        <xsl:param name="boolean_value" />
        <xsl:choose>
            <xsl:when test="$boolean_value='0'">
                <xsl:text>No</xsl:text>
            </xsl:when>
            <xsl:when test="$boolean_value='1'">
                <xsl:text>Yes</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text>Unknown</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
