<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Export\Xls\XlsFileName;
use CultuurNet\UiTPASBeheer\Export\Xls\XlsFileWriter;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\AddressJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\NameJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferencesJsonDeserializer;
use Silex\Application;
use Silex\ServiceProviderInterface;

class PassHolderServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['passholder_service'] = $app->share(
            function (Application $app) {
                return new PassHolderService(
                    $app['uitpas'],
                    $app['counter_consumer_key'],
                    $app['counter']
                );
            }
        );

        $app['passholder_iterator_factory'] = $app->share(
            function (Application $app) {
                return new PassHolderIteratorFactory(
                    $app['passholder_service']
                );
            }
        );

        $app['passholder_export_filewriter'] = $app->share(
            function (Application $app) {
                return new XlsFileWriter(
                    new XlsFileName(PassHolderControllerProvider::EXPORT_FILENAME)
                );
            }
        );

        $app['passholder_json_deserializer'] = $app->share(
            function (Application $app) {
                return new PassHolderJsonDeserializer(
                    new NameJsonDeserializer(),
                    new AddressJsonDeserializer(),
                    new BirthInformationJsonDeserializer(),
                    new ContactInformationJsonDeserializer(),
                    new PrivacyPreferencesJsonDeserializer()
                );
            }
        );

        $app['registration_json_deserializer'] = $app->share(
            function (Application $app) {
                return new RegistrationJsonDeserializer(
                    $app['passholder_json_deserializer'],
                    new KansenStatuutJsonDeserializer()
                );
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
