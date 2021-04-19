<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\Standard;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class MinimalExternalApiNormalizer
 * @package Pim\Bundle\CustomEntityBundle\Normalizer\Standard
 */
class MinimalExternalApiNormalizer implements NormalizerInterface
{
    /** @var array $supportedFormats */
    protected $supportedFormats = ['external_api'];

    /**
     * @param AbstractTranslatableCustomEntity $entity
     * @param null                 $format
     * @param array                $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        return [
            'code' => $entity->getCode(),
            'values' => [
                'label' => $this->getLabels($entity)
            ],
        ];
    }

    /**
     * @param $data
     * @param string|null $locale
     * @param string|null $scope
     * @return array
     */
    protected function valueNormalizer($data, string $locale = null, string $scope = null): array
    {
        return ['locale' => $locale, 'scope' => $scope, 'data' => $data];
    }

    /**
     * @param AbstractTranslatableCustomEntity $object
     * @return array
     */
    protected function getLabels(AbstractTranslatableCustomEntity $object): array
    {
        $labels = [];
        foreach ($object->getTranslations() as $translation) {
            $labels[] = $this->valueNormalizer($translation->getLabel(), $translation->getLocale());
        }

        return $labels;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }
}
