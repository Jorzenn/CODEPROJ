// Database Setup Script - setup.sql
CREATE DATABASE IF NOT EXISTS paymongo_db;
USE paymongo_db;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id VARCHAR(255) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PHP',
    description TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    reference_number VARCHAR(100) UNIQUE,
    customer_email VARCHAR(255),
    customer_name VARCHAR(255),
    checkout_url TEXT,
    receipt_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reference (reference_number),
    INDEX idx_payment_id (payment_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create logs directory (you'll need to create this manually on your server)
-- mkdir logs/
-- chmod 755 logs/

// .htaccess file for clean URLs and security
RewriteEngine On

# Redirect HTTP to HTTPS (uncomment in production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Protect sensitive files
<Files "config/*">
    Require all denied
</Files>

<Files "logs/*">
    Require all denied
</Files>

<Files ".env">
    Require all denied
</Files>

# Clean URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9_-]+)/?$ $1.php [NC,L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>