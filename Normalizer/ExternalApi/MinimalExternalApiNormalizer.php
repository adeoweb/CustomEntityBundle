<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\ExternalApi;

use Akeneo\Tool\Component\Api\Hal\Link;
use Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class MinimalExternalApiNormalizer
 * @package Pim\Bundle\CustomEntityBundle\Normalizer\Standard
 */
class MinimalExternalApiNormalizer implements NormalizerInterface
{
    /** @var array $supportedFormats */
    protected $supportedFormats = ['external_api'];

    private $router;

    /**
     * ErpBrandNormalizer constructor.
     * @param $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

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
     * @param $fileInfo
     * @param string|null $locale
     * @param string|null $scope
     *
     * @return array|null
     */
    protected function getFileData($fileInfo, string $locale = null, string $scope = null): ?array
    {
        if (!$fileInfo) {
            return null;
        }

        return [
            'locale' => $locale,
            'channel' => $scope,
            'data' => $fileInfo->getKey(),
            '_links' => $this->createLink($fileInfo)
        ];
    }

    protected function createLink($file)
    {
        $route = $this->router->generate(
            'veritas_api_media_file_list',
            ['code' => $file->getKey()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link = new Link('download', $route);

        return $link->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }
}
