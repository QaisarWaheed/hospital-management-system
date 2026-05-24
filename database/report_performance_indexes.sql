-- Run once on production MySQL to speed up BK progress & comparison reports.
-- Check existing indexes first: SHOW INDEX FROM tokans;

ALTER TABLE tokans
    ADD INDEX idx_tokans_branch_status_created (branch_id, status, created),
    ADD INDEX idx_tokans_status_created_branch (status, created, branch_id);

ALTER TABLE item_by_doctor
    ADD INDEX idx_ibd_branch_status_created (branch_id, status, created),
    ADD INDEX idx_ibd_status_created_cat (status, created, category_id);

ALTER TABLE gynae_register
    ADD INDEX idx_gynae_branch_created (branch_id, created);
