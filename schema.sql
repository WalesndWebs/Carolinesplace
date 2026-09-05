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
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (1, 1, 'Signature Facial (Caroline Special)', 'Signature Facial (Caroline Special)', 90, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Signature Facial (Caroline Special)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (8, 2, 'Swedish (Relaxation) Massage', 'Swedish (Relaxation) Massage', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Swedish (Relaxation) Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (19, 3, 'Full Body Waxing', 'Full Body Waxing', 90, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Body Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (39, 4, 'Coffee Scrub / Body Wrap', 'Coffee Scrub / Body Wrap', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Coffee Scrub / Body Wrap';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (42, 5, 'Washing of Hair Alone', 'Washing of Hair Alone', 45, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing of Hair Alone';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (94, 6, 'Gel X', 'Gel X', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel X';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (123, 7, 'Regular Pedicure', 'Regular Pedicure', 60, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Regular Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (2, 1, 'Anti-Aging Facial', 'Anti-Aging Facial', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Anti-Aging Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (9, 2, 'Deep Tissue Massage', 'Deep Tissue Massage', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep Tissue Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (20, 3, 'Full Leg Wax', 'Full Leg Wax', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Leg Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (40, 4, 'Hammam (Moroccan Bath)', 'Hammam (Moroccan Bath)', 75, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hammam (Moroccan Bath)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (43, 5, 'Washing & Blow-dry', 'Washing & Blow-dry', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing & Blow-dry';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (95, 6, 'French Tips', 'French Tips', 45, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='French Tips';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (124, 7, 'Dry Pedicure', 'Dry Pedicure', 60, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Dry Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (3, 1, 'Brightening / Glow Facial', 'Brightening / Glow Facial', 60, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Brightening / Glow Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (10, 2, 'Hot Stone Massage', 'Hot Stone Massage', 75, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hot Stone Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (21, 3, 'Half Leg Wax', 'Half Leg Wax', 45, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Half Leg Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (41, 4, 'Steam Bath', 'Steam Bath', 60, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Steam Bath';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (44, 5, 'Washing & Straightening (Temporary)', 'Washing & Straightening (Temporary)', 75, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing & Straightening (Temporary)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (96, 6, 'Chrome', 'Chrome', 45, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Chrome';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (125, 7, 'Spa Pedicure', 'Spa Pedicure', 75, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Spa Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (4, 1, 'Deep Cleansing Facial', 'Deep Cleansing Facial', 45, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep Cleansing Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (11, 2, 'Thai Massage', 'Thai Massage', 60, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Thai Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (22, 3, 'Full Arm Wax', 'Full Arm Wax', 45, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Arm Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (45, 5, 'Protein Treatment (Hair Botox)', 'Protein Treatment (Hair Botox)', 60, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Protein Treatment (Hair Botox)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (97, 6, 'Ombre', 'Ombre', 60, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ombre';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (126, 7, 'Manicure', 'Manicure', 45, 4, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Manicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (5, 1, 'Hydrating Facial', 'Hydrating Facial', 45, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hydrating Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (12, 2, 'Sports Massage', 'Sports Massage', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sports Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (23, 3, 'Half Arm Wax', 'Half Arm Wax', 30, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Half Arm Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (46, 5, 'Deep Conditioning Treatment', 'Deep Conditioning Treatment', 45, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep Conditioning Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (98, 6, 'Coloured Powder', 'Coloured Powder', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Coloured Powder';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (127, 7, 'Pedicure with Cavia', 'Pedicure with Cavia', 60, 5, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Pedicure with Cavia';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (6, 1, 'Acne Treatment Facial', 'Acne Treatment Facial', 60, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acne Treatment Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (13, 2, 'Aromatherapy Massage', 'Aromatherapy Massage', 60, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Aromatherapy Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (24, 3, 'Underarm Wax', 'Underarm Wax', 15, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Underarm Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (47, 5, 'Hot Oil Treatment', 'Hot Oil Treatment', 30, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hot Oil Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (99, 6, 'Ombre Refill', 'Ombre Refill', 45, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ombre Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (128, 7, 'Luxury Pedicure', 'Luxury Pedicure', 90, 6, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Luxury Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (7, 1, 'Men Facial', 'Men Facial', 45, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Men Facial';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (14, 2, 'Reflexology (Feet)', 'Reflexology (Feet)', 45, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Reflexology (Feet)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (25, 3, 'Bikini Line Wax', 'Bikini Line Wax', 30, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Bikini Line Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (48, 5, 'Steam Treatment for Hair', 'Steam Treatment for Hair', 30, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Steam Treatment for Hair';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (100, 6, 'Acrylic Nail Refill', 'Acrylic Nail Refill', 45, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic Nail Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (129, 7, 'Kids Pedicure', 'Kids Pedicure', 30, 7, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Kids Pedicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (15, 2, 'Back / Shoulder / Neck Massage', 'Back / Shoulder / Neck Massage', 30, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Back / Shoulder / Neck Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (26, 3, 'Brazilian Wax', 'Brazilian Wax', 45, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Brazilian Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (49, 5, 'Knotless Braids', 'Knotless Braids', 150, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Knotless Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (101, 6, 'Acrylic Wrap', 'Acrylic Wrap', 60, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic Wrap';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (130, 7, 'Kids Manicure', 'Kids Manicure', 20, 8, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Kids Manicure';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (16, 2, 'Pre-Natal Massage', 'Pre-Natal Massage', 60, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Pre-Natal Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (27, 3, 'Hollywood Wax', 'Hollywood Wax', 45, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hollywood Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (50, 5, 'Tiny / Knotless Braids', 'Tiny / Knotless Braids', 150, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Tiny / Knotless Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (102, 6, 'Stick On', 'Stick On', 45, 9, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stick On';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (17, 2, 'Couples Massage', 'Couples Massage', 90, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Couples Massage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (28, 3, 'Chest Wax', 'Chest Wax', 30, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Chest Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (51, 5, 'Box Braids', 'Box Braids', 150, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Box Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (103, 6, 'Refill Gel', 'Refill Gel', 60, 10, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Refill Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (18, 2, 'Lymphatic Drainage', 'Lymphatic Drainage', 60, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Lymphatic Drainage';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (29, 3, 'Back Wax', 'Back Wax', 30, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Back Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (52, 5, 'Cornrows (Feed-in)', 'Cornrows (Feed-in)', 60, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Cornrows (Feed-in)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (104, 6, 'Acrylic', 'Acrylic', 60, 11, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (30, 3, 'Stomach Wax', 'Stomach Wax', 20, 12, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stomach Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (53, 5, 'Ghana Weaving', 'Ghana Weaving', 90, 12, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ghana Weaving';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (105, 6, 'BIAB', 'BIAB', 60, 12, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='BIAB';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (31, 3, 'Face Wax', 'Face Wax', 15, 13, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Face Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (54, 5, 'Crochet Braids (Hair not included)', 'Crochet Braids (Hair not included)', 120, 13, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Crochet Braids (Hair not included)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (106, 6, 'Hard Gel', 'Hard Gel', 60, 13, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hard Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (32, 3, 'Upper Lip Wax', 'Upper Lip Wax', 10, 14, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Upper Lip Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (55, 5, 'Wig Cap / Wig Construction', 'Wig Cap / Wig Construction', 180, 14, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Wig Cap / Wig Construction';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (107, 6, 'Cateye Polish', 'Cateye Polish', 30, 14, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Cateye Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (33, 3, 'Chin Wax', 'Chin Wax', 10, 15, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Chin Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (56, 5, 'Wig Install', 'Wig Install', 120, 15, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Wig Install';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (108, 6, 'Press On Nails', 'Press On Nails', 30, 15, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Press On Nails';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (34, 3, 'Eyebrow Waxing', 'Eyebrow Waxing', 15, 16, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Eyebrow Waxing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (57, 5, 'Wig Revamp / Takedown + Reinstall', 'Wig Revamp / Takedown + Reinstall', 120, 16, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Wig Revamp / Takedown + Reinstall';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (109, 6, 'Gel Polish', 'Gel Polish', 30, 16, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (35, 3, 'Sideburns Wax', 'Sideburns Wax', 10, 17, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sideburns Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (58, 5, 'Wig Styling (Wash & Style)', 'Wig Styling (Wash & Style)', 60, 17, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Wig Styling (Wash & Style)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (110, 6, 'Regular Polish', 'Regular Polish', 30, 17, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Regular Polish';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (36, 3, 'Neck Wax', 'Neck Wax', 15, 18, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Neck Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (59, 5, 'Colour Retouch / Root Touch-up', 'Colour Retouch / Root Touch-up', 90, 18, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Colour Retouch / Root Touch-up';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (111, 6, 'Deep In (Nails)', 'Deep In (Nails)', 60, 18, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Deep In (Nails)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (37, 3, 'Nose Wax', 'Nose Wax', 10, 19, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nose Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (60, 5, 'Full Head Colour', 'Full Head Colour', 120, 19, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Full Head Colour';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (112, 6, 'Tidy Up of Nails', 'Tidy Up of Nails', 30, 19, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Tidy Up of Nails';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (38, 3, 'Ear Wax', 'Ear Wax', 10, 20, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Ear Wax';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (61, 5, 'Hair Highlights (Balayage/Foils)', 'Hair Highlights (Balayage/Foils)', 120, 20, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hair Highlights (Balayage/Foils)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (113, 6, 'Gel Topcoat', 'Gel Topcoat', 20, 20, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Topcoat';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (62, 5, 'Bleach & Tone', 'Bleach & Tone', 120, 21, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Bleach & Tone';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (114, 6, 'BIAB Refill', 'BIAB Refill', 45, 21, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='BIAB Refill';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (63, 5, 'Silk Press', 'Silk Press', 60, 22, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Silk Press';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (115, 6, 'Nail Art (Per Finger)', 'Nail Art (Per Finger)', 10, 22, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nail Art (Per Finger)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (64, 5, 'Flexi Rod Set', 'Flexi Rod Set', 60, 23, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Flexi Rod Set';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (116, 6, '3D Nail Art Design', '3D Nail Art Design', 15, 23, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='3D Nail Art Design';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (65, 5, 'Perm Rod Set', 'Perm Rod Set', 60, 24, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Perm Rod Set';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (117, 6, 'Stone Nail Art', 'Stone Nail Art', 20, 24, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stone Nail Art';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (66, 5, 'Washing & Setting of Wigs with Rollers', 'Washing & Setting of Wigs with Rollers', 60, 25, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing & Setting of Wigs with Rollers';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (118, 6, 'Nail Replacement (Per Finger)', 'Nail Replacement (Per Finger)', 15, 25, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Nail Replacement (Per Finger)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (67, 5, 'Washing & Straightening of Wigs', 'Washing & Straightening of Wigs', 60, 26, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing & Straightening of Wigs';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (119, 6, 'Big Toes Fixing', 'Big Toes Fixing', 20, 26, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Big Toes Fixing';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (68, 5, 'Washing & Curling', 'Washing & Curling', 60, 27, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Washing & Curling';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (120, 6, 'Gel Dissolve', 'Gel Dissolve', 20, 27, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Gel Dissolve';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (69, 5, 'Straightening Alone', 'Straightening Alone', 45, 28, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Straightening Alone';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (121, 6, 'Sculpting Gel', 'Sculpting Gel', 30, 28, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sculpting Gel';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (70, 5, 'Curling Alone', 'Curling Alone', 45, 29, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Curling Alone';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (122, 6, 'Acrylic / BIAB / Stick-On Dissolve', 'Acrylic / BIAB / Stick-On Dissolve', 20, 29, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Acrylic / BIAB / Stick-On Dissolve';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (71, 5, 'Wig Repairs', 'Wig Repairs', 30, 30, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Wig Repairs';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (72, 5, 'Dyeing of Wig / Weave', 'Dyeing of Wig / Weave', 90, 31, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Dyeing of Wig / Weave';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (73, 5, 'Sharper Powder', 'Sharper Powder', 120, 32, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sharper Powder';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (74, 5, 'Sharper Powder (Sizes)', 'Sharper Powder (Sizes)', 120, 33, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Sharper Powder (Sizes)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (75, 5, 'Mega Growth Relaxer', 'Mega Growth Relaxer', 30, 34, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Mega Growth Relaxer';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (76, 5, 'Mega Growth Relaxer 2', 'Mega Growth Relaxer 2', 45, 35, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Mega Growth Relaxer 2';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (77, 5, 'Loosing of Goodness Braid', 'Loosing of Goodness Braid', 45, 36, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Loosing of Goodness Braid';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (78, 5, 'Olive Oil Relaxer', 'Olive Oil Relaxer', 30, 37, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Olive Oil Relaxer';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (79, 5, 'Olive Oil Relaxer 2', 'Olive Oil Relaxer 2', 45, 38, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Olive Oil Relaxer 2';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (80, 5, 'Bob Marley', 'Bob Marley', 150, 39, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Bob Marley';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (81, 5, 'Stitch Braids', 'Stitch Braids', 150, 40, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Stitch Braids';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (82, 5, 'Hair Trimming / Cutting', 'Hair Trimming / Cutting', 30, 41, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Hair Trimming / Cutting';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (83, 5, 'Scalp Treatment', 'Scalp Treatment', 30, 42, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Scalp Treatment';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (84, 5, 'Henna / Natural Hair Colour', 'Henna / Natural Hair Colour', 60, 43, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Henna / Natural Hair Colour';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (85, 5, 'Weave / Weavon Fixing (Short)', 'Weave / Weavon Fixing (Short)', 90, 44, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Weave / Weavon Fixing (Short)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (86, 5, 'Weave / Weavon Fixing (Long)', 'Weave / Weavon Fixing (Long)', 120, 45, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Weave / Weavon Fixing (Long)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (87, 5, 'Clip-In Extensions Install', 'Clip-In Extensions Install', 60, 46, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Clip-In Extensions Install';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (88, 5, 'Tape-In Extensions Install', 'Tape-In Extensions Install', 90, 47, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Tape-In Extensions Install';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (89, 5, 'Faux Locs (Short)', 'Faux Locs (Short)', 120, 48, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Faux Locs (Short)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (90, 5, 'Faux Locs (Long)', 'Faux Locs (Long)', 150, 49, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Faux Locs (Long)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (91, 5, 'Dreadlocks Maintenance / Retwist', 'Dreadlocks Maintenance / Retwist', 60, 50, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Dreadlocks Maintenance / Retwist';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (92, 5, 'Barbing / Hair Cut (Men)', 'Barbing / Hair Cut (Men)', 30, 51, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Barbing / Hair Cut (Men)';
INSERT INTO services (id, category_id, name, description, duration_minutes, sort_order, is_active, created_at) VALUES (93, 5, 'Kids Hair Styling (Simple)', 'Kids Hair Styling (Simple)', 30, 52, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE name='Kids Hair Styling (Simple)';

-- Seed Options
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (1, 1, 'Standard', 53750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=53750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (2, 2, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (3, 3, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (4, 4, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (5, 5, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (6, 6, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (7, 7, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (8, 8, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (9, 9, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (10, 10, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (11, 11, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (12, 12, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (13, 13, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (14, 14, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (15, 15, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (16, 16, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (17, 17, 'Standard', 64500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=64500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (18, 18, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (19, 19, 'Standard', 53750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=53750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (20, 20, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (21, 21, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (22, 22, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (23, 23, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (24, 24, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (25, 25, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (26, 26, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (27, 27, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (28, 28, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (29, 29, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (30, 30, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (31, 31, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (32, 32, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (33, 33, 'Standard', 3225, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (34, 34, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (35, 35, 'Standard', 3225, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (36, 36, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (37, 37, 'Standard', 3225, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (38, 38, 'Standard', 3225, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (39, 39, 'Standard', 48375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=48375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (40, 40, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (41, 41, '15 mins', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (44, 42, 'Short', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (47, 43, 'Short', 8600, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (50, 44, 'Short', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (53, 45, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (54, 46, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (55, 47, 'Standard', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (56, 48, 'Standard', 8600, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (57, 49, 'Bob (Shoulder)', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (60, 50, 'Bob (Shoulder)', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (63, 51, 'Short/Shoulder', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (66, 52, 'Simple', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (68, 53, 'Short', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (70, 54, 'Short', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (72, 55, 'Closure', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (75, 56, 'Closure', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (78, 57, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (79, 58, 'Loose Curls / Waves', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (82, 59, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (83, 60, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (84, 61, 'Partial', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (86, 62, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (87, 63, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (88, 64, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (89, 65, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (90, 66, 'Short', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (92, 67, 'Short', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (95, 68, 'Short', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (97, 69, 'Short', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (99, 70, 'Short', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (101, 71, 'Wig Band Replacement', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (104, 72, 'Short', 24725, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=24725;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (107, 73, 'Client\'s Product', 29025, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (109, 74, 'Medium', 31175, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=31175;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (111, 75, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (112, 76, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (113, 77, 'Standard', 17200, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=17200;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (114, 78, 'Standard', 2150, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=2150;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (115, 79, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (116, 80, 'Standard', 37625, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (117, 81, 'Medium', 31175, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=31175;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (119, 82, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (120, 83, 'Standard', 8600, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=8600;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (121, 84, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (122, 85, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (123, 86, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (124, 87, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (125, 88, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (126, 89, 'Standard', 32250, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (127, 90, 'Standard', 43000, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (128, 91, 'Standard', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (129, 92, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (130, 93, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (131, 94, 'Long', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (134, 95, 'Standard (Hands)', 7525, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (136, 96, 'Full', 16125, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (138, 97, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (139, 98, 'New Set', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (141, 99, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (142, 100, 'Standard', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (143, 101, 'Standard', 12900, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (144, 102, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (145, 103, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (146, 104, 'Long', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
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
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (168, 124, 'Standard', 13975, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=13975;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (169, 125, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (170, 126, 'Standard', 9675, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=9675;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (171, 127, 'Standard', 21500, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (172, 128, 'Standard', 26875, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (173, 129, 'Standard', 10750, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (174, 130, 'Standard', 5375, 1, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (42, 41, '30 mins', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (45, 42, 'Medium', 6450, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=6450;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (48, 43, 'Medium', 10750, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (51, 44, 'Medium', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (58, 49, 'Mid-Back (Bra Strap)', 48375, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=48375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (61, 50, 'Mid-Back (Bra Strap)', 48375, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=48375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (64, 51, 'Mid-Back', 37625, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (67, 52, 'Intricate / Design', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (69, 53, 'Long', 32250, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (71, 54, 'Long', 21500, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (73, 55, 'Frontal', 32250, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (76, 56, 'Frontal', 21500, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (80, 58, 'Flat Iron (Bone Straight)', 12900, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (85, 61, 'Full Head', 43000, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (91, 66, 'Long', 21500, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (93, 67, 'Medium', 19350, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=19350;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (96, 68, 'Long', 21500, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (98, 69, 'Long', 12900, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (100, 70, 'Long', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (102, 71, 'Band Replacement (Adjustable Pin)', 3225, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=3225;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (105, 72, 'Long', 29025, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=29025;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (108, 73, 'Salon Products', 37625, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (110, 74, 'Small', 37625, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (118, 81, 'Small', 37625, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=37625;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (132, 94, 'Short', 18275, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=18275;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (135, 95, 'Leg', 5375, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (137, 96, 'Alone', 10750, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=10750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (140, 98, 'Refill', 12900, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (147, 104, 'Medium', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (167, 123, 'Male', 16125, 2, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=16125;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (43, 41, '60 mins', 26875, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (46, 42, 'Long', 7525, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=7525;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (49, 43, 'Long', 12900, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (52, 44, 'Long', 18275, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=18275;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (59, 49, 'Waist Length', 56975, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=56975;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (62, 50, 'Waist Length', 56975, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=56975;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (65, 51, 'Waist', 43000, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=43000;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (74, 55, '360 / Full Lace', 53750, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=53750;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (77, 56, '360 / Full Lace', 32250, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=32250;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (81, 58, 'Curls with Wand', 12900, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (94, 67, 'Long', 21500, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=21500;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (103, 71, 'Closure / Frontal Stitching', 5375, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=5375;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (106, 72, 'Highlights', 26875, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=26875;
INSERT INTO options (id, service_id, option_label, price_ngn, sort_order, is_active, created_at) VALUES (133, 94, 'Refill', 12900, 3, 1, '2026-08-31 20:39:05') ON DUPLICATE KEY UPDATE price_ngn=12900;

-- Seed Admins
INSERT INTO admins (id, username, password, display_name, email, is_active) VALUES (1, 'carolines_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Caroline Main Admin', 'admin@carolinesplace.com', 1) ON DUPLICATE KEY UPDATE username='carolines_admin';
INSERT INTO admins (id, username, password, display_name, email, is_active) VALUES (2, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'admin@carolinesplace.com', 1) ON DUPLICATE KEY UPDATE username='admin';

-- Seed Sample Booking
INSERT INTO bookings (id, reference_code, full_name, email, phone, division, preferred_date, preferred_time, total_amount_ngn, notes, status, payment_status, created_at) VALUES (1, 'SPA-439BA962', 'John Doe', 'jOHN@yahoo.com', '+23408136779318', 'spa', '2026-09-02', '06:00 PM', 12900, 'RAh', 'completed', 'paid', '2026-09-01 07:05:22') ON DUPLICATE KEY UPDATE reference_code='SPA-439BA962';
INSERT INTO booking_items (id, booking_id, service_id, option_id, service_name, option_label, unit_price_ngn, quantity, line_total_ngn) VALUES (1, 1, 123, 166, 'Regular Pedicure', 'Female', 12900, 1, 12900) ON DUPLICATE KEY UPDATE service_name='Regular Pedicure';
