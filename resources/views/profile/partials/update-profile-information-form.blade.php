<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Foto de Perfil -->
        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
            <div class="mt-3 flex items-center gap-4">
                <!-- Vista previa de la foto actual o por defecto -->
                <div class="relative">
                    @if ($user->profile_photo_path)
                        <img id="photoPreview" src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile photo" class="h-24 w-24 rounded-full object-cover">
                    @else
                        <div id="photoPreview" class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden" onchange="handlePhotoSelect(event)">
                    <button type="button" onclick="document.getElementById('profile_photo').click()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ __('Choose Photo') }}
                    </button>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Less than 200KB. Formats: JPG, PNG, GIF') }}
                    </p>
                    <p id="photoError" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function handlePhotoSelect(event) {
            const file = event.target.files[0];
            const photoError = document.getElementById('photoError');
            const photoPreview = document.getElementById('photoPreview');
            const maxSize = 200 * 1024; // 200KB

            // Clear previous error
            photoError.classList.add('hidden');
            photoError.textContent = '';

            if (!file) {
                return;
            }

            // Validate file size
            if (file.size > maxSize) {
                photoError.textContent = 'La imagen es muy grande. Debe ser menor a 200KB. Tamaño actual: ' + (file.size / 1024).toFixed(2) + 'KB';
                photoError.classList.remove('hidden');
                event.target.value = ''; // Reset input
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                photoError.textContent = 'Formato de imagen no válido. Usa JPG, PNG, GIF o WebP.';
                photoError.classList.remove('hidden');
                event.target.value = ''; // Reset input
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                if (photoPreview.tagName === 'IMG') {
                    photoPreview.src = e.target.result;
                } else {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="h-24 w-24 rounded-full object-cover">';
                }
            };
            reader.readAsDataURL(file);
        }
    </script>
</section>
