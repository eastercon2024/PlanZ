## This script adds presentation name to sessions
##
##	Created by Andrew January on 2024-01-13
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Sessions
ADD presentationname VARCHAR(512) AFTER persppartinfo;


INSERT INTO PatchLog (patchname) VALUES ('94ZED_presentation-name.sql');
