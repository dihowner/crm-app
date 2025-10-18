// Click Control JavaScript for Order Forms
// This script prevents multiple form submissions and provides user feedback

document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    const submitBtn = document.getElementById('submitBtn');

    if (orderForm && submitBtn) {
        // Prevent multiple submissions
        orderForm.addEventListener('submit', function(e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Disable the submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';
            submitBtn.style.opacity = '0.6';

            // Re-enable after 5 seconds as a safety measure
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'ORDER NOW';
                submitBtn.style.opacity = '1';
            }, 5000);
        });

        // Store original button text
        if (!submitBtn.getAttribute('data-original-text')) {
            submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
        }
    }

    // Add loading animation to form inputs
    const inputs = document.querySelectorAll('#orderForm input, #orderForm select, #orderForm textarea');
    inputs.forEach(function(input) {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#28a745';
        });

        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
    });

    // Form validation feedback
    const requiredFields = document.querySelectorAll('#orderForm [required]');
    requiredFields.forEach(function(field) {
        field.addEventListener('invalid', function() {
            this.style.borderColor = '#dc3545';
            this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
        });

        field.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = '#28a745';
                this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
            }
        });
    });
});

// Copy form HTML to clipboard function (for admin use)
function copyFormToClipboard(formHtml) {
    if (navigator.clipboard && window.isSecureContext) {
        // Use modern clipboard API
        navigator.clipboard.writeText(formHtml).then(function() {
            if (typeof bootbox !== 'undefined') {
                bootbox.alert({
                    message: 'Form HTML copied to clipboard successfully!',
                    buttons: { ok: { label: 'OK', className: 'btn-primary' } }
                });
            } else {
                alert('Form HTML copied to clipboard successfully!');
            }
        }).catch(function(err) {
            console.error('Failed to copy to clipboard: ', err);
            fallbackCopyTextToClipboard(formHtml);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(formHtml);
    }
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            if (typeof bootbox !== 'undefined') {
                bootbox.alert({
                    message: 'Form HTML copied to clipboard successfully!',
                    buttons: { ok: { label: 'OK', className: 'btn-primary' } }
                });
            } else {
                alert('Form HTML copied to clipboard successfully!');
            }
        } else {
            throw new Error('Copy command was unsuccessful');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        if (typeof bootbox !== 'undefined') {
            bootbox.alert({
                message: 'Failed to copy form HTML. Please try again.',
                buttons: { ok: { label: 'OK', className: 'btn-primary' } }
            });
        } else {
            alert('Failed to copy form HTML. Please try again.');
        }
    }

    document.body.removeChild(textArea);
}
