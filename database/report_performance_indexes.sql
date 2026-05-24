-- BK progress / comparison report indexes (run on production MySQL).
-- If an index already exists, MySQL will error on that line — skip it and continue.

-- Tokans: daily branch reports filter by status + created range
ALTER TABLE tokans ADD INDEX idx_tokans_status_created_branch (status, created, branch_id);
ALTER TABLE tokans ADD INDEX idx_tokans_branch_status_created (branch_id, status, created);

-- item_by_doctor: join from tokans + month aggregates
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_tokan_branch (tokan_no, branch_id);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_status_created_cat (status, created, category_id);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_status_cat_created_branch (status, category_id, created, branch_id);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_branch_status_created (branch_id, status, created);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_tokan_branch_status_cat (tokan_no, branch_id, status, category_id);

-- gynae_register
ALTER TABLE gynae_register ADD INDEX idx_gynae_created_branch (created, branch_id);
ALTER TABLE gynae_register ADD INDEX idx_gynae_token_status (token_no, status);

-- logins_detail / summary_details: FR month report
ALTER TABLE logins_detail ADD INDEX idx_login_branch_status_at (branch_id, status, login_at);
ALTER TABLE summary_details ADD INDEX idx_summary_login_id (login_id);

-- Doctor monthly profile: filter by doctor + branch + month
ALTER TABLE tokans ADD INDEX idx_tokans_doctor_branch_status_created (doctor_id, branch_id, status, created);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_doctor_branch_status_created (doctor_id, branch_id, status, created);
ALTER TABLE gynae_register ADD INDEX idx_gynae_doctor_branch_created (doctor_id, branch_id, created);
ALTER TABLE referral_patients ADD INDEX idx_ref_created_to_user (referral_patient_created, to_user_id);
ALTER TABLE referral_patients ADD INDEX idx_ref_created_from_user (referral_patient_created, from_user_id);

-- General pending LG / detail
ALTER TABLE branch_daily_pending_details ADD INDEX idx_bdpd_created (created);
ALTER TABLE branch_daily_pending_details ADD INDEX idx_bdpd_token_created (token_no, created);

-- HR monthly progress (doctor): item_by_doctor by doctor + month
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_doctor_branch_created_cat (doctor_id, branch_id, created, category_id);
