<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Entity normalizer
 */
class EntityNormalizer extends ObjectNormalizer
{
    #[EntityManagerInterface]
    protected $em;
    
    #[EntityManagerInterface]
    public function __construct(
        EntityManagerInterface $em,
        ?ClassMetadataFactoryInterface $classMetadataFactory = null,
        ?NameConverterInterface $nameConverter = null,
        ?PropertyAccessorInterface $propertyAccessor = null,
        ?PropertyTypeExtractorInterface $propertyTypeExtractor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);

        $this->em = $em;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return true;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $invitation = new Invitation();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $key = explode('_', $k);
                if(isset($key[1])) {
                    $method = "set" . ucfirst($key[0]) . ucfirst($key[1]);
                    $invitation->{$method}($this->em->find('App\\Entity\\User', $v));
                }
            }
        }
        return $invitation;
    }
}
