<?php
/**
 * HTML
 *
 * Плагин обеспечивает визуальное редактирование текстографических страниц
 *
 * @version ${product.version}
 *
 * @copyright 2005, Михаил Красильников
 * @copyright 2007, Eresus Group, http://eresus.ru/
 * @copyright 2010, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
 * @author Ghost <ghost@dvaslona.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package HTML
 */

/**
 * Класс плагина
 *
 * @package HTML
 */
class Html extends ContentPlugin
{
    /**
     * Версия
     *
     * @var string
     * @since 1.00
     */
    public $version = '${product.version}';

    /**
     * Требуемая версия CMS
     *
     * @var string
     */
    public $kernel = '3.00b';

    /**
     * Название
     *
     * @var string
     * @since 1.00
     */
    public $title = 'HTML';

    /**
     * Описание
     *
     * @var string
     * @since 1.00
     */
    public $description = 'Плагин обеспечивает визуальное редактирование текстографических страниц';

    /**
     * Настройки
     *
     * @var array
     *
     * @since x.xx
     */
    public $settings = array(
        // Заметки
        'notes' => false
    );

    /**
     * Действия при установке модуля
     */
    public function install()
    {
        parent::install();
        $driver = ORM::getManager()->getDriver();
        $driver->createTable(ORM::getTable($this, 'Page'));
    }

    /**
     * Действия при удалении модуля
     */
    public function uninstall()
    {
        $driver = ORM::getManager()->getDriver();
        $driver->dropTable(ORM::getTable($this, 'Page'));
        parent::uninstall();
    }

    /**
     * Обновление контента
     *
     * @param string $content  новый контент
     *
     * @return void;
     */
    public function updateContent($content)
    {
        $sections = Eresus_CMS::getLegacyKernel()->sections;
        $item = $sections->get(Eresus_Kernel::app()->getPage()->id);
        $section = new Html_SiteSection($item);
        $section->setContent($content);
        $section->setOption('disallowGET', !arg('allowGET', 'int'));
        $section->setOption('disallowPOST', !arg('allowPOST', 'int'));
        $section->setOption('html.rel_canonical', arg('canonical', 'int'));

        if ($this->settings['notes'])
        {
            $notes = arg('notes');
            if (!is_null($notes))
            {
                $section->setNotes($notes);
            }
        }

        $sections->update($section->toArray());
    }

    /**
     * Отрисовка административной части
     *
     * @return string  Контент
     */
    public function adminRenderContent()
    {
        if (arg('action') == 'update')
        {
            $this->adminUpdate();
        }

        $item = Eresus_CMS::getLegacyKernel()->sections->get(Eresus_Kernel::app()->getPage()->id);
        $section = new Html_SiteSection($item);
        $form = array(
            'name' => 'contentEditor',
            'caption' => Eresus_Kernel::app()->getPage()->title,
            'width' => '100%',
            'fields' => array(array('type' => 'hidden', 'name' => 'action', 'value' => 'update')),
            'buttons' => array('apply', 'reset'),
        );

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();

        if ($this->settings['notes'])
        {
            $form['fields'] []= array('type' => 'memo', 'name' => 'notes', 'height' => '5',
                'value' => $section->getNotes(), 'style' => 'display: none',
                'extra' => 'id="notes-input"', 'disabled' => true);

            // TODO Переделать весь этот ужас при переходе на Eresus 3.01
            $script = <<<END
function _html_show_notes()
{
    document.getElementById('notes-button').remove();
    var n=document.getElementById('notes-input');
    n.style.display='block';
    n.removeAttribute('disabled');
}
END;
            $page->addScripts($script);

            $notes = $section->getNotes();
            if ($notes)
            {
                $form['fields'] []= array('type' => 'text', 'value' => InfoBox(nl2br($notes), ''));
                $label = 'Изменить заметку';
            }
            else
            {
                $label = 'Добавить заметку';
            }
            $form['fields'] []= array('type' => 'text',
                'value' => '<a href="javascript:_html_show_notes();" id="notes-button">' . $label . '</a>');
        }
        $form['fields'] []= array('type' => 'html', 'name' => 'content', 'height' => '400px',
                    'value' => $section->getContent());
        $form['fields'] []= array('type' => 'text', 'value' => 'Адрес страницы: <a href="'
                . $section->getClientUrl() . '">' . $section->getClientUrl() . '</a>');
        $form['fields'] []= array('type' => 'checkbox', 'name' => 'allowGET',
                    'label' => 'Разрешить передавать аргументы методом GET',
                    'value' => !$section->getOption('disallowGET', true));
        $form['fields'] []= array('type' => 'checkbox', 'name' => 'allowPOST',
                    'label' => 'Разрешить передавать аргументы методом POST',
                    'value' => !$section->getOption('disallowPOST', true));
        $form['fields'] []= array('type' => 'checkbox', 'name' => 'canonical',
                    'label' => 'Добвлять к странице мета-тег «rel="canonical"»',
                    'value' => $section->getOption('html.rel_canonical', true));

        $result = $page->renderForm($form, $item);
        return $result;
    }

    /**
     * Возвращает контент раздела для КИ
     *
     * @return string  HTML
     */
    public function clientRenderContent()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $section = Html_SiteSection::createFromWebPage($page);

        if (!$this->isValidRequest())
        {
            $page->httpError(404);
        }

        if ($section->getOption('html.rel_canonical', true))
        {
            /*
             * TODO После выполнения https://github.com/Eresus/EresusCMS/issues/39
             * можно будет переделать
             */
            $headProperty = new ReflectionProperty('WebPage', 'head');
            $headProperty->setAccessible(true);
            $head = $headProperty->getValue($page);
            $head['content'] .= '<link rel="canonical" href="' . $section->getClientUrl() . '">';
            $headProperty->setValue($page, $head);
        }

        $html = $page->content;

        return $html;
    }

    /**
     * Диалог настроек
     *
     * @return string
     *
     * @since x.xx
     */
    public function settings()
    {
        $form = array(
            'name' => 'SettingsForm',
            'caption' => $this->title . ' ' . $this->version,
            'width' => '500px',
            'fields' => array (
                array('type' => 'hidden', 'name' => 'update', 'value' => $this->name),
                array('type' => 'checkbox', 'name' => 'notes', 'label' => 'Включить заметки'),
            ),
            'buttons' => array('ok', 'apply', 'cancel'),
        );

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $html = $page->renderForm($form, $this->settings);
        return $html;
    }

    /**
     * Возвращает true, если запрос допустим
     *
     * Проверяет запрос в соответствии с настройками раздела.
     *
     * @return bool
     */
    private function isValidRequest()
    {
        $request = Eresus_CMS::getLegacyKernel()->request;
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $options = $page->options;
        $disallowPOST = array_key_exists('disallowPOST', $options) && $options['disallowPOST'];
        if ('POST' == $request['method'] && $disallowPOST)
        {
            return false;
        }

        $disallowGET = array_key_exists('disallowGET', $options) && $options['disallowGET'];
        $hasGetArgs = $request['url'] != $request['path'];
        if ($hasGetArgs && $disallowGET)
        {
            return false;
        }
        return true;
    }
}

