ALTER TABLE `categories` 
ADD `status` tinyint(1) NOT NULL DEFAULT '1';

-- Cập nhật tất cả danh mục hiện tại thành active
UPDATE `categories` SET `status` = 1;