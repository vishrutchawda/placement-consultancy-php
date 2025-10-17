# Placement Consultancy PHP System [![PHP](https://img.shields.io/badge/PHP-7%2B-blue)](https://www.php.net/) [![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A robust, full-stack web application designed for placement consultancies to facilitate candidate-recruiter interactions, including secure authentication, profile management, and job offer processing. Developed using procedural PHP with MySQLi for direct database operations, this project exemplifies lightweight backend development suitable for academic and small-scale deployments, prioritizing simplicity in query execution and session-based state management.[file:2]

## Table of Contents
- [About the Project](#about-the-project)
- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Security Considerations](#security-considerations)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## About the Project
This system addresses the need for an efficient platform in educational institutions or consultancies to manage job placements by enabling candidates to update profiles (including CV uploads) and respond to offers, while recruiters can search and extend opportunities. Built as a procedural PHP application, it avoids heavy frameworks to demonstrate core web scripting skills, with MySQL handling relational data for users, offers, and profiles. The motivation stems from real-world placement workflows, solving issues like manual tracking by automating CRUD operations and role-based access control.[file:2]

Key motivations include:
- Streamlining user registration and authentication without external libraries.
- Providing role-specific interfaces (candidate, recruiter, admin) via conditional PHP logic.
- Ensuring data integrity through basic validation and hashing for passwords.

## Features
- **Authentication Module**: Supports user registration, login, logout, and password reset with hashed storage (using `password_hash()`) and session verification via `requireLogin()`.
- **Role-Based Access**: Differentiated dashboards for candidates (offer viewing/acceptance), recruiters (candidate filtering by marks/qualification, offer sending), and admins (user oversight and deletion).
- **Profile Management**: Candidates edit details (name, email, marks, qualification) and upload PDF CVs, stored in a file system with size/type validation.
- **Offer System**: Recruiters send pending offers with estimated salary; candidates accept/reject, updating status in the database with timestamps.
- **Admin Controls**: View/delete users and associated offers, with confirmation prompts for data integrity.[file:2]
- **Responsive UI**: Bootstrap-integrated forms and tables for mobile-friendly dashboards.

## Architecture
The application follows a monolithic procedural structure:
- **Backend Logic**: Centralized in PHP scripts (e.g., `config.php` for DB connection, `login.php` for auth checks using MySQLi prepared statements where applicable).
- **Database Schema**: Relational MySQL design with tables like `candidates` (id, name, email, marks, qualification, cv), `recruiters` (id, name, email, company), `offers` (id, candidate_id, recruiter_id, status, estimated_salary), and `admin` for oversight.
- **Frontend Integration**: Inline PHP with HTML/Bootstrap for server-side rendering; JavaScript minimal for toast notifications via Bootstrap.
- **File Flow**: Requests route through index-like entry points (e.g., `dashboard.php`), with includes for shared functions like sanitization (`sanitize()` helper).[file:2]

Data flow: User input → Validation/Sanitization → MySQLi Query → Session Update → Render View.

## Tech Stack
- **Backend**: PHP 7+ (procedural, with MySQLi extension for non-prepared queries; functions like `mysqli_real_escape_string()` for input sanitization).
- **Database**: MySQL 5.7+ (InnoDB engine for foreign key relations in offers table).
- **Frontend**: HTML5, CSS3 (Bootstrap 5.3 for grid/layout), JavaScript (vanilla for form interactions), Font Awesome 6.4 for icons.
- **Server**: Apache 2.4+ (mod_rewrite optional for clean URLs; file uploads via `$_FILES` with `move_uploaded_file()`).
- **Dependencies**: None external (self-contained; no Composer required).[file:2]

## Prerequisites
- PHP 7.0 or higher with MySQLi extension enabled.
- MySQL 5.7 or higher server.
- Apache web server (e.g., via XAMPP/WAMP for local development).
- Basic knowledge of SQL for schema setup.
- Text editor (VS Code recommended) for file management.[file:2]

## Installation
1. **Clone or Download the Repository**:
git clone https://github.com/vishrutchawda/placement-consultancy-php.git
cd placement-consultancy-php

Or download ZIP from GitHub and extract.

2. **Database Setup**:
- Create a MySQL database: `CREATE DATABASE placement_db;`.
- Import the schema (create `schema.sql` if needed):
  ```
  CREATE TABLE candidates (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(100) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      marks FLOAT DEFAULT 0,
      qualification VARCHAR(100),
      cv LONGBLOB
  );
  CREATE TABLE recruiters (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(100) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      company VARCHAR(100)
  );
  CREATE TABLE offers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      candidate_id INT,
      recruiter_id INT,
      status ENUM('PENDING', 'ACCEPTED', 'REJECTED') DEFAULT 'PENDING',
      estimated_salary DECIMAL(10,2),
      timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
      FOREIGN KEY (recruiter_id) REFERENCES recruiters(id) ON DELETE CASCADE
  );
  CREATE TABLE admin (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(100) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL
  );
  ```
- Update `config.php` with your DB credentials:
  ```
  $servername = "localhost";
  $username = "root";  // Your MySQL username
  $password = "";      // Your MySQL password
  $dbname = "placement_db";
  $con = mysqli_connect($servername, $username, $password, $dbname);
  if (!$con) {
      die("Connection failed: " . mysqli_connect_error());
  }
  mysqli_set_charset($con, "utf8mb4");
  ```

3. **Project Configuration**:
- Create an `uploads/` directory in the root: `mkdir uploads && chmod 755 uploads` (for CV storage).
- Ensure `assets/` folder exists for static files (CSS/JS from CDN in code).

4. **Local Server**:
- Place the project in XAMPP's `htdocs/` folder.
- Start Apache and MySQL via XAMPP Control Panel.
- Access at `http://localhost/placement-consultancy-php/login.php`.[file:2]

## Usage
- **Candidate**: Register/login → Edit profile (`candidateprofile.php`) → View/respond to offers in dashboard (`candidatedashboard.php`).
- **Recruiter**: Login → Filter candidates by marks/qualification (`recruiterdashboard.php`) → Send offers via modal form.
- **Admin**: Login with admin credentials → Manage users/offers (`admindashboard.php`).
- Example Offer Update: POST to `updateoffer.php` with `?id=1&status=ACCEPTED` to change status atomically.
All operations use session variables (e.g., `$_SESSION['userrole']`) for authorization checks.[file:2]

## Security Considerations
- Passwords are hashed with `password_hash(PASSWORD_DEFAULT)` and verified via `password_verify()`.
- Input sanitization via custom `sanitize()` function using `mysqli_real_escape_string()`.
- File uploads restricted to PDF (`accept=".pdf"`) with error checks (`UPLOAD_ERR_OK`).
- Session fixation prevented by regenerating IDs on login; CSRF not implemented (recommend adding tokens for production).
- SQL injection mitigated partially via escaping; upgrade to prepared statements for full protection.[file:2]
Vulnerabilities: Direct queries in loops; consider PDO migration.

## Roadmap
- Integrate prepared statements with PDO for enhanced SQL security.
- Add email notifications (PHPMailer) for offer status changes.
- Implement pagination for large candidate lists in recruiter dashboard.
- Frontend enhancements: AJAX for real-time offer updates without page reloads.
- Testing: Unit tests with PHPUnit for auth and query functions.
- Deployment: Dockerize for containerized MySQL/Apache setup.[file:2]

## Contributing
Contributions are welcome! Fork the repo, create a feature branch (`git checkout -b feature/AmazingFeature`), commit changes (`git commit -m 'Add some AmazingFeature'`), and submit a pull request. Ensure code follows procedural style, adds comments for complex queries, and passes basic validation. Review guidelines: No breaking changes to core auth; test on PHP 8+.[file:2]

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/YourFeature`)
3. Commit your Changes (`git commit -m 'Add some YourFeature'`)
4. Push to the Branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

## License
Distributed under the MIT License. See `LICENSE` for more information.[file:2]

## Contact
Vishrut Chawda - Computer Science Student @ A.V Parekh Technical Institue Rajkot  
Email: vishrutchawda@gmail.com   
LinkedIn: www.linkedin.com/in/gp-avpti-comp-vishrut-chawda-s236020307230  
Project Link: [https://github.com/vishrutchawda/placement-consultancy-php](https://github.com/vishrutchawda/placement-consultancy-php)[file:2]
