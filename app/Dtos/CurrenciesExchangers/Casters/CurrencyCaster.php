<?php

namespace App\Dtos\CurrenciesExchangers\Casters;

use App\Dtos\CurrenciesExchangers\CurrencyDto;
use App\Exceptions\CurrencyExchangerException;
use App\Exceptions\DataCasterException;
use App\Repositories\CurrencyExchangers\Interfaces\CurrencyExchangerInterface;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\IterableItemCast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class CurrencyCaster implements Cast, IterableItemCast
{
    public function __construct(private ?CurrencyExchangerInterface $currencyExchanger = null)
    {
        if ($this->currencyExchanger === null) {
            $this->currencyExchanger = app(CurrencyExchangerInterface::class);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws DataCasterException
     */
    public function cast(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context,
    ): CurrencyDto|Uncastable
    {
        return $this->castValue($value);
    }

    /**
     * @param mixed $value
     *
     * @return CurrencyDto|Uncastable
     * @throws DataCasterException
     */
    private function castValue(mixed $value): CurrencyDto|Uncastable
    {
        if ($value instanceof CurrencyDto) {
            return $value;
        }

        if (is_string($value) === false) {
            return Uncastable::create();
        }

        try {
            /** @noinspection ProperNullCoalescingOperatorUsageInspection */
            return $this->currencyExchanger
                ->getCurrencyFromCode(strtoupper($value)) ?? Uncastable::create();
        } catch (CurrencyExchangerException $exception) {
            throw new DataCasterException(
                message: 'Error grabbing currency code for currency caster',
                previous: $exception,
            );
        }
    }

    /**
     * @inheritDoc
     *
     * @throws DataCasterException
     */
    public function castIterableItem(
        DataProperty $property,
        mixed $value,
        array $properties,
        CreationContext $context,
    ): CurrencyDto|Uncastable
    {
        return $this->castValue($value);
    }
}
