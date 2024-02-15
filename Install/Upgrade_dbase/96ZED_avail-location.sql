## This script adds presentation name to sessions
##
##	Created by Andrew January on 2024-01-13
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE `ParticipantAvailabilityTimes`
ADD `location` ENUM('onsite', 'virtual') NOT NULL DEFAULT 'onsite' AFTER `endtime`;

INSERT INTO PatchLog (patchname) VALUES ('96ZED_avail-location.sql');
