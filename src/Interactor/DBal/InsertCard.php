<?php
declare(strict_types=1);

namespace IamPersistent\SimpleShop\Interactor\DBal;

use IamPersistent\SimpleShop\Entity\CreditCard;
use IamPersistent\SimpleShop\Interactor\InsertCardInterface;
use IamPersistent\SimpleShop\Interactor\PascalCase;
use Omnipay\Common\CreditCard as OmniCreditCard;

final class InsertCard extends DBalCommon implements InsertCardInterface
{
    public function insert(CreditCard $creditCard): bool
    {
        $this->persist($creditCard);

        return true;
    }

    private function persist(CreditCard $creditCard)
    {
        $omniCard = new OmniCreditCard(['number' => $creditCard->getCardNumber()]);

        $data = [
            'brand'           => (new PascalCase)($creditCard->getBrand()),
            'card_number'     => $omniCard->getNumberMasked(),
            'card_reference'  => $creditCard->getCardReference(),
            'city'            => $creditCard->getCity(),
            'country'         => $creditCard->getCountry(),
            'expiration_date' => $creditCard->getExpirationDate()->format('Y-m-d'),
            'last_four'       => $omniCard->getNumberLastFour(),
            'name_on_card'    => $creditCard->getNameOnCard(),
            'owner_id'        => $creditCard->getOwnerId(),
            'post_code'       => $creditCard->getPostCode(),
            'state'           => $creditCard->getState(),
            'street_1'        => $creditCard->getStreet1(),
            'street_2'        => $creditCard->getStreet2(),
            'title'           => $creditCard->getTitle(),
        ];
        $response = $this->connection->insert('credit_cards', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $creditCard->setId($id);
        } else {

        }
    }
}
