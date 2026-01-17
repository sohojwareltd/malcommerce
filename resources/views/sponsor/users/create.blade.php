@extends('layouts.sponsor')

@section('title', 'Add New User')

@section('content')
<style>
    :root {
        --color-dark: #0F2854;
        --color-medium: #1C4D8D;
        --color-light: #4988C4;
        --color-accent: #BDE8F5;
    }
    
    .app-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(15, 40, 84, 0.08);
    }
</style>

<div class="min-h-screen pb-6">
    <!-- Header -->
    <div class="app-card mx-4 mt-4 mb-4 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-base sm:text-lg md:text-xl font-bold" style="color: var(--color-dark);">Add New User</h1>
                <p class="text-xs sm:text-sm mt-1" style="color: var(--color-medium);">Add a new user who will be automatically referred by you</p>
            </div>
            <a href="{{ route('sponsor.dashboard') }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap" style="background: var(--color-accent); color: var(--color-dark);">
                ← Back
            </a>
        </div>
    </div>

    <!-- Add User Form -->
    <div class="app-card mx-4 mb-4 p-3 sm:p-4 md:p-6">
        <form id="add-user-form" class="space-y-3 sm:space-y-4 md:space-y-5">
            @csrf
            <!-- Profile Photo -->
            <div class="mb-3 sm:mb-4 md:mb-6">
                <label class="block text-xs sm:text-sm font-medium mb-2 sm:mb-3" style="color: var(--color-dark);">Profile Photo</label>
                <div class="flex flex-col items-center gap-3">
                    <div class="relative group">
                        <input type="file" name="photo" id="user-photo" accept="image/*" class="hidden">
                        <label for="user-photo" class="cursor-pointer">
                            <div id="photo-preview-container" class="relative">
                                <div id="photo-preview-placeholder" class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full bg-gradient-to-br flex items-center justify-center border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90" style="background: linear-gradient(135deg, var(--color-light)/20 0%, var(--color-accent)/30 100%); border-color: var(--color-light)/30;">
                                    <svg class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16" style="color: var(--color-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <img id="photo-preview" src="#" alt="Profile Photo Preview" class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90 hidden" style="border-color: var(--color-medium);">
                                <div id="photo-overlay" class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </label>
                        <div id="preview-loading" class="hidden mt-2 text-center">
                            <div class="inline-block animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2" style="border-color: var(--color-medium);"></div>
                            <p class="text-xs mt-1" style="color: var(--color-medium);">Loading...</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-xs sm:text-sm" style="color: var(--color-medium);">Click the circle to upload photo</p>
                        <p class="text-xs mt-0.5" style="color: var(--color-light);">JPG, PNG or GIF</p>
                        <p id="file-info" class="mt-1 text-xs text-green-600 hidden"></p>
                        <div id="photo-error" class="mt-1 text-xs sm:text-sm text-red-600 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="mb-3 sm:mb-4 md:mb-6">
                <label for="user-name" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="user-name" 
                    name="name" 
                    required 
                    autofocus
                    class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none"
                    style="border-color: var(--color-accent);"
                    placeholder="Enter user name"
                >
                <div id="name-error" class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600 hidden"></div>
            </div>

            <!-- Phone -->
            <div class="mb-3 sm:mb-4 md:mb-6">
                <label for="user-phone" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Phone Number <span class="text-red-500">*</span></label>
                <input 
                    type="tel" 
                    id="user-phone" 
                    name="phone" 
                    required 
                    class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none"
                    style="border-color: var(--color-accent);"
                    placeholder="01XXXXXXXXX"
                >
                <p class="mt-0.5 sm:mt-1 text-xs" style="color: var(--color-medium);">Enter 11-digit phone number (e.g., 01712345678)</p>
                <div id="phone-error" class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600 hidden"></div>
            </div>

            <!-- Address -->
            <div class="mb-3 sm:mb-4 md:mb-6">
                <label for="user-address" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Address</label>
                <textarea name="address" id="user-address" rows="2" 
                          class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none resize-none"
                          style="border-color: var(--color-accent);"
                          placeholder="Enter user's address"></textarea>
                <div id="address-error" class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600 hidden"></div>
            </div>

            <!-- Comment -->
            <div class="mb-3 sm:mb-4 md:mb-6">
                <label for="user-comment" class="block text-xs sm:text-sm font-medium mb-1 sm:mb-2" style="color: var(--color-dark);">Comment</label>
                <textarea name="comment" id="user-comment" rows="2" 
                          class="w-full px-3 sm:px-4 py-1.5 sm:py-2 text-sm sm:text-base border-2 rounded-xl focus:outline-none resize-none"
                          style="border-color: var(--color-accent);"
                          placeholder="Add any comments or notes about this user"></textarea>
                <div id="comment-error" class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-red-600 hidden"></div>
            </div>
            
            
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 pt-1">
                <button 
                    type="submit" 
                    id="add-user-btn"
                    class="px-4 sm:px-6 py-1.5 sm:py-2 md:py-3 rounded-lg text-white text-xs sm:text-sm font-semibold transition"
                    style="background: var(--color-medium);"
                >
                    Add User
                </button>
                <a 
                    href="{{ route('sponsor.dashboard') }}" 
                    class="px-4 sm:px-6 py-1.5 sm:py-2 md:py-3 rounded-lg text-xs sm:text-sm font-semibold text-center transition"
                    style="background: var(--color-accent); color: var(--color-dark);"
                >
                    Cancel
                </a>
            </div>
        </form>
        
        <div id="add-user-success" class="hidden mt-3 sm:mt-4 md:mt-6 p-2 sm:p-3 md:p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-green-900 mb-0.5 sm:mb-1">User Added Successfully!</h3>
                    <p class="text-xs sm:text-sm text-green-800" id="success-message"></p>
                    <div class="mt-2 sm:mt-3 md:mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <a 
                            href="{{ route('sponsor.dashboard') }}" 
                            class="inline-block px-3 sm:px-4 py-1.5 sm:py-2 bg-green-600 text-white text-xs sm:text-sm rounded-lg hover:bg-green-700 transition text-center"
                        >
                            Go to Dashboard
                        </a>
                        <button 
                            type="button"
                            onclick="resetForm()" 
                            class="px-3 sm:px-4 py-1.5 sm:py-2 border border-green-600 text-green-700 text-xs sm:text-sm rounded-lg hover:bg-green-50 transition"
                        >
                            Add Another User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 sm:mt-4 md:mt-6 text-center mx-4">
    <a href="{{ route('sponsor.users.index') }}" class="inline-flex items-center gap-1.5 sm:gap-2 text-primary hover:text-primary-light font-medium text-xs sm:text-sm">
        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        View All Referrals
    </a>
    </div>
</div>

@push('scripts')
<script>
// Format phone input
const userPhoneInput = document.getElementById('user-phone');
if (userPhoneInput) {
    userPhoneInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
}

// Photo preview functionality
const photoInput = document.getElementById('user-photo');
const photoPreviewContainer = document.getElementById('photo-preview-container');
const photoPreview = document.getElementById('photo-preview');
const photoPreviewPlaceholder = document.getElementById('photo-preview-placeholder');

if (photoInput) {
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Please select an image file.');
                photoInput.value = '';
                return;
            }
            
            // Create FileReader to read the file
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Hide placeholder
                if (photoPreviewPlaceholder) {
                    photoPreviewPlaceholder.style.display = 'none';
                }
                
                // Create or update preview image
                let previewImg = photoPreview;
                if (!previewImg) {
                    previewImg = document.createElement('img');
                    previewImg.id = 'photo-preview';
                    previewImg.className = 'w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-full object-cover border-[3px] sm:border-4 shadow-lg transition-all duration-300 group-hover:opacity-90';
                    previewImg.style.borderColor = 'var(--color-medium)';
                    previewImg.alt = 'Profile Photo Preview';
                    const overlay = photoPreviewContainer.querySelector('#photo-overlay');
                    if (overlay) {
                        photoPreviewContainer.insertBefore(previewImg, overlay);
                    } else {
                        photoPreviewContainer.appendChild(previewImg);
                    }
                }
                
                // Add fade-in effect
                previewImg.style.opacity = '0';
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                
                // Fade in animation
                setTimeout(() => {
                    previewImg.style.transition = 'opacity 0.3s ease-in-out';
                    previewImg.style.opacity = '1';
                }, 10);
            };
            
            reader.readAsDataURL(file);
        } else {
            // If no file selected, show placeholder
            if (photoPreviewPlaceholder) {
                photoPreviewPlaceholder.style.display = 'flex';
            }
            photoPreview.style.display = 'none';
        }
    });
}

