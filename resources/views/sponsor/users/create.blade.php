@extends('layouts.sponsor')

@section('title', 'Add New User')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Add New User</h1>
            <p class="text-neutral-600 mt-1">Add a new user who will be automatically referred by you</p>
        </div>
        <a href="{{ route('sponsor.dashboard') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition">
            ← Back to Dashboard
        </a>
    </div>
</div>

<!-- Add User Form -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form id="add-user-form" class="space-y-6">
            @csrf
            <div>
                <label for="user-name" class="block text-sm font-medium text-neutral-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    id="user-name" 
                    name="name" 
                    required 
                    autofocus
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Enter user name"
                >
                <div id="name-error" class="mt-1 text-sm text-red-600 hidden"></div>
            </div>
            
            <div>
                <label for="user-phone" class="block text-sm font-medium text-neutral-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                <input 
                    type="tel" 
                    id="user-phone" 
                    name="phone" 
                    required 
                    class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="01XXXXXXXXX"
                >
                <p class="mt-1 text-xs text-neutral-500">Enter 11-digit phone number (e.g., 01712345678)</p>
                <div id="phone-error" class="mt-1 text-sm text-red-600 hidden"></div>
            </div>
            
            <!-- Profile Photo -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-neutral-700 mb-2">Profile Photo</label>
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0" id="photo-preview-container">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary/20 to-primary-light/20 flex items-center justify-center border-4 border-neutral-200 shadow-sm" id="photo-preview-placeholder">
                            <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <img id="photo-preview" src="#" alt="Profile Photo Preview" class="w-24 h-24 rounded-full object-cover border-4 border-neutral-200 shadow-sm hidden">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="user-photo" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                               class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light file:cursor-pointer transition">
                        <p class="mt-2 text-xs text-neutral-500">JPG, PNG, GIF or WebP</p>
                        <div id="photo-error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="user-address" class="block text-sm font-medium text-neutral-700 mb-2">Address</label>
                <textarea name="address" id="user-address" rows="3" 
                          class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"
                          placeholder="Enter user's address"></textarea>
                <div id="address-error" class="mt-1 text-sm text-red-600 hidden"></div>
            </div>

            <!-- Comment -->
            <div>
                <label for="user-comment" class="block text-sm font-medium text-neutral-700 mb-2">Comment</label>
                <textarea name="comment" id="user-comment" rows="3" 
                          class="w-full px-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"
                          placeholder="Add any comments or notes about this user"></textarea>
                <div id="comment-error" class="mt-1 text-sm text-red-600 hidden"></div>
            </div>
            
            
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    id="add-user-btn"
                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-light transition font-semibold"
                >
                    Add User
                </button>
                <a 
                    href="{{ route('sponsor.dashboard') }}" 
                    class="px-6 py-3 border border-neutral-300 rounded-lg hover:bg-neutral-50 transition font-semibold text-neutral-700"
                >
                    Cancel
                </a>
            </div>
        </form>
        
        <div id="add-user-success" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-green-900 mb-1">User Added Successfully!</h3>
                    <p class="text-sm text-green-800" id="success-message"></p>
                    <div class="mt-4 flex gap-3">
                        <a 
                            href="{{ route('sponsor.dashboard') }}" 
                            class="inline-block px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"
                        >
                            Go to Dashboard
                        </a>
                        <button 
                            type="button"
                            onclick="resetForm()" 
                            class="px-4 py-2 border border-green-600 text-green-700 text-sm rounded-lg hover:bg-green-50 transition"
                        >
                            Add Another User
                        </button>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="mt-6 text-center">
    <a href="{{ route('sponsor.users.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-light font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        View All Referrals
    </a>
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
                
                // Show preview
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
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

