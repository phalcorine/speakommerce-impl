<?php

namespace App\Modules\Storefront\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckoutForm
{
    #[NotBlank(message: 'Full Name is Required', normalizer: 'trim')]
    private ?string $fullName = '';

    #[NotBlank(message: 'Address is Required', normalizer: 'trim')]
    private ?string $address = '';

    #[NotBlank(message: 'ZIP Code is Required', normalizer: 'trim')]
    private ?string $zipCode = '';

    #[NotBlank(message: 'City is Required', normalizer: 'trim')]
    private ?string $city = '';

    #[NotBlank(message: 'Country is Required', normalizer: 'trim')]
    private ?string $country = '';

    #[NotBlank(message: 'Card Number is Required', normalizer: 'trim')]
    private ?string $cardNumber = '';

    #[NotBlank(message: 'Card Expiry Date is Required', normalizer: 'trim')]
    private ?string $cardExpiryDate = '';

    #[NotBlank(message: 'Card Code/CVV is Required', normalizer: 'trim')]
    private ?string $cardCode = '';

    public static function fromRequest(Request $request): CheckoutForm
    {
        return (new self())
            ->setFullName($request->request->get('full_name'))
            ->setAddress($request->request->get('address'))
            ->setZipCode($request->request->get('zip_code'))
            ->setCity($request->request->get('city'))
            ->setCountry($request->request->get('country'))
            ->setCardNumber($request->request->get('card_number'))
            ->setCardExpiryDate($request->request->get('card_expiry_date'))
            ->setCardCode($request->request->get('card_code'));
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return CheckoutForm
     */
    public function setFullName(?string $fullName): CheckoutForm
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return CheckoutForm
     */
    public function setAddress(?string $address): CheckoutForm
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     * @return CheckoutForm
     */
    public function setZipCode(?string $zipCode): CheckoutForm
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return CheckoutForm
     */
    public function setCity(?string $city): CheckoutForm
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     * @return CheckoutForm
     */
    public function setCountry(?string $country): CheckoutForm
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    /**
     * @param string|null $cardNumber
     * @return CheckoutForm
     */
    public function setCardNumber(?string $cardNumber): CheckoutForm
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCardExpiryDate(): ?string
    {
        return $this->cardExpiryDate;
    }

    /**
     * @param string|null $cardExpiryDate
     * @return CheckoutForm
     */
    public function setCardExpiryDate(?string $cardExpiryDate): CheckoutForm
    {
        $this->cardExpiryDate = $cardExpiryDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCardCode(): ?string
    {
        return $this->cardCode;
    }

    /**
     * @param string|null $cardCode
     * @return CheckoutForm
     */
    public function setCardCode(?string $cardCode): CheckoutForm
    {
        $this->cardCode = $cardCode;
        return $this;
    }


}