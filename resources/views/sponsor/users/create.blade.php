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
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Note:</h3>
                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                    <li>The user will be automatically referred by you</li>
                    <li>An affiliate code will be automatically generated for the new user</li>
                    <li>The user will have sponsor access once created</li>
                    <li>You will see this user in your referrals list</li>
                </ul>
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

<!-- Referrals List Table -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Your Referrals</h2>
        <span class="bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $referrals->count() }}</span>
    </div>
    
    @if($referrals->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Affiliate Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Joined</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @foreach($referrals as $referral)
                <tr class="hover:bg-neutral-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-neutral-900">{{ $referral->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-neutral-500">{{ $referral->phone ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono font-semibold text-primary bg-primary/10 px-2 py-1 rounded">{{ $referral->affiliate_code }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold text-neutral-900">{{ $referral->orders_count }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $referral->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-neutral-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="text-sm text-neutral-500 font-medium">No referrals yet</p>
        <p class="text-xs text-neutral-400 mt-1">Start adding users above to see them here</p>
    </div>
    @endif
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

// Add User Form Handler
const addUserForm = document.getElementById('add-user-form');
const addUserBtn = document.getElementById('add-user-btn');
const nameError = document.getElementById('name-error');
const phoneError = document.getElementById('phone-error');
const successMessage = document.getElementById('add-user-success');
const successText = document.getElementById('success-message');

function resetForm() {
    addUserForm.reset();
    successMessage.classList.add('hidden');
    nameError.classList.add('hidden');
    phoneError.classList.add('hidden');
    document.getElementById('user-name').focus();
}

if (addUserForm) {
    addUserForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('user-name').value.trim();
        const phone = userPhoneInput.value.trim();
        
        // Clear previous errors
        nameError.classList.add('hidden');
        phoneError.classList.add('hidden');
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
            const response = await fetch('{{ route("sponsor.users.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    name: name,
                    phone: phone
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                successText.textContent = `User "${data.user.name}" (${data.user.phone}) has been added successfully! Affiliate Code: ${data.user.affiliate_code}`;
                successMessage.classList.remove('hidden');
                
                // Reset form but keep it visible
                addUserForm.reset();
                
                // Scroll to success message
                successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Reload page after 1.5 seconds to show new user in referrals list
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                // Show error
                const errorField = data.errors?.phone ? phoneError : nameError;
                errorField.textContent = data.message || 'Failed to add user';
                errorField.classList.remove('hidden');
                addUserBtn.disabled = false;
                addUserBtn.textContent = 'Add User';
                
                // Focus on the error field
                if (data.errors?.phone) {
                    userPhoneInput.focus();
                } else {
                    document.getElementById('user-name').focus();
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

