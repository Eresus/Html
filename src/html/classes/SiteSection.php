<?php
/**
 * Модель раздела сайта
 *
 * @version ${product.version}
 *
 * @copyright 2014, ООО "Два слона", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников <mk@dvaslona.ru>
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
 */


/**
 * Модель раздела сайта
 *
 * @since 4.01
 */
class Html_SiteSection
{
    /**
     * «Сырые» данные раздела из БД
     *
     * @var array
     * @since 4.01
     */
    private $raw;

    /**
     * Создаёт модель разедла из объекта WebPage
     *
     * @param WebPage $page
     *
     * @return Html_SiteSection
     *
     * @since 4.01
     */
    public static function createFromWebPage(WebPage $page)
    {
        $raw = array();
        if ($page instanceof TClientUI)
        {
            $raw = $page->dbItem;
        }
        $section = new self($raw);
        return $section;
    }

    /**
     * Конструктор раздела
     *
     * @param array $raw  «сырые» данные из БД
     *
     * @since 4.01
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    /**
     * Возвращает контент раздела
     *
     * @return string
     *
     * @since 4.01
     */
    public function getContent()
    {
        return $this->raw['content'];
    }

    /**
     * Задаёт контент раздела
     *
     * @param string $html
     *
     * @since 4.01
     */
    public function setContent($html)
    {
        $this->raw['content'] = $html;
    }

    /**
     * Возвращает значение опции
     *
     * @param string $name     имя опции
     * @param mixed  $default  значение по умолчанию, если опция не задана
     *
     * @return mixed
     *
     * @since 4.01
     */
    public function getOption($name, $default = null)
    {
        return array_key_exists($name, $this->raw['options'])
            ? $this->raw['options'][$name]
            : $default;
    }

    /**
     * Задаёт значение опции
     *
     * @param string $name   имя опции
     * @param mixed  $value  значение
     *
     * @since 4.01
     */
    public function setOption($name, $value)
    {
        $this->raw['options'][$name] = $value;
    }

    /**
     * Возвращает клиентский URL раздела
     *
     * @return string
     *
     * @since 4.01
     */
    public function getClientUrl()
    {
        return Eresus_Kernel::app()->getPage()->clientURL($this->raw['id']);
    }

    /**
     * Возвращает заметки к странице
     *
     * @return string
     *
     * @since x.xx
     */
    public function getNotes()
    {
        $plugin = Eresus_Kernel::app()->getLegacyKernel()->plugins->load('html');
        $table = ORM::getTable($plugin, 'Page');
        /** @var Html_Entity_Page $page */
        $page = $table->find($this->raw['id']);
        if (is_null($page))
        {
            return '';
        }
        return $page->notes;
    }

    /**
     * Задаёт заметки
     *
     * @param string $notes
     *
     * @since x.xx
     */
    public function setNotes($notes)
    {
        $plugin = Eresus_Kernel::app()->getLegacyKernel()->plugins->load('html');
        $table = ORM::getTable($plugin, 'Page');
        /** @var Html_Entity_Page $page */
        $page = $table->find($this->raw['id']);
        if (is_null($page))
        {
            $page = new Html_Entity_Page();
            $page->id = $this->raw['id'];
        }
        $page->notes = $notes;
        if ($page->getEntityState() == ORM_Entity::IS_NEW)
        {
            $table->persist($page);
        }
        else
        {
            $table->update($page);
        }
    }

    /**
     * Возвращает свойства раздела в виде массива
     *
     * @return array
     *
     * @since 4.01
     */
    public function toArray()
    {
        return $this->raw;
    }
}

