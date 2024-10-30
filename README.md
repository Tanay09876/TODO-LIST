# To-Do List Project
This project is a web-based to-do list application that allows users to register, log in, and manage their tasks with features like profile management, password recovery, and session handling. The application is built with PHP and MySQL and uses front-end technologies for responsiveness and styling.

## Features
- User registration and login with form validation
- User-specific task display and management
- Profile management and password change options
- Forgot Password feature with OTP-based reset
- Session handling and secure password hashing with bcrypt

## Technology Stack
- **Languages**: PHP, JavaScript, SQL, HTML, CSS
- **Frontend**: Bootstrap 5, JavaScript, CSS for responsive design
- **Backend**: PHP, MySQL
- **Security**: bcrypt for password hashing
- **Database**: MySQL (using MySQLi)

## Software Requirements
- **XAMPP** (or any LAMP/WAMP stack): For running the Apache server and MySQL database
- **Gmail Account**: For enabling the Forgot Password functionality using Gmail App Password
- **PHP Extensions**: Make sure the `openssl` and `mysqli` extensions are enabled in your PHP configuration.

## Project Setup

1. **Clone the Repository**
   - Download or clone the project files to your local server directory, such as `F:\xampp\htdocs\todo-list`.

2. **Database Setup**
   - Open phpMyAdmin (typically at `http://localhost/phpmyadmin`) and create a new database:
     sql
     CREATE DATABASE TODO_LIST_P;
   
   - Import the provided SQL file in the `database` folder to create the necessary tables (`users` and `tasks`).

3. **Configuration**
   - Update the database credentials in `db.php`:
   php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "TODO_LIST_P";
     

5. **Gmail App Password Setup**
   For the Forgot Password feature, you need to set up an App Password in Gmail to allow the application to send emails through Gmail’s SMTP server.

   ### Steps:
   1. **Enable Two-Factor Authentication (2FA)**: 
      - Go to [Google Account Security](https://myaccount.google.com/security) and enable 2FA if it isn’t already enabled.

   2. **Create an App Password**:
      - After enabling 2FA, go to the **App Passwords** section under "Signing in to Google."
      - Select **Mail** as the app and **Other** for the device name, then generate a password.
      - Copy the generated password; this will be used in the application's email configuration.

   3. **Update Email Configuration**:
      - Update your application to use the App Password for Gmail SMTP by setting it in the email-sending function.

## Usage

- Open the project in your browser at `http://localhost/todo-list/public/index.php`.
- Register or log in to access the task management interface.
- Manage tasks, update your profile, and reset your password via OTP if needed.

## Additional Notes
- **Session Security**: The application uses PHP sessions to manage user login states securely.
- **Password Security**: Passwords are hashed with bcrypt, adding security to stored credentials.


## Video Tutorial
For a visual guide, refer to this YouTube tutorial: (Create an App Password for Google Account).
