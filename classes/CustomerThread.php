<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CustomerThreadCore extends ObjectModel
{
	public $id;
	public $id_lang;
	public $id_contact;
	public $id_customer;
	public $id_order;
	public $id_product;
	public $status;
	public $email;
	public $token;
	public $date_add;
	public $date_upd;
	
	protected $table = 'customer_thread';
	protected $identifier = 'id_customer_thread';
	
	protected $fieldsRequired = array('id_lang', 'id_contact', 'token');
	protected $fieldsSize = array('email' => 254);
	protected $fieldsValidate = array('id_lang' => 'isUnsignedId', 'id_contact' => 'isUnsignedId', 'id_customer' => 'isUnsignedId',
									'id_order' => 'isUnsignedId', 'id_product' => 'isUnsignedId', 'email' => 'isEmail', 'token' => 'isGenericName');

	protected	$webserviceParameters = array(
			'fields' => array(
					'id_lang' => array('xlink_resource' => 'languages'),
					'id_contact' => array('xlink_resource' => 'employees'),
					'id_customer' => array('xlink_resource' => 'customers'),
					'id_order' => array('xlink_resource' => 'orders'),
					'id_product' => array('xlink_resource' => 'products'),
					'status' => array(),
					'email' => array(),
					'token' => array(),
			),
	);
	
	public function getFields()
	{
	 	parent::validateFields();
		$fields['id_lang'] = (int)($this->id_lang);
		$fields['id_contact'] = (int)($this->id_contact);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_order'] = (int)($this->id_order);
		$fields['id_product'] = (int)($this->id_product);
		$fields['status'] = pSQL($this->status);
		$fields['email'] = pSQL($this->email);
		$fields['token'] = pSQL($this->token);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}
	
	public function delete()
	{
		if (!Validate::isUnsignedId($this->id))
			return false;
 		
		$return = true;			
		$result = Db::getInstance()->executeS('
			SELECT `id_customer_message` FROM `'._DB_PREFIX_.'customer_message`
			WHERE `id_customer_thread` = '.(int)$this->id
		);

		if( count($result))
		{
			foreach ($result AS $res)
			{
			    $message = new CustomerMessage((int)$res['id_customer_message']);
			    if (!Validate::isLoadedObject($message))
					$return = false;
			    else
			        $return &= $message->delete();
			}
		}
		$return &= parent::delete();
		return $return;
	}
	
	public static function getCustomerMessages($id_customer)
	{
		return Db::getInstance()->ExecuteS('
		SELECT * FROM '._DB_PREFIX_.'customer_thread ct
		LEFT JOIN '._DB_PREFIX_.'customer_message cm ON ct.id_customer_thread = cm.id_customer_thread
		WHERE id_customer = '.(int)($id_customer));
	}
}