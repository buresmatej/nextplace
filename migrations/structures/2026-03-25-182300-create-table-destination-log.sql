CREATE TABLE destination_log (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id uuid NOT NULL,
    country_id CHAR(5) NOT NULL,
    rating SMALLINT NOT NULL,
    note TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_user
    FOREIGN KEY (user_id) REFERENCES "user"(id),
    CONSTRAINT fk_log_country
    FOREIGN KEY (country_id) REFERENCES country(id),
    CONSTRAINT chk_rating
    CHECK (rating BETWEEN 1 AND 5)
);