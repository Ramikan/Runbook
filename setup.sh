#!/bin/bash

set -e

# CONFIGURATION VARIABLES
APP_DIR="/var/www/html/mitre-runbook"
DB_NAME="mitre"
DB_USER="mitreuser"
DB_PASS="MitreP@ss123"
ADMIN_USER="admin"
ADMIN_PASS="admin123"
REPO_URL="https://github.com/ramikan/mitre-runbook.git"  # <-- Update this

# 1. Install dependencies
echo "[+] Installing system dependencies..."
apt update
apt install -y php php-cli php-mbstring php-xml php-curl php-mysql php-zip \
               mysql-server apache2 libapache2-mod-php composer git unzip curl

# 2. Enable and start services
echo "[+] Starting Apache and MySQL..."
systemctl enable --now mysql
systemctl enable --now apache2

# 3. Secure MySQL and create user + DB
echo "[+] Configuring MySQL..."
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

# 4. Clone application
echo "[+] Cloning application to $APP_DIR..."
rm -rf "$APP_DIR"
git clone "$REPO_URL" "$APP_DIR"
cd "$APP_DIR"

# 5. Install PHP dependencies
echo "[+] Installing PHP libraries with Composer..."
composer install --no-interaction

# 6. Create database schema
echo "[+] Creating database schema..."
cat <<SQL | mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME"
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS saved_runbooks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  apt VARCHAR(100),
  tactics JSON,
  runbook_html LONGTEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
SQL

# 7. Create admin user
echo "[+] Creating admin user..."
HASH=$(php -r "echo password_hash('$ADMIN_PASS', PASSWORD_DEFAULT);")
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e \
"INSERT IGNORE INTO users (username, password_hash) VALUES ('$ADMIN_USER', '$HASH');"

# 8. Set permissions
echo "[+] Setting file permissions..."
chown -R www-data:www-data "$APP_DIR"
chmod -R 755 "$APP_DIR"

# 9. Apache config (optional — assumes default site)
echo "[+] Setting Apache document root..."
sed -i "s|DocumentRoot .*|DocumentRoot $APP_DIR|" /etc/apache2/sites-available/000-default.conf

# 10. Restart Apache
echo "[+] Restarting Apache..."
systemctl restart apache2

# 11. Done
echo
echo "✅ Setup complete!"
echo "Access the app at: http://localhost/"
echo "Login with:"
echo "  Username: $ADMIN_USER"
echo "  Password: $ADMIN_PASS"
