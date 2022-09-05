# MyCMS - Content Management System

Project to management content site. Project allows on create multi website with multi subpages. Each page is able to has different menu. In general project is CRUD application based on REST API. 
## Technologies in project
- Redis - extension to management Redis in php [phpredis](https://github.com/phpredis/phpredis)
- PHP
- MariaDB
- JavaScript
- Bootstrap

### Implementation
Connection with MariaDB provide PDO extension. MariaDB is responsible for store data and Redis for usage by system. Data are synchronised between Redis and MariaDB. Layout of site was created with Bootstrap's. JavaScript is responsible for communication with server and handle forms. Application logic is implemented by MVC architecture. Core of application is managed by URL address.

### Configuration
To configure application necessary is saved .env file own settings.

![.env file](file.env.png)

File include information about MariaDB connection. Mode is setting to change option to test mode and normal mode. Test mode show additional information and allow error debugging. In file is possible to change app name and URL address and location to store users files.

## Usage

### Registration form

![registration site](registration.png)

### Login form
![login site](login.png)

### Websites dashboard
![Websites Dashboard](websitedashboard.png)

### Creation website
![Creation website form](addingwebsite.png)

### Website setting
![Website settings](websitesettings.png)

Website's settings allow on edit data, disable website, adding pages, change limit size file and contact data.

### Adding pages
![Adding page form](singlepage.png)

### Menu single page
![Adding menu](menupage.png)

### Main settings
![Main settings](settingsaccount.png)

### Users search
![Users search](users.png)
