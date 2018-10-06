<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;


/**
 * @group privateMessages
 */
class PrivateMessagesTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCannotAccessCircle()
    {
        $response = $this->get(route('private_messages.inbox'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('private_messages.sent'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('private_messages.create', ['uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->post(route('private_messages.send', ['uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('private_messages.read', ['uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('private_messages.reply', ['uuid' => '1234', 'replyUuid' => '4321']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testCanAccessInbox()
    {
        $user = $this->fetchUser();
        $response = $this->actingAs($user)->get(route('private_messages.inbox'));
        $response->assertStatus(200);
    }

    public function testCanAccessSent()
    {
        $user = $this->fetchUser();
        $response = $this->actingAs($user)->get(route('private_messages.sent'));
        $response->assertStatus(200);
    }

    public function testCanAccessCreate()
    {
        $user = $this->fetchUser();
        $recipient = $this->fetchUser();
        $response = $this->actingAs($user)->get(route('private_messages.create', ['uuid' => $recipient->uuid]));
        $response->assertStatus(200);
    }

    public function testCanReadMessage()
    {
        $user = $this->fetchUser();
        $this->be($user);

        $recipient = $this->fetchUser();
        $privateMessage = $this->fetchPrivateMessage($recipient);

        $response = $this->actingAs($user)->get(route('private_messages.read', ['uuid' => $privateMessage->uuid]));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('private_messages.read', ['uuid' => $this->fetchFaker()->uuid]));
        $response->assertStatus(404);
    }

    public function testCanSendMessage()
    {
        $faker = $this->fetchFaker();
        $user = $this->fetchUser();
        $recipient = $this->fetchUser();
        $response = $this->actingAs($user)->get(route('private_messages.create', ['uuid' => $recipient->uuid]));
        $response->assertStatus(200);


        $response = $this->actingAs($user)->post(route('private_messages.send', ['uuid' => $recipient->uuid]), [
            'body' => $this->fetchFaker()->text(),
            'recipient_id' => $recipient->id,
            'conversation' => $faker->randomDigit,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $privateMessage = $user->privateMessages()->first();
        
        $this->assertEquals($user->id, $privateMessage->user->id);

        $this->assertEquals(null, $privateMessage->conversation);
    }

    public function testCanOnlyReadMessagesAssociatedWithHim()
    {
        $user = $this->fetchUser();
        $this->be($user);

        $recipient = $this->fetchUser();
        $privateMessage = $this->fetchPrivateMessage($recipient);

        $response = $this->actingAs($user)->get(route('private_messages.read', ['uuid' => $privateMessage->uuid]));
        $response->assertStatus(200);

        $user = $this->fetchUser();
        $response = $this->actingAs($user)->get(route('private_messages.read', ['uuid' => $privateMessage->uuid]));
        $response->assertStatus(403);
    }

    public function testCanNotReplyForeignMessage()
    {
        $user = $this->fetchUser();
        $recipient = $this->fetchUser();
        $recipient2 = $this->fetchUser();
        
        $this->be($user);

        $message1 = $this->fetchPrivateMessage($recipient);

        $response = $this->actingAs($recipient2)->get(route('private_messages.reply', [
            'uuid' => $user->uuid,
            'replyUuid' => $message1->uuid,
        ]));

        $response->assertStatus(403);

        $response = $this->actingAs($recipient2)->post(route('private_messages.send_reply', [
                'uuid' => $user->uuid,
                'replyUuid' => $message1->uuid,
            ]), [
                'body' => $this->fetchFaker()->text(),
            ]);

        $response->assertStatus(403);
    }

    public function testCanReplyMessage()
    {
        $faker = $this->fetchFaker();
        $user = $this->fetchUser();
        $recipient = $this->fetchUser();
        
        $this->be($user);

        $message1 = $this->fetchPrivateMessage($recipient);

        $response = $this->actingAs($recipient)->get(route('private_messages.reply', [
            'uuid' => $user->uuid,
            'replyUuid' => $message1->uuid,
        ]));

        $response->assertStatus(200);

        $response = $this->actingAs($recipient)->post(route('private_messages.send_reply', [
            'uuid' => $user->uuid,
            'replyUuid' => $message1->uuid,
        ]), [
            'body' => $faker->text,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $message2 = $recipient->privateMessages()->first();

        $this->assertEquals($user->id, $message2->recipient_id);
        $this->assertEquals($message1->id, $message2->conversation);

        $response = $this->actingAs($user)->post(route('private_messages.send_reply', [
            'uuid' => $recipient->uuid,
            'replyUuid' => $message2->uuid,
        ]), [
            'body' => $faker->text,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $message3 = $user->privateMessages()->get()->last();

        $this->assertEquals($recipient->id, $message3->recipient_id);
        $this->assertEquals($message1->id, $message3->conversation);
    }
}
