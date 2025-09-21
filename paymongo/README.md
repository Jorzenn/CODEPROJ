# PayMongo Integration System

## Features
- Complete payment processing with PayMongo
- Receipt generation and persistent storage
- Admin panel for payment management
- Responsive design with modern UI
- Webhook handling for real-time updates
- Database storage with proper indexing
- Security headers and file protection

## Setup Instructions

1. **Database Setup**
   - Create MySQL database: `paymongo_db`
   - Run the SQL in setup.sql or let the system auto-create tables

2. **Configuration**
   - Update `config/config.php` with your PayMongo keys
   - Update `SITE_URL` to match your domain
   - Update database credentials in `config/database.php`

3. **File Permissions**
   - Create `logs/` directory: `mkdir logs && chmod 755 logs`
   - Ensure web server can write to logs directory

4. **PayMongo Webhook**
   - Set webhook URL in PayMongo dashboard: `https://yourdomain.com/webhook.php`
   - Enable payment.paid and payment.failed events

5. **Production Security**
   - Change admin password in `admin.php`
   - Enable HTTPS redirects in `.htaccess`
   - Use environment variables for sensitive config