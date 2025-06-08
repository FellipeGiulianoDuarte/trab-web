<?php
// Session Manager with Cookie Support
// This class handles session management with cookie persistence

class SessionManager {
    private static $cookie_name = 'user_session';
    private static $cookie_expiry = 86400 * 7; // 7 days in seconds
    private static $secret_key = 'chavesecreta!'; // Change this in production
    
    /**
     * Create a secure session with cookie persistence
     */
    public static function createSession($user_id, $username, $remember_me = false) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        // If remember me is checked, create persistent cookie
        if ($remember_me) {
            $cookie_data = [
                'user_id' => $user_id,
                'username' => $username,
                'timestamp' => time()
            ];
            
            // Create a secure token
            $token = self::createSecureToken($cookie_data);
            
            // Set cookie for 7 days
            $expire_time = time() + self::$cookie_expiry;
            setcookie(self::$cookie_name, $token, $expire_time, "/", "", false, true); // httponly for security
        }
    }
    
    /**
     * Check if user is authenticated (session or cookie)
     */
    public static function isAuthenticated() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check session first
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        
        // Check cookie if session is not set
        if (isset($_COOKIE[self::$cookie_name])) {
            return self::validateCookieSession();
        }
        
        return false;
    }
    
    /**
     * Get current user data
     */
    public static function getCurrentUser() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ];
        }
        
        return null;
    }
    
    /**
     * Validate cookie session and restore session variables
     */
    private static function validateCookieSession() {
        if (!isset($_COOKIE[self::$cookie_name])) {
            return false;
        }
        
        $cookie_data = self::decodeSecureToken($_COOKIE[self::$cookie_name]);
        
        if (!$cookie_data) {
            // Invalid cookie, remove it
            setcookie(self::$cookie_name, "", time() - 3600, "/");
            return false;
        }
        
        // Check if cookie is not expired (7 days)
        if (time() - $cookie_data['timestamp'] > self::$cookie_expiry) {
            // Cookie expired, remove it
            setcookie(self::$cookie_name, "", time() - 3600, "/");
            return false;
        }
        
        // Restore session from cookie
        $_SESSION['user_id'] = $cookie_data['user_id'];
        $_SESSION['username'] = $cookie_data['username'];
        $_SESSION['login_time'] = $cookie_data['timestamp'];
        
        return true;
    }
    
    /**
     * Create a secure token for cookie
     */
    private static function createSecureToken($data) {
        $json_data = json_encode($data);
        $encoded_data = base64_encode($json_data);
        $hash = hash_hmac('sha256', $encoded_data, self::$secret_key);
        
        return $encoded_data . '.' . $hash;
    }
    
    /**
     * Decode and validate secure token
     */
    private static function decodeSecureToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $encoded_data = $parts[0];
        $provided_hash = $parts[1];
        
        // Verify hash
        $expected_hash = hash_hmac('sha256', $encoded_data, self::$secret_key);
        
        if (!hash_equals($expected_hash, $provided_hash)) {
            return false;
        }
        
        // Decode data
        $json_data = base64_decode($encoded_data);
        $data = json_decode($json_data, true);
        
        return $data;
    }
    
    /**
     * Destroy session and remove cookies
     */
    public static function destroySession() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        session_unset();
        
        // Destroy the session
        session_destroy();
        
        // Remove the cookie
        if (isset($_COOKIE[self::$cookie_name])) {
            setcookie(self::$cookie_name, "", time() - 3600, "/");
        }
    }
    
    /**
     * Refresh cookie expiry (call this on user activity)
     */
    public static function refreshCookie() {
        if (isset($_COOKIE[self::$cookie_name])) {
            $cookie_data = self::decodeSecureToken($_COOKIE[self::$cookie_name]);
            if ($cookie_data) {
                // Update timestamp and recreate cookie
                $cookie_data['timestamp'] = time();
                $token = self::createSecureToken($cookie_data);
                $expire_time = time() + self::$cookie_expiry;
                setcookie(self::$cookie_name, $token, $expire_time, "/", "", false, true);
            }
        }
    }
}
?>
