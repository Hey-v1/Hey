-- Database Schema for Certification Platform

-- Use the database
USE zubi;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (username),
    INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'instructor', 'data_entry') NOT NULL DEFAULT 'data_entry',
    profile_image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (username),
    INDEX (email),
    INDEX (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    is_free TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_published TINYINT(1) DEFAULT 0,
    admin_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (slug),
    INDEX (is_free),
    INDEX (is_featured),
    INDEX (is_published),
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exams table
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    course_id INT NOT NULL,
    duration_minutes INT NOT NULL DEFAULT 60,
    passing_score INT NOT NULL DEFAULT 70,
    is_free TINYINT(1) DEFAULT 0,
    is_published TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (slug),
    INDEX (is_free),
    INDEX (is_published),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('single_choice', 'multiple_choice', 'drag_drop') NOT NULL,
    options JSON NOT NULL,
    correct_answer JSON NOT NULL,
    explanation TEXT DEFAULT NULL,
    points INT NOT NULL DEFAULT 1,
    order_number INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (exam_id),
    INDEX (question_type),
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    completion_date DATETIME DEFAULT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    progress INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, course_id),
    INDEX (user_id),
    INDEX (course_id),
    INDEX (is_completed),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exam attempts table
CREATE TABLE IF NOT EXISTS exam_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    started_at DATETIME NOT NULL,
    completed_at DATETIME DEFAULT NULL,
    score INT DEFAULT NULL,
    is_passed TINYINT(1) DEFAULT 0,
    answers JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (exam_id),
    INDEX (is_passed),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    rating INT NOT NULL,
    review TEXT DEFAULT NULL,
    is_approved TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, course_id),
    INDEX (user_id),
    INDEX (course_id),
    INDEX (rating),
    INDEX (is_approved),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Certificates table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_number VARCHAR(50) NOT NULL UNIQUE,
    issue_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (course_id),
    INDEX (certificate_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (course_id),
    INDEX (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User activity log table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    admin_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (admin_id),
    INDEX (action),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User tokens table for remember me and password reset
CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires DATETIME NOT NULL,
    type ENUM('remember_me', 'password_reset') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (selector),
    INDEX (expires),
    INDEX (type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT IGNORE INTO admins (username, email, password, name, role)
VALUES ('admin', 'admin@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Admin User', 'admin');
-- Password: 123123123

-- Insert default instructor
INSERT IGNORE INTO admins (username, email, password, name, role)
VALUES ('instructor', 'instructor@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Instructor User', 'instructor');
-- Password: 123123123

-- Insert default data entry user
INSERT IGNORE INTO admins (username, email, password, name, role)
VALUES ('dataentry', 'dataentry@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Data Entry User', 'data_entry');
-- Password: 123123123

-- Insert sample users
INSERT IGNORE INTO users (username, email, password, first_name, last_name)
VALUES 
('johndoe', 'john@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'John', 'Doe'),
('janedoe', 'jane@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Jane', 'Doe'),
('bobsmith', 'bob@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Bob', 'Smith'),
('alicesmith', 'alice@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Alice', 'Smith'),
('mohammedali', 'mohammed@example.com', '$2y$12$ff72bOqR6xQ9P7rcSx.J2eI12yo4EKGV7f4hNVKCSE3jGv5fJ/lOC', 'Mohammed', 'Ali');
-- Password: 123123123

-- Insert sample courses
INSERT IGNORE INTO courses (title, slug, description, price, is_free, is_featured, is_published, admin_id)
VALUES 
('CCNA Certification', 'ccna-certification', 'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.', 99.99, 0, 1, 1, 1),
('CCNP Enterprise', 'ccnp-enterprise', 'Advanced course for Cisco Certified Network Professional Enterprise certification. Master complex enterprise networking concepts.', 199.99, 0, 1, 1, 1),
('Security+ Certification', 'security-plus-certification', 'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.', 79.99, 0, 1, 1, 2),
('Network+ Basics', 'network-plus-basics', 'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.', 0.00, 1, 0, 1, 2),
('Linux Essentials', 'linux-essentials', 'Learn Linux fundamentals and prepare for the Linux Essentials certification.', 49.99, 0, 0, 1, 2);

-- Insert sample exams
INSERT IGNORE INTO exams (title, slug, description, course_id, duration_minutes, passing_score, is_free, is_published)
VALUES 
('CCNA Practice Exam 1', 'ccna-practice-exam-1', 'Practice exam covering CCNA fundamentals and network basics.', 1, 90, 70, 0, 1),
('CCNA Practice Exam 2', 'ccna-practice-exam-2', 'Advanced practice exam covering CCNA routing and switching concepts.', 1, 90, 70, 0, 1),
('CCNP Enterprise Core Exam', 'ccnp-enterprise-core-exam', 'Practice exam for CCNP Enterprise Core (350-401 ENCOR).', 2, 120, 75, 0, 1),
('Security+ Practice Test', 'security-plus-practice-test', 'Comprehensive practice test for CompTIA Security+ certification.', 3, 90, 75, 0, 1),
('Network+ Sample Questions', 'network-plus-sample-questions', 'Free sample questions for Network+ certification.', 4, 30, 70, 1, 1),
('Linux Essentials Quiz', 'linux-essentials-quiz', 'Quick quiz to test your Linux knowledge.', 5, 45, 70, 0, 1);

-- Insert sample questions (single choice)
INSERT IGNORE INTO questions (exam_id, question_text, question_type, options, correct_answer, explanation, points, order_number)
VALUES 
(1, 'Which of the following is the valid host range for the subnet 192.168.1.128/25?', 'single_choice', 
'["192.168.1.129 - 192.168.1.254", "192.168.1.1 - 192.168.1.126", "192.168.1.129 - 192.168.1.255", "192.168.1.128 - 192.168.1.255"]', 
'"192.168.1.129 - 192.168.1.254"', 
'For a /25 subnet with network address 192.168.1.128, the valid host range is from 192.168.1.129 to 192.168.1.254. The address 192.168.1.255 is the broadcast address.', 
1, 1),

(1, 'Which protocol operates at the Transport layer of the OSI model?', 'single_choice', 
'["HTTP", "IP", "Ethernet", "TCP"]', 
'"TCP"', 
'TCP (Transmission Control Protocol) operates at the Transport layer (Layer 4) of the OSI model.', 
1, 2),

(1, 'What is the purpose of ARP?', 'single_choice', 
'["To resolve domain names to IP addresses", "To resolve IP addresses to MAC addresses", "To assign IP addresses dynamically", "To encrypt network traffic"]', 
'"To resolve IP addresses to MAC addresses"', 
'ARP (Address Resolution Protocol) is used to map IP network addresses to MAC addresses in a local network.', 
1, 3),

(2, 'Which command would you use to verify the Layer 2 to Layer 3 mapping on a Cisco router?', 'single_choice', 
'["show ip route", "show interfaces", "show arp", "show ip interface brief"]', 
'"show arp"', 
'The "show arp" command displays the ARP table, which shows the mapping between IP addresses (Layer 3) and MAC addresses (Layer 2).', 
1, 1),

(3, 'Which technology allows for the creation of multiple virtual routers within a single physical router?', 'single_choice', 
'["VTP", "VRF", "HSRP", "EIGRP"]', 
'"VRF"', 
'VRF (Virtual Routing and Forwarding) allows for multiple routing tables to exist in a router at the same time, effectively creating multiple virtual routers.', 
2, 1);

-- Insert sample questions (multiple choice)
INSERT IGNORE INTO questions (exam_id, question_text, question_type, options, correct_answer, explanation, points, order_number)
VALUES 
(4, 'Which of the following are symmetric encryption algorithms? (Select all that apply)', 'multiple_choice', 
'["AES", "RSA", "DES", "ECC", "3DES"]', 
'["AES", "DES", "3DES"]', 
'AES, DES, and 3DES are symmetric encryption algorithms. RSA and ECC are asymmetric (public key) encryption algorithms.', 
2, 1),

(4, 'Which of the following are common security controls? (Select all that apply)', 'multiple_choice', 
'["Firewall", "Antivirus", "Social engineering", "Access control", "Phishing"]', 
'["Firewall", "Antivirus", "Access control"]', 
'Firewalls, antivirus software, and access control are security controls. Social engineering and phishing are attack methods, not controls.', 
2, 2),

(5, 'Which of the following are valid IPv4 addresses? (Select all that apply)', 'multiple_choice', 
'["192.168.1.256", "10.0.0.1", "172.16.0.1", "256.0.0.1", "224.0.0.1"]', 
'["10.0.0.1", "172.16.0.1", "224.0.0.1"]', 
'Valid IPv4 addresses must have each octet between 0 and 255. 192.168.1.256 and 256.0.0.1 are invalid because they contain octets greater than 255.', 
1, 1);

-- Insert sample questions (drag and drop)
INSERT IGNORE INTO questions (exam_id, question_text, question_type, options, correct_answer, explanation, points, order_number)
VALUES 
(6, 'Match the Linux commands with their descriptions.', 'drag_drop', 
'{"ls": "List directory contents", "cd": "Change directory", "pwd": "Print working directory", "mkdir": "Create a new directory", "rm": "Remove files or directories"}', 
'{"ls": "List directory contents", "cd": "Change directory", "pwd": "Print working directory", "mkdir": "Create a new directory", "rm": "Remove files or directories"}', 
'These are basic Linux commands and their functions.', 
3, 1),

(3, 'Match the OSI layers with the correct protocols.', 'drag_drop', 
'{"Application": "HTTP, FTP, SMTP", "Presentation": "SSL, TLS", "Session": "NetBIOS, RPC", "Transport": "TCP, UDP", "Network": "IP, ICMP", "Data Link": "Ethernet, PPP", "Physical": "Cables, Hubs"}', 
'{"Application": "HTTP, FTP, SMTP", "Presentation": "SSL, TLS", "Session": "NetBIOS, RPC", "Transport": "TCP, UDP", "Network": "IP, ICMP", "Data Link": "Ethernet, PPP", "Physical": "Cables, Hubs"}', 
'This question tests knowledge of which protocols operate at each layer of the OSI model.', 
3, 2);

-- Insert sample enrollments
INSERT INTO enrollments (user_id, course_id, is_completed, progress)
VALUES 
(1, 1, 0, 30),
(1, 3, 0, 20),
(2, 2, 0, 15),
(2, 4, 1, 100),
(3, 4, 1, 100),
(3, 5, 0, 50),
(4, 1, 0, 75),
(5, 3, 0, 40);

-- Insert sample exam attempts
INSERT INTO exam_attempts (user_id, exam_id, started_at, completed_at, score, is_passed, answers)
VALUES 
(1, 1, '2023-01-15 10:00:00', '2023-01-15 11:15:00', 85, 1, '{"1": "192.168.1.129 - 192.168.1.254", "2": "TCP", "3": "To resolve IP addresses to MAC addresses"}'),
(1, 2, '2023-01-20 14:00:00', '2023-01-20 15:30:00', 75, 1, '{"1": "show arp"}'),
(2, 5, '2023-01-10 09:30:00', '2023-01-10 10:00:00', 90, 1, '{"1": ["10.0.0.1", "172.16.0.1", "224.0.0.1"]}'),
(3, 5, '2023-01-12 16:00:00', '2023-01-12 16:25:00', 80, 1, '{"1": ["10.0.0.1", "172.16.0.1"]}'),
(3, 6, '2023-01-25 11:00:00', '2023-01-25 11:40:00', 65, 0, '{"1": {"ls": "Change directory", "cd": "List directory contents", "pwd": "Print working directory", "mkdir": "Create a new directory", "rm": "Remove files or directories"}}'),
(4, 1, '2023-01-18 13:00:00', '2023-01-18 14:15:00', 90, 1, '{"1": "192.168.1.129 - 192.168.1.254", "2": "TCP", "3": "To resolve IP addresses to MAC addresses"}');

-- Insert sample reviews
INSERT INTO reviews (user_id, course_id, rating, review)
VALUES 
(1, 1, 5, 'Excellent course! The content is well-structured and easy to follow. I passed my CCNA exam on the first attempt thanks to this course.'),
(2, 4, 4, 'Good introduction to networking concepts. Would recommend for beginners.'),
(3, 4, 5, 'Perfect for beginners. Clear explanations and good examples.'),
(3, 5, 3, 'Content is good but could use more practical examples.'),
(4, 1, 4, 'Very comprehensive course. The practice exams were particularly helpful.');

-- Insert sample certificates
INSERT INTO certificates (user_id, course_id, certificate_number, issue_date)
VALUES 
(2, 4, 'CERT-NET-2023-001', '2023-01-15'),
(3, 4, 'CERT-NET-2023-002', '2023-01-20');

-- Insert sample payments
INSERT INTO payments (user_id, course_id, amount, payment_method, transaction_id, status)
VALUES 
(1, 1, 99.99, 'credit_card', 'TXN123456', 'completed'),
(1, 3, 79.99, 'paypal', 'PP987654', 'completed'),
(2, 2, 199.99, 'credit_card', 'TXN789012', 'completed'),
(3, 5, 49.99, 'credit_card', 'TXN345678', 'completed'),
(4, 1, 99.99, 'paypal', 'PP456789', 'completed');