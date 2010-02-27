<?php
/**
 * HTML-��������
 *
 * Eresus 2
 *
 * ������ ������������ ���������� �������������� ����������������� �������
 *
 * @version 3.00
 *
 * @copyright 	2005-2006, ProCreat Systems, http://procreat.ru/
 * @copyright   2007-2008, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
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
 */

class Html extends ContentPlugin {
	var $version = '3.00';
	var $kernel = '2.10rc';
	var $title = 'HTML';
	var $description = 'HTML ��������';
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
		$item['options']['allowPOST'] = arg('allowPOST', 'int');
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
				array ('type' => 'checkbox','name' => 'allowPOST', 'label' => '��������� ���������� ��������� ������� POST', 'value'=>isset($item['options']['allowPOST'])?$item['options']['allowPOST']:false),
		 ),
		'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------
	function clientRenderContent()
	{
		global $Eresus, $page;

		$extra_GET_arguments = $Eresus->request['url'] != $Eresus->request['path'];
		$is_ARG_request = count($Eresus->request['arg']);
		$POST_requests_alowed = isset($page->options['allowPOST']) && $page->options['allowPOST'];

		if ($extra_GET_arguments) $page->httpError(404);
		if ($is_ARG_request && !$POST_requests_alowed) $page->httpError(404);

		$result = parent::clientRenderContent();

		return $result;
	}
	//------------------------------------------------------------------------------
}
