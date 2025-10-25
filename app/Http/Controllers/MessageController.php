<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Policies\MessagePolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function inbox(): View
    {
        $user = auth()->user();

        // Get conversations (latest message from each unique sender/recipient pair)
        $conversations = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
                ->orWhereHas('recipients', function ($q) use ($user) {
                    $q->where('recipient_id', $user->id);
                });
        })
            ->with(['sender', 'recipients' => function ($q) use ($user) {
                $q->where('recipient_id', $user->id);
            }])
            ->latest()
            ->paginate(20);

        // Count unread messages
        $unreadCount = Message::whereHas('recipients', function ($q) use ($user) {
            $q->where('recipient_id', $user->id)
                ->whereNull('read_at');
        })->count();

        return view('messages.inbox', compact('conversations', 'unreadCount'));
    }

    public function show(Message $message): View
    {
        $this->authorize('view', $message);

        $user = auth()->user();
        $message->markAsRead($user);

        // Get the thread (parent message and all replies)
        $rootMessage = $message->parentMessage ?? $message;
        $thread = $rootMessage->replies()->with(['sender', 'recipients'])->get()->prepend($rootMessage);

        return view('messages.show', compact('rootMessage', 'thread'));
    }

    public function create(): View
    {
        return view('messages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // First validation: basic message validation
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'recipient_ids' => ['required', 'string'],
        ], [
            'subject.required' => 'El asunto es requerido.',
            'body.required' => 'El mensaje es requerido.',
            'recipient_ids.required' => 'Debes seleccionar al menos un destinatario.',
        ]);

        $user = auth()->user();

        // Process recipient_ids string into array of actual user IDs
        $recipientIds = $this->resolveRecipientIds($validated['recipient_ids'], $user);

        if (empty($recipientIds)) {
            return redirect()->back()->withErrors(['recipient_ids' => 'Debes seleccionar al menos un destinatario válido.']);
        }

        // Create the message
        $message = Message::create([
            'sender_id' => $user->id,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);

        // Create recipient records for each selected recipient
        $policy = new MessagePolicy;
        foreach ($recipientIds as $recipientId) {
            $recipient = User::findOrFail($recipientId);

            // Check authorization using policy
            if (! $policy->sendTo($user, $recipient)) {
                abort(403, 'No está autorizado a enviar un mensaje a este usuario.');
            }

            $message->recipients()->create([
                'recipient_id' => $recipientId,
            ]);
        }

        return redirect()->route('messages.inbox')->with('success', 'Mensaje enviado correctamente.');
    }

    private function resolveRecipientIds(string $recipientString, $user): array
    {
        $parts = array_filter(explode(',', $recipientString));
        $recipientIds = [];

        foreach ($parts as $part) {
            $part = trim($part);

            // Handle group searches (legacy support)
            if (str_starts_with($part, 'all:')) {
                $roleQuery = str_replace('all:', '', $part);
                $groupIds = User::where('role', $roleQuery)
                    ->where('id', '!=', $user->id)
                    ->pluck('id')
                    ->toArray();
                $recipientIds = array_merge($recipientIds, $groupIds);
            } elseif (str_starts_with($part, 'group:')) {
                $groupQuery = str_replace('group:', '', $part);
                $groupIds = User::whereHas('student', function ($q) use ($groupQuery) {
                    $q->where('group', 'like', "%{$groupQuery}%");
                })
                    ->where('role', 'student')
                    ->pluck('id')
                    ->toArray();
                $recipientIds = array_merge($recipientIds, $groupIds);
            } else {
                // Handle individual user ID (numeric)
                if (is_numeric($part)) {
                    $recipientIds[] = (int) $part;
                }
            }
        }

        // Remove duplicates and return
        return array_unique($recipientIds);
    }

    public function reply(Message $message, Request $request): RedirectResponse
    {
        $user = auth()->user();
        $policy = new MessagePolicy;

        if (! $policy->reply($user, $message)) {
            abort(403, 'No puedes responder a este mensaje.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ], [
            'body.required' => 'El mensaje es requerido.',
        ]);

        // Get the root message of the thread
        $rootMessage = $message->parentMessage ?? $message;

        // Create reply message
        $reply = Message::create([
            'sender_id' => $user->id,
            'parent_message_id' => $rootMessage->id,
            'subject' => 'Re: '.$rootMessage->subject,
            'body' => $validated['body'],
        ]);

        // Add the original sender as recipient
        $reply->recipients()->create([
            'recipient_id' => $rootMessage->sender_id,
        ]);

        // Add all original recipients
        foreach ($rootMessage->recipients as $recipient) {
            if ($recipient->recipient_id !== $user->id) {
                $reply->recipients()->create([
                    'recipient_id' => $recipient->recipient_id,
                ]);
            }
        }

        return redirect()->route('messages.show', $rootMessage)->with('success', 'Respuesta enviada correctamente.');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();

        // Check for group searches
        if (str_starts_with($query, 'all:')) {
            return $this->searchGroupsByRole($query, $user);
        }

        if (str_starts_with($query, 'group:')) {
            return $this->searchStudentsByGroup($query, $user);
        }

        // Regular search by name, email, etc.
        $recipients = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('apellido_paterno', 'like', "%{$query}%")
                ->orWhere('apellido_materno', 'like', "%{$query}%");
        });

        // Filter based on user role
        if ($user->isParent() || $user->isStudent()) {
            // Parents and students can only message teachers, admins, and finance admins
            $recipients->whereIn('role', ['teacher', 'admin', 'finance_admin']);
        }

        // Exclude the current user
        $recipients->where('id', '!=', $user->id);

        $results = $recipients->limit(10)
            ->select('id', 'name', 'apellido_paterno', 'apellido_materno', 'email', 'role')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => ucfirst(str_replace('_', ' ', $user->role->value)),
                    'type' => 'user',
                ];
            });

        return response()->json($results);
    }

    private function searchGroupsByRole(string $query, $user): JsonResponse
    {
        $roleQuery = str_replace('all:', '', $query);

        $recipients = User::where('role', $roleQuery)
            ->where('id', '!=', $user->id);

        // Parents and students can only select teachers, admins, and finance admins
        if ($user->isParent() || $user->isStudent()) {
            if (! in_array($roleQuery, ['teacher', 'admin', 'finance_admin'])) {
                return response()->json([]);
            }
        }

        $results = $recipients->select('id', 'name', 'apellido_paterno', 'apellido_materno', 'email', 'role')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => ucfirst(str_replace('_', ' ', $user->role->value)),
                    'type' => 'user',
                ];
            });

        return response()->json($results);
    }

    private function searchStudentsByGroup(string $query, $user): JsonResponse
    {
        // Only admins and teachers can search by group
        if (! $user->isAdmin() && ! $user->isTeacher()) {
            return response()->json([]);
        }

        $groupQuery = str_replace('group:', '', $query);

        // If only "group:" with no group specified, return available groups
        if (strlen($groupQuery) === 0) {
            $groups = Student::query()
                ->select('group')
                ->distinct()
                ->whereNotNull('group')
                ->pluck('group')
                ->map(function ($group) {
                    return [
                        'id' => 'group:'.$group,
                        'name' => 'Grupo '.$group,
                        'full_name' => 'Grupo '.$group,
                        'email' => '',
                        'role' => 'Grupo',
                        'type' => 'group',
                    ];
                });

            return response()->json($groups);
        }

        // Get students in the specified group
        $results = User::whereHas('student', function ($q) use ($groupQuery) {
            $q->where('group', 'like', "%{$groupQuery}%");
        })
            ->where('role', 'student')
            ->select('id', 'name', 'apellido_paterno', 'apellido_materno', 'email', 'role')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => ucfirst(str_replace('_', ' ', $user->role->value)),
                    'type' => 'user',
                ];
            });

        return response()->json($results);
    }
}
