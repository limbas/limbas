<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */
class CLimbasSoapService extends CLimbasSoapComponent
{
	const SOAP_ERROR=1001;
	
	private $className;
//	private $limbasTable;
	private $soapProvider;
//	private $objectName;
	private $wsdlUrl;
	private $serviceUrl;
	
	public $encoding = 'UTF-8';
	public $classMap = array();
	public $soapVersion;
	public $persistence = 'SOAP_PERSISTENCE_SESSION';

	/*
	 * for wsdl generation
	 */
	public $namespace;
	public $serviceName;
	
	private $operations;
	private $types;
	private $messages;
	
	public function __construct($className, $baseUrl)
	{
		$this->className = $className;
//		$this->objectName = ucfirst(lmb_strtolower($this->table));
//		$this->limbasTable = $limbasTable; // new Personen(); //new CLimbasTable($table, $definition);
		$this->soapProvider = new CLimbasSoapProvider($this->className);
		$this->classMap = $this->soapProvider->getClassMap();
		
		$baseUrl .= '?Service=' . $className;
		$wsdlUrl = $baseUrl . '&WSDL';
		$serviceUrl = $baseUrl;
		
		$this->wsdlUrl = $wsdlUrl;
		$this->serviceUrl = $serviceUrl;
	}
	
	public function run()
	{
		header('Content-Type: text/xml;charset=' . $this->encoding);
		if(_DEBUG)
			ini_set("soap.wsdl_cache_enabled", 0);
		$server=new SoapServer($this->wsdlUrl, $this->getOptions());
		try
		{
			if($this->persistence !== null)
				#$server->setPersistence($this->persistence);

			if(method_exists($server, 'setObject'))
				$server->setObject($this->soapProvider);
// 			else
// 				$server->setClass('CSoapObjectWrapper', $this->soapProvider);

			$server->handle();
		}
		catch(Exception $e)
		{
			if($e->getCode()!==self::SOAP_ERROR) // non-PHP error
			{
				// only log for non-PHP-error case because application's error handler already logs it
				// php <5.2 doesn't support string conversion auto-magically
				error_log($e->__toString());
			}
			$message=$e->getMessage();
//			if(YII_DEBUG)
				$message.=' ('.$e->getFile().':'.$e->getLine().")\n".$e->getTraceAsString();

			// We need to end application explicitly because of
			// http://bugs.php.net/bug.php?id=49513
			$server->fault(get_class($e),$message);
			exit(1);
		}
	}

	protected function getOptions()
	{
		$options=array();
		if($this->soapVersion === '1.1') {
			$options['soap_version'] = SOAP_1_1;
		}
		else if($this->soapVersion === '1.2') {
			$options['soap_version'] = SOAP_1_2;
		}
		$options['encoding'] = $this->encoding;
		foreach($this->classMap as $type => $className) {
			//$className=Yii::import($className,true);
			if(is_int($type)) {
				$type = $className;
			}
			$options['classmap'][$type] = $className;
		}

//		$options['classmap'][$this->objectName] = 'CLimbasTable';
		
		//error_log('Options: ' . print_r($options, true));
		
		return $options;
	}
	
	public function setClassMap($classMap) {
		$this->classMap = array_merge($this->classMap, $classMap);
	}
	
	public function renderWsdl()
	{
		$wsdl=$this->generateWsdl($this->className, $this->serviceUrl, $this->encoding);
		header('Content-Type: text/xml;charset=' . $this->encoding);
		header('Content-Length: '.(function_exists('mb_strlen') ? mb_strlen($wsdl,'8bit') : strlen($wsdl)));
		echo $wsdl;
	}
		
	public function generateWsdl($className, $serviceUrl, $encoding='UTF-8') 
	{
		$this->types = $this->soapProvider->getWsdlTypes();
		$this->operations= $this->soapProvider->getWsdlOperations();
		$this->messages = $this->soapProvider->getWsdlMessages();
		if($this->serviceName===null)
			$this->serviceName=$className;
		if($this->namespace===null)
			$this->namespace="urn:{$className}wsdl";
		
		return $this->buildDOM($serviceUrl,$encoding)->saveXML();
	}
	
