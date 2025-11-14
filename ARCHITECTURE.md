# 🏗️ Portfolio CMS - Architecture Overview

## 📊 System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     PORTFOLIO CMS SYSTEM                     │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│              │         │              │         │              │
│   FRONTEND   │◄────────│   REST API   │◄────────│  ADMIN PANEL │
│  (index.html)│         │ (api/index)  │         │  (admin/*.php)│
│              │         │              │         │              │
└──────┬───────┘         └──────┬───────┘         └──────┬───────┘
       │                        │                        │
       │ JavaScript             │ JSON                   │ PHP
       │ Fetch API              │ Response               │ Forms
       │                        │                        │
       └────────────────────────┼────────────────────────┘
                                │
                                ▼
                    ┌───────────────────────┐
                    │   MySQL DATABASE      │
                    │  (9 Tables)           │
                    │  - admin_users        │
                    │  - about              │
                    │  - skills             │
                    │  - experience         │
                    │  - education          │
                    │  - portfolio          │
                    │  - services           │
                    │  - contact_info       │
                    │  - testimonials       │
                    └───────────────────────┘
```

---

## 🔄 Data Flow

### 1. Admin Updates Content

```
┌─────────────┐
│   Admin     │
│   Login     │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Dashboard  │
│  Menu       │
└──────┬──────┘
       │
       ▼
┌─────────────┐      ┌──────────────┐      ┌──────────────┐
│   Edit      │─────▶│   Validate   │─────▶│   Save to    │
│   Form      │      │   & Sanitize │      │   Database   │
└─────────────┘      └──────────────┘      └──────────────┘
```

### 2. Frontend Displays Content

```
┌─────────────┐
│   User      │
│   Visits    │
│   Website   │
└──────┬──────┘
       │
       ▼
┌─────────────┐      ┌──────────────┐      ┌──────────────┐
│  index.html │─────▶│  Fetch API   │─────▶│  Display     │
│  Loads      │      │  /api/index  │      │  Content     │
└─────────────┘      └──────────────┘      └──────────────┘
       ▲                     │
       │                     ▼
       │              ┌──────────────┐
       │              │   Database   │
       │              │   Query      │
       │              └──────────────┘
       │                     │
       └─────────────────────┘
            JSON Response
```

---

## 📁 File Structure & Relationships

```
my-profile/
│
├── index.html ──────────┐
│   (Frontend)           │
│                        │
├── assets/              │
│   └── js/              │
│       └── cms-integration.js ──┐
│           (Fetches API)        │
│                                │
├── api/                         │
│   └── index.php ◄──────────────┤
│       (REST Endpoint)          │
│       │                        │
│       ├── Requires: config/database.php
│       └── Returns: JSON        │
│                                │
├── admin/ ─────────────────────┐│
│   ├── login.php               ││
│   ├── dashboard.php           ││
│   ├── about.php               ││
│   ├── skills.php              ││
│   ├── experience.php          ││
│   ├── education.php           ││
│   ├── portfolio.php           ││
│   ├── services.php            ││
│   └── contact.php             ││
│       (All require auth)      ││
│       │                       ││
│       └── Requires: ──────────┘│
│           - config/database.php│
│           - config/auth.php    │
│                                │
├── config/                      │
│   ├── database.php ◄───────────┘
│   │   (DB Connection)
│   └── auth.php
│       (Auth Functions)
│
└── database/
    └── schema.sql
        (Database Structure)
```

---

## 🔐 Authentication Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION FLOW                       │
└─────────────────────────────────────────────────────────────┘

1. User Access Admin Page
   │
   ▼
┌──────────────┐
│ requireLogin()│ ◄── Called in every admin page
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ isLoggedIn() │ ◄── Check session
└──────┬───────┘
       │
       ├─── YES ──▶ Allow Access
       │
       └─── NO ───▶ Redirect to login.php
                    │
                    ▼
            ┌──────────────┐
            │  Login Form  │
            └──────┬───────┘
                   │ Submit
                   ▼
            ┌──────────────┐
            │  Verify      │
            │  Credentials │
            └──────┬───────┘
                   │
                   ├─── Valid ──▶ loginUser() ──▶ Dashboard
                   │
                   └─── Invalid ──▶ Show Error
```

---

## 💾 Database Schema

```
┌─────────────────────────────────────────────────────────────┐
│                      DATABASE TABLES                         │
└─────────────────────────────────────────────────────────────┘

admin_users                about
├── id (PK)               ├── id (PK)
├── username              ├── title
├── password (hash)       ├── description
├── email                 ├── profile_image
└── timestamps            └── timestamps

skills                     experience
├── id (PK)               ├── id (PK)
├── name                  ├── title
├── percentage            ├── company
├── category              ├── location
├── sort_order            ├── start_date
└── timestamps            ├── end_date
                          ├── is_current
education                 ├── description
├── id (PK)               ├── sort_order
├── degree                └── timestamps
├── institution
├── location              portfolio
├── start_date            ├── id (PK)
├── end_date              ├── title
├── is_current            ├── category
├── description           ├── description
├── sort_order            ├── image
└── timestamps            ├── link
                          ├── github_link
services                  ├── technologies
├── id (PK)               ├── featured
├── title                 ├── sort_order
├── description           └── timestamps
├── icon
├── sort_order            contact_info
└── timestamps            ├── id (PK)
                          ├── email
testimonials              ├── phone
├── id (PK)               ├── birthday
├── name                  ├── location
├── position              ├── linkedin
├── company               ├── instagram
├── avatar                ├── github
├── content               └── updated_at
├── rating
├── is_active
├── sort_order
└── timestamps
```

---

## 🔄 Request/Response Flow

### Admin Panel Request

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────┐
│   Apache    │
│  (Hostinger)│
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  admin/*.php│
└──────┬──────┘
       │
       ├──▶ Check Auth (config/auth.php)
       │
       ├──▶ Connect DB (config/database.php)
       │
       ├──▶ Process Form (POST)
       │    │
       │    ├──▶ Validate Input
       │    ├──▶ Sanitize Data
       │    ├──▶ Execute Query
       │    └──▶ Return Result
       │
       └──▶ Render HTML (includes/header.php + footer.php)
              │
              ▼
       ┌─────────────┐
       │   Browser   │
       │  (Display)  │
       └─────────────┘
```

### API Request

```
┌─────────────┐
│  Frontend   │
│  JavaScript │
└──────┬──────┘
       │ fetch('/api/index.php')
       ▼
┌─────────────┐
│   Apache    │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ api/index.php│
└──────┬──────┘
       │
       ├──▶ Connect DB
       │
       ├──▶ Query All Tables
       │    ├── about
       │    ├── contact_info
       │    ├── skills
       │    ├── experience
       │    ├── education
       │    ├── portfolio
       │    ├── services
       │    └── testimonials
       │
       ├──▶ Format Data (dates, etc)
       │
       └──▶ Return JSON
              │
              ▼
       ┌─────────────┐
       │   Frontend  │
       │  (Process)  │
       └─────────────┘
```

---

## 🛡️ Security Layers

```
┌─────────────────────────────────────────────────────────────┐
│                      SECURITY LAYERS                         │
└─────────────────────────────────────────────────────────────┘

Layer 1: Apache (.htaccess)
├── Protect config files
├── Prevent directory listing
├── Enable compression
└── Set security headers

Layer 2: PHP Session
├── Session management
├── Login timeout
├── Session regeneration
└── Secure cookies

Layer 3: Authentication
├── Password hashing (bcrypt)
├── Login verification
├── Access control
└── Logout cleanup

Layer 4: Input Validation
├── Sanitize input
├── Validate data types
├── Escape output
└── CSRF protection

Layer 5: Database
├── PDO prepared statements
├── SQL injection prevention
├── Parameterized queries
└── Error handling

Layer 6: File System
├── Correct permissions (755/644)
├── Protect uploads directory
├── Prevent direct access
└── Secure file paths
```

---

## 🚀 Performance Optimization

```
┌─────────────────────────────────────────────────────────────┐
│                   PERFORMANCE STRATEGY                       │
└─────────────────────────────────────────────────────────────┘

Frontend Caching
├── LocalStorage (5 min)
├── Browser cache
└── CDN for assets

Database Optimization
├── Indexed columns
├── Optimized queries
├── Connection pooling
└── Query caching

Server Configuration
├── Gzip compression
├── Browser caching
├── Keep-alive
└── Minified assets

API Optimization
├── Single endpoint
├── Efficient queries
├── JSON compression
└── CORS headers
```

---

## 📱 Responsive Design

```
┌─────────────────────────────────────────────────────────────┐
│                    RESPONSIVE LAYOUT                         │
└─────────────────────────────────────────────────────────────┘

Desktop (> 992px)
├── Full sidebar
├── Multi-column layout
├── Large forms
└── Expanded tables

Tablet (768px - 992px)
├── Collapsible sidebar
├── 2-column layout
├── Medium forms
└── Scrollable tables

Mobile (< 768px)
├── Hidden sidebar (toggle)
├── Single column
├── Stacked forms
└── Card-based layout
```

---

## 🔧 Technology Stack

```
┌─────────────────────────────────────────────────────────────┐
│                    TECHNOLOGY STACK                          │
└─────────────────────────────────────────────────────────────┘

Backend
├── PHP 7.4+
│   ├── PDO (Database)
│   ├── Sessions (Auth)
│   └── Password Hashing
│
├── MySQL 5.7+
│   ├── InnoDB Engine
│   ├── UTF8MB4 Charset
│   └── ACID Compliance
│
└── Apache
    ├── mod_rewrite
    ├── .htaccess
    └── PHP Module

Frontend
├── HTML5
├── CSS3
├── JavaScript (ES6+)
│   ├── Fetch API
│   ├── LocalStorage
│   └── DOM Manipulation
│
└── Bootstrap 5
    ├── Grid System
    ├── Components
    └── Icons

Development
├── No Build Tools
├── No Dependencies
├── Pure PHP
└── Vanilla JavaScript
```

---

## 🌐 Deployment Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  HOSTINGER DEPLOYMENT                        │
└─────────────────────────────────────────────────────────────┘

Hostinger Server
│
├── Apache Web Server
│   ├── public_html/
│   │   ├── index.html (Frontend)
│   │   ├── admin/ (Admin Panel)
│   │   ├── api/ (REST API)
│   │   ├── assets/ (Static Files)
│   │   └── config/ (Configuration)
│   │
│   └── .htaccess (Rules)
│
├── MySQL Database
│   ├── Database: u123_portfolio
│   ├── User: u123_admin
│   └── Tables: 9 tables
│
├── PHP 7.4+
│   ├── Extensions: PDO, PDO_MySQL
│   └── Configuration: php.ini
│
└── SSL Certificate
    └── Free AutoSSL (HTTPS)
```

---

## 📊 Data Relationships

```
┌─────────────────────────────────────────────────────────────┐
│                    TABLE RELATIONSHIPS                       │
└─────────────────────────────────────────────────────────────┘

admin_users (1) ──── manages ───▶ (∞) All Content Tables
                                      │
                                      ├─▶ about
                                      ├─▶ skills
                                      ├─▶ experience
                                      ├─▶ education
                                      ├─▶ portfolio
                                      ├─▶ services
                                      ├─▶ contact_info
                                      └─▶ testimonials

Note: No foreign keys (simplified for shared hosting)
All tables independent for easy management
```

---

## 🎯 System Requirements

```
┌─────────────────────────────────────────────────────────────┐
│                   SYSTEM REQUIREMENTS                        │
└─────────────────────────────────────────────────────────────┘

Minimum Requirements
├── PHP: 7.4+
├── MySQL: 5.7+
├── Apache: 2.4+
├── Disk Space: 10MB
├── RAM: 64MB
└── Bandwidth: Minimal

Recommended
├── PHP: 8.0+
├── MySQL: 8.0+
├── SSL: Enabled
├── Disk Space: 50MB
├── RAM: 128MB
└── Backup: Enabled

Browser Support
├── Chrome: Latest
├── Firefox: Latest
├── Safari: Latest
├── Edge: Latest
└── Mobile: iOS 12+, Android 8+
```

---

**Architecture Version**: 1.0.0
**Last Updated**: January 2025
**Designed for**: Hostinger Shared Hosting
**Scalability**: Suitable for personal portfolio (100-1000 visitors/day)
