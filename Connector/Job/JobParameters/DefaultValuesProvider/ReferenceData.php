<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Akeneo\Tool\Component\Localization\Localizer\LocalizerInterface;

use function in_array;
use function sys_get_temp_dir;

/**
 * Default value provider for reference data list
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements DefaultValuesProviderInterface
{
    /** @var string[] */
    protected array $supportedJobNames;

    /**
     * @param string[] $supportedJobNames
     */
    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues(): array
    {
        return [
            'reference_data_name'   => null,
            'storage' => [
                'type' => 'csv',
                'file_path' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'export_%job_label%_%datetime%.csv',
            ],
            'decimal_separator'     => LocalizerInterface::DEFAULT_DECIMAL_SEPARATOR,
            'date_format'           => LocalizerInterface::DEFAULT_DATE_FORMAT,
            'delimiter'             => ';',
            'enclosure'             => '"',
            'withHeader'            => true,
            'users_to_notify'       => [],
            'is_user_authenticated' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job): bool
    {
        return in_array($job->getName(), $this->supportedJobNames, true);
    }
}
