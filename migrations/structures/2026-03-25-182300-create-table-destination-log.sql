CREATE TABLE destination_log (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    country_id CHAR(5) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_log_user
    FOREIGN KEY (user_id) REFERENCES user(id),
    CONSTRAINT fk_log_country
    FOREIGN KEY (country_id) REFERENCES country(id),
    CONSTRAINT chk_rating
    CHECK (rating BETWEEN 1 AND 5)
);