USE `CMS_HUB`
-- TRUNCATE TABLE USERS;
-- TRUNCATE TABLE WEBSITES;

INSERT INTO USERS (ID,loginU, password, is_active, home_directory, permission, created_at) VALUES (NULL, "lio233", "test12345", 1, "lioWebsites", "8",NOW());

INSERT INTO USERS (ID,loginU, password, is_active, home_directory, permission, created_at) VALUES (NULL, "arnhem571", "test67890", 1, "arnhemWebsites", "4",NOW());

INSERT INTO WEBSITES (ID_website, title_website, shortcut_icon_path, ID_user, is_active, created_at, modified_at) VALUES(NULL, "Page Lio of test account", "icons/favicon.ico", 1, 1, NOW(), NOW());

INSERT INTO WEBSITES (ID_website, title_website, shortcut_icon_path, ID_user, is_active, created_at, modified_at) VALUES(NULL, "Page Lio of test account - personal blog", "icons2/favicon.ico", 1, 1, NOW(), NOW());

INSERT INTO WEBSITES (ID_website, title_website, shortcut_icon_path, ID_user, is_active, created_at, modified_at) VALUES(NULL, "Page Arnhem of test account", "iconsImg/favicon.ico", 2, 1, NOW(), NOW());