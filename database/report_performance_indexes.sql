-- BK progress / comparison report indexes (run on production MySQL).
-- If an index already exists, MySQL will error on that line — skip it and continue.

-- Tokans: daily branch reports filter by status + created range
ALTER TABLE tokans ADD INDEX idx_tokans_status_created_branch (status, created, branch_id);
ALTER TABLE tokans ADD INDEX idx_tokans_branch_status_created (branch_id, status, created);

-- item_by_doctor: join from tokans + month aggregates
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_tokan_branch (tokan_no, branch_id);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_status_created_cat (status, created, category_id);
ALTER TABLE item_by_doctor ADD INDEX idx_ibd_branch_status_created (branch_id, status, created);

-- gynae_register
ALTER TABLE gynae_register ADD INDEX idx_gynae_created_branch (created, branch_id);
