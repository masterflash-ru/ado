<?php
/*
*
* 6.2.2019 добавлено преобразование типов, в момент заполнения экземпляра
* 03.04.2013 введено клонирование объектов, теперь RS клониуется со всеми потрохами
*/
namespace ADO\Entity;


class Field
{ // объект - элемент коллекции
 	private $parent_recordset; // объект родительсмкого рекордсета
 	// public $DateFormat;//формат даты
 	 	 	 	 	 	 	   
 	// перегруженное сво-ва
 	private $container = [
        'name' => null,             // имя параметра
        'originalvalue' => null,    // значение поля до каких-либо изменений, т.е.  старое  значение после изменения
        'value' => null,            // зеначение
        'definedsize' => null,      // максимальный размер поля
        'type' => null,             // тип данных
        'precision' => null,        // стиепень точности для числовых значений
        'numericscale' => null,     // кол-во знаков после зяпятой в числах
        'actualsize' => null        //фактический размер поля
    ];

 	
 	public function __construct (array $arr = [])
 	{
 	 	if (isset($arr['name'])){
            $this->container['name'] = $arr['name'];
        } else {
            $this->container['name'] = null;
        }
 	 	
        $this->container['value'] = null;
 	 	
        if (isset($arr['len'])){
            $this->container['actualsize'] = $arr['len'];
        } else {
            $this->container['actualsize'] = null;
        }
 	 	if (isset($arr['DefinedSize'])) {
            $this->container['definedsize'] = $arr['DefinedSize'];
        } else {
            $this->container['definedsize'] = null;
        }
 	 	if (isset($arr['Type'])) {
            $this->container['type'] = $arr['Type'];
        } else {
            $this->container['type'] = null;
        }
 	 	if (isset($arr['precision'])) {
            $this->container['precision'] = $arr['precision'];
        } else {
            $this->container['precision'] = null;
        }
 	 	if (isset($arr['NumericScale'])) {
            $this->container['numericscale'] = $arr['NumericScale'];
        } else {
            $this->container['numericscale'] = null;
        }
 	 	// $this->DateFormat=$arr['DateFormat'];
 	}
 	
 	
 	// ************************** перегрузка
 	public function &__get ($var)
 	{
 	 	$var = strtolower($var);
 	 	// проверим к какой ппеременной обращается
 	 	if (array_key_exists($var, $this->container))
 	 	 	return $this->container[$var];
 	 	$arr = debug_backtrace();
 	 	trigger_error(
 	 	 	 	"Undefined property: Field::\$$var in " . $arr[0]['file'] .
 	 	 	 	 	 	 " on line " . $arr[0]['line'], E_USER_WARNING);
 	 	return $var;
 	}

 	public function __set ($var, $value)
 	{
 	 	$var = strtolower($var);
 	 	if (! array_key_exists($var, $this->container)) {
 	 	 	$arr = debug_backtrace();
 	 	 	trigger_error(
 	 	 	 	 	"Undefined property: Field::\$$var in " . $arr[0]['file'] .
 	 	 	 	 	 	 	 " on line " . $arr[0]['line'], E_USER_WARNING);
 	 	}
        
        if ($var == 'value') {
            /*преобразуем типы*/
            switch ($this->container["type"]){
                case adSmallInt:
                case adInteger:
                case adTinyInt:
                case adUnsignedTinyInt:
                case adUnsignedSmallInt:
                case adUnsignedInt:
                case adBigInt:
                case adUnsignedBigInt: {
                    $value=(int)$value;
                    break;
                }
                case adSingle:
                case adDouble:
                case adCurrency:
                case adDecimal:{
                    $value=(float)$value;
                    break;
                }
            }
 	 	$this->container[$var] = $value;
        $this->parent_recordset->change_value($this);
        }
 	
 	}

 	public function __call ($name, $var)
 	{ // диспетчер служебных функций
 	 	if ($name == 'set_parent_recordset') {
 	 	 	$this->parent_recordset = $var[0];
 	 	 	return;
 	 	}
 	 	if ($name == 'set_value') {
 	 	 	$this->container['value'] = $var[0];
 	 	 	return;
 	 	}
 	 	echo 'Metod ' . $name . " is not found in Field object!\n";
 	
 	}
 	
 	// ************************* конец перегрузки

}
