-- Migration: Add activity logging system
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL COMMENT 'User who performed action',
    action VARCHAR(100) NOT NULL COMMENT 'book.created, user.banned, loan.created, etc.',
    entity_type VARCHAR(50) NULL COMMENT 'book, user, loan, etc.',
    entity_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_logs_user (user_id),
    INDEX idx_activity_logs_action (action),
    INDEX idx_activity_logs_entity (entity_type, entity_id),
    INDEX idx_activity_logs_created (created_at),
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

