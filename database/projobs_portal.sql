-- ProJobs Portal - Expanded Schema
-- Keeps all original tables and data; adds marketplace features safely

CREATE DATABASE IF NOT EXISTS `projobs_portal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `projobs_portal`;

-- ===================== USERS (expanded roles: admin | seeker) =====================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(40) NOT NULL DEFAULT 'admin',
  phone VARCHAR(40) DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  skills TEXT DEFAULT NULL,
  education TEXT DEFAULT NULL,
  experience TEXT DEFAULT NULL,
  cv_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@projobs.local', '$2y$10$6mtEmBw8ljzMeZNmN.dwtuYdtyysSb6Ugd.OqYMjuPlyOgpuGRiYi', 'admin')
ON DUPLICATE KEY UPDATE password=VALUES(password), role=VALUES(role);

INSERT INTO users (name, email, password, role, phone, location, skills, education, experience) VALUES
('Abebe Kebede', 'abebe@example.com', '$2y$10$/LJq.kT1EziMtIR.l5fUBO5wVqePnvxyBPEyOx9hMNQN.7/dw84.a', 'seeker', '+251911223344', 'Addis Ababa', 'PHP, MySQL, JavaScript, Bootstrap, Laravel', 'BSc Computer Science - AAU', '3 years Full Stack Developer')
ON DUPLICATE KEY UPDATE password=VALUES(password), role=VALUES(role);











-- Demo admin (password: Admin123!)  -- note: using a known bcrypt for "password" placeholder; real hash set in seed below
-- We'll set proper hashes via PHP later if needed. For demo we use password_hash compatible.







-- ===================== COMPANIES =====================
CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  email VARCHAR(160) DEFAULT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  website VARCHAR(180) DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL,
  status VARCHAR(30) DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== CATEGORIES =====================
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  icon VARCHAR(50) DEFAULT 'briefcase',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== JOBS (expanded columns) =====================
CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT DEFAULT NULL,
  category_id INT DEFAULT NULL,
  title VARCHAR(180) NOT NULL,
  location VARCHAR(120) DEFAULT NULL,
  salary VARCHAR(100) DEFAULT NULL,
  salary_min INT DEFAULT NULL,
  salary_max INT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  short_description VARCHAR(500) DEFAULT NULL,
  responsibilities TEXT DEFAULT NULL,
  requirements TEXT DEFAULT NULL,
  skills TEXT DEFAULT NULL,
  benefits TEXT DEFAULT NULL,
  job_type VARCHAR(50) DEFAULT 'Full Time',
  work_mode VARCHAR(50) DEFAULT 'On-site',
  image VARCHAR(255) DEFAULT NULL,
  featured TINYINT(1) DEFAULT 0,
  status VARCHAR(30) DEFAULT 'open',
  deadline DATE DEFAULT NULL,
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_featured (featured),
  INDEX idx_category (category_id),
  INDEX idx_company (company_id)
);

-- ===================== APPLICATIONS (expanded) =====================
CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  user_id INT DEFAULT NULL,
  candidate_name VARCHAR(150) NOT NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  cover_letter TEXT DEFAULT NULL,
  resume VARCHAR(255) DEFAULT NULL,
  message TEXT DEFAULT NULL,
  status VARCHAR(30) DEFAULT 'Pending',
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_application (job_id, email),
  INDEX idx_job (job_id),
  INDEX idx_user (user_id),
  INDEX idx_status (status)
);

-- ===================== SAVED JOBS =====================
CREATE TABLE IF NOT EXISTS saved_jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  job_id INT NOT NULL,
  saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_save (user_id, job_id),
  INDEX idx_user (user_id)
);

-- ===================== NOTIFICATIONS =====================
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT DEFAULT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (user_id)
);

-- ===================== SAMPLE DATA =====================
INSERT INTO companies (name, email, phone, website, location, description, status) VALUES
('ABC Technology', 'hr@abctech.et', '+251911000111', 'https://abctech.et', 'Addis Ababa', 'Leading software development company in Ethiopia.', 'active'),
('Ethio Finance PLC', 'careers@ethiofinance.com', '+251911000222', 'https://ethiofinance.com', 'Addis Ababa', 'Premier financial services provider.', 'active'),
('Green Agro Solutions', 'jobs@greenagro.et', '+251911000333', NULL, 'Bahir Dar', 'Innovative agricultural technology company.', 'active'),
('Nile Digital Agency', 'hello@niledigital.com', '+251911000444', 'https://niledigital.com', 'Addis Ababa', 'Creative digital marketing and design agency.', 'active'),
('Horizon Logistics', 'hr@horizonlogistics.et', '+251911000555', NULL, 'Dire Dawa', 'Nationwide logistics and supply chain solutions.', 'active');

INSERT INTO categories (name, icon) VALUES
('Information Technology', 'code-slash'),
('Finance & Accounting', 'cash-coin'),
('Marketing & Sales', 'megaphone'),
('Engineering', 'gear'),
('Healthcare', 'heart-pulse'),
('Education', 'book'),
('Design & Creative', 'palette'),
('Customer Service', 'headset'),
('Human Resources', 'people'),
('Logistics & Supply Chain', 'truck');

INSERT INTO jobs (company_id, category_id, title, location, salary, salary_min, salary_max, short_description, description, responsibilities, requirements, skills, benefits, job_type, work_mode, featured, status, deadline) VALUES
(1, 1, 'Full Stack Web Developer', 'Addis Ababa', 'ETB 25,000 - 40,000', 25000, 40000,
 'Build modern web applications using PHP, MySQL, JavaScript and Bootstrap.',
 'We are looking for a talented Full Stack Web Developer to join our growing team. You will work on client projects and internal products using modern web technologies.',
 '• Develop and maintain web applications\n• Write clean, maintainable code\n• Collaborate with designers and product managers\n• Participate in code reviews',
 '• 2+ years of experience in web development\n• Strong knowledge of PHP and MySQL\n• Experience with JavaScript and Bootstrap\n• Bachelor degree in Computer Science or related field',
 'PHP, MySQL, JavaScript, Bootstrap, HTML, CSS, Git',
 '• Competitive salary\n• Health insurance\n• Flexible working hours\n• Professional development budget',
 'Full Time', 'Hybrid', 1, 'open', DATE_ADD(CURDATE(), INTERVAL 30 DAY)),

(1, 1, 'Frontend Developer (React)', 'Addis Ababa', 'ETB 30,000 - 45,000', 30000, 45000,
 'Create beautiful and responsive user interfaces with React.js.',
 'Join our frontend team to build engaging user experiences for our clients across Ethiopia and beyond.',
 '• Build reusable React components\n• Optimize applications for performance\n• Work closely with UX designers\n• Ensure cross-browser compatibility',
 '• 2+ years React experience\n• Strong JavaScript/TypeScript skills\n• Familiarity with REST APIs\n• Portfolio of previous work',
 'React, JavaScript, TypeScript, CSS, HTML, Redux',
 '• Competitive salary\n• Remote work options\n• Learning stipend',
 'Full Time', 'Remote', 1, 'open', DATE_ADD(CURDATE(), INTERVAL 21 DAY)),

(2, 2, 'Senior Accountant', 'Addis Ababa', 'ETB 20,000 - 35,000', 20000, 35000,
 'Manage financial records and prepare reports for a leading finance company.',
 'Ethio Finance is seeking an experienced Senior Accountant to oversee daily accounting operations and ensure compliance.',
 '• Prepare monthly and annual financial statements\n• Manage accounts payable and receivable\n• Coordinate external audits\n• Mentor junior accountants',
 '• ACCA or CPA preferred\n• 4+ years accounting experience\n• Strong Excel skills\n• Knowledge of Ethiopian tax regulations',
 'Accounting, Excel, QuickBooks, Taxation, Financial Reporting',
 '• Competitive package\n• Pension contribution\n• Medical coverage',
 'Full Time', 'On-site', 0, 'open', DATE_ADD(CURDATE(), INTERVAL 14 DAY)),

(3, 4, 'Agricultural Engineer', 'Bahir Dar', 'ETB 18,000 - 28,000', 18000, 28000,
 'Design and implement modern farming solutions for Ethiopian agriculture.',
 'Green Agro Solutions needs an Agricultural Engineer to support our field projects and technology deployments.',
 '• Design irrigation and farming systems\n• Support farmers with technical advice\n• Collect and analyze field data\n• Prepare project reports',
 '• Degree in Agricultural Engineering\n• 2+ years field experience\n• Willingness to travel\n• Knowledge of modern farming techniques',
 'Agricultural Engineering, Irrigation, Data Analysis, Project Management',
 '• Housing allowance\n• Field allowances\n• Career growth opportunities',
 'Full Time', 'On-site', 0, 'open', DATE_ADD(CURDATE(), INTERVAL 25 DAY)),

(4, 7, 'UI/UX Designer', 'Addis Ababa', 'ETB 22,000 - 38,000', 22000, 38000,
 'Design delightful digital experiences for websites and mobile apps.',
 'Nile Digital Agency is looking for a creative UI/UX Designer to craft beautiful and usable interfaces.',
 '• Create wireframes and high-fidelity designs\n• Conduct user research\n• Collaborate with developers\n• Maintain design systems',
 '• Strong portfolio required\n• Proficiency in Figma or Adobe XD\n• Understanding of user-centered design\n• 2+ years experience',
 'Figma, Adobe XD, UI Design, UX Research, Prototyping',
 '• Creative environment\n• Flexible hours\n• Latest design tools',
 'Full Time', 'Hybrid', 1, 'open', DATE_ADD(CURDATE(), INTERVAL 20 DAY)),

(5, 10, 'Logistics Coordinator', 'Dire Dawa', 'ETB 15,000 - 25,000', 15000, 25000,
 'Coordinate shipments and optimize supply chain operations.',
 'Horizon Logistics needs a proactive Logistics Coordinator to manage daily operations and client deliveries.',
 '• Plan and track shipments\n• Coordinate with drivers and warehouses\n• Resolve delivery issues\n• Maintain accurate records',
 '• Diploma or degree in Logistics/Supply Chain\n• 1+ years experience\n• Strong communication skills\n• Ability to work under pressure',
 'Logistics, Supply Chain, Communication, MS Office',
 '• Transport allowance\n• Performance bonuses\n• Training opportunities',
 'Full Time', 'On-site', 0, 'open', DATE_ADD(CURDATE(), INTERVAL 18 DAY)),

(2, 3, 'Digital Marketing Specialist', 'Addis Ababa', 'ETB 18,000 - 30,000', 18000, 30000,
 'Grow brand presence through digital channels and campaigns.',
 'Drive customer acquisition and engagement through social media, SEO and paid campaigns.',
 '• Manage social media accounts\n• Run Google and Meta ads\n• Analyze campaign performance\n• Create content calendars',
 '• 2+ years digital marketing experience\n• Google Ads & Meta Ads certification preferred\n• Strong analytical skills',
 'Digital Marketing, SEO, Google Ads, Social Media, Analytics',
 '• Performance incentives\n• Remote flexibility\n• Continuous learning',
 'Full Time', 'Hybrid', 0, 'open', DATE_ADD(CURDATE(), INTERVAL 28 DAY)),

(1, 1, 'Junior PHP Developer', 'Addis Ababa', 'ETB 12,000 - 20,000', 12000, 20000,
 'Entry-level opportunity to grow your PHP development skills.',
 'Perfect for recent graduates who want to start a career in web development with mentorship.',
 '• Assist senior developers\n• Fix bugs and implement small features\n• Write documentation\n• Learn best practices',
 '• Basic PHP and MySQL knowledge\n• Willingness to learn\n• Good problem-solving skills\n• Recent graduate preferred',
 'PHP, MySQL, HTML, CSS, JavaScript',
 '• Mentorship program\n• Career growth path\n• Friendly team',
 'Full Time', 'On-site', 0, 'open', DATE_ADD(CURDATE(), INTERVAL 35 DAY));
