// General JavaScript functions for the application

// Function to show loading state
function showLoading() {
    const loadingElements = document.querySelectorAll('.loading');
    loadingElements.forEach(el => el.style.display = 'block');
}

// Function to hide loading state
function hideLoading() {
    const loadingElements = document.querySelectorAll('.loading');
    loadingElements.forEach(el => el.style.display = 'none');
}

// Function to show success message
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        font-weight: bold;
        animation: slideIn 0.3s ease-in-out;
    `;
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.color = '#155724';
        messageDiv.style.border = '1px solid #c3e6cb';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
    }
    
    document.body.appendChild(messageDiv);
    
    // Remove message after 3 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Function to validate email format
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Function to validate password strength
function isValidPassword(password) {
    return password.length >= 6;
}

// Add keyframe animation for messages
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Auto-focus first input field on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.querySelector('input[type="text"], input[type="email"], input[type="password"]');
    if (firstInput) {
        firstInput.focus();
    }
});
