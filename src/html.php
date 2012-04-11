<?php
/**
 * HTML
 *
 * Eresus 2.10
 *
 * ������ ������������ ���������� �������������� ����������������� �������
 *
 * @version 3.01
 *
 * @copyright 2005, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Group, http://eresus.ru/
 * @copyright 2010, ��� "��� �����", http://dvaslona.ru/
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @author Ghost <ghost@dvaslona.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package HTML
 *
 * $Id: html.php 60 2010-03-01 03:41:02Z ghost $
 */

/**
 * ����� �������
 *
 * @package HTML
 */
class Html extends ContentPlugin
{
	var $version = '3.01a';
	var $kernel = '2.10';
	var $title = 'HTML';
	var $description = '������ ������������ ���������� �������������� ����������������� �������';
	var $type = 'client,content,ondemand';

	/**
	 * ���������� ��������
	 *
	 * @param string $content  ����� �������
	 */
	function updateContent($content)
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get($page->id);
		$item['content'] = arg('content');
		$item['options']['disallowPOST'] = arg('disallowPOST', 'int');
		$Eresus->sections->update($item);
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������������� �����
	 *
	 * @return  string  �������
	 */
	function adminRenderContent()
	{
		global $Eresus, $page;

		if (arg('action') == 'update') $this->adminUpdate();
		$item = $Eresus->sections->get($page->id);
		$url = $page->clientURL($item['id']);
		$form = array(
		'name' => 'contentEditor',
		'caption' => $page->title,
		'width' => '100%',
		'fields' => array (
			array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'html','name' => 'content','height' => '400px', 'value'=>$item['content']),
				array ('type' => 'text', 'value' => '����� ��������: <a href="'.$url.'">'.$url.'</a>'),
				array ('type' => 'checkbox','name' => 'disallowPOST', 'label' => '��������� ���������� ��������� ������� POST', 'value'=>isset($item['options']['disallowPOST'])?$item['options']['disallowPOST']:false),
		 ),
		'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ???
	 * @return string
	 */
	function clientRenderContent()
	{
		global $Eresus, $page;

		$extra_GET_arguments = $Eresus->request['url'] != $Eresus->request['path'];
		$is_ARG_request = count($Eresus->request['arg']);
		$POST_requests_disallowed = isset($page->options['disallowPOST']) && $page->options['disallowPOST'];

		if ($extra_GET_arguments) $page->httpError(404);
		if ($is_ARG_request && $POST_requests_disallowed) $page->httpError(404);

		$result = parent::clientRenderContent();

		return $result;
	}
	//------------------------------------------------------------------------------
}
