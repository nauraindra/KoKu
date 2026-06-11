CREATE DATABASE IF NOT EXISTS koku_new CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE koku_new;

DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS preferences;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS rooms;

CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(30) NOT NULL UNIQUE,
    type VARCHAR(80) NOT NULL DEFAULT 'Standard',
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('kosong','terisi','maintenance') NOT NULL DEFAULT 'kosong',
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','penyewa') NOT NULL DEFAULT 'penyewa',
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT users_room_id_foreign FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
);

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    month CHAR(7) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
    paid_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY payments_tenant_month_unique (tenant_id, month),
    CONSTRAINT payments_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT payments_room_id_foreign FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

CREATE TABLE preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    theme ENUM('light','dark') NOT NULL DEFAULT 'light',
    accent_color VARCHAR(20) NOT NULL DEFAULT '#ec4899',
    weather_city VARCHAR(100) NOT NULL DEFAULT 'Jakarta',
    latitude DECIMAL(10,6) NOT NULL DEFAULT -6.200000,
    longitude DECIMAL(10,6) NOT NULL DEFAULT 106.816666,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT preferences_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    visited_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT visits_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
