----------------------------------Project Documentation--------------------------------------
This document provides an overview of the project files and their respective functionalities.

1. upload_file.php
Handles file uploads from users. It processes the incoming file, checks for validity, and saves it to the server.

2. upload.php
A script for sending user based input to upload_file.php which then sends the data to the server to process.

3. settings.html
Contains the user interface for modifying application settings. This could include user preferences, application configurations, and other customizable options. Now it just stands as a placeholder for future settings and stuff. It already contains settings for the background canvas.

4. register_user.php
Processes user registration requests. It collects user data, validates the input, and stores new user information in the database.

5. register.php
Like upload.php, this script is also meant to take the user based input and send it to register_user.php for the server to process.

6. punish_user.php
A script for administrative actions to punish or restrict users. This could include banning users, limiting access, or other disciplinary actions. It can now only be used to give bans or cooldowns based on the inputs it gets from the URL.

7. main.php
The main entry point of the application. It  includes the primary logic for rendering the main page and dashboard of the application which contains the archive itself for the 3D-Prints.

8. logOut.php
Handles user logout requests. It destroys user sessions and redirects them to the login page or homepage.

9. logln.php
Another script for handling login requests. This time logging the user in instead of logging it out. It's task is to send the user input to database_connection.php to handle.

10. index.html
The homepage HTML file for the application. This is the default page loaded when accessing the application. It contains basic info and explanations about the website. Why it's created, for what reasons etc...

11. fetch-data.php
Fetches data from the database for displaying and processing in both admin panel and the archive itself. It handles AJAX requests to retrieve specific information.

12. download_file.php
Handles file download requests from users. Builds up a path based on the item ID it got thru the URL, and then takes the approppriate file from the server using path. Then it downloads the file with a different name than depicted in the server's database.

13. database_connection.php
Manages the connection to the database when logging in. This script includes the credentials and settings necessary to establish a database connection.

14. config.php
Contains configuration settings for the application. The website uses this to get database name,table,user and password.

15. admin.php
Provides administrative functionality. This script includes tools for managing users and other admin-specific tasks. (I highly suggest adding more functionality in the future).