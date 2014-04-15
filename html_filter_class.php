<?php

/*
	Название:	 	PHP класс для фильтрации HTML кода
	Описание:		http://savvateev.org/blog/36/
	Автор: 			Олег Савватеев (http://savvateev.org)
	Лицензия:		MIT License
	Версия:			1.0.1 от 11.04.2011
*/

	class html_filter{
		
		private $tags = array();
		private $del_tags = FALSE;
		
		//Этот метод устанавливает тэги для фильтрации
		function set_tags($arr=array()){
			if(!is_array($arr)) $arr=array();
			foreach($arr as $key=>$value){
				for($i=0; $i<count($value); $i++){
					$arr[$key][$i] = strtolower($arr[$key][$i]);
				}
			}
			$this->tags = array_change_key_case($arr);
		}
		
		//Этот метод устанавливает способ обработки недопустимых тэгов
		//По умолчанию тэги экранируются
		function del_invalid_tags($value){
			if($value) $this->del_tags = TRUE;
		}
		
		//Этот метод фильтрует код
		function filter($html_code){
			
			$open_tags_stack = array();
			$code = FALSE;
		
			//Разбиваем полученный код на учатки простого текста и теги
			$seg = array();
			while(preg_match('/<[^<>]+>/siu', $html_code, $matches, PREG_OFFSET_CAPTURE)){
				if($matches[0][1]) $seg[] = array('seg_type'=>'text', 'value'=>substr($html_code, 0, $matches[0][1]));	
				$seg[] = array('seg_type'=>'tag', 'value'=>$matches[0][0]);
				$html_code = substr($html_code, $matches[0][1]+strlen($matches[0][0]));
			}
			if($html_code != '') $seg[] = array('seg_type'=>'text', 'value'=>$html_code);
			
			//Обрабатываем полученные участки
			for($i=0; $i<count($seg); $i++){
			
				//Если участок является простым текстом экранируем в нем спец. символы HTML
				if($seg[$i]['seg_type'] == 'text') $seg[$i]['value'] = htmlentities($seg[$i]['value'], ENT_QUOTES, 'UTF-8');
			
				//Если участок является тэгом...
				elseif($seg[$i]['seg_type'] == 'tag'){
				
					//находим тип тэга(открывающий/закрывающий), имя тэга, строку атрибутов
					preg_match('#^<\s*(/)?\s*([a-z0-9]+)(.*?)>$#siu', $seg[$i]['value'], $matches);
					$matches[1] ? $seg[$i]['tag_type']='close' : $seg[$i]['tag_type']='open';
					$seg[$i]['tag_name'] = strtolower($matches[2]);
					
					if(($seg[$i]['tag_name']=='code') && ($seg[$i]['tag_type']=='close')) $code = FALSE;
					
					//Если этот тэг находится внутри конструкции <code></code> рассматриваем его не как тэг, а как простой текст
					if($code) {
						$seg[$i]['seg_type'] = 'text';
						$i--;
						continue;
					}
						
					//если тэг открывающий
					if($seg[$i]['tag_type'] == 'open') {
						
						//если тэг недопустимый экранируем/удаляем его
						if(!array_key_exists($seg[$i]['tag_name'], $this->tags)){
							if($this->del_tags) $seg[$i]['action'] = 'del';	
							else {$seg[$i]['seg_type'] = 'text';
								$i--;
								continue;
							}	
						}
					
						//если допустимый
						else {
						
							//находим атрибуты и оставляем только допустимые
							preg_match_all('#([a-z]+)\s*=\s*([\'\"])\s*(.*?)\s*\2#siu', $matches[3], $attr_m, PREG_SET_ORDER);
							$attr = array();
							foreach($attr_m as $arr) {
								if(in_array(strtolower($arr[1]), $this->tags[$seg[$i]['tag_name']])) $attr[strtolower($arr[1])] = htmlentities($arr[3], ENT_QUOTES, 'UTF-8');
							}
							$seg[$i]['attr'] = $attr;
							
							if($seg[$i]['tag_name'] == 'code') $code = TRUE;
							
							//если тэг требует закрывающего тэга заносим в стек открывающих тэгов
							if(!count($this->tags[$seg[$i]['tag_name']]) || ($this->tags[$seg[$i]['tag_name']][count($this->tags[$seg[$i]['tag_name']])-1] != FALSE)) array_push($open_tags_stack, $seg[$i]['tag_name']);
						}
					}
					
					
					//если тэг закрывающий
					else {
						
						//если тэг допустимый...
						if(array_key_exists($seg[$i]['tag_name'], $this->tags) && (!count($this->tags[$seg[$i]['tag_name']]) || ($this->tags[$seg[$i]['tag_name']][count($this->tags[$seg[$i]['tag_name']])-1] != FALSE))){
							
							if($seg[$i]['tag_name'] == 'code') $code = FALSE;
							
							//если стек открывающих тэгов пуст экранируем/удаляем этот тэг
							//...или в нем нет тэга с таким именем
							if((count($open_tags_stack) == 0) || (!in_array($seg[$i]['tag_name'], $open_tags_stack))) {
								if($this->del_tags) $seg[$i]['action'] = 'del';	
								else {$seg[$i]['seg_type'] = 'text';
									$i--;
									continue;
								}
							}
							
							//в противном случае...
							else {
			
								//если этот тэг не соответствует последнему из стека открывающих тэгов добавляем правильный закрывающий тэг
								$tn = array_pop($open_tags_stack);
								if($seg[$i]['tag_name'] != $tn){
									array_splice($seg, $i, 0, array(array('seg_type'=>'tag', 'tag_type'=>'close', 'tag_name'=>$tn, 'action'=>'add')));	
								}	
							}
								
						}
						
						//если тэг недопустимый удаляем его
						else {
							if($this->del_tags) $seg[$i]['action'] = 'del';	
							else {$seg[$i]['seg_type'] = 'text';
								$i--;
								continue;
							}
						}
					}
				}
			} 
											   								   
			//Закрываем оставшиеся в стеке тэги
			foreach(array_reverse($open_tags_stack) as $value) {
				array_push($seg, array('seg_type'=>'tag', 'tag_type'=>'close', 'tag_name'=>$value, 'action'=>'add'));
			}
			
			//Собираем профильтрованный код и возвращаем его
			$filtered_HTML = '';
			foreach($seg as $segment) {
				if($segment['seg_type'] == 'text') $filtered_HTML .= $segment['value'];
				
				elseif(($segment['seg_type'] == 'tag') && ($segment['action'] != 'del')) {
					if($segment['tag_type'] == 'open') {
						$filtered_HTML .= '<'.$segment['tag_name'];
						if(is_array($segment['attr'])){
							foreach($segment['attr'] as $attr_key=>$attr_val){
								$filtered_HTML .= ' '.$attr_key.'="'.$attr_val.'"';	
							}
						}
						if (count($this->tags[$segment['tag_name']]) && ($this->tags[$segment['tag_name']][count($this->tags[$segment['tag_name']])-1] == FALSE)) $filtered_HTML .= " /";
						$filtered_HTML .= '>';
					}
					elseif($segment['tag_type'] == 'close'){
						$filtered_HTML .= '</'.$segment['tag_name'].'>';
					}
				}
			}
			
			return $filtered_HTML;
		}			
	};