<?php

use App\Models\Message;
use App\Models\User;
use App\UserRole;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Admin User',
        'role' => UserRole::Admin,
    ]);
    $this->teacher = User::factory()->create([
        'name' => 'Teacher Example',
        'role' => UserRole::Teacher,
    ]);
    $this->student = User::factory()->create([
        'name' => 'Student Name',
        'role' => UserRole::Student,
    ]);
    $this->parent = User::factory()->create([
        'name' => 'Parent Guardian',
        'role' => UserRole::Parent,
    ]);
});

describe('Message Inbox', function () {
    it('shows inbox page for authenticated user', function () {
        $response = $this->actingAs($this->student)->get('/messages');

        $response->assertSuccessful();
        $response->assertViewIs('messages.inbox');
    });

    it('displays unread message count', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $message->recipients()->create([
            'recipient_id' => $this->student->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->student)->get('/messages');

        $response->assertViewHas('unreadCount', 1);
    });

    it('marks messages as read when viewed', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $recipient = $message->recipients()->create([
            'recipient_id' => $this->student->id,
            'read_at' => null,
        ]);

        $this->actingAs($this->student)->get("/messages/{$message->id}");

        expect($recipient->fresh()->read_at)->not->toBeNull();
    });
});

describe('Create Message', function () {
    it('shows create message form', function () {
        $response = $this->actingAs($this->student)->get('/messages/create');

        $response->assertSuccessful();
        $response->assertViewIs('messages.create');
    });

    it('allows student to send message to teacher', function () {
        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test Subject',
            'body' => 'Test message body',
            'recipient_ids' => (string) $this->teacher->id,
        ]);

        $response->assertRedirect('/messages');

        expect(Message::whereSubject('Test Subject')->first())
            ->sender_id->toBe($this->student->id)
            ->body->toBe('Test message body');
    });

    it('creates message recipient records', function () {
        $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => (string) $this->teacher->id,
        ]);

        $message = Message::whereSubject('Test')->first();

        expect($message->recipients)
            ->toHaveCount(1)
            ->first()->recipient_id->toBe($this->teacher->id);
    });

    it('allows multiple recipients', function () {
        $admin2 = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($this->admin)->post('/messages', [
            'subject' => 'Announcement',
            'body' => 'Important announcement',
            'recipient_ids' => implode(',', [$this->teacher->id, $admin2->id, $this->student->id]),
        ]);

        $message = Message::whereSubject('Announcement')->first();

        expect($message->recipients)->toHaveCount(3);
    });

    it('requires at least one recipient', function () {
        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => '',
        ]);

        $response->assertSessionHasErrors('recipient_ids');
    });

    it('requires subject and body', function () {
        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => '',
            'body' => '',
            'recipient_ids' => (string) $this->teacher->id,
        ]);

        $response->assertSessionHasErrors(['subject', 'body']);
    });
});

describe('Message Authorization', function () {
    it('student cannot send message to another student', function () {
        $otherStudent = User::factory()->create(['role' => UserRole::Student]);

        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => (string) $otherStudent->id,
        ]);

        $response->assertForbidden();
    });

    it('student cannot send message to parent', function () {
        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => (string) $this->parent->id,
        ]);

        $response->assertForbidden();
    });

    it('parent cannot send message to student', function () {
        $response = $this->actingAs($this->parent)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => (string) $this->student->id,
        ]);

        $response->assertForbidden();
    });

    it('admin can send message to anyone', function () {
        $response = $this->actingAs($this->admin)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => implode(',', [$this->student->id, $this->teacher->id, $this->parent->id]),
        ]);

        $response->assertRedirect('/messages');

        expect(Message::whereSubject('Test')->first()->recipients)
            ->toHaveCount(3);
    });

    it('user cannot send message to themselves', function () {
        $response = $this->actingAs($this->student)->post('/messages', [
            'subject' => 'Test',
            'body' => 'Body',
            'recipient_ids' => (string) $this->student->id,
        ]);

        $response->assertForbidden();
    });
});

describe('Message Viewing', function () {
    it('sender can view their own message', function () {
        $message = Message::factory()->create(['sender_id' => $this->student->id]);
        $message->recipients()->create(['recipient_id' => $this->teacher->id]);

        $response = $this->actingAs($this->student)->get("/messages/{$message->id}");

        $response->assertSuccessful();
    });

    it('recipient can view message', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $message->recipients()->create(['recipient_id' => $this->student->id]);

        $response = $this->actingAs($this->student)->get("/messages/{$message->id}");

        $response->assertSuccessful();
    });

    it('non-sender non-recipient cannot view message', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $message->recipients()->create(['recipient_id' => $this->student->id]);

        $response = $this->actingAs($this->parent)->get("/messages/{$message->id}");

        $response->assertForbidden();
    });
});

describe('Message Replies', function () {
    it('recipient can reply to message', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $message->recipients()->create(['recipient_id' => $this->student->id]);

        $response = $this->actingAs($this->student)->post("/messages/{$message->id}/reply", [
            'body' => 'Reply message',
        ]);

        $response->assertRedirect();

        $reply = Message::where('parent_message_id', $message->id)->first();

        expect($reply)
            ->not->toBeNull()
            ->sender_id->toBe($this->student->id)
            ->body->toBe('Reply message');
    });

    it('sender cannot reply to their own message', function () {
        $message = Message::factory()->create(['sender_id' => $this->student->id]);
        $message->recipients()->create(['recipient_id' => $this->teacher->id]);

        $response = $this->actingAs($this->student)->post("/messages/{$message->id}/reply", [
            'body' => 'Reply message',
        ]);

        $response->assertForbidden();
    });

    it('reply includes all participants in thread', function () {
        $message = Message::factory()->create(['sender_id' => $this->teacher->id]);
        $message->recipients()->create(['recipient_id' => $this->student->id]);
        $message->recipients()->create(['recipient_id' => $this->admin->id]);

        $response = $this->actingAs($this->student)->post("/messages/{$message->id}/reply", [
            'body' => 'Reply',
        ]);

        $reply = Message::where('parent_message_id', $message->id)->first();

        expect($reply)->not->toBeNull();
        expect($reply->recipients()->pluck('recipient_id')->toArray())
            ->toHaveCount(2)
            ->toContain($this->teacher->id, $this->admin->id);
    });
});

describe('Message Search API', function () {
    it('searches users for message recipients', function () {
        $response = $this->actingAs($this->student)->get('/api/messages/search?q=Teacher');

        $response->assertSuccessful();
        $data = $response->json();

        expect(collect($data)->pluck('id'))->toContain($this->teacher->id);
    });

    it('filters results based on user role', function () {
        $response = $this->actingAs($this->student)->get('/api/messages/search?q=Student');

        // Should not include other students
        $ids = collect($response->json())->pluck('id');

        expect($ids)->not->toContain($this->student->id);
    });

    it('requires minimum query length', function () {
        $response = $this->actingAs($this->student)->get('/api/messages/search?q=a');

        $response->assertSuccessful();

        expect($response->json())->toBeEmpty();
    });

    it('admin can search all users', function () {
        $response = $this->actingAs($this->admin)->get('/api/messages/search?q=Student');

        $response->assertSuccessful();
        $ids = collect($response->json())->pluck('id');

        expect($ids)->toContain($this->student->id);
    });
});
