<?php
declare(strict_types=1);

namespace IamPersistent\SimpleShop\Entity;

use DateTime;
use Money\Currency;
use Money\Money;

class Invoice
{
    /** @var Currency */
    private $currency;
    /** @var mixed */
    private $entrantId;
    /** @var string */
    private $header = '';
    /** @var mixed */
    private $id;
    /** @var DateTime */
    private $invoiceDate;
    /** @var string|null */
    private $invoiceNumber;
    /** @var InvoiceItem[] */
    private $items = [];
    /** @var mixed */
    private $recipientId;
    /** @var Paid|null */
    private $paid;
    /** @var Money */
    private $subtotal;
    /** @var Money */
    private $taxes;
    private string|null $taxRate = null;
    /** @var Money */
    private $total;

    public function calculate(): void
    {
        $this->sumInvoiceItems();
        $this->calculateTax();
        $this->calculateTotal();
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): Invoice
    {
        $this->currency = $currency;

        return $this;
    }

    public function getEntrantId()
    {
        return $this->entrantId;
    }

    public function setEntrantId($entrantId)
    {
        $this->entrantId = $entrantId;

        return $this;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setHeader(string $header): Invoice
    {
        $this->header = $header;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getInvoiceDate(): DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(DateTime $invoiceDate): Invoice
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): Invoice
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function addItem(InvoiceItem $invoiceItem, bool $recalculateTotal = true): Invoice
    {
        $this->items[] = $invoiceItem;
        if ($recalculateTotal) {
            $this->calculateInvoice();
        }

        return $this;
    }

    /**
     * @return InvoiceItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): Invoice
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->addItem($item, false);
        }

        return $this;
    }

    public function getRecipientId()
    {
        return $this->recipientId;
    }

    public function setRecipientId($recipientId)
    {
        $this->recipientId = $recipientId;

        return $this;
    }

    public function getPaid(): ?Paid
    {
        return $this->paid;
    }

    public function setPaid(Paid $paid): Invoice
    {
        $this->paid = $paid;

        return $this;
    }

    public function getSubtotal(): ?Money
    {
        return $this->subtotal;
    }

    public function setSubtotal(Money $subtotal): Invoice
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTaxes(): ?Money
    {
        return $this->taxes;
    }

    public function setTaxes(Money $taxes): Invoice
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getTaxRate(): string|null
    {
        return $this->taxRate;
    }

    public function setTaxRate(string|float|null $taxRate): Invoice
    {
        if (is_float($taxRate)) {
            $taxRate = (string) $taxRate;
        }
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getTotal(): ?Money
    {
        return $this->total;
    }

    public function setTotal(Money $total): Invoice
    {
        $this->total = $total;

        return $this;
    }

    private function calculateTax(): void
    {
        $taxableTotal = new Money(0, $this->getCurrency());
        foreach ($this->getItems() as $item) {
            if ($item->isTaxable()) {
                $taxableTotal = $taxableTotal->add($item->getTotalAmount());
            }
        }
        $taxes = $taxableTotal->multiply($this->getTaxRate());
        $this->setTaxes($taxes);
    }

    private function calculateItem(InvoiceItem $item): void
    {
        $quantity = $item->getQuantity() ?? 1;
        $totalAmount = $item->getAmount()->multiply($quantity);
        $item->setTotalAmount($totalAmount);
    }

    private function calculateTotal(): void
    {
        $total = $this->getSubtotal();
        $total = $total->add($this->getTaxes());
        $this->setTotal($total);
    }

    private function sumInvoiceItems(): void
    {
        $sum = new Money(0, $this->getCurrency());
        foreach ($this->getItems() as $item) {
            $this->calculateItem($item);
            $sum = $sum->add($item->getTotalAmount());
        }
        $this->setSubtotal($sum);
    }
}
