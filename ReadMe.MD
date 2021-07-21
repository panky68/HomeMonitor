#WebSite to Monitor/Contol a House

This project was created to Monitor and Control a home remotely. I have created a Website as my user interface to the system. The Hardware was created and based around the Arduino ESP32 MicroController. 

Within the website contains:
Login Page, please email me for username and password
Home Page, currently with just a title, as additional functionality will be added later 
Camera page, to view all the camera's simutaneously
Webpage per room, to view the camera, temperature and humidity readings, and 8 switches, to turn on/off electrical appliances.
Contact Page - to send a message

Currently, I have my Garage and Office monitored and controlled, and this can of course be extented by adding additional hardware and duplicating the code for the webpages for addtional rooms.

Camera
I chose the IPTV camera, Foscam C2, as it had HTTP and HTTPS Protocals. You will need a RTSP account which is free to create. Live feed from the cameras is sent to RTSP.me live server and my website then accesses the feed from the server. You need to Port Forward each camera on your router, in order for RTSP server to access your camera. You will get a secure URL from RTSP.me to feed into the website.

Hardware
I have designed and created the hardware required by this project, and this is shown in a schematic within this repository to view all the required components and connections. At the center of the hardware is an ESP32_Wroom DevKit-1 board. This board has an Arduino ESP32 MicroController, and is the main element of the board. It has Webserver functionality, ie allowing it to POST/GET information to a webpage. It also has Inputs and Outputs (I/O) to allow it to talk to other hardware devices on the board. Additional hardware on the board is also the BME280, which is the Temperature and Humidity Sensor and a 8-Channel Relay Board, used to control external appliances. Code for the Arduino is within this repository.

Website
It is the user interface of this project, and using the POST/GET methods, Data is either passed to/retreived from by the Arduino MicroController by the website. This Data is then exchanged with a MySQL database to keep all current IO states and temperature/Humidity readings. The Website will then use the database as the source of information, keeping it updated every second with the current IO states.
The website was written in PHP, MySQL, Bootstrap, JQuery, HTML and CSS.

Database Requirement
In order for the project to work, you will need to create a database in either MySQL or any other database that you can access via php.
The following tables were created with their respective fields:
Boards: 
id - int - primary key
board - int
last_request - timestamp

garageSensorData: 
id - int (primary key)
sensor - varchar(30)
location - varchar(30)
value1 - varchar(10)
value1 - varchar(10)
value1 - varchar(10)
reading_time - timestamp

officeSensorData: 
id - int (primary key)
sensor - varchar(30)
location - varchar(30)
value1 - varchar(10)
value1 - varchar(10)
value1 - varchar(10)
reading_time - timestamp

Outputs:
id - int (primary key)
name - varchar(64)
board - int(6)
gpio - int(6)
state - int(6)

users:
id - int (primary key)
username - varchar(64)
password - varchar(32)
firstName - varchar(32)
lastName - varchar(32)

This Project was created solely for the purpose of creating a website interacting with hardware. I plan to expand it further in the future with additional functionality.

