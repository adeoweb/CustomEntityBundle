<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Platform\Bundle\ImportExportBundle\Infrastructure\Validation\Storage;
use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

use function array_combine;
use function array_flip;
use function array_keys;
use function in_array;
use function json_encode;

/**
 * Constraint collection provider adding the reference data list as validation constraint
 *
 * @author     Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements ConstraintCollectionProviderInterface
{
    /** @var Registry */
    protected Registry $configurationRegistry;

    /** @var string[] */
    protected array $supportedJobNames;

    /** @var string[] */
    protected array $decimalSeparators;

    /** @var string[] */
    protected array $dateFormats;

    /**
     * @param Registry $configurationRegistry
     * @param string[] $supportedJobNames
     * @param string[] $decimalSeparators
     * @param string[] $dateFormats
     */
    public function __construct(
        Registry $configurationRegistry,
        array $supportedJobNames,
        array $decimalSeparators,
        array $dateFormats,
    ) {
        $this->configurationRegistry = $configurationRegistry;
        $this->supportedJobNames = $supportedJobNames;
        $this->decimalSeparators = $decimalSeparators;
        $this->dateFormats = $dateFormats;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection(): Collection
    {
        $referenceDataNames = $this->configurationRegistry->getNames();

        return new Collection(
            [
                'fields' => [
                    'reference_data_name' => [
                        new NotBlank(),
                        new Choice(
                            [
                                'choices' => array_combine($referenceDataNames, $referenceDataNames),
                                'message' => 'The value must be one of the configured reference data names'
                            ]
                        )
                    ],
                    'storage'   => new Storage(['csv']),
                    'decimal_separator' => [
                        new NotBlank(),
                        new Choice(
                            [
                                'strict' => true,
                                'choices' => array_flip($this->decimalSeparators),
                                'message' => 'The value must be one of: ' . json_encode(array_keys($this->decimalSeparators)),
                            ]
                        ),
                    ],
                    'date_format' => [
                        new NotBlank(),
                        new Choice(
                            [
                                'strict' => true,
                                'choices' => array_flip($this->dateFormats),
                                'message' => 'The value must be one of: ' . json_encode(array_keys($this->dateFormats)),
                            ]
                        ),
                    ],
                    'delimiter'  => [
                        new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
                        new Choice(
                            [
                                'strict' => true,
                                'choices' => [",", ";", "|"],
                                'message' => 'The value must be one of , or ; or |',
                                'groups'  => ['Default', 'FileConfiguration'],
                            ]
                        ),
                    ],
                    'enclosure'  => [
                        [
                            new NotBlank(['groups' => ['Default', 'FileConfiguration']]),
                            new Choice(
                                [
                                    'strict' => true,
                                    'choices' => ['"', "'"],
                                    'message' => 'The value must be one of " or \'',
                                    'groups'  => ['Default', 'FileConfiguration'],
                                ]
                            ),
                        ],
                    ],
                    'withHeader' => new Type(
                        [
                            'type'   => 'bool',
                            'groups' => ['Default', 'FileConfiguration'],
                        ]
                    ),
                    'users_to_notify' => [
                        new Type('array'),
                        new All(new Type('string')),
                    ],
                    'is_user_authenticated' => new Type('bool'),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job): bool
    {
        return in_array($job->getName(), $this->supportedJobNames, true);
    }
}
