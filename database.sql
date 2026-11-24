CREATE DATABASE IF NOT EXISTS student_job_finder;
USE student_job_finder;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    school VARCHAR(150) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    phone_number VARCHAR(20) NULL,
    address VARCHAR(255) NULL,
    course_major VARCHAR(100) NULL,
    year_level VARCHAR(50) NULL,
    skills TEXT NULL,
    bio TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    category ENUM('Web Developer', 'Virtual Assistant', 'IT Support', 'Designer') NOT NULL,
    work_setup VARCHAR(50) NOT NULL,
    salary_range VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    status ENUM('Pending', 'Reviewed', 'Accepted', 'Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (user_id, job_id)
);

-- Default Admin Account (Email: admin@studentjobfinder.com | Password: password)
INSERT IGNORE INTO users (full_name, email, password_hash, school, role) VALUES 
('System Admin', 'admin@studentjobfinder.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Portal', 'admin');

-- Sample Jobs from your Figma Screens
INSERT INTO jobs (title, company_name, location, category, work_setup, salary_range)
SELECT * FROM (
    SELECT 'Junior Web Developer' AS title, 'VA PLATINUM' AS company_name, 'CEBU CITY' AS location, 'Web Developer' AS category, 'Part-time' AS work_setup, 'P15,000 - P20,000' AS salary_range
    UNION ALL SELECT 'Virtual Assistant', 'Ardent Networks Inc.', 'BANILAD CEBU CITY', 'Virtual Assistant', 'Full-time', 'P20,000 - P30,000'
    UNION ALL SELECT 'IT Support Intern', 'Aspired Inc.', 'BASAK PARDO CEBU CITY', 'IT Support', 'Internship', 'P5,000 - P8,000'
    UNION ALL SELECT 'Graphic Designer', 'Creative Hub', 'APAS CEBU CITY', 'Designer', 'Part-time', 'P12,000 - P15,000'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM jobs LIMIT 1);