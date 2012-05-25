<?php
/**
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2012, ООО «Два слона», http://dvaslona.ru/
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
	 * @covers Html::isValidRequest
	 */
	public function test_isValidRequest()
	{
		$m_isValidRequest = new ReflectionMethod('Html', 'isValidRequest');
		$m_isValidRequest->setAccessible(true);

		$plugin = new Html;

		$Eresus = new stdClass();
		$Eresus_CMS = $this->getMock('stdClass', array('getLegacyKernel'));
		$Eresus_CMS->expects($this->any())->method('getLegacyKernel')->
			will($this->returnValue($Eresus));
		Eresus_CMS::setMock($Eresus_CMS);

		$Eresus->request = array(
			'method' => 'GET',
			'url' => 'http://example.org/',
			'path' => 'http://example.org/',
		);
		$this->assertTrue($m_isValidRequest->invoke($plugin));

		$Eresus->request = array(
			'method' => 'GET',
			'url' => 'http://example.org/file',
			'path' => 'http://example.org/',
		);
		$this->assertFalse($m_isValidRequest->invoke($plugin));

		$Eresus->request = array(
			'method' => 'GET',
			'url' => 'http://example.org/?foo=bar',
			'path' => 'http://example.org/',
		);
		$this->assertFalse($m_isValidRequest->invoke($plugin));

		$Eresus->request = array('method' => 'POST');

		$page = new stdClass();
		$app = $this->getMock('stdClass', array('getPage'));
		$app->expects($this->any())->method('getPage')->will($this->returnValue($page));
		$Eresus_Kernel = $this->getMock('stdClass', array('app'));
		$Eresus_Kernel->expects($this->any())->method('app')->will($this->returnValue($app));
		Eresus_Kernel::setMock($Eresus_Kernel);

		$page->options = array();
		$this->assertTrue($m_isValidRequest->invoke($plugin));

		$page->options = array('disallowPOST' => false);
		$this->assertTrue($m_isValidRequest->invoke($plugin));

		$page->options = array('disallowPOST' => true);
		$this->assertFalse($m_isValidRequest->invoke($plugin));
	}
	//-----------------------------------------------------------------------------
}