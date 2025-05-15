<?php

namespace App\Services\RedisService;

use App\Enums\Bank\BankField;
use App\Enums\Amount\AmountField;
use App\Enums\Wallet\WalletField;
use App\Enums\Country\CountryField;
use App\Enums\Requisite\RequisiteField;

class RedisSessionService extends BaseService
{
    // === CLIENT ===

    public function setClientIdForChat(string|int $chatId, int $clientId, ?int $ttl = null): void
    {
        $this->set('client_id', $chatId, (string) $clientId, $ttl);
    }
    public function getClientIdForChat(string|int $chatId): ?int
    {
        $value = $this->get('client_id', $chatId);
        return $value !== null ? (int) $value : null;
    }
    public function forgetClientIdForChat(string|int $chatId): void
    {
        $this->forget('client_id', $chatId);
    }

    // === COUNTRY ===

    public function setSelectedCountry(int $chatId, int $countryId, ?int $ttl = null): void
    {
        $this->set('select_country', $chatId, $countryId, $ttl);
    }
    public function getSelectedCountry(int $chatId): ?string
    {
        return $this->get('select_country', $chatId);
    }
    public function setCountryCode(int $chatId, string $countryCode, ?int $ttl = null): void
    {
        $this->set(CountryField::COUNTRY_CODE->value, $chatId, $countryCode, $ttl);
    }
    public function getCountryCode(int $chatId): ?string
    {
        return $this->get(CountryField::COUNTRY_CODE->value, $chatId);
    }
    public function setCountryConsultant(int $chatId, ?int $ttl = null): void
    {
        $this->set(CountryField::COUNTRYCONSULTANT->value, $chatId, true, $ttl);
    }
    public function getCountryConsultant(int $chatId): ?bool
    {
        $value = $this->get(CountryField::COUNTRYCONSULTANT->value, $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    public function forgetCountryConsultant(int $chatId): void
    {
        $this->forget(CountryField::COUNTRYCONSULTANT->value, $chatId);
    }

    // === BANK ===

    public function setBankConsultant(int $chatId, ?int $ttl = null): void
    {
        $this->set(BankField::BANKCONSULTANT->value, $chatId, true, $ttl);
    }
    public function getBankConsultant(int $chatId): ?bool
    {
        $value = $this->get(BankField::BANKCONSULTANT->value, $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    public function forgetBankConsultant(int $chatId): void
    {
        $this->forget(BankField::BANKCONSULTANT->value, $chatId);
    }

    // === AMOUNT ===

    public function setAmountConsultant(int $chatId, ?int $ttl = null): void
    {
        $this->set(AmountField::AmountConsultant->value, $chatId, true, $ttl);
    }
    public function getAmountConsultant(int $chatId): ?bool
    {
        $value = $this->get(AmountField::AmountConsultant->value, $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    public function forgetAmountConsultant(int $chatId): void
    {
        $this->forget(AmountField::AmountConsultant->value, $chatId);
    }

    // === REQUISITE ===

    public function setRequisiteConsultant(int $chatId, ?int $ttl = null): void
    {
        $this->set(RequisiteField::REQUISITECONSULTANT->value, $chatId, true, $ttl);
    }
    public function getRequisiteConsultant(int $chatId): ?bool
    {
        $value = $this->get(RequisiteField::REQUISITECONSULTANT->value, $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    public function forgetRequisiteConsultant(int $chatId): void
    {
        $this->forget(RequisiteField::REQUISITECONSULTANT->value, $chatId);
    }

    // === WALLET ===

    public function setWalletConsultant(int $chatId, ?int $ttl = null): void
    {
        $this->set(WalletField::WALLETCONSULTANT->value, $chatId, true, $ttl);
    }
    public function getWalletConsultant(int $chatId): ?bool
    {
        $value = $this->get(WalletField::WALLETCONSULTANT->value, $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    public function forgetWalletConsultant(int $chatId): void
    {
        $this->forget(WalletField::WALLETCONSULTANT->value, $chatId);
    }


    // // === CONSULTATION ===

    public function setMessageGroupForConsultation(int $chatId, int $messageGroupId, ?int $ttl = null): void
    {
        $this->set('message_group_in_consultation', $chatId, $messageGroupId, $ttl);
    }
    public function getMessageGroupForConsultation(int $chatId): ?string
    {
        return $this->get('message_group_in_consultation', $chatId);
    }
    public function forgetMessageGroupForConsultation(int $chatId): void
    {
        $this->forget('message_group_in_consultation', $chatId);
    }
    public function setClientInConsultation(int $chatId, ?int $ttl = null): void
    {
        $this->set('client_in_consultation', $chatId, true, $ttl);
    }
    public function getClientInConsultation(int $chatId): ?bool
    {
        $value = $this->get('client_in_consultation', $chatId);
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }


    // === LANGUAGE ===

    public function setLanguage(int $clientId, string $lang): void
    {
        $this->set('lang', $clientId, $lang);
    }
    public function getLanguage(int $clientId): ?string
    {
        return $this->get('lang', $clientId);
    }
}

