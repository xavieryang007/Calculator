<?php

class Token{
	private $type;
	private $value;
	public function __construct($type,$value)
	{
		$this->type=$type;
		$this->value=$value;
	}
	
	public function __get($name)
	{
		return $this->{$name};
	}
	
	public function __toString()
    {
        return 'type:'.$this->type.' value:'.$this->value;
    }
}

class Interpreter{
	private $current_char ;
	private $current_token ;
	private $text;
	private $pos=0;
	public function __construct($text){
		$this->text=trim($text);
	}
	
	public function error()
	{
		throw new \Exception('Lexer eroor');
	}
	
	public function get_next_token()
	{
		$text=$this->text;
		if ($this->pos > strlen($text)-1){
			return new Token('EOF', null);
		}
		
		$this->current_char = $text[$this->pos];
		if (is_numeric($this->current_char)){
			$token=new Token('INTEGER',intval($this->current_char));
			$this->pos++;
			return $token;
		}
		
		if ($this->current_char=="+"){
			$token = new Token('PLUS', $this->current_char);
            $this->pos ++;
            return $token;
		}
		$this->error();
	}
	
	public function eat($token_type)
	{
		if ($this->current_token->type==$token_type){
			$this->current_token=$this->get_next_token();
		}else{
			$this->error();
		}
	}
	
	
	public function expr()
	{
		$this->current_token=$this->get_next_token();
		$left=$this->current_token;
		$this->eat('INTEGER');
		$op=$this->current_token;
		$this->eat('PLUS');
		$right=$this->current_token;
		$this->eat('INTEGER');
		$result=$left->value+$right->value;
		return $result;
	}
}

do{
	fwrite(STDOUT,'xav>');;
	$input=fgets(STDIN);
	$Interpreter=new Interpreter($input);
	echo $Interpreter->expr();
	unset($Interpreter);
	
}while(true);


