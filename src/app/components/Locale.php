<?php
//class for translation
namespace App\Components;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class Locale extends Injectable
{

    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        // Ask browser what is the best language
        $selectedLanguage = $this->request->get('locale');
        $language = $selectedLanguage ? $selectedLanguage : $this->request->getBestLanguage();
        $messages = [];

        $translationFile = '../app/messages/' . $language . '.php';

        if (true !== file_exists($translationFile)) {
            $translationFile = '../app/messages/en.php';
        }

        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }

    /**
     * getLocale()
     * 
     * function to check if file already exists is cache and return it
     *
     * @return void
     */
    public function getLocale()
    {
        $selectedLanguage = $this->request->get('locale');
        $language = $selectedLanguage ? $selectedLanguage : $this->request->getBestLanguage();
        if ($language) {
            if (!$this->cache->has($language)) {
                $this->cache->clear();
                $this->cache->set($language, $this->locale);
            }
            $locale = $this->cache->get($language);
            return $locale;
        } else {
            return $this->locale;
        }
    }
}
