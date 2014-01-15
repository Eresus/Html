<?php
/**
 * Таблица страниц HTML
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
 * Таблица страниц HTML
 *
 * @since x.xx
 */
class Html_Entity_Table_Page extends ORM_Table
{
    protected function setTableDefinition()
    {
        $this->setTableName($this->getPlugin()->name);
        $this->hasColumns(array(
            'id' => array( // Должен всегда совпадать с ID раздела
                'type' => 'integer',
                'unsigned' => true,
            ),
            // TODO В будущем перенести сюда контент старницы
            'notes' => array(
                'type' => 'string',
                'length' => 65535,
            ),
        ));
    }
}

