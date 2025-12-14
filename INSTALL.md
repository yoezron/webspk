# Panduan Instalasi WebSPK

## Instalasi Development

### 1. Persiapan Environment

```bash
# Clone repository
git clone https://github.com/yoezron/webspk.git
cd webspk

# Buat virtual environment
python3 -m venv venv

# Aktivasi virtual environment
# Linux/Mac:
source venv/bin/activate
# Windows:
venv\Scripts\activate
```

### 2. Install Dependencies

```bash
pip install -r requirements.txt
```

### 3. Konfigurasi

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Edit .env sesuai kebutuhan
nano .env  # atau editor lainnya
```

### 4. Inisialisasi Database

```bash
# Buat semua tabel database
python -c "from app import create_app, db; app = create_app(); app.app_context().push(); db.create_all(); print('Database initialized!')"
```

### 5. Buat Admin User (Opsional)

```bash
python -c "from app import create_app, db; from app.models import User; app = create_app(); app.app_context().push(); u = User(username='admin', email='admin@spk.com', is_admin=True); u.set_password('admin123'); db.session.add(u); db.session.commit(); print('Admin user created: username=admin, password=admin123')"
```

**PENTING**: Ganti password default setelah login pertama kali!

### 6. Jalankan Aplikasi

```bash
python run.py
```

Buka browser dan akses: `http://localhost:5000`

## Instalasi Production

### 1. Persiapan Server

Pastikan server memiliki:
- Python 3.8+
- PostgreSQL atau MySQL (untuk production database)
- Nginx atau Apache (untuk reverse proxy)
- Supervisor atau systemd (untuk process management)

### 2. Setup Database Production

#### PostgreSQL:
```bash
# Install PostgreSQL
sudo apt-get install postgresql postgresql-contrib

# Buat database dan user
sudo -u postgres psql
CREATE DATABASE webspk_db;
CREATE USER webspk_user WITH PASSWORD 'strong_password';
GRANT ALL PRIVILEGES ON DATABASE webspk_db TO webspk_user;
\q
```

#### MySQL:
```bash
# Install MySQL
sudo apt-get install mysql-server

# Buat database dan user
mysql -u root -p
CREATE DATABASE webspk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'webspk_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON webspk_db.* TO 'webspk_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Setup Aplikasi

```bash
# Clone dan masuk ke direktori
cd /var/www/
git clone https://github.com/yoezron/webspk.git
cd webspk

# Buat virtual environment
python3 -m venv venv
source venv/bin/activate

# Install dependencies + production server
pip install -r requirements.txt
pip install gunicorn psycopg2-binary  # untuk PostgreSQL
# atau
pip install gunicorn mysqlclient  # untuk MySQL

# Setup environment variables
cp .env.example .env
nano .env
```

Edit `.env` untuk production:
```
SECRET_KEY=generate-a-strong-random-secret-key
DATABASE_URL=postgresql://webspk_user:strong_password@localhost/webspk_db
# atau untuk MySQL:
# DATABASE_URL=mysql://webspk_user:strong_password@localhost/webspk_db
FLASK_ENV=production
FLASK_DEBUG=False
```

```bash
# Inisialisasi database
python -c "from app import create_app, db; app = create_app(); app.app_context().push(); db.create_all()"

# Buat admin user
python -c "from app import create_app, db; from app.models import User; app = create_app(); app.app_context().push(); u = User(username='admin', email='admin@yourdomain.com', is_admin=True); u.set_password('secure_password'); db.session.add(u); db.session.commit()"
```

### 4. Setup Gunicorn dengan Systemd

Buat file service `/etc/systemd/system/webspk.service`:

```ini
[Unit]
Description=Gunicorn instance to serve WebSPK
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/webspk
Environment="PATH=/var/www/webspk/venv/bin"
ExecStart=/var/www/webspk/venv/bin/gunicorn --workers 4 --bind 127.0.0.1:8000 wsgi:app

[Install]
WantedBy=multi-user.target
```

Start dan enable service:
```bash
sudo systemctl start webspk
sudo systemctl enable webspk
sudo systemctl status webspk
```

### 5. Setup Nginx

Buat file konfigurasi `/etc/nginx/sites-available/webspk`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /static {
        alias /var/www/webspk/app/static;
        expires 30d;
    }

    location /uploads {
        alias /var/www/webspk/uploads;
        expires 30d;
    }

    client_max_body_size 10M;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/webspk /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6. Setup SSL dengan Let's Encrypt (Opsional tapi Direkomendasikan)

```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 7. Setup Backup Database (Opsional)

Buat script backup `/var/www/webspk/backup.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/webspk"
mkdir -p $BACKUP_DIR

# PostgreSQL
pg_dump -U webspk_user webspk_db > $BACKUP_DIR/backup_$DATE.sql

# MySQL
# mysqldump -u webspk_user -p'strong_password' webspk_db > $BACKUP_DIR/backup_$DATE.sql

# Hapus backup lama (lebih dari 30 hari)
find $BACKUP_DIR -type f -name "*.sql" -mtime +30 -delete

echo "Backup completed: backup_$DATE.sql"
```

Buat executable dan tambahkan ke cron:
```bash
chmod +x /var/www/webspk/backup.sh

# Edit crontab
sudo crontab -e

# Tambahkan baris ini untuk backup harian jam 2 pagi
0 2 * * * /var/www/webspk/backup.sh >> /var/log/webspk_backup.log 2>&1
```

## Troubleshooting

### Error: ModuleNotFoundError
```bash
# Pastikan virtual environment aktif
source venv/bin/activate
pip install -r requirements.txt
```

### Error: Database connection
```bash
# Check database credentials di .env
# Pastikan database service berjalan
sudo systemctl status postgresql  # atau mysql
```

### Error: Permission denied
```bash
# Set permission yang benar
sudo chown -R www-data:www-data /var/www/webspk
sudo chmod -R 755 /var/www/webspk
```

### Port already in use
```bash
# Cek process yang menggunakan port
sudo lsof -i :5000
# Kill process jika perlu
kill -9 <PID>
```

## Maintenance

### Update Aplikasi
```bash
cd /var/www/webspk
git pull origin main
source venv/bin/activate
pip install -r requirements.txt --upgrade
sudo systemctl restart webspk
```

### Backup Manual
```bash
# PostgreSQL
pg_dump -U webspk_user webspk_db > backup_$(date +%Y%m%d).sql

# MySQL
mysqldump -u webspk_user -p webspk_db > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
# PostgreSQL
psql -U webspk_user webspk_db < backup_file.sql

# MySQL
mysql -u webspk_user -p webspk_db < backup_file.sql
```

## Monitoring

### Check Logs
```bash
# Application logs
sudo journalctl -u webspk -f

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### Check Service Status
```bash
sudo systemctl status webspk
sudo systemctl status nginx
sudo systemctl status postgresql  # atau mysql
```
