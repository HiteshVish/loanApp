// Laravel Auth - Glassmorphism Login & Register Form JavaScript

class AuthForm {
    constructor() {
        this.loginForm = document.getElementById('loginForm');
        this.registerForm = document.getElementById('registerForm');
        this.authTabs = document.querySelectorAll('.auth-tab');
        this.tabSlider = document.querySelector('.tab-slider');
        this.successMessage = document.getElementById('successMessage');
        
        this.activeTab = 'login';
        
        this.init();
    }
    
    init() {
        this.setupTabs();
        this.setupPasswordToggles();
        this.setupFloatingLabels();
        this.setupForms();
        this.setupSocialButtons();
        this.addEntranceAnimation();
    }
    
    // Tab Switching
    setupTabs() {
        this.authTabs.forEach(tab => {
            tab.addEventListener('click', (e) => this.handleTabClick(e));
        });
    }
    
    handleTabClick(e) {
        const tab = e.currentTarget;
        const targetTab = tab.getAttribute('data-tab');
        
        if (targetTab === this.activeTab) return;
        
        // Update active tab
        this.authTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Show/hide form containers
        document.querySelectorAll('.auth-form-container').forEach(container => {
            container.classList.remove('active');
        });
        
        const targetContainer = document.getElementById(`${targetTab}-form-container`);
        if (targetContainer) {
            targetContainer.classList.add('active');
        }
        
        this.activeTab = targetTab;
        
        // Add ripple effect to slider
        this.tabSlider.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    }
    
    // Password Toggle
    setupPasswordToggles() {
        const passwordToggles = document.querySelectorAll('.password-toggle');
        
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const targetId = toggle.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const eyeIcon = toggle.querySelector('.eye-icon');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.classList.add('show-password');
                } else {
                    input.type = 'password';
                    eyeIcon.classList.remove('show-password');
                }
            });
        });
    }
    
    // Floating Labels
    setupFloatingLabels() {
        const inputs = document.querySelectorAll('.input-wrapper input');
        
        inputs.forEach(input => {
            // Check initial state
            if (input.value) {
                input.classList.add('has-value');
            }
            
            input.addEventListener('input', () => {
                if (input.value) {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            });
            
            input.addEventListener('focus', () => {
                input.closest('.input-wrapper')?.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.closest('.input-wrapper')?.classList.remove('focused');
                this.validateField(input);
            });
        });
    }
    
    // Form Setup
    setupForms() {
        if (this.loginForm) {
            this.loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }
        
        if (this.registerForm) {
            this.registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }
    }
    
    // Handle Login
    async handleLogin(e) {
        e.preventDefault();
        
        const submitBtn = this.loginForm.querySelector('.auth-btn');
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        
        // Client-side validation
        let isValid = true;
        
        if (!this.validateEmail(email)) {
            this.showError('login-email', 'Please enter a valid email address');
            isValid = false;
        } else {
            this.clearError('login-email');
        }
        
        if (!password || password.length < 6) {
            this.showError('login-password', 'Password must be at least 6 characters');
            isValid = false;
        } else {
            this.clearError('login-password');
        }
        
        if (!isValid) {
            this.shakeForm(this.loginForm);
            return;
        }
        
        // Show loading state
        submitBtn.classList.add('loading');
        
        // Submit the form normally (Laravel handles the redirect)
        this.loginForm.submit();
    }
    
    // Handle Register
    async handleRegister(e) {
        e.preventDefault();
        
        const submitBtn = this.registerForm.querySelector('.auth-btn');
        const name = document.getElementById('register-name').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const passwordConfirmation = document.getElementById('register-password_confirmation').value;
        
        // Client-side validation
        let isValid = true;
        
        if (!name || name.length < 2) {
            this.showError('register-name', 'Name must be at least 2 characters');
            isValid = false;
        } else {
            this.clearError('register-name');
        }
        
        if (!this.validateEmail(email)) {
            this.showError('register-email', 'Please enter a valid email address');
            isValid = false;
        } else {
            this.clearError('register-email');
        }
        
        if (!password || password.length < 8) {
            this.showError('register-password', 'Password must be at least 8 characters');
            isValid = false;
        } else {
            this.clearError('register-password');
        }
        
        if (password !== passwordConfirmation) {
            this.showError('register-password_confirmation', 'Passwords do not match');
            isValid = false;
        } else {
            this.clearError('register-password_confirmation');
        }
        
        if (!isValid) {
            this.shakeForm(this.registerForm);
            return;
        }
        
        // Show loading state
        submitBtn.classList.add('loading');
        
        // Submit the form normally (Laravel handles the redirect)
        this.registerForm.submit();
    }
    
    // Validate Field
    validateField(input) {
        const value = input.value.trim();
        const fieldId = input.id;
        
        if (!value) return;
        
        if (fieldId.includes('email')) {
            if (!this.validateEmail(value)) {
                this.showError(fieldId, 'Please enter a valid email address');
            } else {
                this.clearError(fieldId);
            }
        } else if (fieldId.includes('password') && !fieldId.includes('confirmation')) {
            if (value.length < 8) {
                this.showError(fieldId, 'Password must be at least 8 characters');
            } else {
                this.clearError(fieldId);
            }
        }
    }
    
    // Email Validation
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
    
    // Show Error
    showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        const formGroup = input?.closest('.form-group');
        const errorElement = document.getElementById(`${fieldId}Error`);
        
        if (formGroup) {
            formGroup.classList.add('error');
        }
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }
    
    // Clear Error
    clearError(fieldId) {
        const input = document.getElementById(fieldId);
        const formGroup = input?.closest('.form-group');
        const errorElement = document.getElementById(`${fieldId}Error`);
        
        if (formGroup) {
            formGroup.classList.remove('error');
        }
        
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }
    }
    
    // Shake Form
    shakeForm(form) {
        form.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            form.style.animation = '';
        }, 500);
    }
    
    // Social Buttons
    setupSocialButtons() {
        const socialButtons = document.querySelectorAll('.social-btn');
        
        socialButtons.forEach(btn => {
            // Add hover animation for visual feedback
            btn.addEventListener('mousedown', (e) => {
                btn.style.transform = 'scale(0.95)';
            });
            
            btn.addEventListener('mouseup', (e) => {
                setTimeout(() => {
                    btn.style.transform = 'scale(1)';
                }, 150);
            });
        });
    }
    
    // Show Notification
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 16px 24px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        `;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Entrance Animation
    addEntranceAnimation() {
        const authCard = document.querySelector('.auth-card');
        if (authCard) {
            authCard.style.opacity = '0';
            authCard.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                authCard.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                authCard.style.opacity = '1';
                authCard.style.transform = 'translateY(0)';
            }, 100);
        }
    }
}

// Add notification animations via CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AuthForm();
});

// Handle page visibility changes
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        const activeElement = document.activeElement;
        if (activeElement && activeElement.tagName !== 'INPUT') {
            const emailInput = document.querySelector('#login-email');
            if (emailInput && !emailInput.value) {
                setTimeout(() => emailInput.focus(), 100);
            }
        }
    }
});

