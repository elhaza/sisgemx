<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $this->isSender($user, $message) || $this->isRecipient($user, $message);
    }

    public function reply(User $user, Message $message): bool
    {
        return $this->isRecipient($user, $message);
    }

    public function delete(User $user, Message $message): bool
    {
        return $this->isSender($user, $message);
    }

    public function sendTo(User $sender, User $recipient): bool
    {
        // Prevent sending to yourself
        if ($sender->id === $recipient->id) {
            return false;
        }

        // Admin can send to anyone
        if ($sender->isAdmin()) {
            return true;
        }

        // FinanceAdmin can send to anyone
        if ($sender->isFinanceAdmin()) {
            return true;
        }

        // Teachers can send to anyone
        if ($sender->isTeacher()) {
            return true;
        }

        // Parents can only send to teachers, admins, and finance admins
        if ($sender->isParent()) {
            return $recipient->isTeacher() || $recipient->isAdmin() || $recipient->isFinanceAdmin();
        }

        // Students can only send to teachers, admins, and finance admins
        if ($sender->isStudent()) {
            return $recipient->isTeacher() || $recipient->isAdmin() || $recipient->isFinanceAdmin();
        }

        return false;
    }

    private function isSender(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    private function isRecipient(User $user, Message $message): bool
    {
        return $message->recipients()
            ->where('recipient_id', $user->id)
            ->exists();
    }
}
