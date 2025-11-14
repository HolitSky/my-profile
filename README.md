<div align="center">
  <img src="./assets/images/logo.svg" alt="Logo" width="120" />
  
  # Khalid Saifullah - Portfolio Website
  
  A modern, dynamic portfolio website with a comprehensive Content Management System (CMS) built with PHP, MySQL, and vanilla JavaScript. Features a beautiful UI with video backgrounds, smooth animations, and full admin control over all content.
  
  [![Live Demo](https://img.shields.io/badge/demo-live-success)](http://khalidsaifullah.me/)
  [![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
  [![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
</div>

## 📸 Screenshots

### Frontend
- **Modern Portfolio Interface**: Clean, responsive design with video backgrounds
- **Dynamic Content**: All sections powered by CMS
- **Smooth Animations**: Interactive cursor effects and transitions

### Admin Panel
- **Secure Dashboard**: Login system with rate limiting and CSRF protection
- **Content Management**: Full CRUD operations for all sections
- **Drag & Drop Sorting**: Intuitive reordering for portfolio items, experience, education, and technologies

---

## ✨ Features

### 🎨 Frontend Features
- **Responsive Design**: Mobile-first approach, works on all devices
- **Video Backgrounds**: Dynamic video overlay on About section
- **Interactive Cursor**: Custom cursor with smooth animations
- **Image Optimization**: WebP format support with fallbacks
- **Lazy Loading**: Optimized image loading for better performance
- **Social Media Integration**: LinkedIn, Instagram, and GitHub links
- **SEO Optimized**: Meta tags for Facebook, Twitter, and WhatsApp sharing
- **Lightbox Gallery**: Fancybox integration for portfolio images

### 🔐 Security Features
- **Secure Authentication**: Password hashing with bcrypt
- **Rate Limiting**: Login attempt throttling (7 attempts per 15 minutes)
- **CSRF Protection**: Token-based form validation
- **SQL Injection Prevention**: Prepared statements for all queries
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: Secure session handling with regeneration
- **Environment-based Config**: Separate development and production settings

### 📊 Content Management System

#### **Dashboard**
- Overview statistics (total portfolio items, experience entries, etc.)
- Quick access to all sections
- Recent activity monitoring

#### **About Section**
- Personal information management
- Video background upload/selection
- Rich text description editor

#### **Contact Information**
- Email, phone, birthday, location
- Social media links (LinkedIn, Instagram, GitHub)
- Dynamic updates to frontend

#### **Services Management**
- Add/Edit/Delete services
- Icon upload support
- Drag & drop sorting
- Active/inactive status toggle

#### **Technologies I Work With**
- Technology name and icon management
- File upload for icons (SVG, PNG, JPG)
- Manual path entry option
- Drag & drop reordering
- Live icon preview

#### **Experience Timeline**
- Job title, company, location
- Start/end dates with "current position" support
- Technologies used per role
- Detailed description with rich text
- Drag & drop chronological sorting

#### **Education History**
- Degree, institution, location
- Date range tracking
- GPA display
- Detailed achievements
- Sortable entries

#### **Portfolio Projects**
- Project title, category, description
- Image upload with WebP optimization
- Demo URL and GitHub repository links
- Technologies used tags
- Featured projects highlighting
- Drag & drop sorting
- Category filtering

#### **Testimonials**
- Client name, position, company
- Star rating system (1-5)
- Active/inactive status
- Sortable display order

### 🛠️ Admin Utilities

#### **Security Tools**
- **Test Database Connection**: Verify database connectivity
- **Generate Password Hash**: Create secure bcrypt hashes
- **Run Migrations**: Execute database schema updates
- **Run Seeders**: Populate database with sample data
- **Login Activity Monitor**: Track authentication attempts
- **Rate Limit Manager**: View and manage login restrictions

#### **Database Management**
- **Migration System**: Version-controlled schema changes
- **Seeder System**: Sample data population
- **Backup Support**: Database export functionality
- **Error Logging**: Detailed migration/seeder logs

---

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/HolitSky/my-profile.git
   cd my-profile
   ```

2. **Configure Database**
   
   Edit `config/config.php`:
   ```php
   // Development Configuration
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'portfolio_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Create Database**
   ```sql
   CREATE DATABASE portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Run Migrations**
   
   Access: `http://localhost/my-profile/admin/login.php`
   
   Login with default credentials:
   - Username: `admin`
   - Password: `admin123`
   
   Navigate to: **Security → Run Migrations**

5. **Seed Database (Optional)**
   
   Navigate to: **Security → Run Seeders**
   
   This will populate the database with sample data.

6. **Access the Site**
   - Frontend: `http://localhost/my-profile/`
   - Admin Panel: `http://localhost/my-profile/admin/`

### Production Deployment (Hostinger)

1. **Upload Files**
   
   Upload all files to `public_html` directory via FTP/File Manager

2. **Configure Production Database**
   
   Edit `config/config.php`:
   ```php
   // Production Configuration
   define('ENVIRONMENT', 'production');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'u734000704_khalid_profile');
   define('DB_USER', 'u734000704_root');
   define('DB_PASS', 'your_secure_password');
   ```

3. **Create Production Database**
   
   Use Hostinger's MySQL Database tool to create database

4. **Run Migrations**
   
   Access: `https://yourdomain.com/admin/login.php`
   
   Navigate to: **Security → Run Migrations**

5. **Update Admin Password**
   
   Navigate to: **Security → Generate Password Hash**
   
   Generate a new hash and update the database:
   ```sql
   UPDATE admin_users SET password = 'new_hash_here' WHERE username = 'admin';
   ```

---

## 📁 Project Structure

```
my-profile/
├── admin/                      # Admin panel
│   ├── includes/              # Shared admin components
│   │   ├── header.php         # Admin header with sidebar
│   │   └── footer.php         # Admin footer
│   ├── index.php              # Admin dashboard
│   ├── login.php              # Authentication page
│   ├── logout.php             # Logout handler
│   ├── about.php              # About section management
│   ├── contact.php            # Contact info management
│   ├── services.php           # Services CRUD
│   ├── skills.php             # Technologies management
│   ├── experience.php         # Experience timeline
│   ├── education.php          # Education history
│   ├── portfolio.php          # Portfolio projects
│   ├── testimonials.php       # Testimonials management
│   ├── security.php           # Security tools & utilities
│   ├── test-db.php            # Database connection tester
│   ├── generate-password.php  # Password hash generator
│   ├── run-migrations.php     # Migration runner
│   └── run-seeders.php        # Seeder runner
├── api/                       # REST API endpoints
│   └── index.php              # Main API endpoint
├── assets/                    # Static assets
│   ├── css/                   # Stylesheets
│   │   ├── style.css          # Main styles
│   │   └── cms-fixes.css      # CMS-specific styles
│   ├── images/                # Images
│   │   ├── icon/              # Technology icons
│   │   ├── optimized/         # WebP optimized images
│   │   └── video/             # Background videos
│   └── js/                    # JavaScript files
│       ├── script.js          # Main frontend logic
│       └── cms-integration.js # CMS data integration
├── config/                    # Configuration
│   └── config.php             # Database & environment config
├── database/                  # Database files
│   ├── migrations.sql         # Schema definitions
│   └── seeders.sql            # Sample data
├── includes/                  # PHP utilities
│   ├── db.php                 # Database connection
│   ├── functions.php          # Helper functions
│   └── auth.php               # Authentication functions
├── index.html                 # Main frontend page
└── README.md                  # This file
```

---

## 🗄️ Database Schema

### Tables Overview

1. **admin_users** - Admin authentication
2. **about** - About section content
3. **contact_info** - Contact information
4. **services** - Services offered
5. **skills** - Technologies/skills
6. **experience** - Work experience
7. **education** - Educational background
8. **portfolio** - Portfolio projects
9. **testimonials** - Client testimonials
10. **login_attempts** - Rate limiting tracking

### Key Features
- **Foreign Keys**: Referential integrity
- **Timestamps**: Created/updated tracking
- **Soft Deletes**: `is_active` flags
- **Sorting**: `sort_order` fields for drag & drop

---

## 🔌 API Endpoints

### GET `/api/index.php`

Returns all portfolio data in JSON format:

```json
{
  "about": {
    "title": "About me",
    "description": "...",
    "video_background": "./assets/images/video/video4.mp4"
  },
  "contact": {
    "email": "holitsky98@gmail.com",
    "phone": "+62 821-4768-8858",
    "birthday": "1998-07-15",
    "location": "South Tangerang, West Java, Indonesia",
    "linkedin": "https://www.linkedin.com/...",
    "instagram": "https://www.instagram.com/...",
    "github": "https://github.com/HolitSky"
  },
  "services": [...],
  "skills": [...],
  "experience": [...],
  "education": [...],
  "portfolio": [...],
  "testimonials": [...]
}
```

---

## 🎨 Technologies Used

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with animations
- **JavaScript (ES6+)** - Vanilla JS, no frameworks
- **Fancybox** - Lightbox gallery
- **Ionicons** - Icon library

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL** - Database management
- **PDO** - Database abstraction layer

### Admin Panel
- **Bootstrap 5** - UI framework
- **Bootstrap Icons** - Icon set
- **Drag & Drop API** - HTML5 native

### Security
- **bcrypt** - Password hashing
- **CSRF Tokens** - Form protection
- **Prepared Statements** - SQL injection prevention
- **Rate Limiting** - Brute force protection

---

## 🔒 Security Best Practices

1. **Password Security**
   - Bcrypt hashing with cost factor 10
   - Minimum 8 characters required
   - Special characters recommended

2. **Session Management**
   - Secure session cookies
   - Session regeneration on login
   - Automatic timeout after inactivity

3. **Input Validation**
   - Server-side validation for all inputs
   - Sanitization before database storage
   - Escaping on output

4. **Rate Limiting**
   - 7 login attempts per 15 minutes
   - IP-based tracking
   - Automatic cleanup of old attempts

5. **CSRF Protection**
   - Token generation on form load
   - Token validation on submission
   - Single-use tokens

---

## 📝 Usage Guide

### Adding a New Portfolio Project

1. Login to admin panel
2. Navigate to **Portfolio**
3. Click **Add Project**
4. Fill in details:
   - Title, category, description
   - Upload project image
   - Add demo URL and GitHub link
   - List technologies used
   - Mark as featured (optional)
5. Click **Save**
6. Drag to reorder if needed

### Managing Technologies

1. Navigate to **Technologies**
2. Click **Add Technology**
3. Enter technology name
4. Upload icon (SVG recommended) or enter path
5. Click **Add Technology**
6. Drag to reorder display order

### Updating About Section

1. Navigate to **About**
2. Edit title and description
3. Update video background path
4. Click **Update About**

### Running Database Migrations

1. Navigate to **Security**
2. Click **Run Migrations**
3. Review migration log
4. Verify tables created successfully

---

## 🐛 Troubleshooting

### Database Connection Issues
- Verify credentials in `config/config.php`
- Check MySQL service is running
- Test connection via **Security → Test Database**

### Login Problems
- Check rate limiting status
- Verify password hash in database
- Clear browser cookies/cache

### Image Upload Failures
- Check folder permissions (755 for directories, 644 for files)
- Verify upload directory exists
- Check PHP upload limits in `php.ini`

### Migration Errors
- Review error logs in migration output
- Check SQL syntax in `database/migrations.sql`
- Verify database user has CREATE/DROP permissions

---

## 🚧 Future Enhancements

- [ ] Blog section with categories and tags
- [ ] Contact form with email notifications
- [ ] Multi-language support (i18n)
- [ ] Dark/light theme toggle
- [ ] Advanced analytics dashboard
- [ ] Image compression on upload
- [ ] PDF resume generator
- [ ] REST API documentation
- [ ] Unit tests for critical functions
- [ ] Docker containerization

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Khalid Saifullah**

- Website: [khalidsaifullah.me](http://khalidsaifullah.me/)
- LinkedIn: [khalid-saifullah](https://www.linkedin.com/in/khalid-saifullah-a90a05217/)
- GitHub: [@HolitSky](https://github.com/HolitSky)
- Instagram: [@holit_sky](https://www.instagram.com/holit_sky/)
- Email: holitsky98@gmail.com

---

## 🙏 Acknowledgments

- **Bootstrap** - For the amazing UI framework
- **Ionicons** - For the beautiful icon set
- **Fancybox** - For the elegant lightbox solution
- **PHP Community** - For excellent documentation and support

---

## 📞 Support

For support, email holitsky98@gmail.com or open an issue on GitHub.

---

<div align="center">

**Made with ❤️ by Khalid Saifullah**

⭐ Star this repo if you find it helpful!

</div>
