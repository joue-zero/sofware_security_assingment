# REvil (Sodinokibi) Ransomware Technical Analysis

## 1. Reconnaissance
- **Initial Access**: REvil operators primarily used:
  - Exploiting vulnerable RDP servers (CVE-2019-19781 in Citrix ADC)
  - Phishing emails with weaponized Office documents
  - Compromised MSP (Managed Service Provider) credentials
  - Exploiting Pulse Secure VPN vulnerabilities (CVE-2019-11510)

## 2. Weaponization
- **Delivery Mechanism**:
  - Custom PowerShell scripts embedded in Office documents
  - DLL sideloading using legitimate executables
  - Encrypted payloads delivered through compromised websites
  - Custom dropper written in C++ with anti-analysis capabilities

## 3. Delivery
- **Initial Infection Vectors**:
  ```powershell
  # Example of initial PowerShell payload
  $enc = [Convert]::FromBase64String("BASE64_ENCODED_PAYLOAD");
  $key = [System.Text.Encoding]::UTF8.GetBytes("ENCRYPTION_KEY");
  $iv = [System.Text.Encoding]::UTF8.GetBytes("INITIALIZATION_VECTOR");
  $aes = New-Object System.Security.Cryptography.AesManaged;
  $aes.Key = $key;
  $aes.IV = $iv;
  $dec = $aes.CreateDecryptor().TransformFinalBlock($enc, 0, $enc.Length);
  [System.Reflection.Assembly]::Load($dec);
  ```

## 4. Exploitation
- **Privilege Escalation**:
  - Exploited Windows Print Spooler vulnerability (CVE-2021-34527)
  - Abused Windows Task Scheduler for persistence
  - Used Windows Management Instrumentation (WMI) for lateral movement
  - Exploited Windows Defender exclusion paths

## 5. Installation
- **Persistence Mechanisms**:
  - Registry modifications:
    ```reg
    HKEY_CURRENT_USER\Software\Microsoft\Windows\CurrentVersion\Run
    HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Run
    ```
  - Scheduled tasks creation
  - WMI event subscriptions
  - Service installation

## 6. Command & Control (C2)
- **Communication Protocol**:
  - Custom encrypted protocol over HTTPS
  - Domain Generation Algorithm (DGA) for C2 server rotation
  - Tor hidden services for command and control
  - Fallback mechanisms using legitimate cloud services

## 7. Actions on Objectives
- **Encryption Mechanism**:
  - Hybrid encryption using:
    - RSA-4096 for file encryption keys
    - Salsa20 stream cipher for file content encryption
    - Custom file extension (.REvil)
  - Selective file targeting:
    ```c
    // Example of file targeting logic
    const char* excluded_extensions[] = {
        ".exe", ".dll", ".sys", ".msi", ".REvil"
    };
    const char* targeted_extensions[] = {
        ".doc", ".docx", ".xls", ".xlsx", ".pdf", ".jpg", ".png"
    };
    ```

- **Lateral Movement Techniques**:
  - Windows Management Instrumentation (WMI)
  - PsExec deployment
  - Remote Desktop Protocol (RDP)
  - Server Message Block (SMB) exploitation
  - Windows Remote Management (WinRM)

## Technical Mitigation Strategies

1. **Prevention**:
   - Implement application whitelisting
   - Disable macro execution in Office documents
   - Patch vulnerable services (RDP, SMB, etc.)
   - Implement network segmentation
   - Use multi-factor authentication for RDP

2. **Detection**:
   - Monitor for suspicious file operations
   - Implement EDR solutions
   - Monitor for unusual network traffic patterns
   - Set up SIEM alerts for suspicious activities

3. **Response**:
   - Maintain offline backups
   - Implement incident response plan
   - Regular security assessments
   - Network traffic analysis

## Technical Indicators of Compromise (IOCs)

1. **File System**:
   - Creation of ransom note (README.txt)
   - File extension changes to .REvil
   - Suspicious file operations in system directories

2. **Network**:
   - Unusual outbound connections to Tor nodes
   - Suspicious DNS queries
   - Unusual SMB traffic patterns

3. **Registry**:
   - Modified autorun keys
   - Suspicious service installations
   - Modified security policies

## Encryption Technical Details

1. **Key Generation**:
   ```c
   // Pseudo-code for key generation
   void generate_keys() {
       RSA_key = generate_rsa_key(4096);
       session_key = generate_random_bytes(32);
       file_key = generate_random_bytes(32);
   }
   ```

2. **File Encryption Process**:
   ```c
   // Pseudo-code for file encryption
   void encrypt_file(file) {
       file_key = generate_random_bytes(32);
       encrypted_file_key = RSA_encrypt(file_key, RSA_key);
       encrypted_content = Salsa20_encrypt(file_content, file_key);
       write_encrypted_file(encrypted_content, encrypted_file_key);
   }
   ```

## Cyber Kill Chain Mapping

| Kill Chain Phase | REvil Implementation |
|------------------|----------------------|
| Reconnaissance   | RDP scanning, vulnerability assessment |
| Weaponization    | Custom PowerShell scripts, encrypted payloads |
| Delivery         | Phishing emails, compromised websites |
| Exploitation     | RDP/SMB vulnerabilities, privilege escalation |
| Installation     | Registry modifications, scheduled tasks |
| Command & Control| Tor-based C2, DGA for server rotation |
| Actions on Objectives | File encryption, data exfiltration |

## Conclusion

This technical analysis demonstrates the sophisticated nature of the REvil ransomware attack and its alignment with the Cyber Kill Chain framework. The attack combines multiple exploitation techniques, advanced encryption mechanisms, and sophisticated lateral movement capabilities, making it one of the most dangerous ransomware strains in recent history.

Understanding these technical details is crucial for implementing effective security measures and developing robust defense strategies against similar ransomware attacks. 