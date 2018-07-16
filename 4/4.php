<?php
define('ISINTEGER','ISINTEGER');//定义整数类型描述
define('PLUS','PLUS');//定义操作符号类型描述 加法
define('MINUS','MINUS');//定义操作符号类型描述 减法
define('MUL','MUL');//定义操作符号类型描述 乘法
define('DIV','DIV');//定义操作符号类型描述 除法
define('WHITESPACE',' ');//定义空格
/**
Token  用来存储输入字符的类型
*/
class Token{
	private $type;
	private $value;
	/**
	$type ISINTEGER/PLUS/MINUS
	$value 对应的字符串
	*/
	public function __construct($type,$value)
	{
		$this->type=$type;
		$this->value=$value;
	}
	
	/**
	通过该方法来获取类的私有属性
	*/
	public function __get($name)
	{
		return $this->{$name};
	}
	/**
	用于调试
	*/
	public function __toString()
    {
        return 'type:'.$this->type.' value:'.$this->value;
    }
}

class Lexer{
	private $current_char ;
	private $current_token ;
	private $text;
	private $pos=0;
	/***
	$text 需要进行解释的字符串
	*/
	public function __construct($text){
		//去除前后可能存在的空格 这些空格是无效的
		$this->text=trim($text);
		//初始化 获取第一个字符
		$this->current_char = $this->text[$this->pos];
	}
	
	public function error()
	{
		throw new \Exception('Lexer eroor');
	}
	
	/*
	步进方法，每操作一个字符后前进一位
	*/
	public function advance()
	{
		$this->pos++;
		if ($this->pos>strlen($this->text)-1){
			$this->current_char=null;
		}else{
			$this->current_char=$this->text[$this->pos];
		}
	}
	
	/*
	去除空格
	*/
	public function skip_whitespace()
	{
		if ($this->current_char!=null&&$this->current_char==WHITESPACE){
			$this->advance();
		}
	}
	
	/*
	如果要支持多位的整数，则需要将每位数字存储起来
	*/
	public function integers()
	{
		$result='';//用于存储数字
		while($this->current_char!=null&&is_numeric($this->current_char)){//只要当前字符是数字就一直循环并将数字存储于$result
			$result.=$this->current_char;
			$this->advance();//步进方法，每操作一个字符后前进一位
		}
		return intval($result);//将数字字符串转成整数
	}
	
	//获取当前字符的Token  
	public function get_next_token()
	{
		while($this->current_char!=null){
			if ($this->current_char==WHITESPACE){
				$this->skip_whitespace();
				continue;
			}
			if (is_numeric($this->current_char)){
				return new Token(ISINTEGER,$this->integers());
			}
			
			if ($this->current_char=="+"){
				$this->advance();
				return new Token(PLUS,'+');
			}
			
			if ($this->current_char=="-"){
				$this->advance();
				return new Token(MINUS,'-');
			}
			
			if ($this->current_char=="*"){
				$this->advance();
				return new Token(MUL,'*');
			}
			
			if ($this->current_char=="/"){
				$this->advance();
				return new Token(DIV,'/');
			}
			return new Token('EOF', null);
		}
	}
}

//解释器
class Interpreter{
	private $current_token ;
	private $lexer ;
	
	public function __construct($lexer){
		//去除前后可能存在的空格 这些空格是无效的
		$this->lexer=$lexer;
		//初始化 获取第一个字符
		$this->current_token=$this->lexer->get_next_token();
	}
	
	//如果字符类型和判断的类型一致，则继续，否则输入错误
	public function eat($token_type)
	{
		if ($this->current_token->type==$token_type){
			$this->current_token=$this->lexer->get_next_token();
		}else{
			$this->error();
		}
	}
	
	public function error()
	{
		throw new \Exception('eroor');
	}
	public function factor()
	{
		$token=$this->current_token;
		$this->eat(ISINTEGER);
		return $token->value;
	}
	
	public function term()
	{
		$result=$this->factor();
		while(in_array($this->current_token->type,[MUL,DIV])){
			$token=$this->current_token;
			if ($token->type==MUL){
				$this->eat(MUL);
				$result=$result*$this->factor();
			}
			else if ($token->type==DIV){
				$this->eat(DIV);
				$result=$result/$this->factor();
			}
		}
		
		return $result;
		
	}
	
	//解释方法
	public function expr()
	{
		$result=$this->term();
		while(in_array($this->current_token->type,[PLUS,MINUS])){
			$token=$this->current_token;
			if ($token->type==PLUS){
				$this->eat(PLUS);
				$result=$result+$this->term();
			}
			else if ($token->type==MINUS){
				$this->eat(MINUS);
				$result=$result-$this->term();
			}
			
		}
		return $result;
	}
}

do{
	fwrite(STDOUT,'xav>');;
	$input=fgets(STDIN);
	$Interpreter=new Interpreter(new Lexer($input));
	echo $Interpreter->expr();
	unset($Interpreter);
	
}while(true);


