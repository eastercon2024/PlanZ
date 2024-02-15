## This script adds location to assignments
##
##	Created by Andrew January on 2024-01-13
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE `ParticipantOnSession`
ADD `location` ENUM('unknown', 'onsite', 'virtual') NOT NULL DEFAULT 'unknown' AFTER `moderator`;

INSERT INTO PatchLog (patchname) VALUES ('97ZED_assigned-location.sql');
