<?php
namespace ADmad\I18n\Routing\Route;

use Cake\Core\Configure;
use Cake\Routing\Route\DashedRoute;
use Cake\Utility\Hash;

class I18nRoute extends DashedRoute
{
    /**
     * Regular expression for `lang` route element.
     *
     * @var string
     */
    protected static $_langRegEx = null;

    /**
     * Constructor for a Route.
     *
     * @param string $template Template string with parameter placeholders
     * @param array $defaults Array of defaults for the route.
     * @param string $options Array of parameters and additional options for the Route
     *
     * @return void
     */
    public function __construct($template, $defaults = [], array $options = [])
    {
        if (strpos($template, ':language') === false) {
            $template = '/:language' . $template;
        }
        if ($template === '/:language/') {
            $template = '/:language';
        }

        $options['inflect'] = 'dasherize';
        $options['persist'][] = 'language';

        if (!array_key_exists('language', $options)) {
            if (self::$_langRegEx === null &&
                $langs = Configure::read('I18n.languages')
            ) {
                self::$_langRegEx = implode('|', array_keys(Hash::normalize($langs)));
            }
            $options['language'] = self::$_langRegEx;
        }

        parent::__construct($template, $defaults, $options);
    }
}