// Add User Form Handler
const addUserForm = document.getElementById('add-user-form');
const addUserBtn = document.getElementById('add-user-btn');
const nameError = document.getElementById('name-error');
const phoneError = document.getElementById('phone-error');
const photoError = document.getElementById('photo-error');
const addressError = document.getElementById('address-error');
const commentError = document.getElementById('comment-error');
const successMessage = document.getElementById('add-user-success');
const successText = document.getElementById('success-message');

function resetForm() {
    addUserForm.reset();
    successMessage.classList.add('hidden');
    nameError.classList.add('hidden');
    phoneError.classList.add('hidden');
    photoError.classList.add('hidden');
    addressError.classList.add('hidden');
    commentError.classList.add('hidden');
    
    // Reset photo preview
    if (photoPreviewPlaceholder) {
        photoPreviewPlaceholder.style.display = 'flex';
    }
    if (photoPreview) {
        photoPreview.style.display = 'none';
    }
    
    document.getElementById('user-name').focus();
}

if (addUserForm) {
    addUserForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('user-name').value.trim();
        const phone = userPhoneInput.value.trim();
        const address = document.getElementById('user-address').value.trim();
        const comment = document.getElementById('user-comment').value.trim();
        const photoFile = photoInput.files[0];
        
        // Clear previous errors
        nameError.classList.add('hidden');
        phoneError.classList.add('hidden');
        photoError.classList.add('hidden');
        addressError.classList.add('hidden');
        commentError.classList.add('hidden');
        successMessage.classList.add('hidden');
        
        // Validate
        if (!name) {
            nameError.textContent = 'Name is required';
            nameError.classList.remove('hidden');
            document.getElementById('user-name').focus();
            return;
        }
        
        if (!phone || phone.length < 10) {
            phoneError.textContent = 'Please enter a valid phone number (at least 10 digits)';
            phoneError.classList.remove('hidden');
            userPhoneInput.focus();
            return;
        }
        
        // Disable button
        addUserBtn.disabled = true;
        addUserBtn.textContent = 'Adding...';
        
        try {
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('address', address);
            formData.append('comment', comment);
            if (photoFile) {
                formData.append('photo', photoFile);
            }
            
            const response = await fetch('{{ route("sponsor.users.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                successText.textContent = `User "${data.user.name}" (${data.user.phone}) has been added successfully! Affiliate Code: ${data.user.affiliate_code}`;
                successMessage.classList.remove('hidden');
                
                // Reset form but keep it visible
                resetForm();
                
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Reload page after 1.5 seconds to show new user in referrals list
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                // Show error
                if (data.errors) {
                    if (data.errors.name) {
                        nameError.textContent = data.errors.name[0] || data.errors.name;
                        nameError.classList.remove('hidden');
                    }
                    if (data.errors.phone) {
                        phoneError.textContent = data.errors.phone[0] || data.errors.phone;
                        phoneError.classList.remove('hidden');
                    }
                    if (data.errors.photo) {
                        photoError.textContent = data.errors.photo[0] || data.errors.photo;
                        photoError.classList.remove('hidden');
                    }
                    if (data.errors.address) {
                        addressError.textContent = data.errors.address[0] || data.errors.address;
                        addressError.classList.remove('hidden');
                    }
                    if (data.errors.comment) {
                        commentError.textContent = data.errors.comment[0] || data.errors.comment;
                        commentError.classList.remove('hidden');
                    }
                } else {
                    const errorField = data.message?.includes('phone') ? phoneError : nameError;
                    errorField.textContent = data.message || 'Failed to add user';
                    errorField.classList.remove('hidden');
                }
                
                addUserBtn.disabled = false;
                addUserBtn.textContent = 'Add User';
                
                // Focus on the first error field
                if (!nameError.classList.contains('hidden')) {
                    document.getElementById('user-name').focus();
                } else if (!phoneError.classList.contains('hidden')) {
                    userPhoneInput.focus();
                }
            }
        } catch (error) {
            phoneError.textContent = 'An error occurred. Please try again.';
            phoneError.classList.remove('hidden');
            addUserBtn.disabled = false;
            addUserBtn.textContent = 'Add User';
            userPhoneInput.focus();
        }
    });
}
</script>
@endpush
@endsection

