# 🟢 Retest Report: Validation of Security Fixes

| Vulnerability (OWASP)         | Test Performed                                                                 | Result (Fixed?) | Notes                                                                                 |
|-------------------------------|-------------------------------------------------------------------------------|-----------------|---------------------------------------------------------------------------------------|
| **A1: Injection**             | Attempted SQL injection in login and transfer forms                           | ✅ Yes          | Prepared statements used everywhere; injection attempts fail.                         |
| **A2: Broken Authentication** | Tried login with wrong password, session hijack, and checked password storage | ✅ Yes          | Passwords are hashed, not stored in session; session checks enforced.                 |
| **A3: Sensitive Data Exposure**| Checked session and DB for plain passwords                                    | ✅ Yes          | No sensitive data in session or DB; only hashes stored.                               |
| **A4: XXE**                   | Tried uploading XML with XXE payload                                          | ✅ Yes          | File type/size restrictions block XML and other dangerous files.                      |
| **A5: Broken Access Control** | Tried accessing admin/user pages as wrong role                                | ✅ Yes          | Role and session checks enforced everywhere.                                          |
| **A6: Security Misconfiguration**| Tried uploading PHP, .htaccess, and large files                             | ✅ Yes          | Only images and PDFs <2MB allowed; safe file naming.                                  |
| **A7: XSS**                   | Injected `<script>` in messages/complaints                                   | ✅ Yes          | All output is escaped with `htmlspecialchars()`.                                      |
| **A8: Insecure Deserialization**| Manipulated transfer/complaint/message data                                 | ✅ Yes          | All input validated and handled with prepared statements.                             |
| **A9: Known Vulnerabilities** | Checked for outdated PHP/MySQL, insecure libraries                            | ✅ Yes          | No known vulnerable libraries used; recommend keeping PHP/MySQL updated.              |
| **A10: Insufficient Logging** | Performed actions and checked admin logs                                      | ✅ Yes          | All key actions are logged and viewable by admin.                                     |

---

# 🟢 Comparison Table: Vulnerable vs. Fixed Code

| Vulnerability | Vulnerable Code Example | Fixed Code Example | Description of Fix |
|---------------|------------------------|--------------------|--------------------|
| **A1: Injection** | `$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";` | `$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?"); $stmt->bind_param("s", $username);` | Used prepared statements to prevent SQL injection. |
| **A2: Broken Auth** | `password` stored in session and DB as plain text | `password_hash()` in DB, `password_verify()` on login, not stored in session | Passwords are hashed and never stored in session. |
| **A3: Sensitive Data** | `$_SESSION['password'] = $password;` | (Removed) | No sensitive data in session. |
| **A4: XXE/File Upload** | `move_uploaded_file($file_tmp, $upload_dir . $file_name);` | File type/size checks, safe file naming | Only safe file types and names allowed. |
| **A5: Access Control** | No role/session checks on admin pages | `if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { ... }` | Enforced session and role checks. |
| **A6: Misconfiguration** | Any file type/size accepted | Only images/PDFs <2MB, safe names | Prevents dangerous uploads. |
| **A7: XSS** | `echo $msg['message'];` | `echo htmlspecialchars($msg['message']);` | Escaped all output to prevent XSS. |
| **A8: Insecure Deserialization** | Directly used POST data in queries | All input validated, prepared statements | No unsafe deserialization or direct use. |
| **A9: Known Vulns** | (N/A) | (N/A) | Use up-to-date PHP/MySQL. |
| **A10: Logging** | No or file-based logging | All actions logged in DB, viewable by admin | Centralized, queryable logging. |

---

# 🟢 Description of Implemented Fixes

- **SQL Injection:** All database queries now use prepared statements.
- **Password Security:** Passwords are hashed using `password_hash()` and checked with `password_verify()`.
- **Session Security:** No sensitive data is stored in session; session checks are enforced everywhere.
- **File Uploads:** Only safe file types and sizes are allowed; files are renamed to prevent path traversal.
- **XSS:** All user-generated output is escaped with `htmlspecialchars()`.
- **CSRF:** All forms include a CSRF token, which is checked on POST.
- **Access Control:** All pages check for the correct session and role before allowing access.
- **Logging:** All key actions (login, logout, transfer, complaint, message) are logged in the database and viewable by admin.
- **General:** No output before headers, session is always started at the top, and all user input is validated.

---

# 🟢 Conclusion

- All vulnerabilities from Assignment 1 have been fixed.
- The application now follows secure coding best practices.
- All fixes are commented in the code for clarity. 