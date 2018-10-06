<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group privateMessage
 */
class PrivateMessageTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGenerateUuid()
    {
        $faker = $this->fetchFaker();
        $message = new \App\PrivateMessage();

        $this->assertEquals(0, strlen($message->uuid));

        $message->body = $faker->text;
        $message->user_id = $faker->randomDigit;
        $message->recipient_id = $faker->randomDigit;

        $message->save();

        $uuid = $message->uuid;

        $this->assertEquals(36, strlen($message->uuid));

        $message->body = $faker->text;

        $message->save();

        $this->assertEquals($uuid, $message->uuid);

        $m = \App\PrivateMessage::withUuid($uuid)->get()->first();

        $this->assertEquals($message->id, $m->id);
    }

    public function testValidationRules()
    {
        $rules = \App\PrivateMessage::validationRules();
        $rules2 = \App\PrivateMessage::validationRules(['recipient_id']);

        $this->assertTrue(count($rules) > 0);

        $this->assertTrue(key_exists('recipient_id', $rules));

        $this->assertFalse(key_exists('recipient_id', $rules2));
    }
}
