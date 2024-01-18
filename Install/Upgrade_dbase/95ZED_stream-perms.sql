## This script adds presentation name to sessions
##
##	Created by Andrew January on 2024-01-13
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE `Participants`
ADD `live_stream` tinyint(1) DEFAULT NULL AFTER `use_photo`,
ADD `vod` tinyint(1) DEFAULT NULL AFTER `live_stream`;


INSERT INTO PatchLog (patchname) VALUES ('95ZED_stream-perms.sql');
