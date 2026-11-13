<?php
define('MN_DB_HOST', 'localhost');
define('MN_DB_NAME', 'maison_nord');
define('MN_DB_USER', 'root');
define('MN_DB_PASS', '');

function mn_db() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    try {
        $init = new PDO(
            'mysql:host=' . MN_DB_HOST . ';charset=utf8',
            MN_DB_USER, MN_DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        $init->exec('CREATE DATABASE IF NOT EXISTS `' . MN_DB_NAME . '` CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $init = null;

        $pdo = new PDO(
            'mysql:host=' . MN_DB_HOST . ';dbname=' . MN_DB_NAME . ';charset=utf8',
            MN_DB_USER, MN_DB_PASS,
            array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            )
        );

        /* ── Table utilisateurs ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_users (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                email      VARCHAR(255) NOT NULL UNIQUE,
                password   VARCHAR(255) NULL,
                prenom     VARCHAR(100) NOT NULL,
                nom        VARCHAR(100) NOT NULL,
                google_id  VARCHAR(100) NULL,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY idx_google_id (google_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* Migrations pour installations existantes */
        try { $pdo->exec("ALTER TABLE mn_users MODIFY COLUMN password VARCHAR(255) NULL"); }
        catch (PDOException $e) {}
        try { $pdo->exec("ALTER TABLE mn_users ADD COLUMN google_id VARCHAR(100) NULL"); }
        catch (PDOException $e) {}
        try { $pdo->exec("ALTER TABLE mn_users ADD UNIQUE INDEX idx_google_id (google_id)"); }
        catch (PDOException $e) {}

        /* ── Table produits ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_products (
                id          VARCHAR(100) NOT NULL PRIMARY KEY,
                name        VARCHAR(255) NOT NULL,
                category    VARCHAR(100) NOT NULL,
                price       INT          NOT NULL,
                image       VARCHAR(500) NOT NULL DEFAULT \'\',
                short_desc  TEXT         NOT NULL,
                long_desc   TEXT         NOT NULL,
                features    TEXT         NOT NULL,
                composition VARCHAR(500) NOT NULL DEFAULT \'\',
                sizes       VARCHAR(255) NOT NULL DEFAULT \'\',
                colors      VARCHAR(255) NOT NULL DEFAULT \'\',
                badge       VARCHAR(50)  NULL,
                sort_order  INT          NOT NULL DEFAULT 0,
                active      TINYINT(1)   NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* Seeder les produits une seule fois */
        if ((int) $pdo->query('SELECT COUNT(*) FROM mn_products')->fetchColumn() === 0) {
            require_once __DIR__ . '/seed-products.php';
            mn_seed_products($pdo);
        }

        /* ── Table panier sauvegardé ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_cart_items (
                user_id    INT          NOT NULL,
                product_id VARCHAR(100) NOT NULL,
                size       VARCHAR(50)  NOT NULL DEFAULT \'\',
                color      VARCHAR(100) NOT NULL DEFAULT \'\',
                qty        INT          NOT NULL DEFAULT 1,
                name       VARCHAR(255) NOT NULL DEFAULT \'\',
                price      INT          NOT NULL DEFAULT 0,
                image      VARCHAR(500) NOT NULL DEFAULT \'\',
                PRIMARY KEY (user_id, product_id, size, color),
                CONSTRAINT fk_cart_user FOREIGN KEY (user_id)
                    REFERENCES mn_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table commandes ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_orders (
                id           INT AUTO_INCREMENT PRIMARY KEY,
                user_id      INT          NOT NULL,
                total        INT          NOT NULL,
                status       VARCHAR(50)  NOT NULL DEFAULT \'pending\',
                prenom       VARCHAR(100) NOT NULL DEFAULT \'\',
                nom          VARCHAR(100) NOT NULL DEFAULT \'\',
                email        VARCHAR(255) NOT NULL DEFAULT \'\',
                phone        VARCHAR(50)  NOT NULL DEFAULT \'\',
                address      TEXT         NOT NULL,
                city         VARCHAR(100) NOT NULL DEFAULT \'\',
                notes        TEXT         NULL,
                fedapay_id   VARCHAR(100) NULL,
                created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_order_user FOREIGN KEY (user_id)
                    REFERENCES mn_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table articles commandes ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_order_items (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                order_id   INT          NOT NULL,
                product_id VARCHAR(100) NOT NULL,
                name       VARCHAR(255) NOT NULL,
                price      INT          NOT NULL,
                size       VARCHAR(50)  NOT NULL DEFAULT \'\',
                color      VARCHAR(100) NOT NULL DEFAULT \'\',
                qty        INT          NOT NULL DEFAULT 1,
                CONSTRAINT fk_item_order FOREIGN KEY (order_id)
                    REFERENCES mn_orders(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table réinitialisation mot de passe ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_password_resets (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                email      VARCHAR(255) NOT NULL,
                token      VARCHAR(100) NOT NULL UNIQUE,
                expires_at DATETIME     NOT NULL,
                used       TINYINT(1)   NOT NULL DEFAULT 0,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table messages de contact ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_contact_messages (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                prenom     VARCHAR(100) NOT NULL,
                nom        VARCHAR(100) NOT NULL,
                email      VARCHAR(255) NOT NULL,
                subject    VARCHAR(255) NOT NULL,
                message    TEXT         NOT NULL,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table adresses de livraison client ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_addresses (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                user_id    INT          NOT NULL,
                label      VARCHAR(100) NOT NULL DEFAULT \'Domicile\',
                prenom     VARCHAR(100) NOT NULL DEFAULT \'\',
                nom        VARCHAR(100) NOT NULL DEFAULT \'\',
                phone      VARCHAR(50)  NOT NULL DEFAULT \'\',
                address    TEXT         NOT NULL,
                city       VARCHAR(100) NOT NULL DEFAULT \'\',
                is_default TINYINT(1)   NOT NULL DEFAULT 0,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_addr_user FOREIGN KEY (user_id)
                    REFERENCES mn_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* ── Table zones de livraison ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_delivery_zones (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(100) NOT NULL,
                cities     TEXT         NOT NULL DEFAULT \'\',
                fee        INT          NOT NULL DEFAULT 0,
                delay      VARCHAR(50)  NOT NULL DEFAULT \'24-48h\',
                active     TINYINT(1)   NOT NULL DEFAULT 1,
                sort_order INT          NOT NULL DEFAULT 0,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* Seed zones par défaut */
        if ((int)$pdo->query('SELECT COUNT(*) FROM mn_delivery_zones')->fetchColumn() === 0) {
            $pdo->exec("INSERT INTO mn_delivery_zones (name, cities, fee, delay, active, sort_order) VALUES
                ('Lomé Centre',    'Lomé,Bè,Agoè',                  500,  '2-4h',   1, 1),
                ('Lomé Périphérie','Adidogomé,Tokoin,Djidjolé,Wuiti',1000, '4-8h',   1, 2),
                ('Grand Lomé',     'Aného,Vogan,Dapaong',           2000, '1-2j',   1, 3),
                ('Intérieur Togo', 'Kpalimé,Atakpamé,Sokodé,Kara', 3500, '2-3j',   1, 4)
            ");
        }

        /* ── Table avis produits ── */
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS mn_reviews (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                product_id VARCHAR(100) NOT NULL,
                user_id    INT          NOT NULL,
                rating     TINYINT      NOT NULL DEFAULT 5,
                comment    TEXT         NULL,
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_review (product_id, user_id),
                CONSTRAINT fk_review_user FOREIGN KEY (user_id)
                    REFERENCES mn_users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        /* Migrations mn_orders — ignorées si colonnes déjà présentes */
        try { $pdo->exec("ALTER TABLE mn_orders ADD COLUMN delivery_fee INT NOT NULL DEFAULT 0"); }
        catch (PDOException $ignored) { /* colonne existe déjà */ }
        try { $pdo->exec("ALTER TABLE mn_orders ADD COLUMN delivery_zone VARCHAR(100) NOT NULL DEFAULT ''"); }
        catch (PDOException $ignored) { /* colonne existe déjà */ }
        try { $pdo->exec("ALTER TABLE mn_products ADD COLUMN stock INT NOT NULL DEFAULT 0"); }
        catch (PDOException $ignored) { /* colonne existe déjà */ }
        try { $pdo->exec("ALTER TABLE mn_products ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"); }
        catch (PDOException $ignored) {}
        try { $pdo->exec("ALTER TABLE mn_products ADD COLUMN gallery TEXT NULL"); }
        catch (PDOException $ignored) {}

    } catch (PDOException $e) {
        die('Erreur de connexion : ' . htmlspecialchars($e->getMessage()));
    }
    return $pdo;
}
