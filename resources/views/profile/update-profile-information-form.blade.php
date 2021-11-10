<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            wire:model="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview">
                    <span class="block rounded-full w-20 h-20"
                          x-bind:style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-jet-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-jet-secondary-button>
                @endif

                <x-jet-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Username -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="username" value="{{ __('Username') }}" />
            <x-jet-input id="username" type="text" class="mt-1 block w-full" wire:model.defer="state.username" autocomplete="username" />
            <x-jet-input-error for="username" class="mt-2" />
        </div>

        <!-- Fullname -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="fullname" value="{{ __('Fullname') }}" />
            <x-jet-input id="fullname" type="text" class="mt-1 block w-full" wire:model.defer="state.fullname" autocomplete="fullname" />
            <x-jet-input-error for="fullname" class="mt-2" />
        </div>

        <!-- NIP 18 Digit -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="nip" value="{{ __('NIP 18 Digit') }}" />
            <x-jet-input id="nip" type="text" class="mt-1 block w-full" wire:model.defer="state.nip" autocomplete="nip" />
            <x-jet-input-error for="nip" class="mt-2" />
        </div>

        <!-- Organisasi -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="organisasi" value="{{ __('Organisasi') }}" />
            <x-jet-input id="organisasi" type="text" class="mt-1 block w-full" wire:model.defer="state.organisasi" autocomplete="organisasi" />
            <x-jet-input-error for="organisasi" class="mt-2" />
        </div>

        <!-- Unit Kerja -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="unit_kerja" value="{{ __('Unit Kerja') }}" />
            <x-jet-input id="unit_kerja" type="text" class="mt-1 block w-full" wire:model.defer="state.unit_kerja" autocomplete="unit_kerja" />
            <x-jet-input-error for="unit_kerja" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="email" value="{{ __('Email') }}" />
            <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.email" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
