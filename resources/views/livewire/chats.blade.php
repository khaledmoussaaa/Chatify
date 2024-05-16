<div class="w-full flex h-full bg-blue-50 p-2" wire:poll.300ms>
    <!-- Left Column - Contacts -->
    <div class="w-full bg-white rounded-xl overflow-y-auto relative md:w-1/4 {{$selectedUser != 0 ? 'hidden' : 'block'}} md:block">
        <!-- Contact List -->
        <div class="w-full h-full absolute p-4">
            @foreach($contacts as $contact)
            <div class="flex items-center gap-2 my-5 cursor-pointer" wire:click="setSelectedUser({{$contact['user']->id}})">
                <div class="min-w-14 h-14 relative border-2 overflow-hidden rounded-full">
                    <img src="{{asset('1.png')}}" alt="" class="w-full h-full absolute">
                </div>
                <div class="flex flex-col overflow-hidden w-full">
                    <div class="relative">
                        <span class="truncate font-semibold">{{ $contact['user']->name }}</span>
                        <span class="bg-blue-200 text-gray-600 text-sm w-5 h-5 text-center absolute right-0 rounded-full">{{ $contact['user']?->messages_count }}</span>
                    </div>
                    <span class="truncate text-sm text-gray-400">{{ $contact['lastmessage']}}</span>
                    <hr class="mt-1">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right Column - Conversation -->
    <div class="w-full h-full md:w-3/4 bg-blue-50 p-4 flex flex-col relative {{$selectedUser != 0 ? 'block' : 'hidden'}} md:flex">
        <!-- Chat Header -->
        @foreach($contacts as $contact)
        @if($selectedUser == $contact['user']->id)
        <div class="flex items-center gap-3 bg-white rounded-t-lg p-2">
            <i class="bi bi-chevron-left" wire:click="setSelectedUser(0)"></i>
            <div class="min-w-14 h-14 relative border-2 overflow-hidden rounded-full">
                <img src="{{asset('1.png')}}" alt="" class="w-full h-full absolute">
            </div>
            <h3 class="text-lg font-semibold text-gray-800">{{$contact['user']->name}}</h3>
        </div>
        @endif
        @endforeach

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto mt-4 relative">
            <div class="mt-4 absolute w-full h-full">
                @forelse ($conversations as $timePeriod => $messagesByDate)
                @foreach ($messagesByDate as $date => $conversations)
                <div class="w-max mx-auto text-center font-light text-gray-500 text-sm p-2 bg-zinc-50 rounded-xl">
                    <span>{{ $timePeriod }}</span>
                    <span>{{ $date }}</span>
                </div>
                <!-- Received Message -->
                @foreach($conversations as $conversation)
                @if ($conversation->user_id != Auth::id())
                <div class="flex items-start mb-4">
                    <div class="bg-gray-200 rounded-lg p-3 {{ $conversation->photo ? '' : '' }}">
                        @if($conversation->photo)
                        <img src="{{ asset($conversation->photo) }}" alt="Photo" class="chat-photo" onclick="openPhotoModal('{{ asset($conversation->photo) }}')">
                        @else
                        <p class="text-sm">{{ $conversation->message }}</p>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-xs text-gray-600">{{ $conversation->created_at->format('h:m A') }}</span>
                        </div>
                    </div>
                </div>
                @else
                <!-- Sent Message -->
                <div class="flex items-end justify-end mb-4">
                    <div class="bg-white text-black rounded-lg p-3 {{ $conversation->photo ? 'mx-1/2 w-1/3 h-1/3' : '' }}">
                        @if($conversation->photo)
                        <img src="{{ asset($conversation->photo) }}" alt="Photo" class="chat-photo" onclick="openPhotoModal('{{ asset($conversation->photo) }}')">
                        @else
                        <p class="text-sm">{{ $conversation->message }}</p>
                        @endif
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-xs text-gray-600">{{ $conversation->created_at->format('h:m A') }}</span>
                            <i class="bi bi-check-all"></i>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endforeach
                @empty
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-slate-200 p-3 rounded-xl font-semibold text-gray-700">
                    <p>Hey there! 👋 Currently, there are no new messages. Feel free to start a conversation or ask anything you'd like. I'm here to chat and help out!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Message Input -->
        <form wire:submit.prevent="createMessage" enctype="multipart/form-data" class="mt-4 flex items-center gap-2">
            @csrf
            <label for="photo" class="cursor-pointer bg-white text-black font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
                <i class="bi bi-paperclip"></i>
            </label>
            <input type="file" id="photo" name="photo" wire:model.lazy="photo" accept="image/*" class="hidden">
            <textarea cols="1" rows="1" name="message" placeholder="Type your message..." wire:model.lazy="message" class="flex-1 pt-2 rounded-lg indent-2 border border-gray-300 focus:outline-none focus:border-blue-100 focus:ring-0"></textarea>
            <div class="flex items-center">
                <button class="bg-white text-black font-semibold shadow-sm  px-4 py-2 rounded-lg hover:bg-gray-50 focus:outline-none">Send</button>
            </div>
        </form>
    </div>
</div>
