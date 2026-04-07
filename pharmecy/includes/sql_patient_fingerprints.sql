-- Run once on database ycdomlt (adjust name if different).
-- Stores both-thumb templates for patients enrolled at rehabilitation branches.

CREATE TABLE IF NOT EXISTS `patient_fingerprints` (
  `patient_id` int(11) NOT NULL,
  `thumb_left` longtext NOT NULL,
  `thumb_right` longtext NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional but recommended for 1:N accuracy over time:
-- Keep multiple templates per patient (per finger, per visit/re-capture).
CREATE TABLE IF NOT EXISTS `patient_fingerprint_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `finger_code` varchar(20) NOT NULL,
  `template_data` longtext NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
