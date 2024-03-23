## Add published versions
##
##	Created by Andrew January on 2024-01-13
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
CREATE TABLE `PublishedSchedule`
(
    `pub_sched_id` VARCHAR(32) NOT NULL,
    `program_json` LONGTEXT NOT NULL,
    `people_json` LONGTEXT NOT NULL,
    PRIMARY KEY (`pub_sched_id`)
);

CREATE TABLE `PublishHistory`
(
    `publish_id` INT NOT NULL AUTO_INCREMENT,
    `pub_sched_id` VARCHAR(32) NOT NULL,
    `status` VARCHAR(255) NOT NULL,
    `published_time` DATETIME NOT NULL,
    `published_user` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`publish_id`),
    FOREIGN KEY (`pub_sched_id`) REFERENCES `PublishedSchedule`(`pub_sched_id`)
);