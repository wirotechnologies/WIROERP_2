<?php
namespace App\Service\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\Serializer\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory as FactoryClassMetadataFactory;

class DTOSerializer implements SerializerInterface
{

    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            //normalizers
            [new ObjectNormalizer(
                classMetadataFactory: new FactoryClassMetadataFactory(new AnnotationLoader(new AnnotationReader())) ,
                nameConverter: new CamelCaseToSnakeCaseNameConverter())],
            //encoders
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

}
