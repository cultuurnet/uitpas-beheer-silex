<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications\HasAvailableCoupon;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications\HasAvailableKansentarief;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications\HasDifferentiation;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications\HasReachedMaximumSales;
use ValueObjects\Number\Real;

class SalesInformation implements \JsonSerializable
{
    /**
     * @var Prices
     */
    protected $basePrices;

    /**
     * @var Tariff[]
     */
    protected $tariffs;

    /**
     * @param Prices $basePrices
     */
    public function __construct(Prices $basePrices)
    {
        $this->basePrices = $basePrices;
        $this->tariffs = array();
    }

    /**
     * @return Prices
     */
    public function getBasePrices()
    {
        return $this->basePrices;
    }

    /**
     * @return Tariff[]
     */
    public function getTariffs()
    {
        return $this->tariffs;
    }

    /**
     * @param Tariff $tariff
     * @return SalesInformation
     */
    public function withTariff(Tariff $tariff)
    {
        if (!$tariff->getPrices()->containsOnlyPriceClassesOf($this->basePrices)) {
            throw new \InvalidArgumentException(
                'The provided tariff has a price for a price class that is not in the base prices.'
            );
        }

        $c = clone $this;
        $c->tariffs[] = $tariff;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = array(
            'maximumReached' => HasReachedMaximumSales::isSatisfiedBy($this),
            'differentiation' => HasDifferentiation::isSatisfiedBy($this),
            'base' => $this->basePrices->jsonSerialize(),
            'tariffs' => array(
                'kansentariefAvailable' => HasAvailableKansentarief::isSatisfiedBy($this),
                'couponAvailable' => HasAvailableCoupon::isSatisfiedBy($this),
            ),
        );

        if (!empty($this->tariffs)) {
            $lowestAvailable = null;
            $encodedTariffs = [];

            // Loop over each tariff and serialize them to json.
            foreach ($this->tariffs as $tariff) {
                $encodedTariffs[] = $tariff->jsonSerialize();

                // Loop over all prices of the tariff and compare against the
                // lowest so far, but only if the tariff has not reached it's
                // maximum number of sales.
                if (!$tariff->hasReachedMaximum()) {
                    foreach ($tariff->getPrices() as $price) {
                        $price = $price->toNative();
                        if ($price < $lowestAvailable || is_null($lowestAvailable)) {
                            $lowestAvailable = $price;
                        }
                    }
                }
            }

            // Sort the json encoded tariffs so the kansentarief tariff
            // is always on top.
            usort(
                $encodedTariffs,
                function (array $a, array $b) {
                    if ($a['type'] == $b['type']) {
                        // A and B have the same type, so have an equal weight.
                        return 0;
                    } elseif ($a['type'] == TariffType::KANSENTARIEF) {
                        // A has kansentarief, so A is lighter than B.
                        return -1;
                    } elseif ($b['type'] == TariffType::KANSENTARIEF) {
                        // B has kansentarief, so A is heavier than B.
                        return 1;
                    } else {
                        // Neither A nor B has kansentarief, so they have equal weight.
                        return 0;
                    }
                }
            );

            $data['tariffs'] += array(
                'lowestAvailable' => $lowestAvailable,
                'list' => $encodedTariffs,
            );
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent $event
     * @return SalesInformation
     */
    public static function fromCultureFeedUitpasEvent(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        // @todo Populate the basePrices based on the priceClasses object on
        // $event when it becomes available in the API. For now we will
        // populate the basePrices based on the info found in the priceClasses
        // of the ticketSales (which contain both the base price, and the
        // tariff).
        $basePrices = new Prices();
        foreach ($event->ticketSales as $ticketSale) {
            foreach ($ticketSale->priceClasses as $priceClass) {
                $basePrices = $basePrices->withPricing(
                    new PriceClass($priceClass->name),
                    new Real($priceClass->price)
                );
            }
        }
        if (empty($basePrices->count())) {
            // If we have no base prices from ticketSales, we have to use the
            // old price property on $event. Otherwise the IsFree specification
            // thinks that the activity is free, as there would be no base
            // price higher than zero.
            /* @var Prices $basePrices */
            $basePrices = $basePrices->withPricing(
                new PriceClass('Standaard'),
                new Real((float) $event->price)
            );
        }

        $salesInformation = new SalesInformation($basePrices);

        foreach ($event->ticketSales as $ticketSale) {
            $constraint = $ticketSale->buyConstraintReason;
            if (is_null($constraint) ||
                $constraint == \CultureFeed_Uitpas_Event_TicketSale_Opportunity::BUY_CONSTRAINT_MAXIMUM_REACHED
            ) {
                $salesInformation = $salesInformation->withTariff(
                    Tariff::fromCultureFeedTicketSaleOpportunity($ticketSale)
                );
            }
        }

        return $salesInformation;
    }
}
