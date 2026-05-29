-- Optional indexes for pharmecy procedure turn / pending pages.
-- Run once on production DB (check existing indexes first to avoid duplicates).

ALTER TABLE `item_by_doctor`
  ADD INDEX `idx_ibd_branch_user_status` (`branch_id`, `user_id`, `status`),
  ADD INDEX `idx_ibd_tokan_no` (`tokan_no`);

ALTER TABLE `item_register_to_branches`
  ADD INDEX `idx_irb_branch_status` (`branch_id`, `status`),
  ADD INDEX `idx_irb_branch_item` (`branch_id`, `item_id`, `status`);

ALTER TABLE `items`
  ADD INDEX `idx_items_category_status` (`category_id`, `status`);

ALTER TABLE `branch_pending_details`
  ADD INDEX `idx_bpd_branch_status` (`branch_id`, `status`),
  ADD INDEX `idx_bpd_token_no` (`token_no`);

ALTER TABLE `tokans`
  ADD INDEX `idx_tokans_patient` (`patient_id`);
