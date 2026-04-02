CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS app_settings (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    site_name TEXT NOT NULL DEFAULT 'Lavender Restaurant',
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO app_settings (id, site_name)
VALUES (1, 'Lavender Restaurant')
ON CONFLICT(id) DO NOTHING;

CREATE TABLE IF NOT EXISTS weekly_menu (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    week_label TEXT NOT NULL DEFAULT '',
    menu_text TEXT NOT NULL DEFAULT '',
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO weekly_menu (id, week_label, menu_text)
VALUES (1, '', '')
ON CONFLICT(id) DO NOTHING;
