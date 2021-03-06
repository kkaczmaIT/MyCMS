-- IMPORT  source d:\xampp\htdocs\BDProject\databaseInit.sql --

DROP DATABASE IF EXISTS `CMS_HUB`;

CREATE DATABASE IF NOT EXISTS `CMS_HUB` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE CMS_HUB;
CREATE TABLE IF NOT EXISTS USERS
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    loginU VARCHAR(100),
    password VARCHAR(128),
    is_active BOOLEAN,
    home_directory VARCHAR(255),
    permission TINYINT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS USERS_INDEX
ON USERS (ID, loginU);

-- SUBTABLE OF USERS--

CREATE TABLE IF NOT EXISTS FILEREGISTER
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    ID_user INTEGER,
    root_path VARCHAR(255),
    filenameF VARCHAR(255),
    type_mime VARCHAR(120),
    size INTEGER,
    path_file VARCHAR(255),
    created_at DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified_at DATETIME
);

CREATE INDEX IF NOT EXISTS FILEREGISTER_INDEX
ON FILEREGISTER (ID, ID_user, path_file);

CREATE TABLE IF NOT EXISTS LOGI
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    ID_user INTEGER,
    ID_website INTEGER,
    content VARCHAR(255),
    created_at DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS WEBSITES
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    title_website VARCHAR(300),
    shortcut_icon_path VARCHAR(255),
    ID_user INTEGER,
    is_active BOOLEAN,
    ID_settings INTEGER,
    created_at DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified_at DATETIME
);

CREATE INDEX IF NOT EXISTS WEBSITES_INDEX
ON WEBSITES (ID, ID_user);

-- SUBTABLES WEBSITES--
CREATE TABLE IF NOT EXISTS SETTINGS
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    PHP_version VARCHAR(255),
    limit_upload_file_size INTEGER,
    contact VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS CONTENTPAGE
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    ID_page INTEGER,
    content VARCHAR(2000),
    role_section VARCHAR(255),
    order_content INTEGER
);

CREATE INDEX IF NOT EXISTS CONTENT_INDEX
ON CONTENTPAGE (ID, ID_page);

CREATE TABLE IF NOT EXISTS PAGESWEB
(
    ID INTEGER PRIMARY KEY AUTO_INCREMENT UNIQUE,
    ID_menu INTEGER,
    ID_theme INTEGER,
    ID_website INTEGER,
    title VARCHAR(80),
    keyphrases VARCHAR(70),
    description_meta VARCHAR(155),
    content VARCHAR(2000),
    footer_text VARCHAR(500)
);

-- PAGESWEB SUBTABLES --
CREATE TABLE IF NOT EXISTS THEME
(
    ID INTEGER PRIMARY KEY UNIQUE AUTO_INCREMENT,
    custom_fields VARCHAR(2000)
);

CREATE TABLE IF NOT EXISTS MODULES
(
    ID INTEGER PRIMARY KEY UNIQUE AUTO_INCREMENT,
    ID_theme INTEGER,
    module_name VARCHAR(255),
    ID_file INTEGER,
    role_module VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS MENU
(
    ID INTEGER PRIMARY KEY UNIQUE AUTO_INCREMENT,
    level_menu INTEGER
);

CREATE TABLE IF NOT EXISTS LISTITEM
(
    ID INTEGER PRIMARY KEY UNIQUE AUTO_INCREMENT,
    ID_menu INTEGER,
    text_link VARCHAR(255),
    href VARCHAR(400),
    depth INTEGER,
    order_item INTEGER
);

-- Foreign keys

ALTER TABLE FILEREGISTER
ADD CONSTRAINT FK_fileregisterusers
FOREIGN KEY (ID_user) REFERENCES USERS(ID);

ALTER TABLE LOGI
ADD CONSTRAINT FK_logiusers
FOREIGN KEY (ID_user) REFERENCES USERS(ID);

ALTER TABLE LOGI
ADD CONSTRAINT FK_logiwebsites
FOREIGN KEY (ID_website) REFERENCES WEBSITES(ID);

ALTER TABLE WEBSITES
ADD CONSTRAINT FK_websitesusers
FOREIGN KEY (ID_user) REFERENCES USERS(ID);

ALTER TABLE WEBSITES
ADD CONSTRAINT FK_websitessettings
FOREIGN KEY (ID_settings) REFERENCES SETTINGS(ID);

ALTER TABLE CONTENTPAGE
ADD CONSTRAINT FK_contentpagesweb
FOREIGN KEY (ID_page) REFERENCES PAGESWEB(ID);

ALTER TABLE PAGESWEB
ADD CONSTRAINT FK_pageswebmenu
FOREIGN KEY (ID_menu) REFERENCES MENU(ID);

ALTER TABLE PAGESWEB
ADD CONSTRAINT FK_pageswebtheme
FOREIGN KEY (ID_theme) REFERENCES THEME(ID);

ALTER TABLE PAGESWEB
ADD CONSTRAINT FK_pageswebwebsites
FOREIGN KEY (ID_website) REFERENCES WEBSITES(ID);

ALTER TABLE MODULES
ADD CONSTRAINT FK_modulesfileregister 
FOREIGN KEY (ID_file) REFERENCES FILEREGISTER(ID);

ALTER TABLE MODULES
ADD CONSTRAINT FK_theme 
FOREIGN KEY (ID_theme) REFERENCES THEME(ID);


ALTER TABLE LISTITEM
ADD CONSTRAINT FK_listItemMenu
FOREIGN KEY (ID_menu) REFERENCES MENU(ID);