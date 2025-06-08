<?php
class SessionManager {
    private static $cookie_name = 'user_session';
    private static $cookie_expiry = 86400 * 7;
    private static $secret_key = 'chavesecreta!';
    
    public static function createSession($user_id, $username, $remember_me = false) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        if ($remember_me) {
            $cookie_data = [
                'user_id' => $user_id,
                'username' => $username,
                'timestamp' => time()
            ];
            
            $token = self::createSecureToken($cookie_data);
            
            $expire_time = time() + self::$cookie_expiry;
            setcookie(self::$cookie_name, $token, $expire_time, "/", "", false, true);
        }
    }
    
    public static function isAuthenticated() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        
        if (isset($_COOKIE[self::$cookie_name])) {
            return self::validateCookieSession();
        }
        
        return false;
    }
    
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
    
    private static function validateCookieSession() {
        if (!isset($_COOKIE[self::$cookie_name])) {
            return false;
        }
        
        $cookie_data = self::decodeSecureToken($_COOKIE[self::$cookie_name]);
        
        if (!$cookie_data) {
            setcookie(self::$cookie_name, "", time() - 3600, "/");
            return false;
        }
        
        if (time() - $cookie_data['timestamp'] > self::$cookie_expiry) {
            setcookie(self::$cookie_name, "", time() - 3600, "/");
            return false;
        }
        
        $_SESSION['user_id'] = $cookie_data['user_id'];
        $_SESSION['username'] = $cookie_data['username'];
        $_SESSION['login_time'] = $cookie_data['timestamp'];
        
        return true;
    }
    
    private static function createSecureToken($data) {
        $json_data = json_encode($data);
        $encoded_data = base64_encode($json_data);
        $hash = hash_hmac('sha256', $encoded_data, self::$secret_key);
        
        return $encoded_data . '.' . $hash;
    }
    
    private static function decodeSecureToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $encoded_data = $parts[0];
        $provided_hash = $parts[1];
        
        $expected_hash = hash_hmac('sha256', $encoded_data, self::$secret_key);
        
        if (!hash_equals($expected_hash, $provided_hash)) {
            return false;
        }
        
        $json_data = base64_decode($encoded_data);
        $data = json_decode($json_data, true);
        
        return $data;
    }
    
    public static function destroySession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        if (isset($_COOKIE[self::$cookie_name])) {
            setcookie(self::$cookie_name, "", time() - 3600, "/");
        }
    }
    
    public static function refreshCookie() {
        if (isset($_COOKIE[self::$cookie_name])) {
            $cookie_data = self::decodeSecureToken($_COOKIE[self::$cookie_name]);
            if ($cookie_data) {
                $cookie_data['timestamp'] = time();
                $token = self::createSecureToken($cookie_data);
                $expire_time = time() + self::$cookie_expiry;
                setcookie(self::$cookie_name, $token, $expire_time, "/", "", false, true);
            }
        }
    }
}
?>
