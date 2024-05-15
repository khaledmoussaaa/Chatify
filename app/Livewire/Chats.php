<?php

namespace App\Livewire;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chats extends Component
{
    use WithFileUploads;

    // Declerating & Intialization
    public $auth;
    public $message;
    public $photo;
    public $file;
    public $selectedUser;

    public function mount()
    {
        $this->auth = Auth::id();
        $this->selectedUser = session('selectedUser');
    }

    // Rendering Component
    public function render()
    {
        if ($this->file) {
            $this->message = $this->file->getClientOriginalName();
        } else if ($this->photo) {
            $this->message = $this->photo->getClientOriginalName();
        }

        $this->updateSeen();

        return view('livewire.chats', [
            'contacts' => $this->newContacts(),
            'conversations' => $this->selectedUser ? $this->conversations($this->selectedUser) : collect(),
        ]);
    }


    // Create a message
    public function createMessage()
    {

        $this->validate(
            [
                'message' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,gif|max:5120',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,pptx,ppt|max:5120',
            ]
        );

        try {
            $messages = [
                'user_id' => $this->auth,
                'recipient_id' => $this->selectedUser,
                'message' => $this->message,
                'seen' => 0,
                'photo' => '',
                'file' => '',
            ];
            $this->processFileUpload($messages);
            Messages::create($messages);

            $this->reset(['message', 'photo', 'file']);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    // Upload photos and files
    private function processFileUpload(&$messageData)
    {
        if ($this->photo || $this->file) {
            if ($this->photo) {
                $photoPath = $this->photo->store('photos', 'public');
                $messageData['photo'] .= 'storage/' . $photoPath;
            } elseif ($this->file) {
                $filePath = $this->file->store('files', 'public');
                $messageData['file'] .= 'storage/' . $filePath;
            }
        }
    }

    // Selected UserId
    public function setSelectedUser($uid)
    {
        $this->selectedUser = $uid;
        session(['selectedUser' =>  $this->selectedUser]);
        $this->updateSeen();
    }

    // Update Seen
    public function updateSeen()
    {
        Messages::where('user_id', $this->selectedUser)
            ->where('recipient_id', $this->auth)
            ->update(['seen' => 1]);
    }

    // Contacts
    public function conacts()
    {
    }

    // New Contacts
    public function newContacts()
    {
        return User::where('id', '!=', $this->auth)->get();
    }

    // Conversations
    private function conversations($selectedUser)
    {
        $conversations = Messages::whereIn('user_id', [$this->auth, $selectedUser])
            ->whereIn('recipient_id', [$this->auth, $selectedUser])
            ->orderBy('created_at', 'asc')
            ->get();

        $groupedConversation = [];

        foreach ($conversations as $conversation) {
            $date = $conversation['created_at']->toDateString();
            $isToday = $conversation['created_at']->isToday();
            $isYesterday = $conversation['created_at']->isYesterday();

            if ($isToday) {
                $groupedConversation['Today'][$date][] = $conversation;
            } elseif ($isYesterday) {
                $groupedConversation['Yesterday'][$date][] = $conversation;
            } else {
                // For older dates, use the actual date
                $formattedDate = $conversation['created_at']->format('F j, Y');
                $groupedConversation[$formattedDate][$date][] = $conversation;
            }
        }
        return collect($groupedConversation);
    }
}
