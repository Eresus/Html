<?php
/**
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО «Два слона», http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt    GPL License 3
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
 *
 * @package HTML
 * @subpackage Tests
 *
 * $Id: MyPlugin_Test.php 2173 2012-05-18 14:45:27Z mk $
 */


require_once __DIR__ . '/bootstrap.php';
require_once TESTS_SRC_DIR . '/html.php';

/**
 * @package HTML
 * @subpackage Tests
 */
class MyPlugin_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isValidRequestProvider
     * @covers Html::isValidRequest
     */
    public function testIsValidRequest($request, $options, $isValid, $message)
    {
        $isValidRequest = new ReflectionMethod('Html', 'isValidRequest');
        $isValidRequest->setAccessible(true);

        $plugin = new Html;

        $Eresus = new stdClass();
        $Eresus_CMS = $this->getMock('stdClass', array('getLegacyKernel'));
        $Eresus_CMS->expects($this->any())->method('getLegacyKernel')->
            will($this->returnValue($Eresus));
        Eresus_CMS::setMock($Eresus_CMS);

        $page = new stdClass();
        $app = $this->getMock('stdClass', array('getPage'));
        $app->expects($this->any())->method('getPage')->will($this->returnValue($page));
        $Eresus_Kernel = $this->getMock('stdClass', array('app'));
        $Eresus_Kernel->expects($this->any())->method('app')->will($this->returnValue($app));
        Eresus_Kernel::setMock($Eresus_Kernel);

        $Eresus->request = $request;
        $page->options = $options;
        $this->assertEquals($isValid, $isValidRequest->invoke($plugin), $message);
    }

    /**
     * @return array
     */
    public function isValidRequestProvider()
    {
        $requestEmpty = array(
            'method' => 'GET',
            'url' => 'http://example.org/',
            'path' => 'http://example.org/',
        );

        $requestWithFile = array(
            'method' => 'GET',
            'url' => 'http://example.org/file',
            'path' => 'http://example.org/',
        );

        $requestWithGetArgs = array(
            'method' => 'GET',
            'url' => 'http://example.org/?foo=bar',
            'path' => 'http://example.org/',
        );

        $requestWithFileAndGetArgs = array(
            'method' => 'GET',
            'url' => 'http://example.org/file?foo=bar',
            'path' => 'http://example.org/',
        );

        $requestPostEmpty = array(
            'method' => 'POST',
            'url' => 'http://example.org/',
            'path' => 'http://example.org/',
        );

        $optionsNone = array();
        $optionsAllowGet = array('disallowGET' => false);
        $optionsDisallowGet = array('disallowGET' => true);
        $optionsAllowPost = array('disallowPOST' => false);
        $optionsDisallowPost = array('disallowPOST' => true);

        return array(
            array($requestEmpty, $optionsNone, true, 'без параметров и опций'),

            array($requestWithGetArgs, $optionsNone, true, 'аргументы GET без опций'),
            array($requestWithGetArgs, $optionsAllowGet, true, 'аргументы GET с разрешнием GET'),
            array($requestWithGetArgs, $optionsDisallowGet, false,
                'аргументы GET без разрешения GET'),

            array($requestWithFile, $optionsNone, true, 'файл без опций'),
            array($requestWithFile, $optionsAllowGet, true, 'файл с разрешнием GET'),
            array($requestWithFile, $optionsDisallowGet, false, 'файл без разрешения GET'),

            array($requestWithFileAndGetArgs, $optionsNone, true,
                'файл и аргументы GET без опций'),
            array($requestWithFileAndGetArgs, $optionsAllowGet, true,
                'файл и аргументы GET с разрешнием GET'),
            array($requestWithFileAndGetArgs, $optionsDisallowGet, false,
                'файл и аргументы GET без разрешения GET'),

            array($requestPostEmpty, $optionsNone, true, 'POST без опций'),
            array($requestPostEmpty, $optionsAllowPost, true, 'POST с разрешением POST'),
            array($requestPostEmpty, $optionsDisallowPost, false, 'POST без разрешения POST'),
        );
    }
}

