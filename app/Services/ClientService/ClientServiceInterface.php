<?php

namespace App\Services\ClientService;

interface ClientServiceInterface
{
    public function getClient($clientId);
    public function getClientLanguage($clientId);
    public function setStatus($clientId, $status);
    public function setClientChangeLanguageInput($clientId, $language, $setStatus);
    public function isClientChangeLanguageInput($clientId);
    public function checkIfClientExit($data);
    public function setClientMainInput($clientId, $status);
    public function setClientAmountInput($clientId);
    public function setClientAmountSuccessInput($clientId);
    public function isUserInAmountInput($clientId);
    public function setUserCountryInput($clientId);
    public function setClientBankInput($clientId);
    public function isUserInACountryInput($clientId);
    public function setClientCurrencyInput($clientId);
    public function setClientSendScreenshot($clientId);
    public function setClientConsultationInput($clientId);
    public function isClientConsultationInput($clientId);
}
