<?php

	class UP
	{
		public $msg;         // 保存上传的消息
		public $fileName;    // 保存上传之后的随机文件名
		private $f;          // 单个上传的文件
		private $maxSize;    // 控制大小
		private $types;      // 允许类型
		private $saveDir;    // 保存位置
		
		// 预设 保存位置  允许类型  最大限制
		public function __construct($saveDir, $types, $maxSize)
		{
			$this -> saveDir = $saveDir;
			$this -> types   = $types;
			$this -> maxSize = $maxSize;
		}
		
		// 检测错误号
		private function checkError()
		{
			
		    switch($this -> f['error']){
				case 0: $this -> msg = '上传成功'; return true;
				case 1: $this -> msg = '文件过大'; break;
				case 2: $this -> msg = '文件过大'; break;
				case 3: $this -> msg = '部分文件过大'; break;
				case 4: $this -> msg = '没有文件被上传'; break;
				case 6: $this -> msg = '找不到临时目录'; break;
				case 7: $this -> msg = '写入文件失败'; break;
				default:
					$this -> msg = '未知错误';
			}
			
			return false;
			
		}
		
		// 检测大小
		private function checkSize()
		{
			if ($this -> f['size'] > $this -> maxSize) {
			   $this -> msg = '文件过大';
			   return false;
			}
			
			return true;
			
		}
		
		// 检测类型
		private function checkType()
		{
			if (in_array($this -> f['type'], $this -> types)) {
			    return true;
			}
			$this -> msg = '文件类型不符合要求';
			return false;
		}
		
		// 生成随机的自定义文件名
		private function rndName()
		{
		   $ext = '.'.pathinfo($this->f['name'], PATHINFO_EXTENSION);
		   do{
		       $fileName = date('YmdHis').rand(1000,9999).$ext;
		   }while(file_exists($this -> saveDir.$fileName));
		   $this -> fileName = $fileName;
		}
		
		// 判断是否为合法上传
		private function checkHf()
		{
			
			if (is_uploaded_file($this -> f['tmp_name'])){
				return true;
			}
			$this -> msg = '不是合法的上传文件';
			return false;
		}
		
		/*
            功能: 执行上传处理    
            参数: $file 相当于 $_FILES['gpic']
            返回: 成功返回true 失败返回false
                  上传后的随机文件名保存到 $file
        */ 
		public function upload($file)
		{
			
			$this->f = $file;
		    $flag = $this->checkError() && $this->checkSize() && $this->checkType() && $this->checkHf();
			
			if ($flag) {
			   $this -> rndName();
			   // var_dump($this -> saveDir.$this->fileName);
			   $up_res =  move_uploaded_file($this->f['tmp_name'],$this -> saveDir.$this->fileName);
			   if($up_res){
			   		return $this -> saveDir.$this->fileName;
			   }else{
			   		return false;
			   }

			}
			
			return false;
			
		}
	
	
	}


