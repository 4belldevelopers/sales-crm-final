-- Intent: extend the existing default/company offer terms model with named candidate offer templates.
-- Apply once to MySQL 8 before deploying the matching application code.
ALTER TABLE `global_settings`
    ADD COLUMN IF NOT EXISTS `internship_offer_terms` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `offer_terms`,
    ADD COLUMN IF NOT EXISTS `contract_offer_terms` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `internship_offer_terms`;

ALTER TABLE `companies`
    ADD COLUMN IF NOT EXISTS `internship_offer_terms` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `offer_terms`,
    ADD COLUMN IF NOT EXISTS `contract_offer_terms` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `internship_offer_terms`;

-- Seed the new default templates from the existing Candidate Offer terms so Super Admin can edit the text later.
UPDATE `global_settings`
SET
    `internship_offer_terms` = COALESCE(NULLIF(`internship_offer_terms`, ''), `offer_terms`),
    `contract_offer_terms` = COALESCE(NULLIF(`contract_offer_terms`, ''), `offer_terms`)
WHERE `id` = (SELECT `id` FROM (SELECT MIN(`id`) AS `id` FROM `global_settings`) AS gs);

-- Rollback (data-destructive; run only when explicitly approved):
-- ALTER TABLE `companies` DROP COLUMN `contract_offer_terms`, DROP COLUMN `internship_offer_terms`;
-- ALTER TABLE `global_settings` DROP COLUMN `contract_offer_terms`, DROP COLUMN `internship_offer_terms`;
