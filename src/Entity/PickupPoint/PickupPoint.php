<?php

declare(strict_types=1);

namespace App\Entity\PickupPoint;

use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Repository\PickupPoint\PickupPointRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PickupPointRepository::class)]
#[ORM\Table(name: 'pickup_points')]
#[ORM\UniqueConstraint(name: 'carrier_externalId_country', columns: ['carrier', 'externalId', 'country'])]
final class PickupPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint', options: ['unsigned' => true])]
    // Doctrine assigns generated identifiers after persisting; PHPStan cannot infer that write.
    /** @phpstan-ignore-next-line property.unusedType */
    private ?string $id = null;

    public function __construct(
        #[ORM\Column(name: 'externalId', length: 255)]
        private string $externalId,
        #[ORM\Column(name: 'carrier', length: 255)]
        private string $carrier,
        #[ORM\Column(name: 'type', length: 16, enumType: PickupPointType::class)]
        private PickupPointType $type,
        #[ORM\Column(name: 'status', length: 32, enumType: PickupPointStatus::class)]
        private PickupPointStatus $status,
        #[ORM\Column(name: 'city', length: 255)]
        private string $city,
        #[ORM\Column(name: 'name', length: 255)]
        private string $name,
        #[ORM\Column(name: 'address', length: 255)]
        private string $address,
        #[ORM\Column(name: 'zipCode', length: 255)]
        private string $zipCode,
        #[ORM\Column(name: 'country', length: 2)]
        private string $country,
        #[ORM\Column(name: 'latitude', type: 'decimal', precision: 10, scale: 8)]
        private string $latitude,
        #[ORM\Column(name: 'longitude', type: 'decimal', precision: 11, scale: 8)]
        private string $longitude,
        #[ORM\Column(name: 'openingHours', type: 'text', nullable: true)]
        private ?string $openingHours = null,
        #[ORM\Column(name: 'created', type: 'datetime_immutable')]
        private DateTimeImmutable $created = new DateTimeImmutable(),
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function getType(): PickupPointType
    {
        return $this->type;
    }

    public function getStatus(): PickupPointStatus
    {
        return $this->status;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function getOpeningHours(): ?string
    {
        return $this->openingHours;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function update(
        PickupPointType $type,
        PickupPointStatus $status,
        string $city,
        string $name,
        string $address,
        string $zipCode,
        string $latitude,
        string $longitude,
        ?string $openingHours,
    ): void {
        $this->type = $type;
        $this->status = $status;
        $this->city = $city;
        $this->name = $name;
        $this->address = $address;
        $this->zipCode = $zipCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->openingHours = $openingHours;
    }
}