	private function buildDOM($serviceUrl,$encoding)
	{
		$xml="<?xml version=\"1.0\" encoding=\"$encoding\"?>
		<definitions name=\"{$this->serviceName}\" targetNamespace=\"{$this->namespace}\"
		xmlns=\"http://schemas.xmlsoap.org/wsdl/\"
		xmlns:tns=\"{$this->namespace}\"
		xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\"
		xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
		xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\"
		xmlns:soap-enc=\"http://schemas.xmlsoap.org/soap/encoding/\"></definitions>";
	
		$dom=new DOMDocument();
		$dom->loadXml($xml);
		$this->addTypes($dom);
		
		$this->addMessages($dom);
		$this->addPortTypes($dom);
		$this->addBindings($dom);
		$this->addService($dom,$serviceUrl);
	
		return $dom;
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 */
	private function addTypes($dom)
	{
		$types=$dom->createElement('wsdl:types');
		$schema=$dom->createElement('xsd:schema');
		$schema->setAttribute('targetNamespace',$this->namespace);
/*		
		$simpleType = $dom->createElement('xsd:simpleType');
		$simpleType->setAttribute('name', 'id');
		$restriction = $dom->createElement('xsd:restriction'); 
		$restriction->setAttribute('base', 'xsd:integer');
		$constraint = $dom->createElement('xsd:minExclusive'); 
		$constraint->setAttribute('value', '1');
		$restriction->appendChild($constraint);
		$simpleType->appendChild($restriction);
		$schema->appendChild($simpleType);
*/		
		foreach($this->types as $phpType => $xmlType) {
			if (is_array($xmlType)) {
				$complexType = $dom->createElement('xsd:complexType');
				$complexType->setAttribute('name', $phpType);
				$container = $dom->createElement('xsd:sequence');
				//$container = $dom->createElement('xsd:all');
				//$container->setAttribute('minOccurs', '0');
				foreach($xmlType as $name => $soaptype)
				{
					$element = $dom->createElement('xsd:element');
					$element->setAttribute('name', $name);
					$element->setAttribute('type', $soaptype);
					$container->appendChild($element);
				}
				$complexType->appendChild($container);
				$schema->appendChild($complexType);
				$types->appendChild($schema);
			}
			else if ('complex_Type_Array' === $xmlType) {
				$complexType = $dom->createElement('xsd:complexType');
				$complexType->setAttribute('name', $phpType);
/*
				$complexContent = $dom->createElement('xsd:complexContent');
				$restriction = $dom->createElement('xsd:restriction');
				$restriction->setAttribute('base','soap-enc:Array');
				$attribute=$dom->createElement('xsd:attribute');
				$attribute->setAttribute('ref','soap-enc:arrayType');
				$attribute->setAttribute('wsdl:arrayType', lmb_substr($phpType, 0, lmb_strlen($phpType) - 5) .'[]');
				$restriction->appendChild($attribute);
				$complexContent->appendChild($restriction);
				$complexType->appendChild($complexContent);
*/
				$container = $dom->createElement('xsd:sequence');
				$element = $dom->createElement('xsd:element');
				$element->setAttribute('name', 'items');
				$element->setAttribute('minOccurs', '0');
				$element->setAttribute('maxOccurs', 'unbounded');
				$element->setAttribute('type', 'tns:' . lmb_substr($phpType, 0, lmb_strlen($phpType) - 5));
				$container->appendChild($element);
				$complexType->appendChild($container);
				
				$schema->appendChild($complexType);
				$types->appendChild($schema);
			}
		}
		$dom->documentElement->appendChild($types);
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 */
	private function addMessages($dom)
	{
		foreach($this->messages as $name => $message)
		{
			$element=$dom->createElement('wsdl:message');
			$element->setAttribute('name', $name);
			foreach($this->messages[$name] as $partName => $part)
			{
				if(is_array($part))
				{
					$partElement=$dom->createElement('wsdl:part');
					$partElement->setAttribute('name',$partName);
					$partElement->setAttribute('type',$part[0]);
					$element->appendChild($partElement);
				}
			}
			$dom->documentElement->appendChild($element);
		}
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 */
	private function addPortTypes($dom)
	{
		$portType=$dom->createElement('wsdl:portType');
		$portType->setAttribute('name', $this->soapProvider->className . 'PortType');
		$dom->documentElement->appendChild($portType);
		foreach ($this->operations as $name => $doc) {
			$portType->appendChild($this->createPortElement($dom,$name,$doc));
		}
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 * @param string $name method name
	 * @param string $doc doc
	 */
	private function createPortElement($dom,$name,$doc)
	{
		$operation=$dom->createElement('wsdl:operation');
		$operation->setAttribute('name',$name);

		$input = $dom->createElement('wsdl:input');
		$input->setAttribute('message', 'tns:'.$name.'Request');
		$output = $dom->createElement('wsdl:output');
		$output->setAttribute('message', 'tns:'.$name.'Response');

		$operation->appendChild($dom->createElement('wsdl:documentation',$doc));
		$operation->appendChild($input);
		$operation->appendChild($output);

		return $operation;
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 */
	private function addBindings($dom)
	{
		$binding=$dom->createElement('wsdl:binding');
		$binding->setAttribute('name', $this->soapProvider->className  . 'Binding');
		$binding->setAttribute('type', 'tns:' . $this->soapProvider->className  . 'PortType');

		$soapBinding=$dom->createElement('soap:binding');
		$soapBinding->setAttribute('style','rpc');
		$soapBinding->setAttribute('transport','http://schemas.xmlsoap.org/soap/http');
		$binding->appendChild($soapBinding);

		$dom->documentElement->appendChild($binding);


		foreach ($this->operations as $name => $doc) {
			$binding->appendChild($this->createOperationElement($dom, $name));
		}
	}

	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 * @param string $name method name
	 */
	private function createOperationElement($dom,$name)
	{
		$operation=$dom->createElement('wsdl:operation');
		$operation->setAttribute('name', $name);
		$soapOperation = $dom->createElement('soap:operation');
		$soapOperation->setAttribute('soapAction', $this->namespace.'#'.$name);
		$soapOperation->setAttribute('style','rpc');

		$input = $dom->createElement('wsdl:input');
		$output = $dom->createElement('wsdl:output');

		$soapBody = $dom->createElement('soap:body');
		$soapBody->setAttribute('use', 'encoded');
		$soapBody->setAttribute('namespace', $this->namespace);
		$soapBody->setAttribute('encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');
		$input->appendChild($soapBody);
		$output->appendChild(clone $soapBody);

		$operation->appendChild($soapOperation);
		$operation->appendChild($input);
		$operation->appendChild($output);

		return $operation;
	}
	/*
	 * @param DOMDocument $dom Represents an entire HTML or XML document; serves as the root of the document tree
	 * @param string $serviceUrl Web service URL
	 */
	private function addService($dom,$serviceUrl)
	{
		$service=$dom->createElement('wsdl:service');
		$service->setAttribute('name', $this->className . 'Service');

		$port=$dom->createElement('wsdl:port');
		$port->setAttribute('name', $this->className . 'Port');
		$port->setAttribute('binding', 'tns:' . $this->className . 'Binding');

		$soapAddress=$dom->createElement('soap:address');
		$soapAddress->setAttribute('location', $serviceUrl);
		$port->appendChild($soapAddress);
		$service->appendChild($port);
		$dom->documentElement->appendChild($service);
	}
}	

class LimbasException extends Exception
{
	
}