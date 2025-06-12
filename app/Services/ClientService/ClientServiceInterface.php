<?php

namespace App\Services\ClientService;

interface ClientServiceInterface
{
    public function getClient($clientBotId, $chatId);
    public function getClientStatus($clientBotId);
    public function getClientLanguage($clientBotId);
    public function setClientChangeLanguageInput($clientBotId, $language, $setStatus);
    public function checkIfClientExit($data);
    public function setClientMainInput($clientId, $status);
    public function setClientAmountInput($clientId);
    public function setClientAmountSuccessInput($clientId);
    public function setUserCountryInput($clientId);
    public function setClientBankInput($clientId);
    public function setClientCurrencyInput($clientId);
    public function setClientSendScreenshot($clientId);
    public function setClientConsultationInput($chatId, $clientId);
    public function isClientInACountryInput($clientBotId);
    public function isClientChangeLanguageInput($clientBotId);
    public function isUserInAmountInput($clientBotId);
    public function isClientSendScreenshot($clientBotId);
    public function isClientConsultationInput($clientBotId);
}
