<?php

namespace Tests\Unit\Validators;

use Tests\TestCase;
use App\Validators\AmountValidator;
use App\DTO\CallbackTelegramData;

class AmountValidatorTest extends TestCase
{
    /** @test */
    public function valid_amount_passes_validation()
    {
        $callback = new CallbackTelegramData(
            text: '1500',
            photos: [],
            chatId: 123,
            clientBotId: 456,
            firsName: 'Test',
            userName: 'test_user',
            fromBot: false,
            messageId: 789
        );

        $this->assertTrue(AmountValidator::isValid($callback));
    }

    /** @test */
    public function text_instead_of_amount_fails_validation()
    {
        $callback = new CallbackTelegramData(
            text: 'hello world',
            photos: [],
            chatId: 123,
            clientBotId: 456,
            firsName: 'Test',
            userName: 'test_user',
            fromBot: false,
            messageId: 789
        );

        $this->assertFalse(AmountValidator::isValid($callback));
    }

    /** @test */
    public function photo_without_text_fails_validation()
    {
        $callback = new CallbackTelegramData(
            text: '',
            photos: [['file_id' => 'abc123']],
            chatId: 123,
            clientBotId: 456,
            firsName: 'Test',
            userName: 'test_user',
            fromBot: false,
            messageId: 789
        );

        $this->assertFalse(AmountValidator::isValid($callback));
    }

    /** @test */
    public function photo_and_text_fails_validation()
    {
        $callback = new CallbackTelegramData(
            text: '500',
            photos: [['file_id' => 'abc123']],
            chatId: 123,
            clientBotId: 456,
            firsName: 'Test',
            userName: 'test_user',
            fromBot: false,
            messageId: 789
        );

        $this->assertFalse(AmountValidator::isValid($callback));
    }

    /** @test */
    public function empty_text_fails_validation()
    {
        $callback = new CallbackTelegramData(
            text: '',
            photos: [],
            chatId: 123,
            clientBotId: 456,
            firsName: 'Test',
            userName: 'test_user',
            fromBot: false,
            messageId: 789
        );

        $this->assertFalse(AmountValidator::isValid($callback));
    }
}
