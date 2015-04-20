<?php
/**
 * Visual Markdown Editor Field for Kirby 2
 *
 * @version   1.2.0
 * @author    Jonas Döbertin <hello@jd-powered.net>
 * @copyright Jonas Döbertin <hello@jd-powered.net>
 * @link      https://github.com/JonasDoebertin/kirby-visual-markdown
 * @license   GNU GPL v3.0 <http://opensource.org/licenses/GPL-3.0>
 */

/**
 * Visual Markdown Editor Field
 *
 * @since 1.0.0
 */
class MarkdownField extends InputField {

    /**
     * Language files directory
     *
     * @since 1.2.0
     */
    const LANG_DIR = 'languages';

    /**
     * Define frontend assets
     *
     * @var array
     * @since 1.0.0
     */
    public static $assets = array(
        'js' => array(
            'screenfull-2.0.0.min.js',
            'codemirror-5.1.0.js',
            'codemirror-addon-continuelist-5.1.0.js',
            'codemirror-mode-xml-5.1.0.js',
            'codemirror-mode-markdown-5.1.0.js',
            'visualmarkdownfield.js',
            'visualmarkdowneditor.js',
        ),
        'css' => array(
            'codemirror-5.1.0.css',
            'visualmarkdown.css',
        ),
    );

    /**
     * Option: Show/Hide toolbar
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $toolbar = true;

    /**
     * Option: Header 1
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $header1 = 'h1';

    /**
     * Option: Header 2
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $header2 = 'h2';

    /**
     * Translated strings
     *
     * @since 1.2.0
     *
     * @var array
     */
    protected $translation;

    /**************************************************************************\
    *                          GENERAL FIELD METHODS                           *
    \**************************************************************************/

    /**
     * Field setup
     *
     * (1) Load language files
     *
     * @since 1.2.0
     */
    public function __construct()
    {
        /*
            (1) Load language files
         */
        $baseDir = __DIR__ . DS . self::LANG_DIR . DS;
        $lang    = panel()->language();
        if(file_exists($baseDir . $lang . '.php'))
        {
            $this->translation = include $baseDir . $lang . '.php';
        }
        else
        {
            $this->translation = include $baseDir . 'en.php';
        }
    }

    /**
     * Magic setter
     *
     * Set a fields property and apply default value if required.
     *
     * @since 1.1.0
     *
     * @param string $option
     * @param mixed  $value
     */
    public function __set($option, $value)
    {
        /* Set given value */
        $this->$option = $value;

        /* Check if value is valid */
        switch($option)
        {
            case 'toolbar':
                if(in_array($value, array(false, 'hide')))
                {
                    $this->toolbar = false;
                }
                else
                {
                    $this->toolbar = true;
                }
                break;
            case 'header1':
                if(!in_array($value, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6')))
                {
                    $this->header1 = 'h1';
                }
                break;
            case 'header2':
                if(!in_array($value, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6')))
                {
                    $this->header2 = 'h2';
                }
                break;
        }

    }

    /**
     * Convert result to markdown
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function result()
    {
        return str_replace(array("\r\n", "\r"), "\n", parent::result());
    }

    /**************************************************************************\
    *                            PANEL FIELD MARKUP                            *
    \**************************************************************************/

    /**
     * Create input element
     *
     * @since 1.0.0
     *
     * @return \Brick
     */
    public function input()
    {
        // Set up translation
        $translation = tpl::load(__DIR__ . DS . 'partials' . DS . 'translation.php', array('translations' => $this->translation));

        // Set up textarea
        $input = parent::input();
        $input->tag('textarea');
        $input->removeAttr('type');
        $input->removeAttr('value');
        $input->html($this->value() ?: false);
        $input->data(array(
            'field'   => 'markdownfield',
            'toolbar' => ($this->toolbar) ? 'true' : 'false',
            'header1' => $this->header1,
            'header2' => $this->header2,
        ));

        // Set up wrapping element
        $wrapper = new Brick('div', false);
        $wrapper->addClass('markdownfield-wrapper');
        $wrapper->addClass('markdownfield-field-' . $this->name);

        return $wrapper->append($translation)->append($input);
    }

    /**
     * Create outer field element
     *
     * @since 1.0.0
     *
     * @return \Brick
     */
    public function element()
    {
        $element = parent::element();
        $element->addClass('field-with-visualmarkdown');
        return $element;
    }

}
