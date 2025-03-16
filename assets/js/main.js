document.addEventListener('DOMContentLoaded', function() {
    // Image URL preview
    const imageUrlInput = document.getElementById('image_url');
    const imagePreview = document.getElementById('image_preview');
    
    if (imageUrlInput && imagePreview) {
        imageUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                imagePreview.innerHTML = `<img src="${url}" alt="Preview" class="img-fluid mb-3">`;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.innerHTML = '';
                imagePreview.style.display = 'none';
            }
        });
    }
    
    // Form validation
    const postForm = document.getElementById('postForm');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            const imageUrl = document.getElementById('image_url').value.trim();
            const postText = document.getElementById('post_text').value.trim();
            
            if (!imageUrl || !postText) {
                e.preventDefault();
                alert('Please provide both an image URL and post text.');
            }
        });
    }
    
    // Character counter for text areas
    const textAreas = document.querySelectorAll('textarea[maxlength]');
    textAreas.forEach(textarea => {
        const counter = document.createElement('div');
        counter.className = 'text-muted small mt-1';
        counter.innerHTML = `<span>${textarea.value.length}</span>/${textarea.maxLength} characters`;
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);
        
        textarea.addEventListener('input', function() {
            counter.querySelector('span').textContent = this.value.length;
        });
    });
    
    // Tooltips initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Fade in animations
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '1';
        }, 100 * index);
    });
});
