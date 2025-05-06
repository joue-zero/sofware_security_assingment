# Vulnerable Banking Application

This is a deliberately vulnerable banking application that demonstrates OWASP Top 10 vulnerabilities. It is intended for educational purposes only.

## Setup Instructions

1. Create a MySQL database and import the schema:
```bash
mysql -u root -p < schema.sql
```

2. Configure the database connection in `config.php`

3. Create an uploads directory:
```bash
mkdir uploads
chmod 777 uploads
```

4. Start a PHP server:
```bash
php -S localhost:8000
```

## OWASP Top 10 Vulnerabilities and Exploitation Scenarios

### 1. Injection (A1:2017)
- **Location**: Multiple files using direct SQL queries
- **Exploitation**: 
  - Login bypass: `' OR '1'='1`
  - SQL injection in transfer: `' OR '1'='1' -- `
  - Impact: Unauthorized access, data manipulation

### 2. Broken Authentication (A2:2017)
- **Location**: `login.php`, `schema.sql`
- **Exploitation**:
  - Weak passwords stored in plain text
  - No password complexity requirements
  - Impact: Easy password cracking, unauthorized access

### 3. Sensitive Data Exposure (A3:2017)
- **Location**: `login.php`
- **Exploitation**:
  - Passwords stored in session
  - No encryption for sensitive data
  - Impact: Session hijacking, data theft

### 4. XML External Entities (XXE) (A4:2017)
- **Location**: `complaint.php`
- **Exploitation**:
  - Upload XML files with external entity references
  - Impact: Server-side request forgery, file disclosure

### 5. Broken Access Control (A5:2017)
- **Location**: Multiple files
- **Exploitation**:
  - Direct access to admin pages
  - Manipulate session variables
  - Impact: Privilege escalation

### 6. Security Misconfiguration (A6:2017)
- **Location**: `complaint.php`
- **Exploitation**:
  - Upload malicious files
  - Access to sensitive directories
  - Impact: Remote code execution

### 7. Cross-Site Scripting (XSS) (A7:2017)
- **Location**: `message.php`, `dashboard.php`
- **Exploitation**:
  - Inject JavaScript in messages
  - Steal session cookies
  - Impact: Session hijacking, malware distribution

### 8. Insecure Deserialization (A8:2017)
- **Location**: `dashboard.php`
- **Exploitation**:
  - Manipulate transaction data
  - Impact: Data integrity compromise

### 9. Using Components with Known Vulnerabilities (A9:2017)
- **Location**: All files
- **Exploitation**:
  - Outdated PHP version
  - Unpatched MySQL
  - Impact: Various depending on vulnerabilities

### 10. Insufficient Logging & Monitoring (A10:2017)
- **Location**: `admin.php`
- **Exploitation**:
  - Basic logging without proper monitoring
  - No alerting system
  - Impact: Delayed detection of attacks

## Disclaimer

This application is intentionally vulnerable and should only be used for educational purposes in a controlled environment. Do not deploy this application in a production environment or expose it to the internet. 