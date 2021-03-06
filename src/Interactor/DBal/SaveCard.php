<?php
declare(strict_types=1);

namespace IamPersistent\SimpleShop\Interactor\DBal;

use IamPersistent\SimpleShop\Entity\CreditCard;
use IamPersistent\SimpleShop\Interactor\ObjectHasId;
use IamPersistent\SimpleShop\Interactor\PascalCase;
use IamPersistent\SimpleShop\Interactor\SaveCardInterface;
use Omnipay\Common\CreditCard as OmniCreditCard;

final class SaveCard extends DBalCommon implements SaveCardInterface
{
    public function save(CreditCard $card): bool
    {
        if ((new ObjectHasId)($card)) {
            return $this->update($card);
        } else {
            return $this->insert($card);
        }
    }

    public function insert(CreditCard $creditCard): bool
    {
        $this->persist($creditCard);

        return true;
    }

    private function persist(CreditCard $creditCard)
    {
        $omniCard = new OmniCreditCard(['number' => $creditCard->getCardNumber()]);

        $data = [
            'brand' => (new PascalCase)($creditCard->getBrand()),
            'card_number' => $omniCard->getNumberMasked(),
            'card_reference' => $creditCard->getCardReference(),
            'expiration_date' => $creditCard->getExpirationDate()->format('Y-m-d'),
            'last_four' => $omniCard->getNumberLastFour(),
            'owner_id' => $creditCard->getOwnerId(),
            'title' => $creditCard->getTitle(),
        ];
        $response = $this->connection->insert('credit_cards', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $creditCard->setId($id);
        } else {

        }
    }
}
