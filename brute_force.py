import requests
import time
from datetime import datetime

# Configuration
BASE_URL = "http://localhost:8000/"
LOGIN_URL = f"{BASE_URL}login.php"
USERNAME = "admin"  # We'll focus on one user to see the lockout
PASSWORDS = [
    "wrongpass1",
    "wrongpass2",
    "wrongpass3",
    "wrongpass4",
    "wrongpass5",
    "wrongpass6",  # This should trigger lockout
    "Admin@123456789"  # Correct password won't work during lockout
]

def try_login(username, password):
    print(f"\n[{datetime.now()}] Attempting login with {username}:{password}")
    
    session = requests.Session()
    
    try:
        # Try to login
        response = session.post(LOGIN_URL, data={
            "username": username,
            "password": password
        })
        
        # Check if we got redirected to dashboard (successful login)
        if "dashboard.php" in response.url:
            print("[+] SUCCESS! Login successful!")
            return True
            
        # Check for lockout message
        if "Account is locked" in response.text:
            print("[-] ACCOUNT LOCKED! Further attempts will be blocked")
            return "LOCKED"
            
        print("[-] Login failed")
        return False
        
    except Exception as e:
        print(f"[-] Error occurred: {str(e)}")
        return False

def main():
    print("[*] Starting brute force attack with new security measures...")
    print(f"[*] Target: {LOGIN_URL}")
    print(f"[*] Username: {USERNAME}")
    print(f"[*] Number of passwords to try: {len(PASSWORDS)}")
    
    for password in PASSWORDS:
        result = try_login(USERNAME, password)
        
        if result == "LOCKED":
            print("\n[!] Account is now locked. Waiting 30 seconds to try again...")
            time.sleep(30)
            
            # Try one more time to demonstrate lockout is still active
            print("\n[*] Trying again after waiting...")
            if try_login(USERNAME, "Admin@123456789") == "LOCKED":
                print("[!] Account is still locked as expected!")
                break
                
        elif result is True:
            break
            
        time.sleep(1)  # Small delay between attempts

if __name__ == "__main__":
    main() 