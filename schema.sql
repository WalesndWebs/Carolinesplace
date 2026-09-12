-- ============================================================
-- Caroline's Place Database Schema
-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB / Hostinger MySQL
-- ============================================================

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  icon VARCHAR(50) DEFAULT '✨',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  duration_minutes INT DEFAULT 60,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  option_label VARCHAR(255) NOT NULL,
  price_ngn DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_service (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  display_name VARCHAR(255),
  email VARCHAR(255),
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  division VARCHAR(50) DEFAULT 'spa',
  service_id INT DEFAULT NULL,
  preferred_date DATE NOT NULL,
  preferred_time VARCHAR(20) NOT NULL,
  total_amount_ngn DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  notes TEXT,
  status VARCHAR(20) DEFAULT 'pending',
  payment_status VARCHAR(20) DEFAULT 'unpaid',
  admin_notes TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_ref (reference_code),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  service_id INT DEFAULT NULL,
  option_id INT DEFAULT NULL,
  service_name VARCHAR(255) NOT NULL,
  option_label VARCHAR(255) DEFAULT 'Standard',
  unit_price_ngn DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  quantity INT NOT NULL DEFAULT 1,
  line_total_ngn DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Categories
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (1, 'Spa Section', 'Signature facials and skin-care sanctuary', '🧖‍♀️', 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Spa Section';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (2, 'Massage', 'Therapeutic, relaxation, specialty massages', '💆', 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Massage';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (3, 'Waxing', 'Full-body hair removal with wax options', '✨', 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Waxing';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (4, 'Body Treatment', 'Scrubs, wraps, steam baths, hammam, hair masks', '🧴', 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Body Treatment';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (5, 'Hair Section', 'Braids, cornrows, wigs, relaxers, styling, perms', '💇', 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hair Section';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (6, 'Nails Price List', 'Manicures, gel, acrylic, BIAB, art, chrome, gel X', '💅', 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nails Price List';
INSERT INTO categories (id, name, description, icon, sort_order, is_active, created_at) VALUES (7, 'Pedicure Section', 'Pedicures, manicure combos & kids variants', '🦶', 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Pedicure Section';

-- Seed Services
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (1, 1, 'Anti-Aging Facial', 'Anti-Aging Facial', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Anti-Aging Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (2, 1, 'Microdermabrasion Facial', 'Microdermabrasion Facial', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Microdermabrasion Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (3, 1, 'Men Facial', 'Men Facial', 45, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Men Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (4, 1, 'Dermaplaning Facial', 'Dermaplaning Facial', 45, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Dermaplaning Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (5, 1, 'Vitamin C Brightening Facial', 'Vitamin C Brightening Facial', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Vitamin C Brightening Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (6, 1, 'Deep Cleansing Facial', 'Deep Cleansing Facial', 60, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep Cleansing Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (7, 1, 'Acne Treatment Facial', 'Acne Treatment Facial', 60, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acne Treatment Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (8, 2, 'Swedish (Relaxation) Massage', 'Swedish (Relaxation) Massage', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Swedish (Relaxation) Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (9, 2, 'Deep Tissue Massage', 'Deep Tissue Massage', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep Tissue Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (10, 2, 'Hot Stone Massage', 'Hot Stone Massage', 75, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hot Stone Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (11, 2, 'Couple Swedish Massage', 'Couple Swedish Massage', 60, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Couple Swedish Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (12, 2, 'Couple Deep Tissue Massage', 'Couple Deep Tissue Massage', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Couple Deep Tissue Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (13, 2, 'Back Massage', 'Back Massage', 45, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Back Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (14, 2, 'Reflexology Massage', 'Reflexology Massage', 45, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Reflexology Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (15, 2, 'Aromatherapy Massage / Steam', 'Aromatherapy Massage / Steam', 60, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Aromatherapy Massage / Steam';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (16, 2, 'Prenatal Massage', 'Prenatal Massage', 60, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Prenatal Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (17, 2, 'Head Massage', 'Head Massage', 30, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Head Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (18, 2, 'Peace Balance', 'Peace Balance', 60, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Peace Balance';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (19, 3, 'Chin Wax', 'Chin Wax', 15, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Chin Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (20, 3, 'Upper Lip Wax', 'Upper Lip Wax', 15, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Upper Lip Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (21, 3, 'Underarm Waxing', 'Underarm Waxing', 15, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Underarm Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (22, 3, 'Full Hand Waxing', 'Full Hand Waxing', 30, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Hand Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (23, 3, 'Half Hand Waxing', 'Half Hand Waxing', 20, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Half Hand Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (24, 3, 'Full Leg Waxing', 'Full Leg Waxing', 45, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Leg Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (25, 3, 'Half Leg Waxing', 'Half Leg Waxing', 30, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Half Leg Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (26, 3, 'Bikini Waxing', 'Bikini Waxing', 30, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Bikini Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (27, 3, 'Brazilian Waxing', 'Brazilian Waxing', 45, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Brazilian Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (28, 3, 'Eyebrow Waxing', 'Eyebrow Waxing', 15, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Eyebrow Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (29, 3, 'Full Body Waxing', 'Full Body Waxing', 90, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Body Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (30, 3, 'Hollywood Waxing', 'Hollywood Waxing', 45, 12, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hollywood Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (39, 4, 'Coffee Scrub / Body Wrap', 'Coffee Scrub / Body Wrap', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Coffee Scrub / Body Wrap';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (40, 4, 'Hammam (Moroccan Bath)', 'Hammam (Moroccan Bath)', 75, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hammam (Moroccan Bath)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (41, 4, 'Steam Bath', 'Steam Bath', 60, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Steam Bath';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (94, 6, 'Gel X', 'Gel X', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel X';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (95, 6, 'French Tips', 'French Tips', 45, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='French Tips';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (96, 6, 'Chrome', 'Chrome', 45, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Chrome';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (97, 6, 'Ombre', 'Ombre', 60, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ombre';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (98, 6, 'Coloured Powder', 'Coloured Powder', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Coloured Powder';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (99, 6, 'Ombre Refill', 'Ombre Refill', 45, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ombre Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (100, 6, 'Acrylic Nail Refill', 'Acrylic Nail Refill', 45, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic Nail Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (101, 6, 'Acrylic Wrap', 'Acrylic Wrap', 60, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic Wrap';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (102, 6, 'Stick On', 'Stick On', 45, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stick On';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (103, 6, 'Refill Gel', 'Refill Gel', 60, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Refill Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (104, 6, 'Acrylic', 'Acrylic', 60, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (105, 6, 'BIAB', 'BIAB', 60, 12, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='BIAB';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (106, 6, 'Hard Gel', 'Hard Gel', 60, 13, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hard Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (107, 6, 'Cateye Polish', 'Cateye Polish', 30, 14, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Cateye Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (108, 6, 'Press On Nails', 'Press On Nails', 30, 15, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Press On Nails';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (109, 6, 'Gel Polish', 'Gel Polish', 30, 16, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (110, 6, 'Regular Polish', 'Regular Polish', 30, 17, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Regular Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (111, 6, 'Deep In (Nails)', 'Deep In (Nails)', 60, 18, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep In (Nails)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (112, 6, 'Tidy Up of Nails', 'Tidy Up of Nails', 30, 19, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Tidy Up of Nails';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (113, 6, 'Gel Topcoat', 'Gel Topcoat', 20, 20, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Topcoat';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (114, 6, 'BIAB Refill', 'BIAB Refill', 45, 21, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='BIAB Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (115, 6, 'Nail Art (Per Finger)', 'Nail Art (Per Finger)', 10, 22, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nail Art (Per Finger)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (116, 6, '3D Nail Art Design', '3D Nail Art Design', 15, 23, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='3D Nail Art Design';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (117, 6, 'Stone Nail Art', 'Stone Nail Art', 20, 24, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stone Nail Art';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (118, 6, 'Nail Replacement (Per Finger)', 'Nail Replacement (Per Finger)', 15, 25, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nail Replacement (Per Finger)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (119, 6, 'Big Toes Fixing', 'Big Toes Fixing', 20, 26, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Big Toes Fixing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (120, 6, 'Gel Dissolve', 'Gel Dissolve', 20, 27, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Dissolve';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (121, 6, 'Sculpting Gel', 'Sculpting Gel', 30, 28, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sculpting Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (122, 6, 'Acrylic / BIAB / Stick-On Dissolve', 'Acrylic / BIAB / Stick-On Dissolve', 20, 29, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic / BIAB / Stick-On Dissolve';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (123, 7, 'Regular Pedicure', 'Regular Pedicure', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Regular Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (124, 7, 'Dry Pedicure', 'Dry Pedicure', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Dry Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (125, 7, 'Spa Pedicure', 'Spa Pedicure', 75, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Spa Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (126, 7, 'Manicure', 'Manicure', 45, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Manicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (127, 7, 'Pedicure with Cavia', 'Pedicure with Cavia', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Pedicure with Cavia';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (128, 7, 'Luxury Pedicure', 'Luxury Pedicure', 90, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Luxury Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (129, 7, 'Kids Pedicure', 'Kids Pedicure', 30, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Kids Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (130, 7, 'Kids Manicure', 'Kids Manicure', 20, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Kids Manicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (131, 5, 'Washing of Hair (Permed)', 'Professional wash and gentle cleansing for permed hair.', 60, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing of Hair (Permed)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (132, 5, 'Washing of Hair (Natural)', 'Deep clarifying and hydrating wash for natural hair.', 60, 2, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing of Hair (Natural)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (133, 5, 'Washing of Kid\'s Hair', 'Gentle tear-free wash and conditioning for kids.', 60, 3, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing of Kid\'s Hair';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (134, 5, 'Detangling', 'Gentle, knot-free hair detangling treatment.', 60, 4, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Detangling';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (135, 5, 'Detangling with Washing', 'Deep cleansing wash combined with thorough knot detangling.', 60, 5, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Detangling with Washing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (136, 5, 'Regular Steaming', 'Hydrating moisture steam treatment for healthy hair revival.', 60, 6, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Regular Steaming';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (137, 5, 'Steaming with Client\'s Products', 'Steam treatment application using your preferred hair products.', 60, 7, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Steaming with Client\'s Products';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (138, 5, 'Protein Steaming', 'Intensive protein-infused steam treatment for hair strengthening.', 60, 8, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Protein Steaming';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (139, 5, 'Placenta Treatment', 'Nourishing placenta extract conditioning treatment.', 60, 9, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Placenta Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (140, 5, 'Hot Oil Treatment', 'Therapeutic warm botanical oil treatment for scalp and cuticle nourishment.', 60, 10, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Hot Oil Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (141, 5, 'Wash & Silk Press/Setting', 'Clarifying shampoo, deep condition, blow-dry and glass-shine silk press.', 60, 11, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Wash & Silk Press/Setting';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (142, 5, 'Wash & Setting', 'Shampoo wash, roller setting, and salon drying for bouncy volume.', 60, 12, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Wash & Setting';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (143, 5, 'Retouching & Set/Silk Press', 'Root relaxer retouch complete with styling setting or silk press.', 60, 13, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Retouching & Set/Silk Press';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (144, 5, 'Retouching', 'Precise relaxer retouch for new hair growth.', 60, 14, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Retouching';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (145, 5, 'Salon Products (Dyeing)', 'Full hair coloring with salon premium dyes and care.', 60, 15, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Salon Products (Dyeing)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (146, 5, 'Client Products (Dyeing)', 'Professional hair coloring application using client-provided dye.', 60, 16, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Client Products (Dyeing)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (147, 5, 'Cornrows / Didi - Big (6 Pieces)', 'Clean, defined big cornrows / didi styling (6 pieces).', 60, 17, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows / Didi - Big (6 Pieces)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (148, 5, 'Cornrows / Didi - Medium (10 Pieces)', 'Neat medium-width cornrows / didi braiding (10 pieces).', 60, 18, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows / Didi - Medium (10 Pieces)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (149, 5, 'Cornrows / Didi - Small (14 Pieces)', 'Intricate small cornrows / didi braiding (14 pieces).', 60, 19, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows / Didi - Small (14 Pieces)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (150, 5, 'Cornrows / Didi - Tiny (18 Pieces)', 'Delicate tiny cornrows / didi braiding (18 pieces).', 60, 20, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows / Didi - Tiny (18 Pieces)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (151, 5, 'Cornrows / Didi - Extra Tiny (20-22 Pieces)', 'Ultra-fine precision cornrows / didi styling (20-22 pieces).', 60, 21, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows / Didi - Extra Tiny (20-22 Pieces)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (152, 5, 'Natural Twist - Medium', 'Medium two-strand twists on natural hair.', 60, 22, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Natural Twist - Medium';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (153, 5, 'Natural Twist - Small', 'Small defined two-strand twists on natural hair.', 60, 23, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Natural Twist - Small';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (154, 5, 'Natural Twist - Tiny', 'Fine micro two-strand twists with lasting hold.', 60, 24, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Natural Twist - Tiny';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (155, 5, 'Blow Drying of Natural Hair', 'Gentle heat-protectant blow drying and stretch for natural hair.', 60, 25, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Blow Drying of Natural Hair';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (156, 5, 'Revamping (Short)', 'Restorative wash, treatment and restyling for short wigs/weaves.', 60, 26, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Revamping (Short)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (157, 5, 'Revamping (Long)', 'Complete wash, deep treatment and restyling for long wigs/weaves.', 60, 27, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Revamping (Long)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (158, 5, 'School Hair for Kids', 'Neat, comfortable school-friendly hairstyles for children.', 60, 28, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='School Hair for Kids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (159, 5, 'Teens (School Hair)', 'Trendy, smart school-approved hairstyles for teenagers.', 60, 29, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Teens (School Hair)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (160, 5, 'Shade Adu', 'Classic Shade Adu signature braided updo.', 60, 30, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Shade Adu';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (161, 5, 'Kid\'s Hair - Braids/Crochet', 'Protective braids or crochet styling for kids.', 60, 31, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kid\'s Hair - Braids/Crochet';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (162, 5, 'Kid\'s Hair - Cornrow', 'Neat and pain-free cornrow styling for children.', 60, 32, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kid\'s Hair - Cornrow';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (163, 5, 'Kid\'s Hair - With Extension', 'Kids braided hairstyle with lightweight extensions.', 60, 33, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kid\'s Hair - With Extension';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (164, 5, 'Adult Crochet with Weaving', 'Cornrow base with flawless crochet hair installation.', 60, 34, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Adult Crochet with Weaving';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (165, 5, 'Braid Crochet', 'Full crochet install using pre-braided extensions.', 60, 35, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Braid Crochet';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (166, 5, 'Big Braid - Bra Length', 'Chunky box braids extending to bra-strap length.', 60, 36, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Big Braid - Bra Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (167, 5, 'Big Braid - Waist Length', 'Chunky box braids extending to waist length.', 60, 37, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Big Braid - Waist Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (168, 5, 'Big Braid - Bum Length', 'Chunky box braids extending to bum length.', 60, 38, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Big Braid - Bum Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (169, 5, 'Small Braids - Bra Length', 'Neat small braids extending to bra-strap length.', 60, 39, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Small Braids - Bra Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (170, 5, 'Small Braids - Waist Length', 'Neat small braids extending to waist length.', 60, 40, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Small Braids - Waist Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (171, 5, 'Small Braids - Bum Length', 'Neat small braids extending to bum length.', 60, 41, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Small Braids - Bum Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (172, 5, 'Tiny / Knotless Braids - Shoulder Length', 'Painless, seamless knotless braids at shoulder length.', 60, 42, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Tiny / Knotless Braids - Shoulder Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (173, 5, 'Tiny / Knotless Braids - Bum Length', 'Painless, seamless knotless braids extending to bum length.', 60, 43, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Tiny / Knotless Braids - Bum Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (174, 5, 'Tiny / Knotless Braids - Waist Length', 'Seamless lightweight knotless braids extending to waist length.', 60, 44, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Tiny / Knotless Braids - Waist Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (175, 5, 'Tiny / Knotless Braids - Full Length', 'Ultimate long luxury knotless braids extending full length.', 60, 45, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Tiny / Knotless Braids - Full Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (176, 5, 'Mega Growth Treatment', 'Strengthening anti-breakage Mega Growth therapy.', 60, 46, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Mega Growth Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (177, 5, 'Touch Up Braids', 'Perimeter and hairline braid re-braiding for a fresh look.', 60, 47, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Touch Up Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (178, 5, 'Touch Up Wigs', 'Quick styling, edge neatening and lace touch up for wigs.', 60, 48, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Touch Up Wigs';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (179, 5, 'Ghana Weaving - Large', 'Classic feed-in Ghana weaving with bold parts.', 60, 49, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ghana Weaving - Large';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (180, 5, 'Ghana Weaving - Medium', 'Neat medium feed-in Ghana weaving.', 60, 50, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ghana Weaving - Medium';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (181, 5, 'Ghana Weaving - Small / Stitch Braids', 'Intricate small stitch feed-in Ghana weaving.', 60, 51, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ghana Weaving - Small / Stitch Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (182, 5, 'Ghana Weaving - Tiny', 'Ultra-fine intricate Ghana weaving with razor-sharp parts.', 60, 52, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ghana Weaving - Tiny';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (183, 5, 'Ghana Weaving - Extra Tiny', 'Micro-precision Ghana weaving with high durability.', 60, 53, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ghana Weaving - Extra Tiny';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (184, 5, 'Loosing of Braids - Big', 'Careful, breakage-free takedown of big braids.', 60, 54, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Loosing of Braids - Big';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (185, 5, 'Loosing of Braids - Medium', 'Careful, breakage-free takedown of medium braids.', 60, 55, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Loosing of Braids - Medium';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (186, 5, 'Loosing of Braids - Small', 'Gentle, damage-free takedown of small braids.', 60, 56, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Loosing of Braids - Small';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (187, 5, 'Loosing of Braids - Tiny', 'Pain-free removal of micro / tiny braids.', 60, 57, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Loosing of Braids - Tiny';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (188, 5, 'Cornrows (Other)', 'Under-wig flat cornrows or custom cornrow styling.', 60, 58, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Cornrows (Other)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (189, 5, 'Kinky Braids - Short', 'Textured kinky twist braids at short length.', 60, 59, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kinky Braids - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (190, 5, 'Kinky Braids - Mid Length', 'Textured kinky twist braids at mid-back length.', 60, 60, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kinky Braids - Mid Length';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (191, 5, 'Kinky Braids - Long', 'Textured kinky twist braids at waist length.', 60, 61, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Kinky Braids - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (192, 5, 'Closure Wigs Construction + Styling - Short', 'Custom machine/hand wig-making with closure and styling (short).', 60, 62, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Closure Wigs Construction + Styling - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (193, 5, 'Closure Wigs Construction + Styling - Long', 'Custom wig-making with closure and styling (long).', 60, 63, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Closure Wigs Construction + Styling - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (194, 5, 'Frontal Wigs Construction + Styling - Short', 'Custom frontal wig-making with plucked hairline & styling (short).', 60, 64, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Frontal Wigs Construction + Styling - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (195, 5, 'Frontal Wigs Construction + Styling - Long', 'Custom frontal wig-making with plucked hairline & styling (long).', 60, 65, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Frontal Wigs Construction + Styling - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (196, 5, 'Frontal Installation', 'Flawless frontal lace melting and secure installation.', 60, 66, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Frontal Installation';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (197, 5, 'Sew In - With Leave Out', 'Traditional sew-in weave with natural hair leave-out.', 60, 67, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sew In - With Leave Out';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (198, 5, 'Sew In - Closure', 'Full sew-in weave with lace closure.', 60, 68, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sew In - Closure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (199, 5, 'Sew In - Frontal', 'Full sew-in weave with lace frontal.', 60, 69, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sew In - Frontal';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (200, 5, 'Ponytail', 'Sleek high or low ponytail styling with extensions.', 60, 70, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Ponytail';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (201, 5, 'Half Up / Half Down', 'Glamorous half-up half-down sew-in or quick-weave styling.', 60, 71, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Half Up / Half Down';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (202, 5, 'Washing & Setting of Wigs with Rollers - Short', 'Deep wash, roller setting and drying for short wigs.', 60, 72, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Setting of Wigs with Rollers - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (203, 5, 'Washing & Setting of Wigs with Rollers - Long', 'Deep wash, roller setting and drying for long wigs.', 60, 73, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Setting of Wigs with Rollers - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (204, 5, 'Washing & Straightening of Wigs - Short', 'Shampoo wash, conditioning and bone-straight press (short).', 60, 74, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Straightening of Wigs - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (205, 5, 'Washing & Straightening of Wigs - Medium', 'Shampoo wash, conditioning and bone-straight press (medium).', 60, 75, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Straightening of Wigs - Medium';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (206, 5, 'Washing & Straightening of Wigs - Long', 'Shampoo wash, conditioning and bone-straight press (long).', 60, 76, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Straightening of Wigs - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (207, 5, 'Washing & Curling - Short', 'Wash, condition, and wand/barrel curls for short wigs.', 60, 77, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Curling - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (208, 5, 'Washing & Curling - Long', 'Wash, condition, and wand/barrel curls for long wigs.', 60, 78, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Washing & Curling - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (209, 5, 'Straightening Alone - Short', 'Precision flat iron straightening for short hair/wigs.', 60, 79, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Straightening Alone - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (210, 5, 'Straightening Alone - Long', 'Precision flat iron straightening for long hair/wigs.', 60, 80, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Straightening Alone - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (211, 5, 'Curling Alone - Short', 'Hot tool curling and styling for short hair/wigs.', 60, 81, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Curling Alone - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (212, 5, 'Curling Alone - Long', 'Hot tool curling and styling for long hair/wigs.', 60, 82, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Curling Alone - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (213, 5, 'Wig Band Replacement', 'Elastic band replacement on wig for snug fit.', 60, 83, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Wig Band Replacement';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (214, 5, 'Band Replacement (Adjustable Pin)', 'Adjustable pin band installation for secure grip.', 60, 84, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Band Replacement (Adjustable Pin)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (215, 5, 'Closure/Frontal Stitching', 'Repair and reinforcement stitching for closures/frontals.', 60, 85, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Closure/Frontal Stitching';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (216, 5, 'Dyeing of Wig / Weave - Short', 'Professional coloring and toning for short wigs/bundles.', 60, 86, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Dyeing of Wig / Weave - Short';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (217, 5, 'Dyeing of Wig / Weave - Long', 'Professional coloring and toning for long wigs/bundles.', 60, 87, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Dyeing of Wig / Weave - Long';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (218, 5, 'Highlights', 'Custom streak highlights, ombre or balayage for hair/wigs.', 60, 88, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Highlights';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (219, 5, 'Sharper Powder - Client\'s Product', 'Hair lightening application using client-provided powder.', 60, 89, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sharper Powder - Client\'s Product';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (220, 5, 'Sharper Powder - Salon Products', 'Hair lightening and bleaching with salon premium powder.', 60, 90, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sharper Powder - Salon Products';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (221, 5, 'Sharper Powder (Medium Size)', 'Medium application lightening treatment.', 60, 91, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sharper Powder (Medium Size)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (222, 5, 'Sharper Powder (Small Size)', 'Small application precision lightening treatment.', 60, 92, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Sharper Powder (Small Size)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (223, 5, 'Mega Growth Relaxer', 'Standard Mega Growth relaxer application.', 60, 93, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Mega Growth Relaxer';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (224, 5, 'Mega Growth Relaxer 2', 'Premium Mega Growth relaxer deep application.', 60, 94, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Mega Growth Relaxer 2';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (225, 5, 'Loosing of Goodness Braid', 'Gentle, careful takedown of Goodness braids.', 60, 95, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Loosing of Goodness Braid';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (226, 5, 'Olive Oil Relaxer', 'Standard organic Olive Oil relaxer application.', 60, 96, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Olive Oil Relaxer';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (227, 5, 'Olive Oil Relaxer 2', 'Premium organic Olive Oil relaxer deep application.', 60, 97, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Olive Oil Relaxer 2';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (228, 5, 'Bob Marley', 'Signature Bob Marley twist braids styling.', 60, 98, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Bob Marley';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (229, 5, 'Stitch Braids (Medium)', 'Defined medium stitch cornrows with precision lines.', 60, 99, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Stitch Braids (Medium)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (230, 5, 'Stitch Braids (Small)', 'Intricate small stitch cornrows with precision lines.', 60, 100, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE name='Stitch Braids (Small)';

-- Seed Options
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (1, 1, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (2, 2, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (3, 3, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (4, 4, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (5, 5, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (6, 6, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (7, 7, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (8, 8, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (9, 9, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (10, 10, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (11, 11, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (12, 12, 'Standard', 53750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=53750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (13, 13, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (14, 14, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (15, 15, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (16, 16, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (17, 17, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (18, 18, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (19, 19, 'Standard', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (20, 20, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (21, 21, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (22, 22, 'Standard', 15050, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=15050;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (23, 23, 'Standard', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (24, 24, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (25, 25, 'Standard', 9675, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=9675;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (26, 26, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (27, 27, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (28, 28, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (29, 29, 'Standard', 53750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=53750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (30, 30, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (39, 39, 'Standard', 48375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=48375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (40, 40, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (41, 41, '15 mins', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (42, 41, '30 mins', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (43, 41, '60 mins', 26875, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (131, 94, 'Long', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (132, 94, 'Short', 18275, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=18275;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (133, 94, 'Refill', 12900, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (134, 95, 'Standard (Hands)', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (135, 95, 'Leg', 5375, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (136, 96, 'Full', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (137, 96, 'Alone', 10750, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (138, 97, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (139, 98, 'New Set', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (140, 98, 'Refill', 12900, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (141, 99, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (142, 100, 'Standard', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (143, 101, 'Standard', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (144, 102, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (145, 103, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (146, 104, 'Long', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (147, 104, 'Medium', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (148, 105, 'Standard', 17200, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=17200;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (149, 106, 'Standard', 19350, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (150, 107, 'Standard', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (151, 108, 'Standard', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (152, 109, 'Standard', 8600, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (153, 110, 'Standard', 6450, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=6450;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (154, 111, 'Standard', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (155, 112, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (156, 113, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (157, 114, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (158, 115, 'Standard', 528, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=528;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (159, 116, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (160, 117, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (161, 118, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (162, 119, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (163, 120, 'Standard', 3225, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (164, 121, 'Standard', 8600, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (165, 122, 'Standard', 6450, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=6450;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (166, 123, 'Female', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (167, 123, 'Male', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (168, 124, 'Standard', 13975, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=13975;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (169, 125, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (170, 126, 'Standard', 9675, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=9675;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (171, 127, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (172, 128, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (173, 129, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (174, 130, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (175, 131, 'Standard', 7525, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (176, 132, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (177, 133, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (178, 134, 'Standard', 7525, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (179, 135, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (180, 136, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (181, 137, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (182, 138, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (183, 139, 'Standard', 3225, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (184, 140, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (185, 141, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (186, 142, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (187, 143, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (188, 144, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (189, 145, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (190, 146, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (191, 147, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (192, 148, 'Standard', 7525, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (193, 149, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (194, 150, 'Standard', 12900, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (195, 151, 'Standard', 18275, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=18275;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (196, 152, 'Standard', 19350, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (197, 153, 'Standard', 22575, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=22575;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (198, 154, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (199, 155, 'Standard', 8600, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (200, 156, 'Standard', 25800, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=25800;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (201, 157, 'Standard', 30100, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=30100;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (202, 158, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (203, 159, 'Standard', 7525, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (204, 160, 'Standard', 12900, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (205, 161, 'Standard', 22575, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=22575;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (206, 162, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (207, 163, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (208, 164, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (209, 165, 'Standard', 35475, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=35475;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (210, 166, 'Standard', 22575, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=22575;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (211, 167, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (212, 168, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (213, 169, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (214, 170, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (215, 171, 'Standard', 41925, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=41925;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (216, 172, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (217, 173, 'Standard', 41925, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=41925;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (218, 174, 'Standard', 56975, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=56975;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (219, 175, 'Standard', 78475, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=78475;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (220, 176, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (221, 177, 'Standard', 8600, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (222, 178, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (223, 179, 'Standard', 19350, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (224, 180, 'Standard', 22575, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=22575;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (225, 181, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (226, 182, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (227, 183, 'Standard', 44075, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=44075;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (228, 184, 'Standard', 8600, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (229, 185, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (230, 186, 'Standard', 12900, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (231, 187, 'Standard', 17200, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=17200;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (232, 188, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (233, 189, 'Standard', 31175, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=31175;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (234, 190, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (235, 191, 'Standard', 44075, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=44075;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (236, 192, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (237, 193, 'Standard', 26875, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (238, 194, 'Standard', 26875, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (239, 195, 'Standard', 32250, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (240, 196, 'Standard', 19350, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (241, 197, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (242, 198, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (243, 199, 'Standard', 26875, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (244, 200, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (245, 201, 'Standard', 26875, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (246, 202, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (247, 203, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (248, 204, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (249, 205, 'Standard', 19350, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (250, 206, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (251, 207, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (252, 208, 'Standard', 21500, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (253, 209, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (254, 210, 'Standard', 12900, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (255, 211, 'Standard', 10750, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (256, 212, 'Standard', 16125, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (257, 213, 'Standard', 2150, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (258, 214, 'Standard', 3225, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (259, 215, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (260, 216, 'Standard', 24725, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=24725;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (261, 217, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (262, 218, 'Standard', 26875, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (263, 219, 'Standard', 29025, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (264, 220, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (265, 221, 'Standard', 31175, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=31175;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (266, 222, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (267, 223, 'Standard', 2150, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (268, 224, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (269, 225, 'Standard', 17200, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=17200;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (270, 226, 'Standard', 2150, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (271, 227, 'Standard', 5375, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (272, 228, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (273, 229, 'Standard', 31175, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=31175;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (274, 230, 'Standard', 37625, 1, 1, '2026-09-11 20:53:17') ON DUPLICATE KEY UPDATE price_ngn=37625;

-- Default Admin Account (caroline / Caroline@2026!)
INSERT INTO admins (id, username, password, display_name, email, is_active) VALUES (1, 'caroline', ''y'$gQZ7TzLz1iI8lY7Qj6K9Me7Y0f5qF5i0hG3j1k2l3m4n5o6p7q8r', 'Caroline Admin', 'info@carolinesplace.com', 1) ON DUPLICATE KEY UPDATE username='caroline';
